const { spawn } = require('child_process');
const path = require('path');

console.log('====================================================');
console.log(' 🚀 Starting WORKWAY Full-Stack Application');
console.log('====================================================');
console.log(' Starting Backend Server on http://localhost:5000 ...');
console.log(' Starting Frontend Dev Server on http://localhost:5173 ...');
console.log('====================================================\n');

const backend = spawn('npm', ['start'], {
  cwd: path.join(__dirname, 'backend'),
  stdio: 'inherit',
  shell: true
});

const frontend = spawn('npm', ['run', 'dev'], {
  cwd: path.join(__dirname, 'frontend'),
  stdio: 'inherit',
  shell: true
});

backend.on('error', (err) => {
  console.error('Failed to start backend server:', err);
});

frontend.on('error', (err) => {
  console.error('Failed to start frontend dev server:', err);
});

process.on('SIGINT', () => {
  console.log('\nStopping WORKWAY servers...');
  backend.kill();
  frontend.kill();
  process.exit();
});
