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
        
        <div class="card-header">
          <h2>Password Baru</h2>
          <p>Silakan masukkan password baru Anda untuk akun administrator</p>
        </div>

        <div v-if="isSuccess" class="success-screen">
          <div class="success-box">Password Anda berhasil diperbarui! Silakan kembali ke halaman login.</div>
          <button @click="goToLogin" class="login-btn">KE HALAMAN LOGIN</button>
        </div>

        <form v-else @submit.prevent="submitNewPassword">
          <div class="form-group">
            <label>Password Baru</label>
            <div class="password-box">
              <input v-model="password" :type="showPassword ? 'text' : 'password'" placeholder="Minimal 8 karakter" required :disabled="loading" />
              <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                {{ showPassword ? "🙈" : "👁️" }}
              </button>
            </div>
            <small class="password-hint">Password wajib menyertakan huruf besar, angka, dan karakter spesial.</small>
          </div>

          <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <div class="password-box">
              <input v-model="confirmPassword" :type="showConfirmPassword ? 'text' : 'password'" placeholder="Ulangi password" required :disabled="loading" />
              <button type="button" class="toggle-password" @click="showConfirmPassword = !showConfirmPassword">
                {{ showConfirmPassword ? "🙈" : "👁️" }}
              </button>
            </div>
          </div>

          <div v-if="error" class="error-box">{{ error }}</div>

          <button type="submit" class="login-btn" :disabled="loading">
            <span v-if="!loading">PERBARUI PASSWORD</span>
            <span v-else>Memproses...</span>
          </button>
        </form>

        <div class="footer-text">© 2026 Bangkesbangpol Boyolali</div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRouter, useRoute } from "vue-router"
import api from "../services/api"

const router = useRouter()
const route = useRoute()

const password = ref("")
const confirmPassword = ref("")
const token = ref("")
const loading = ref(false)
const error = ref("")
const isSuccess = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

onMounted(() => {
  // Membaca token dari URL (?token=xxxxx)
  if (route.query.token) {
    token.value = route.query.token
  } else {
    error.value = "Token reset tidak ditemukan atau tidak valid."
  }
})

const goToLogin = () => { router.push("/login") }

const submitNewPassword = async () => {
  error.value = ""

  if (password.value !== confirmPassword.value) {
    error.value = "Konfirmasi password tidak cocok."
    return
  }

  const pass = password.value
  if (pass.length < 8) { error.value = "Password minimal 8 karakter."; return }
  if (!/[A-Z]/.test(pass)) { error.value = "Password harus memiliki minimal satu huruf kapital."; return }
  if (!/[0-9]/.test(pass)) { error.value = "Password harus memiliki minimal satu angka."; return }
  if (!/[!@#$%^&*(),.?\":{}|<>_\-+=/\\[\]]/.test(pass)) { error.value = "Password harus memiliki karakter spesial."; return }

  if (!token.value) {
    error.value = "Token tidak valid. Silakan ajukan reset password kembali."
    return
  }

  loading.value = true
  try {
    const response = await api.post("/reset-password", {
      token: token.value,
      password: password.value
    })

    if (response.data.success || response.status === 200) {
      isSuccess.value = true
    }
  } catch (err) {
    error.value = err.response?.data?.message || "Gagal memperbarui password."
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
.password-hint { display:block; margin-top:6px; color:#6b7280; font-size:12px; line-height:1.4; }
.password-box{ display:flex; }
.password-box input{ border-radius:12px 0 0 12px; }
.toggle-password{ width:60px; border:none; background:#b71c1c; color:#fff; cursor:pointer; border-radius:0 12px 12px 0; transition:.3s; font-size:16px; }
.toggle-password:hover{ background:#8f1717; }
.error-box, .success-box{ padding:12px; border-radius:10px; margin-bottom:20px; text-align:center; font-size:14px; }
.error-box{ background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; }
.success-box{ background:#dcfce7; color:#166534; border:1px solid #bbf7d0; line-height:1.5; }
.success-screen { display: flex; flex-direction: column; gap:10px; }
.login-btn{ width:100%; padding:15px; border:none; border-radius:12px; background:#b71c1c; color:#fff; font-size:16px; font-weight:700; cursor:pointer; transition:.3s; }
.login-btn:hover{ background:#8f1717; transform:translateY(-2px); }
.login-btn:disabled{ opacity:.7; cursor:not-allowed; transform:none; }
.footer-text{ margin-top:25px; text-align:center; font-size:13px; color:#9ca3af; }
@keyframes fadeUp{ from{ opacity:0; transform:translateY(30px); } to{ opacity:1; transform:translateY(0); } }
@keyframes float{ 0%{ transform:translateY(0px); } 50%{ transform:translateY(-10px); } 100%{ transform:translateY(0px); } }
@media(max-width:900px){ .login-page{ grid-template-columns:1fr; } .login-left{ display:none; } .login-right{ padding:25px; } .login-card{ max-width:100%; padding:30px; } }
</style>