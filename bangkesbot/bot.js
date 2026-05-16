require("dotenv").config()

const { Client, LocalAuth } = require("whatsapp-web.js")
const qrcode = require("qrcode-terminal")
const mysql = require("mysql2/promise")
const Groq = require("groq-sdk")

const groq = new Groq({
  apiKey: process.env.GROQ_API_KEY
})

/* ======================
   DATABASE
====================== */
const db = mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  database: process.env.DB_NAME
})

/* ======================
   WHATSAPP
====================== */
const client = new Client({
  authStrategy: new LocalAuth(),
  puppeteer: {
    headless: false
  }
})

client.on("qr", qr => {
  qrcode.generate(qr, { small: true })
})

client.on("ready", () => {
  console.log("✅ Bot siap")
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
- bantu informasi pendaftaran mitra
- arahkan ke admin jika perlu
          `
        },
        {
          role: "user",
          content: text
        }
      ],
      model: "llama-3.3-70b-versatile",
      temperature: 0.6,
      max_tokens: 500
    })

    return response.choices[0].message.content

  } catch (err) {
    console.error("Groq Error:", err.message)
    return "Maaf sistem sedang sibuk."
  }
}

/* ======================
   MESSAGE
====================== */
client.on("message", async msg => {
  try {
    if (msg.from.includes("@g.us")) return
    if (!msg.body) return

    console.log("Pesan:", msg.body)

    /* simpan user */
    await db.execute(
      `INSERT IGNORE INTO users (nomor, created_at, updated_at)
       VALUES (?, NOW(), NOW())`,
      [msg.from]
    )

    /* simpan pesan user */
    await db.execute(
      `INSERT INTO messages
       (nomor, pesan, sender, created_at, updated_at)
       VALUES (?, ?, 'user', NOW(), NOW())`,
      [msg.from, msg.body]
    )

    /* AI reply */
    const reply = await askAI(msg.body)

    await msg.reply(reply)

    /* simpan balasan bot */
    await db.execute(
      `INSERT INTO messages
       (nomor, pesan, sender, created_at, updated_at)
       VALUES (?, ?, 'bot', NOW(), NOW())`,
      [msg.from, reply]
    )

  } catch (err) {
    console.error("BOT ERROR:", err)
  }
})

client.initialize()