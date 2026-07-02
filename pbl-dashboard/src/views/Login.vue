<template>
  <div class="login-page">

    <div class="login-left">
      <div class="overlay"></div>
      <div class="left-content">
        <img src="../assets/Logo_Boyo2.png" alt="Logo Bangkesbangpol" class="logo" />
        <h1>Bangkesbangpol Boyolali</h1>
        <h2>Dashboard Monitoring Chatbot</h2>
        <p>Sistem Monitoring Chatbot WhatsApp Badan Kesatuan Bangsa dan Politik Kabupaten Boyolali</p>
      </div>
    </div>

    <div class="login-right">
      <div class="login-card">

        <div v-if="!isForgotPassword">
          <div class="card-header">
            <h2>Login Admin</h2>
            <p>Silakan login menggunakan akun administrator</p>
          </div>

          <form @submit.prevent="login">
            <div class="form-group">
              <label>Email</label>
              <input v-model="email" type="email" placeholder="admin@gmail.com" required />
            </div>

            <div class="form-group">
              <label>Password</label>
              <div class="password-box">
                <input v-model="password" :type="showPassword ? 'text' : 'password'" placeholder="Masukkan Password" required />
                <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                  {{ showPassword ? "🙈" : "👁️" }}
                </button>
              </div>
            </div>

            <div class="forgot-link-container">
              <a href="#" @click.prevent="toggleForgotMode(true)" class="forgot-link">Lupa Password?</a>
            </div>

            <div v-if="error" class="error-box">{{ error }}</div>
            <div v-if="success" class="success-box">{{ success }}</div>

            <button type="submit" class="login-btn" :disabled="loading">
              <span v-if="!loading">LOGIN</span>
              <span v-else>Loading...</span>
            </button>
          </form>
        </div>

        <div v-else>
          <div class="card-header">
            <h2>Reset Password</h2>
            <p>Masukkan email terdaftar untuk mengatur ulang password Anda</p>
          </div>

          <form @submit.prevent="handleResetPassword">
            <div class="form-group">
              <label>Email Terdaftar</label>
              <input v-model="resetEmail" type="email" placeholder="admin@gmail.com" required :disabled="loading" />
            </div>

            <div v-if="error" class="error-box">{{ error }}</div>
            <div v-if="success" class="success-box">{{ success }}</div>

            <button type="submit" class="login-btn" :disabled="loading">
              <span v-if="!loading">KIRIM LINK RESET</span>
              <span v-else>Memproses...</span>
            </button>

            <div class="back-to-login-container">
              <a href="#" @click.prevent="toggleForgotMode(false)" class="back-link">← Kembali ke Login</a>
            </div>
          </form>
        </div>

        <div class="footer-text">© 2026 Bangkesbangpol Boyolali</div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import api from "../services/api"

const router = useRouter()

const email = ref("")
const password = ref("")
const isForgotPassword = ref(false)
const resetEmail = ref("")
const success = ref("")
const loading = ref(false)
const error = ref("")
const showPassword = ref(false)

const toggleForgotMode = (status) => {
  isForgotPassword.value = status
  error.value = ""
  success.value = ""
  resetEmail.value = ""
  email.value = ""
  password.value = ""
}

const login = async () => {
  error.value = ""
  success.value = ""
  localStorage.removeItem("user")
  localStorage.removeItem("adminEmail")

  if (!email.value || !password.value) {
    error.value = "Email dan Password wajib diisi."
    return
  }

  loading.value = true
  try {
    const response = await api.post("/login", { email: email.value, password: password.value })
    if (response.data.success) {
      localStorage.setItem("isLogin", "true")
      localStorage.setItem("user", JSON.stringify(response.data.user))
      router.push("/dashboard")
    }
  } catch (err) {
    error.value = err.response?.data?.message || "Tidak dapat terhubung ke server."
  } finally {
    loading.value = false
  }
}

