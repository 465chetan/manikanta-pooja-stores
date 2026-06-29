const fs = require('fs');
const http = require('http');

let content = fs.readFileSync('js/products.js', 'utf8');
// strip const to var so we can eval it easily
content = content.replace(/const PRODUCTS/, 'var PRODUCTS').replace(/const CATEGORIES/, 'var CATEGORIES').replace(/const FESTIVALS/, 'var FESTIVALS');

eval(content);

const data = JSON.stringify(PRODUCTS);

const options = {
  hostname: 'localhost',
  port: 8000,
  path: '/sync.php',
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Content-Length': Buffer.byteLength(data)
  }
};

const req = http.request(options, (res) => {
  let body = '';
  res.on('data', chunk => body += chunk);
  res.on('end', () => console.log('Response:', body));
});

req.on('error', (e) => console.error('Error:', e.message));
req.write(data);
req.end();
