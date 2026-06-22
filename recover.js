const fs = require('fs');

const transcriptPath = 'C:\\Users\\oguzh\\.gemini\\antigravity-ide\\brain\\bc5289c4-3b83-4afa-8acf-259dd6b285f9\\.system_generated\\logs\\transcript.jsonl';
const lines = fs.readFileSync(transcriptPath, 'utf-8').split('\n');

for (let i = lines.length - 1; i >= 0; i--) {
  if (lines[i].includes('Header.tsx') && lines[i].includes('The following is the entire, complete content of the requested file.')) {
    fs.writeFileSync('header-recovery.json', lines[i]);
    break;
  }
}

for (let i = lines.length - 1; i >= 0; i--) {
  if (lines[i].includes('Footer.tsx') && lines[i].includes('The following is the entire, complete content of the requested file.')) {
    fs.writeFileSync('footer-recovery.json', lines[i]);
    break;
  }
}

for (let i = lines.length - 1; i >= 0; i--) {
  if (lines[i].includes('Hero.tsx') && lines[i].includes('The following is the entire, complete content of the requested file.')) {
    fs.writeFileSync('hero-recovery.json', lines[i]);
    break;
  }
}
