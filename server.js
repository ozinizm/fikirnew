const { createServer } = require('http')
const { parse } = require('url')
const next = require('next')
const fs = require('fs')
const path = require('path')

// Write production env vars to .env.local for PHP admin panel
try {
  const envContent = [
    `DB_HOST=${process.env.DB_HOST || '127.0.0.1'}`,
    `DB_PORT=${process.env.DB_PORT || '3306'}`,
    `DB_NAME=${process.env.DB_NAME || ''}`,
    `DB_USER=${process.env.DB_USER || ''}`,
    `DB_PASSWORD=${process.env.DB_PASSWORD || ''}`
  ].join('\n') + '\n';
  fs.writeFileSync(path.join(__dirname, '.env.local'), envContent);

  // Write debug env keys (safely without values)
  const envKeys = Object.keys(process.env).filter(k => 
    k.includes('DB') || k.includes('PASS') || k.includes('USER') || k.includes('NAME') || k.includes('PORT') || k.includes('HOST')
  );
  if (!fs.existsSync(path.join(__dirname, 'tmp'))) {
    fs.mkdirSync(path.join(__dirname, 'tmp'), { recursive: true });
  }
  fs.writeFileSync(path.join(__dirname, 'tmp/env_keys_debug.txt'), JSON.stringify({ envKeys, hasDbVars: Boolean(process.env.DB_NAME) }, null, 2));
} catch (e) {
  console.error("Error writing .env.local or debug files on startup:", e);
}

const dev = false
const app = next({ dev })
const handle = app.getRequestHandler()

// cPanel Passenger passes the port to bind dynamically in process.env.PORT
const port = process.env.PORT || 3000

app.prepare().then(() => {
  createServer((req, res) => {
    const parsedUrl = parse(req.url, true)
    handle(req, res, parsedUrl)
  }).listen(port, err => {
    if (err) throw err
    console.log(`> Ready on port ${port}`)
  })
})

