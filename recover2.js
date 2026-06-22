const fs = require('fs');

const transcriptPath = 'C:\\Users\\oguzh\\.gemini\\antigravity-ide\\brain\\bc5289c4-3b83-4afa-8acf-259dd6b285f9\\.system_generated\\logs\\transcript.jsonl';
const lines = fs.readFileSync(transcriptPath, 'utf-8').split('\n');

for (let i = lines.length - 1; i >= 0; i--) {
  if (!lines[i]) continue;
  try {
    const obj = JSON.parse(lines[i]);
    if (obj.content && obj.content.includes('The following is the entire, complete content of the requested file.') && obj.content.includes('Header.tsx')) {
      fs.writeFileSync('header-full.txt', obj.content);
      break;
    }
  } catch(e) {}
}

for (let i = lines.length - 1; i >= 0; i--) {
  if (!lines[i]) continue;
  try {
    const obj = JSON.parse(lines[i]);
    if (obj.content && obj.content.includes('The following is the entire, complete content of the requested file.') && obj.content.includes('Footer.tsx')) {
      fs.writeFileSync('footer-full.txt', obj.content);
      break;
    }
  } catch(e) {}
}

for (let i = lines.length - 1; i >= 0; i--) {
  if (!lines[i]) continue;
  try {
    const obj = JSON.parse(lines[i]);
    if (obj.content && obj.content.includes('The following is the entire, complete content of the requested file.') && obj.content.includes('Hero.tsx')) {
      fs.writeFileSync('hero-full.txt', obj.content);
      break;
    }
  } catch(e) {}
}
