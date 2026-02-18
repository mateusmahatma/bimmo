const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const axios = require('axios');

// Initialize the client
const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ],
        authTimeoutMs: 60000,
    },
    // Adding userAgent is often required to prevent "Protocol error"
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36'
});

// Generate QR Code
client.on('qr', (qr) => {
    console.log('Scan QR Code ini dengan WhatsApp Anda:');
    qrcode.generate(qr, { small: true });
});

// Client is ready
client.on('ready', () => {
    console.log('Client is ready! Bot WhatsApp sudah aktif.');
    console.log('Coba kirim pesan ke diri sendiri/nomor lain: MASUK 50000 GAJI Gaji Bulanan');
});

// Message handling (Supports self-messages/Note to Self)
client.on('message_create', async msg => {
    const text = msg.body;

    // Check keywords (Case insensitive start)
    const upperText = text.toUpperCase();
    const keywords = ['MASUK', 'PEMASUKAN', 'IN', 'KELUAR', 'PENGELUARAN', 'OUT'];

    const isTransaction = keywords.some(keyword => upperText.startsWith(keyword));

    if (isTransaction) {
        console.log(`[Pesan Masuk] ${msg.from}: ${text}`);

        try {
            // Resolving Real Number (Handle Linked Device IDs / LIDs)
            const contact = await msg.getContact();
            const realSenderNumber = contact.number || contact.id.user;

            console.log(`[Message From] RAW: ${msg.from} | REAL: ${realSenderNumber}`);

            const apiUrl = 'https://app.bimmo.id/api/webhook/whatsapp';

            console.log(`[Sending to API] ${apiUrl} | Data: ${JSON.stringify({ message: text, sender: realSenderNumber })}`);

            const response = await axios.post(apiUrl, {
                message: text,
                sender: realSenderNumber
            });

            console.log(`[API Response] Status: ${response.status} | Data:`, response.data);

            if (response.data && response.data.reply) {
                msg.reply(response.data.reply);
                console.log(`[Balasan ke WA] ${response.data.reply}`);
            }

        } catch (error) {
            console.error('Error hitting Laravel API:', error.message);
            if (error.response) {
                console.error('Response Status:', error.response.status);
                console.error('Response Data:', JSON.stringify(error.response.data));
            } else if (error.request) {
                console.error('No response received from API. Check if Server is running.');
            }
            msg.reply('Maaf, sistem sedang offline atau ada kesalahan teknis.');
        }
    }
});

client.initialize();
