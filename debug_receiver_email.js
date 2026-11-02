const dotenv = require('dotenv');
const path = require('path');
dotenv.config({ path: path.join(__dirname, '../.env') });
dotenv.config({ path: path.join(__dirname, '.env') });

const { sendTestEmail } = require('./utils/emailService');

async function testReceiver() {
  const testRecipients = process.argv.slice(2);
  if (testRecipients.length === 0) {
    console.error('Usage: node backend/debug_receiver_email.js <gmail-address> <college-address>');
    process.exitCode = 1;
    return;
  }

  for (const recipient of testRecipients) {
    console.log(`\n--- Testing delivery to: ${recipient} ---`);
    const result = await sendTestEmail(recipient);
    console.log(`Test result for ${recipient}:`, result.success ? 'accepted by SMTP' : result.error);
    if (!result.success) process.exitCode = 1;
  }
}

testReceiver();
