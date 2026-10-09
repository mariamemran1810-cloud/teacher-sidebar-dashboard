<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AIController extends Controller
{
    /**
     * Display AI tools page.
     */
    public function index(): View
    {
        $subjects = Subject::all();
        return view('ai.index', compact('subjects'));
    }

    /**
     * Generate a lesson plan using AI.
     */
    public function generateLesson(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic' => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
            'duration' => 'required|integer|min:15|max:180',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);

        $prompt = "Create a detailed lesson plan for {$subject->name} on the topic '{$validated['topic']}' "
            . "for grade level {$validated['grade_level']}, duration {$validated['duration']} minutes. "
            . "Include objectives, materials needed, introduction, main activities, and assessment.";

        $response = $this->callAI($prompt);

        return redirect()->route('ai.index')
            ->with('success', 'Lesson plan generated successfully.')
            ->with('ai_response', $response);
    }

    /**
     * Generate an exam using AI.
     */
    public function generateExam(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic' => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
            'question_count' => 'required|integer|min:5|max:50',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);

        $prompt = "Generate an exam for {$subject->name} on '{$validated['topic']}' "
            . "for grade {$validated['grade_level']}. "
            . "Create {$validated['question_count']} questions with {$validated['difficulty']} difficulty. "
            . "Include multiple choice, short answer, and essay questions with an answer key.";

        $response = $this->callAI($prompt);

        return redirect()->route('ai.index')
            ->with('success', 'Exam generated successfully.')
            ->with('ai_response', $response);
    }

    /**
     * Generate practice questions using AI.
     */
    public function generateQuestions(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic' => 'required|string|max:255',
            'count' => 'required|integer|min:1|max:20',
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);

        $prompt = "Generate {$validated['count']} {$validated['type']} questions "
            . "for {$subject->name} on the topic '{$validated['topic']}'. "
            . "Include correct answers and explanations.";

        $response = $this->callAI($prompt);

        return redirect()->route('ai.index')
            ->with('success', 'Questions generated successfully.')
            ->with('ai_response', $response);
    }

    /**
     * Generate a worksheet using AI.
     */
    public function generateWorksheet(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic' => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
            'include_answer_key' => 'boolean',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);

        $prompt = "Create a printable worksheet for {$subject->name} on '{$validated['topic']}' "
            . "suitable for grade {$validated['grade_level']}. "
            . "Include clear instructions and a variety of exercises.";

        if ($validated['include_answer_key'] ?? false) {
            $prompt .= " Include a separate answer key section.";
        }

        $response = $this->callAI($prompt);

        return redirect()->route('ai.index')
            ->with('success', 'Worksheet generated successfully.')
            ->with('ai_response', $response);
    }

    /**
     * Test connection to AI provider.
     */
    public function testConnection(Request $request): JsonResponse
    {
        try {
            $response = $this->callAI('Hello, this is a connection test. Respond with "Connection successful."');

            return response()->json([
                'success' => true,
                'message' => 'AI connection is working.',
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to AI provider: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Call the AI provider API.
     */
    private function callAI(string $prompt): string
    {
        $apiKey = config('services.ai.api_key');
        $endpoint = config('services.ai.endpoint');

        if (!$apiKey || !$endpoint) {
            return 'AI service is not configured. Please set up your AI API credentials.';
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($endpoint, [
            'model' => config('services.ai.model', 'gpt-4'),
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 2000,
        ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content', 'No response generated.');
        }

        throw new \Exception('AI API request failed: ' . $response->body());
    }
}
