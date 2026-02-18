const axios = require('axios');

// Ganti dengan URL hosting Anda
const apiUrl = 'https://app.bimmo.id/api/webhook/whatsapp';

// Data dummy (Pura-pura dari WA)
const payload = {
    message: 'MASUK 10000 TEST API Test Connection',
    sender: '628123456789' // Nomor asal (pastikan ada di DB hosting atau pakai yg belum ada untuk tes error)
};

console.log(`Mengirim tes ke: ${apiUrl}`);

axios.post(apiUrl, payload)
    .then(response => {
        console.log('✅ SUKSES!');
        console.log('Status:', response.status);
        console.log('Response:', response.data);
    })
    .catch(error => {
        console.log('❌ GAGAL!');
        if (error.response) {
            console.log('Status:', error.response.status); // 404, 419, 500?
            console.log('Data:', error.response.data);
        } else {
            console.log('Error:', error.message);
        }
    });
