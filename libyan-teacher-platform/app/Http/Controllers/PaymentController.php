<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('student')->latest()->paginate(10);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        return view('payments.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|in:cash,bank_transfer,card,online',
            'status' => 'nullable|in:pending,completed,failed,refunded',
            'description' => 'nullable|string',
            'receipt_number' => 'nullable|string|max:100|unique:payments,receipt_number',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Payment::create($validator->validated());

        return redirect()->route('payments.index')->with('success', 'تم إضافة الدفعة بنجاح');
    }

    public function show(Payment $payment)
    {
        $payment->load('student');

        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|in:cash,bank_transfer,card,online',
            'status' => 'nullable|in:pending,completed,failed,refunded',
            'description' => 'nullable|string',
            'receipt_number' => 'nullable|string|max:100|unique:payments,receipt_number,' . $payment->id,
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $payment->update($validator->validated());

        return redirect()->route('payments.index')->with('success', 'تم تحديث الدفعة بنجاح');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'تم حذف الدفعة بنجاح');
    }

    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'payment_method' => 'nullable|in:cash,bank_transfer,card,online',
            'status' => 'nullable|in:pending,completed,failed,refunded',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $query = Payment::query();

        if ($request->filled('start_date')) {
            $query->where('payment_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $statistics = [
            'total_payments' => $query->count(),
            'total_amount' => (clone $query)->where('status', 'completed')->sum('amount'),
            'pending_amount' => (clone $query)->where('status', 'pending')->sum('amount'),
            'failed_amount' => (clone $query)->where('status', 'failed')->sum('amount'),
            'refunded_amount' => (clone $query)->where('status', 'refunded')->sum('amount'),
            'average_amount' => (clone $query)->where('status', 'completed')->avg('amount'),
            'by_payment_method' => (clone $query)->where('status', 'completed')
                ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                ->groupBy('payment_method')
                ->get(),
            'by_status' => (clone $query)
                ->select('status', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                ->groupBy('status')
                ->get(),
            'monthly_summary' => (clone $query)->where('status', 'completed')
                ->select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('count(*) as count'),
                    DB::raw('sum(amount) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get(),
            'recent_payments' => (clone $query)->with('student')->latest()->limit(10)->get(),
        ];

        return view('payments.report', compact('statistics'));
    }
}
