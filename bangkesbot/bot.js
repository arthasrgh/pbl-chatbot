require("dotenv").config()

const { Client, LocalAuth } = require("whatsapp-web.js")
const qrcode = require("qrcode-terminal")
const mysql = require("mysql2/promise")
const Groq = require("groq-sdk")

/* ==========================
   GROQ
========================== */
const groq = new Groq({
  apiKey: process.env.GROQ_API_KEY
})

/* ==========================
   DATABASE
========================== */
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
    console.log("❌ DB ERROR:", err.message)
  }
}

/* ==========================
   WHATSAPP
========================== */
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

client.on("disconnected", () => {
  console.log("⚠ reconnect...")
  client.initialize()
})

/* ==========================
   MENU
========================== */
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

/* ==========================
   SAFE REPLY
========================== */
async function safeReply(msg, text) {
  try {
    await msg.reply(text)
  } catch {
    try {
      await client.sendMessage(msg.from, text)
    } catch {
      console.log("Reply gagal")
    }
  }
}

/* ==========================
   AI
========================== */
async function askAI(text) {
  try {

    const response =
      await groq.chat.completions.create({
        messages: [
          {
            role: "system",
            content: `
Kamu adalah asisten resmi Bangkesbangpol Boyolali.

Jawablah berdasarkan pengetahuan dunia nyata yang valid.

Aturan:
- jawab natural
- singkat tapi jelas
- gunakan fakta nyata
- jangan terlalu sering menolak
- jika pertanyaan umum, jawab langsung
- gunakan bahasa Indonesia sopan
- jangan kaku seperti robot
- jika benar-benar tidak tahu jawab:
"Maaf, saya belum menemukan informasi pasti."
`
          },
          {
            role: "user",
            content: text
          }
        ],
        model: "llama-3.3-70b-versatile",
        temperature: 0.4,
        max_tokens: 500
      })

    return response.choices[0].message.content

  } catch (err) {
    console.log("AI ERROR:", err.message)
    return "Maaf, sistem sedang sibuk."
  }
}

/* ==========================
   MESSAGE HANDLER
========================== */
client.on("message", async msg => {
  try {

    if (!msg.body?.trim()) return
    if (msg.from.includes("@g.us")) return
    if (msg.from === "status@broadcast") return

    const nomor = msg.from.replace("@c.us", "")
const text = msg.body.trim()

console.log("Nomor:", nomor)
console.log("Pesan:", text)

    console.log("📩 Pesan:", text)

    const [cek] = await db.execute(
      "SELECT nomor FROM users WHERE nomor=?",
      [nomor]
    )

    /* chat pertama / menu */
    if (cek.length === 0) {

  await db.execute(`
    INSERT INTO users
    (nomor, created_at, updated_at)
    VALUES (?, NOW(), NOW())
  `, [nomor])

} {

      await db.execute(`
        INSERT IGNORE INTO users
        (nomor, created_at, updated_at)
        VALUES (?, NOW(), NOW())
      `, [nomor])

      const menu = getMenu()

      await safeReply(msg, menu)

      await db.execute(`
        INSERT INTO messages
        (nomor, pesan, sender, created_at, updated_at)
        VALUES (?, ?, 'bot', NOW(), NOW())
      `, [nomor, menu])

      return
    }
if (
  ["halo","hai","menu","start"]
  .includes(text.toLowerCase())
) {

  const menu = getMenu()

  await safeReply(msg, menu)

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

console.log("✅ Pesan user tersimpan")

    let reply = ""

    switch(text){

      case "1":
        reply =
          "Pendaftaran dapat dilakukan melalui website resmi Pemerintah Kabupaten Boyolali."
        break

      case "2":
        reply =
          "Persyaratan umum meliputi KTP, proposal organisasi, dan surat permohonan resmi."
        break

      case "3":
        reply =
          "Admin resmi Bangkesbangpol Boyolali: 0878-2501-9307"
        break

      case "4":
        reply =
          "Silakan tuliskan pertanyaan Anda."
        break

      default:
        reply = await askAI(text)
    }

    await safeReply(msg, reply)

    /* simpan balasan bot */
    await db.execute(`
      INSERT INTO messages
      (nomor, pesan, sender, created_at, updated_at)
      VALUES (?, ?, 'bot', NOW(), NOW())
    `, [nomor, reply])

  } catch (err) {
    console.log("BOT ERROR:", err.message)
  }
})


/* ==========================
   START
========================== */
testDB()
client.initialize()