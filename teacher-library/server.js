/* خادم محلي بسيط بدون اعتماديات — لتشغيل المشروع وتجربة الطباعة/PDF
   التشغيل: node server.js   ثم افتحي http://localhost:4173 */
const http = require('http');
const fs   = require('fs');
const path = require('path');

const ROOT = __dirname;
const PORT = Number(process.env.PORT) || 4173;

const TYPES = {
  '.html':'text/html; charset=utf-8',
  '.css' :'text/css; charset=utf-8',
  '.js'  :'text/javascript; charset=utf-8',
  '.json':'application/json; charset=utf-8',
  '.md'  :'text/markdown; charset=utf-8',
  '.svg' :'image/svg+xml',
  '.png' :'image/png',
  '.jpg' :'image/jpeg',
  '.ico' :'image/x-icon',
  '.woff2':'font/woff2'
};

const server = http.createServer((req, res) => {
  let urlPath = decodeURIComponent((req.url || '/').split('?')[0]);
  if(urlPath === '/') urlPath = '/index.html';

  const filePath = path.normalize(path.join(ROOT, urlPath));
  if(!filePath.startsWith(ROOT)){
    res.writeHead(403); res.end('403 Forbidden'); return;
  }

  fs.stat(filePath, (err, stat) => {
    let target = filePath;
    if(!err && stat.isDirectory()) target = path.join(filePath, 'index.html');

    fs.readFile(target, (err2, buf) => {
      if(err2){
        res.writeHead(404, {'Content-Type':'text/plain; charset=utf-8'});
        res.end('404 — الملف غير موجود');
        return;
      }
      const ext = path.extname(target).toLowerCase();
      res.writeHead(200, {
        'Content-Type': TYPES[ext] || 'application/octet-stream',
        'Cache-Control': 'no-cache',
        'X-Content-Type-Options': 'nosniff'
      });
      res.end(buf);
    });
  });
});

server.listen(PORT, () => {
  console.log('==================================================');
  console.log('  مكتبتي التعليمية للمعلمة الليبية — خادم محلي');
  console.log('  http://localhost:' + PORT);
  console.log('  اضغطي Ctrl+C للإيقاف');
  console.log('==================================================');
});
