<template>
  <div class="page">

    <!-- HEADER -->
    <header class="header">

      <div class="header-left">
        <img :src="logo" alt="logo" class="header-logo" />
        <span>Bakesbangpol Boyolali</span>
      </div>

      <button class="back-btn" @click="goLogin">
        Back
      </button>

    </header>

    <!-- MAIN -->
    <main class="main-content">

      <div class="forgot-card">

        <h2>Lupa Password</h2>

        <p class="desc">
          Masukkan email akun untuk menerima link reset password.
        </p>

        <!-- SUCCESS -->
        <p
          v-if="successMessage"
          class="success-text"
        >
          {{ successMessage }}
        </p>

        <!-- ERROR -->
        <p
          v-if="errorMessage"
          class="error-text"
        >
          {{ errorMessage }}
        </p>

        <!-- EMAIL -->
        <label>Email</label>

        <input
          type="email"
          v-model="email"
          placeholder="Masukkan email"
        />

        <!-- BUTTON -->
        <button
          class="reset-btn"
          @click="sendReset"
        >
          Kirim Link Reset
        </button>

        <p
          class="login-link"
          @click="goLogin"
        >
          Kembali ke Login
        </p>

      </div>

    </main>

    <!-- FOOTER -->
    <footer class="footer">

      <div class="footer-container">

        <div class="footer-left">

          <img
            :src="logo"
            alt="logo"
            class="footer-logo"
          />

          <div class="footer-text">

            <h3>
              Badan Kesatuan Bangsa dan Politik
            </h3>

            <p>
              Kabupaten Boyolali
            </p>

            <div class="contact-item">
              <span class="icon">📞</span>
              <span>(0276) 321087</span>
            </div>

            <div class="contact-item">
              <span class="icon">✉️</span>
              <span>bakesbangpol@boyolali.go.id</span>
            </div>

          </div>

        </div>

        <div class="footer-bottom">
          Copyright@2026 Boyolali. Developed by System Hub
        </div>

      </div>

    </footer>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import logo from '../assets/Logo_Boyo2.png'

const router = useRouter()

const email = ref('')
const errorMessage = ref('')
const successMessage = ref('')

const sendReset = () => {

  errorMessage.value = ''
  successMessage.value = ''

  /* VALIDASI KOSONG */
  if (!email.value) {
    errorMessage.value =
      'Email wajib diisi'
    return
  }

  /* VALIDASI EMAIL */
  const emailPattern =
    /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/

  if (!emailPattern.test(email.value)) {
    errorMessage.value =
      'Format email tidak valid'
    return
  }

  /* SUCCESS */
  successMessage.value =
    'Link reset password berhasil dikirim ke email'
}

const goLogin = () => {
  router.push('/login')
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
}

.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #8d96a6;
}

/* HEADER */
.header {
  height: 55px;
  background: #252742;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 22px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
  font-size: 22px;
  font-weight: bold;
}

.header-logo {
  width: 30px;
  height: 30px;
  object-fit: contain;
}

.back-btn {
  border: none;
  background: white;
  color: #252742;
  padding: 8px 22px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 14px;
  cursor: pointer;
}

/* MAIN */
.main-content {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 50px 20px;
}

/* CARD */
.forgot-card {
  width: 420px;
  background: #f3163a;
  border-radius: 28px;
  padding: 35px;
  color: white;
}

.forgot-card h2 {
  text-align: center;
  font-size: 26px;
  margin-bottom: 12px;
}

.desc {
  text-align: center;
  font-size: 14px;
  margin-bottom: 20px;
  line-height: 1.5;
}

.forgot-card label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
}

.forgot-card input {
  width: 100%;
  padding: 14px 18px;
  border: none;
  border-radius: 30px;
  outline: none;
  margin-bottom: 18px;
}

.reset-btn {
  width: 100%;
  border: none;
  background: white;
  color: black;
  font-weight: bold;
  padding: 12px;
  border-radius: 25px;
  cursor: pointer;
  font-size: 15px;
}

.login-link {
  margin-top: 18px;
  text-align: center;
  font-size: 14px;
  cursor: pointer;
}

/* ERROR */
.error-text {
  background: #ffd4d4;
  color: #a40000;
  padding: 10px;
  border-radius: 12px;
  margin-bottom: 15px;
  font-size: 14px;
  text-align: center;
}

/* SUCCESS */
.success-text {
  background: #d4ffd9;
  color: #006b1b;
  padding: 10px;
  border-radius: 12px;
  margin-bottom: 15px;
  font-size: 14px;
  text-align: center;
}

/* FOOTER */
.footer {
  background: #f3f3f3;
  padding: 35px 55px 15px;
  border-top: 1px solid #ddd;
}

.footer-container {
  display: flex;
  flex-direction: column;
}

.footer-left {
  display: flex;
  align-items: flex-start;
  gap: 18px;
}

.footer-logo {
  width: 34px;
  height: 34px;
  object-fit: contain;
  margin-top: 4px;
}

.footer-text h3 {
  font-size: 22px;
  font-weight: 700;
  color: #111;
}

.footer-text p {
  margin: 2px 0 18px;
  font-size: 14px;
  color: #444;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
  font-size: 14px;
  color: #222;
}

.icon {
  width: 18px;
  text-align: center;
}

.footer-bottom {
  margin-top: 35px;
  font-size: 13px;
  color: #333;
}

/* RESPONSIVE */
@media (max-width: 600px) {

  .forgot-card {
    width: 100%;
  }

  .footer {
    padding: 25px;
  }

  .footer-text h3 {
    font-size: 18px;
  }

  .header-left {
    font-size: 18px;
  }
}
</style>
