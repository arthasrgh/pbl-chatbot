require("dotenv").config()

const { Client, LocalAuth } = require("whatsapp-web.js")
const qrcode = require("qrcode-terminal")
const mysql = require("mysql2/promise")
const Groq = require("groq-sdk")

/* ======================
   GROQ AI
====================== */
const groq = new Groq({
  apiKey: process.env.GROQ_API_KEY
})

/* ======================
   DATABASE
====================== */
const db = mysql.createPool({
  host: process.env.DB_HOST,
  port: process.env.DB_PORT || 3306,
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10
})

async function testDB() {
  try {
    await db.query("SELECT 1")
    console.log("✅ Database terhubung")
  } catch (err) {
    console.error("❌ DB ERROR:", err)
  }
}

/* ======================
   WHATSAPP
====================== */
const client = new Client({
  authStrategy: new LocalAuth(),
  puppeteer: {
    headless: false,
    args: [
      "--no-sandbox",
      "--disable-setuid-sandbox",
      "--disable-dev-shm-usage"
    ]
  }
})

client.on("qr", qr => {
  qrcode.generate(qr, { small: true })
})

client.on("ready", () => {
  console.log("✅ Bot siap")
})

client.on("disconnected", reason => {
  console.log("⚠️ Disconnect:", reason)
  client.initialize()
})

/* ======================
   AI
====================== */
async function askAI(text) {
  try {
    const response = await groq.chat.completions.create({
      messages: [
        {
          role: "system",
          content: `
Kamu adalah customer service resmi Bangkesbangpol Boyolali.

Aturan:
- jawab singkat
- profesional
- sopan
- bantu user
- arahkan ke admin jika perlu
`
        },
        {
          role: "user",
          content: text
        }
      ],
      model: "llama-3.3-70b-versatile",
      temperature: 0.5,
      max_tokens: 300
    })

    return response.choices?.[0]?.message?.content ||
      "Maaf, saya belum bisa menjawab."

  } catch (err) {
    console.error("AI ERROR:", err.message)
    return "Maaf, server AI sedang sibuk."
  }
}

/* ======================
   MENU
====================== */
function getMenu() {
  return `
*Selamat Datang di Bangkesbangpol Boyolali*

Silakan pilih menu:

1️⃣ Informasi Pendaftaran  
2️⃣ Syarat Menjadi Mitra  
3️⃣ Hubungi Admin  
4️⃣ Bantuan Lainnya

Ketik angka menu.
`
}

/* ======================
   MESSAGE HANDLER
====================== */
client.on("message", async msg => {
  try {
    if (msg.from.includes("@g.us")) return
    if (!msg.body?.trim()) return

    const nomor = msg.from
    const text = msg.body.trim()

    console.log("📩 Pesan:", text)

    /* cek user */
    const [cek] = await db.execute(
      "SELECT * FROM users WHERE nomor = ?",
      [nomor]
    )

    /* USER BARU = MENU */
    if (cek.length === 0) {

      await db.execute(`
        INSERT INTO users
        (nomor, created_at, updated_at)
        VALUES (?, NOW(), NOW())
      `, [nomor])

      const menu = getMenu()

      await msg.reply(menu)

      await db.execute(`
        INSERT INTO messages
        (nomor, pesan, sender, created_at, updated_at)
        VALUES (?, ?, 'bot', NOW(), NOW())
      `, [nomor, menu])

      return
    }

    /* simpan pesan user */
    await db.execute(`
      INSERT INTO messages
      (nomor, pesan, sender, created_at, updated_at)
      VALUES (?, ?, 'user', NOW(), NOW())
    `, [nomor, text])

    let reply = ""

    switch (text) {

      case "1":
        reply =
          "Informasi pendaftaran tersedia di website resmi Pemerintah Boyolali."
        break

      case "2":
        reply =
          "Syarat mitra: KTP, surat permohonan, proposal, dan dokumen pendukung."
        break

      case "3":
        reply =
          "Hubungi admin:\n0878-2501-9307"
        break

      case "4":
        reply =
          "Silakan tuliskan pertanyaan Anda."
        break

      default:
        reply = await askAI(text)
    }

    await msg.reply(reply)

    /* simpan balasan */
    await db.execute(`
      INSERT INTO messages
      (nomor, pesan, sender, created_at, updated_at)
      VALUES (?, ?, 'bot', NOW(), NOW())
    `, [nomor, reply])

  } catch (err) {
    console.error("========== BOT ERROR ==========")
    console.error(err)
    console.error("Message:", err.message)
    console.error("Code:", err.code)
    console.error("===============================")
  }
})

/* ======================
   START
====================== */
testDB()
client.initialize()