<template>
  <div class="page">

    <header class="header">
      <div class="header-left">
        <img :src="logo" alt="logo" class="header-logo" />
        <span>Bakesbangpol Boyolali</span>
      </div>

      <button class="back-btn" @click="goHome">
        Back
      </button>
    </header>

    <main class="main-content">
      <div class="login-card">
        <h2>Login</h2>

        <p
          v-if="errorMessage"
          class="error-text"
        >
          {{ errorMessage }}
        </p>

        <label>Username</label>
        <input
          v-model="email"
          type="text"
          placeholder="Masukkan username"
        />

        <label>Password</label>

        <div class="password-box">
          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Masukkan password"
          />

          <button
            type="button"
            class="show-btn"
            @click="showPassword = !showPassword"
          >
            {{ showPassword ? '🙈' : '👁️' }}
          </button>
        </div>

        <button class="login-btn" @click="login">
          Login
        </button>

        <p
          class="forgot"
          @click="router.push('/forgot-password')"
        >
          Lupa Password? Klik di sini
        </p>

      </div>
    </main>

    <footer class="footer">
      <div class="footer-container">

        <div class="footer-left">
          <img
            :src="logo"
            alt="logo"
            class="footer-logo"
          />

          <div class="footer-text">
            <h3>Badan Kesatuan Bangsa dan Politik</h3>
            <p>Kabupaten Boyolali</p>

            <div class="contact-item">
              📞 (0276) 321087
            </div>

            <div class="contact-item">
              ✉️ bakesbangpol@boyolali.go.id
            </div>
          </div>
        </div>

        <div class="footer-bottom">
          Copyright@2026 Boyolali
        </div>

      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import api from "../services/api"
import logo from "../assets/Logo_Boyo2.png"

const router = useRouter()

const email = ref("")
const password = ref("")
const showPassword = ref(false)
const errorMessage = ref("")

const login = async () => {
  errorMessage.value = ""

  if (!email.value || !password.value) {
    errorMessage.value =
      "Username dan password wajib diisi"
    return
  }

  try {
    const res = await api.post("/login", {
      username: email.value,
      password: password.value
    })

    if (res.data.success) {
      localStorage.setItem("isLogin", "true")
      router.push("/dashboard")
    } else {
      errorMessage.value =
        res.data.message || "Login gagal"
    }

  } catch (err) {
    errorMessage.value =
      "Server backend tidak terhubung"
    console.error(err)
  }
}

const goHome = () => {
  router.push("/")
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #8d96a6;
}

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
  font-size: 20px;
  font-weight: bold;
}

.header-logo,
.footer-logo {
  width: 34px;
}

.back-btn {
  border: none;
  background: white;
  padding: 8px 20px;
  border-radius: 20px;
  cursor: pointer;
}

.main-content {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.login-card {
  width: 420px;
  background: #f3163a;
  padding: 35px;
  border-radius: 25px;
  color: white;
}

.login-card input {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 25px;
  margin-top: 8px;
}

.password-box {
  position: relative;
}

.show-btn {
  position: absolute;
  right: 15px;
  top: 15px;
  border: none;
  background: transparent;
  cursor: pointer;
}

.login-btn {
  margin-top: 18px;
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 25px;
  cursor: pointer;
}

.error-text {
  background: white;
  color: red;
  padding: 10px;
  border-radius: 12px;
  margin-bottom: 15px;
}

.footer {
  background: #eee;
  padding: 30px;
}

.forgot {
  margin-top: 20px;
  cursor: pointer;
}
</style>