const handleResetPassword = async () => {
  error.value = ""
  success.value = ""

  if (!resetEmail.value) {
    error.value = "Email wajib diisi."
    return
  }

  loading.value = true
  try {
    // Membuat basis URL dinamis aplikasi (cth: http://localhost:5173/reset-password)
    const redirectUrl = `${window.location.origin}/reset-password`

    const response = await api.post("/forgot-password", {
      email: resetEmail.value,
      redirect_url: redirectUrl 
    })

    if (response.data.success || response.status === 200) {
      success.value = response.data.message || "Link instruksi reset password telah dikirim ke email Anda."
      resetEmail.value = ""
    }
  } catch (err) {
    error.value = err.response?.data?.message || "Gagal memproses permintaan reset password."
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
*{ margin:0; padding:0; box-sizing:border-box; font-family:"Poppins",sans-serif; }
.login-page{ width:100%; min-height:100vh; display:grid; grid-template-columns:1fr 1fr; background:#f8fafc; }
.login-left{ position:relative; display:flex; justify-content:center; align-items:center; overflow:hidden; background:linear-gradient(135deg,#b71c1c,#d32f2f); color:#fff; padding:50px; }
.overlay{ position:absolute; inset:0; background:rgba(0,0,0,.15); }
.left-content{ position:relative; z-index:2; text-align:center; max-width:450px; }
.logo{ width:150px; margin:0 auto 30px; animation:float 3s ease-in-out infinite; }
.left-content h1{ font-size:36px; font-weight:700; margin-bottom:10px; }
.left-content h2{ font-size:22px; margin-bottom:20px; font-weight:500; }
.left-content p{ line-height:1.8; opacity:.95; font-size:15px; }
.login-right{ display:flex; justify-content:center; align-items:center; padding:50px; background:#f5f7fb; }
.login-card{ width:100%; max-width:430px; background:#fff; border-radius:20px; padding:40px; box-shadow:0 15px 40px rgba(0,0,0,.08); animation:fadeUp .6s ease; }
.card-header{ text-align:center; margin-bottom:30px; }
.card-header h2{ color:#b71c1c; font-size:30px; margin-bottom:10px; }
.card-header p{ color:#6b7280; font-size:14px; }
.form-group{ margin-bottom:20px; }
.form-group label{ display:block; margin-bottom:8px; font-size:14px; font-weight:600; color:#374151; }
.form-group input{ width:100%; padding:14px 16px; border:1px solid #d1d5db; border-radius:12px; font-size:15px; outline:none; transition:.3s; }
.form-group input:focus{ border-color:#b71c1c; box-shadow:0 0 0 3px rgba(183,28,28,.15); }
.form-group input:disabled{ background:#f3f4f6; cursor:not-allowed; }
.password-box{ display:flex; }
.password-box input{ border-radius:12px 0 0 12px; }
.toggle-password{ width:60px; border:none; background:#b71c1c; color:#fff; cursor:pointer; border-radius:0 12px 12px 0; transition:.3s; }
.toggle-password:hover{ background:#8f1717; }
.forgot-link-container{ text-align:right; margin-bottom:20px; margin-top:-10px; }
.forgot-link{ font-size:13px; color:#b71c1c; text-decoration:none; font-weight:600; transition:.2s; }
.forgot-link:hover{ color:#d32f2f; text-decoration:underline; }
.back-to-login-container{ text-align:center; margin-top:20px; }
.back-link{ font-size:14px; color:#6b7280; text-decoration:none; font-weight:500; transition:.2s; }
.back-link:hover{ color:#374151; }
.error-box, .success-box{ padding:12px; border-radius:10px; margin-bottom:20px; text-align:center; font-size:14px; }
.error-box{ background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; }
.success-box{ background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
.login-btn{ width:100%; padding:15px; border:none; border-radius:12px; background:#b71c1c; color:#fff; font-size:16px; font-weight:700; cursor:pointer; transition:.3s; }
.login-btn:hover{ background:#8f1717; transform:translateY(-2px); }
.login-btn:disabled{ opacity:.7; cursor:not-allowed; transform:none; }
.footer-text{ margin-top:25px; text-align:center; font-size:13px; color:#9ca3af; }
@keyframes fadeUp{ from{ opacity:0; transform:translateY(30px); } to{ opacity:1; transform:translateY(0); } }
@keyframes float{ 0%{ transform:translateY(0px); } 50%{ transform:translateY(-10px); } 100%{ transform:translateY(0px); } }
@media(max-width:900px){ .login-page{ grid-template-columns:1fr; } .login-left{ display:none; } .login-right{ padding:25px; } .login-card{ max-width:100%; padding:30px; } }
</style>