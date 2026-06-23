const axios = require('axios');
const pdf = require('pdf-parse');
const mammoth = require('mammoth');
const { Client, LocalAuth } = require('whatsapp-web.js');
const ADMIN_NUMBER = '110071723901092@lid';

const client = new Client({
    authStrategy: new LocalAuth(),
    takeoverOnConflict: true,
    takeoverTimeoutMs: 30000,

    puppeteer: {
        headless: false,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--disable-gpu'
        ]
    }
});
// Anti Spam
const cooldowns = new Map();
const aiUsers = new Set();
const csUsers = new Set();
const activeUsers = new Map();
const userDocuments = new Map();

async function send(chatId, text) {
    try {
        await client.sendMessage(chatId, text);
    } catch (err) {
        console.error('SEND ERROR:', err);
    }
}

async function getUserNumber(msg) {
    try {
        const contact = await msg.getContact();
        return contact.number || msg.from.replace(/@.*/, '');
    } catch {
        return msg.from.replace(/@.*/, '');
    }
}

client.on('ready', () => {
    console.log('✅ Bot siap digunakan!');
});

client.on('message', async (msg) => {

    console.log('=== PESAN MASUK ===');

    try {

            const sender = msg.from;
            const text = (msg.body || '').trim().toLowerCase();
            if (text === 'test faq') {

    try {

        const res = await axios.get(
            'http://127.0.0.1:8000/api/faqs/search/skt'
        );

        console.log(res.data);

        await msg.reply(
            res.data.data.answer
        );

    } catch (err) {

        console.log(err.message);

        await msg.reply(
            'FAQ tidak ditemukan'
        );
    }

    return;
}
            const now = Date.now();

            const senderNumber = sender.replace(/@.*/, '');
            const adminNumber = ADMIN_NUMBER.replace(/@.*/, '');

            console.log('senderNumber:', senderNumber);
            console.log('adminNumber:', adminNumber);

            if (!msg.body && !msg.hasMedia) return;
            if (msg.fromMe) return;
            if (msg.from.includes('@g.us')) return;
            if (msg.isStatus) return;

const contact = await msg.getContact();

// ======================
// BACA PDF & DOCX
// ======================

if (msg.hasMedia) {

    try {

        const media = await msg.downloadMedia();

        if (!media) {
            await msg.reply('❌ Gagal membaca file.');
            return;
        }

        const buffer = Buffer.from(
            media.data,
            'base64'
        );

        let extractedText = '';

        // PDF
        if (media.mimetype === 'application/pdf') {

            console.log('=== PDF DEBUG ===');
            console.log('TYPE:', typeof pdf);
            console.log('MIMETYPE:', media.mimetype);
            console.log('BUFFER SIZE:', buffer.length);
            console.log('=================');

            const pdfData = await pdf(buffer);

            extractedText = pdfData.text;
        }

        // DOCX
        else if (
            media.mimetype.includes(
                'wordprocessingml.document'
            )
        ) {

            const result =
                await mammoth.extractRawText({
                    buffer
                });

            extractedText = result.value;
        }

        else {

            await msg.reply(
                '📄 Saat ini hanya mendukung PDF dan DOCX.'
            );

            return;
        }

        userDocuments.set(
            sender,
            extractedText
        );

        await msg.reply(
`📄 Dokumen berhasil dibaca.

Silakan ajukan pertanyaan terkait isi dokumen.`
        );

        return;

    } catch (err) {

        console.error(err);

        await msg.reply(
            '❌ Gagal memproses dokumen.'
        );

        return;
    }
}

// =========================
// ADMIN REPLY
// =========================

if (
    msg.from === ADMIN_NUMBER &&
    msg.body.startsWith('!reply ')
) {

    const parts = msg.body.split(' ');

    const targetNumber = parts[1];
    const replyMessage = parts.slice(2).join(' ');

    if (!targetNumber || !replyMessage) {

        await msg.reply(
            'Format:\n!reply 628xxxxxxxxxx Pesan'
        );

        return;
    }

    const targetChat = `${targetNumber}@c.us`;

    try {

        await client.sendMessage(
            targetChat,
            `👨‍💼 *Pesan dari Petugas Bangkesbangpol*\n\n${replyMessage}`
        );

        await msg.reply('✅ Pesan berhasil dikirim');

    } catch (err) {

        console.error(err);

        await msg.reply('❌ Gagal mengirim pesan');
    }

    return;
}

        // =========================
        // 1. FILTER DASAR (WAJIB)
        // =========================
        if (!msg.body) return;                 // kosong
        if (msg.fromMe) return;                // pesan bot sendiri
        if (msg.from.includes('@g.us')) return; // grup
        if (msg.isStatus) return;              // status WA

        console.log("FROM:", sender);
        console.log("TEXT:", text);
        console.log("Nama:", contact.pushname);
        console.log("Nomor:", contact.number);

        // =========================
        // 2. ADMIN STOP CS
        // =========================
        if (text === 'stop cs' && msg.from === ADMIN_NUMBER) {
            csUsers.clear();
            await msg.reply('✅ Semua sesi CS ditutup.');
            return;
        }

        // =========================
        // 3. MASUK CS MODE
        // =========================
if (text === 'cs') {

    activeUsers.set(
        ADMIN_NUMBER,
        sender
    );

    csUsers.add(sender);

    await msg.reply(
        '👨‍💼 CS MODE AKTIF'
    );

    return;
}

// =========================
// ADMIN BALAS CS
// =========================
if (sender.includes('110071723901092')) {

    console.log('ADMIN TERDETEKSI');

    const targetUser =
        activeUsers.get(ADMIN_NUMBER);

    console.log('TARGET USER:', targetUser);

    if (targetUser) {

        await client.sendMessage(
            targetUser,
            `👨‍💼 CS Bangkesbangpol\n\n${msg.body}`
        );

        return;
    }

}

        // =========================
        // 4. FORWARD CS MODE
        // =========================
        if (
            csUsers.has(sender) &&
            text !== 'menu'
            ) {

            const userNumber = sender.replace(/@.*/, '');

            await client.sendMessage(
                ADMIN_NUMBER,
                `💬 CHAT USER (CS MODE)

📱 ${userNumber}

💬 ${msg.body}`
            );

            return;
        }

// Perintah yang tidak terkena cooldown
const bypassCooldown = [
    'menu',
    '1',
    '2',
    '3',
    '4',
    '5',
    '6',
    'cs'
];

// Anti spam 5 detik
if (!bypassCooldown.includes(text)) {

    if (cooldowns.has(sender)) {

        const last = cooldowns.get(sender);

        if (now - last < 5000) {
            return;
        }
    }

    cooldowns.set(sender, now);
}

// Menu utama (selalu bisa dipanggil)
if (text === 'menu') {

    aiUsers.delete(sender);
    csUsers.delete(sender);

    await send(
        msg.from,
`🏛️ *MITRA BANGKESBANGPOL KABUPATEN BOYOLALI*

Selamat datang.

Silakan pilih layanan berikut:

1️⃣ Informasi SKT Ormas
2️⃣ Persyaratan Pendaftaran Ormas
3️⃣ Alur Pengajuan SKT
4️⃣ Jam Operasional & Lokasi Kantor
5️⃣ Hubungi Petugas/Admin
6️⃣ Konsultasi AI

Ketik angka *1 - 6* sesuai kebutuhan Anda.`
    );

    return;
}

        if (aiUsers.has(sender) && text !== 'menu') {

// ======================
// CEK FAQ LARAVEL
// ======================

    try {


        const faqResponse = await axios.get(
            `http://127.0.0.1:8000/api/faqs/search/${encodeURIComponent(text)}`
        );

        if (faqResponse.data.found) {

            await msg.reply(
                `📚 Informasi Bangkesbangpol\n\n${faqResponse.data.data.answer}`
            );

            return;
        }

    } catch (err) {

        console.log('FAQ tidak ditemukan');
    }
    try {

        const documentContext =
        userDocuments.get(sender) || '';

let prompt = '';

if (documentContext) {

    prompt = `
Berikut isi dokumen:

${documentContext.substring(0, 10000)}

Pertanyaan:
${msg.body}

Jawab HANYA berdasarkan isi dokumen.

Jika tidak ditemukan,
katakan:
Informasi tidak ditemukan dalam dokumen.
`;

} else {

    prompt = `
Anda adalah asisten virtual Bangkesbangpol Boyolali.

Jawab dengan bahasa Indonesia yang sopan dan jelas.

Pertanyaan:
${msg.body}
`;
}

// ======================
// CEK FAQ LARAVEL DULU
// ======================

try {

    const faqRes = await axios.get(
        `http://127.0.0.1:8000/api/faqs/search/${encodeURIComponent(text)}`
    );

    if (faqRes.data.found) {

        await msg.reply(
            faqRes.data.data.answer
        );

        return;
    }

} catch (err) {

    console.log('FAQ tidak ditemukan');
}

// ======================
// CEK FAQ LARAVEL DULU
// ======================

try {

    const faqRes = await axios.get(
        `http://127.0.0.1:8000/api/faqs/search/${encodeURIComponent(text)}`
    );

    if (faqRes.data.found) {

        console.log('FAQ DITEMUKAN');

        await client.sendMessage(
            msg.from,
            faqRes.data.data.answer
        );

        return;
    }

} catch (err) {

    console.log('FAQ tidak ditemukan');
}

const response = await axios.post(
    'http://localhost:11434/api/generate',
    {
        model: 'qwen2.5:3b',
        prompt: prompt, // ← DI SINI
        stream: false
    }
);

        const jawaban = response.data.response;
        console.log('=== JAWABAN AI ===');
        console.log(jawaban);
        console.log('==================');

        await client.sendMessage(
            msg.from,
                jawaban.substring(0, 3000)
        );

        } catch (err) {

            console.error(err);

            await msg.reply(
             '❌ Maaf, layanan AI sedang mengalami gangguan.'
         );
        }

        return;
    }

        // Kata pemicu menu
    const greetings = [
        'halo',
        'hai',
        'hi',
        'assalamualaikum',
        'info',
        'layanan'
    ];

        if (greetings.includes(text)) {

            await msg.reply(
`🏛️ *MITRA BANGKESBANGPOL KABUPATEN BOYOLALI*

Selamat datang.

Silakan pilih layanan berikut:

1️⃣ Informasi SKT Ormas
2️⃣ Persyaratan Pendaftaran Ormas
3️⃣ Alur Pengajuan SKT
4️⃣ Jam Operasional & Lokasi Kantor
5️⃣ Hubungi Petugas/Admin
6️⃣ Konsultasi AI

Ketik angka *1 - 6* sesuai kebutuhan Anda.`
        );

            return;
        }

        switch (text) {

            case '1':
                await msg.reply(
`📋 *INFORMASI SKT ORMAS*

SKT (Surat Keterangan Terdaftar) merupakan dokumen yang diberikan kepada organisasi kemasyarakatan yang telah memenuhi persyaratan administrasi sesuai ketentuan yang berlaku.

Untuk mengetahui persyaratan dan alur pendaftaran, silakan pilih menu 2 atau 3.`
            );
                break;

            case '2':
                await msg.reply(
`📄 *PERSYARATAN PENDAFTARAN ORMAS*

Dokumen yang umumnya diperlukan:

• Surat permohonan
• Akta pendirian organisasi
• AD/ART
• Susunan pengurus
• Program kerja organisasi
• Domisili sekretariat
• Dokumen pendukung lainnya sesuai ketentuan

Petugas dapat meminta dokumen tambahan sesuai regulasi yang berlaku.`
            );
                break;

            case '3':
                await msg.reply(
`📝 *ALUR PENGAJUAN SKT*

1. Menyiapkan dokumen persyaratan
2. Mengajukan permohonan
3. Verifikasi administrasi
4. Perbaikan berkas (jika diperlukan)
5. Penerbitan SKT apabila persyaratan telah lengkap

Untuk konsultasi lebih lanjut silakan hubungi petugas.`
            );
                break;

            case '4':
                await msg.reply(
`🕒 *JAM OPERASIONAL*

Senin - Kamis
07.30 - 16.00 WIB

Jumat
07.30 - 11.00 WIB

📍 Badan Kesatuan Bangsa dan Politik Kabupaten Boyolali

Silakan hubungi petugas untuk informasi lokasi dan jadwal terbaru.`
            );
                break;

            case '5':
                await msg.reply(
`👨‍💼 *HUBUNGI PETUGAS*

Silakan ketik:

*CS*

untuk diteruskan kepada petugas Bangkesbangpol Boyolali.`
            );
                break;

           case '6':

        aiUsers.add(sender);

        await msg.reply(
`🤖 *KONSULTASI AI AKTIF*

Silakan ajukan pertanyaan terkait:

• SKT Ormas
• Organisasi Kemasyarakatan
• Kesbangpol
• Layanan Bangkesbangpol

Ketik *menu* untuk kembali ke menu utama.`
        );
            break;

            case 'cs':
                await msg.reply(
`📢 Permintaan Anda telah diteruskan kepada petugas.

Mohon tunggu respon dari admin Bangkesbangpol Boyolali.`
            );
                break;

            default:
                // Diam jika pesan tidak dikenali
                return;
            }

        } catch (err) {
            console.error('❌ Error:', err);
        }
    });

client.on('auth_failure', msg => {
    console.error('❌ Auth Failure:', msg);
});

client.on('disconnected', reason => {
    console.log('⚠️ WhatsApp Disconnect:', reason);
});

client.on('change_state', state => {
    console.log('📱 State:', state);
});

client.initialize();