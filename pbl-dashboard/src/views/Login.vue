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

  /* VALIDASI KOSONG */
  if (!email.value || !password.value) {
    errorMessage.value =
      "Username dan password wajib diisi"
    return
  }

  /* VALIDASI PASSWORD */
  const pass = password.value

  /* MINIMAL 8 KARAKTER */
  if (pass.length < 8) {
    errorMessage.value =
      "Password minimal 8 karakter"
    return
  }

  /* HURUF KAPITAL */
  if (!/[A-Z]/.test(pass)) {
    errorMessage.value =
      "Password harus memiliki huruf kapital"
    return
  }

  /* ANGKA */
  if (!/[0-9]/.test(pass)) {
    errorMessage.value =
      "Password harus memiliki angka"
    return
  }

  /* KARAKTER SPESIAL */
  if (!/[!@#$%^&*(),.?":{}|<>_]/.test(pass)) {
    errorMessage.value =
      "Password harus memiliki karakter spesial"
    return
  }

  try {

    const res = await api.post("/login", {
      email: "admin@gmail.com",
      password: "Admin123!"
    })

    if (res.data.success) {

      localStorage.setItem(
        "isLogin",
        "true"
      )

      router.push("/dashboard")

    } else {

      errorMessage.value =
        res.data.message ||
        "Login gagal"

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

<template>
<div class="page">

<header class="header">

<div class="header-left">
<img :src="logo" class="header-logo">
<span>Bakesbangpol Boyolali</span>
</div>

<button
class="back-btn"
@click="goHome"
>
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

<label>Email</label>

<input
v-model="email"
type="email"
placeholder="Masukkan email"
/>

<label>Password</label>

<div class="password-box">

<input
v-model="password"
:type="showPassword ? 'text':'password'"
placeholder="Masukkan password"
/>

<button
type="button"
class="show-btn"
@click="showPassword=!showPassword"
>

<i
:class="
showPassword
? 'fa-solid fa-eye-slash'
: 'fa-solid fa-eye'
"
></i>

</button>

</div>

<button
class="login-btn"
@click="login"
>
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
📞 (0276) 321087
</div>

<div class="contact-item">
✉️ bakesbangpol@boyolali.go.id
</div>

</div>
</div>

<div class="footer-bottom">
Copyright@2026 Boyolali.
Developed by System Hub
</div>

</div>

</footer>

</div>
</template>

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial,sans-serif;
}

.page{
min-height:100vh;
display:flex;
flex-direction:column;
background:#8d96a6;
}

/* HEADER */

.header{
height:55px;
background:#252742;
display:flex;
justify-content:space-between;
align-items:center;
padding:0 22px;
}

.header-left{
display:flex;
align-items:center;
gap:10px;
color:white;
font-size:22px;
font-weight:bold;
}

.header-logo{
width:30px;
}

.back-btn{
border:none;
background:white;
color:#252742;
padding:8px 22px;
border-radius:20px;
font-weight:bold;
cursor:pointer;
}

/* MAIN */

.main-content{
flex:1;
display:flex;
justify-content:center;
align-items:center;
padding:50px 20px;
}

.login-card{
width:420px;
background:#f3163a;
border-radius:28px;
padding:35px;
color:white;
}

.login-card h2{
text-align:center;
margin-bottom:25px;
}

.login-card label{
display:block;
margin-bottom:8px;
margin-top:14px;
}

.login-card input{
width:100%;
padding:14px 18px;
border:none;
border-radius:30px;
outline:none;
}

.password-box{
position:relative;
}

.password-box input{
padding-right:60px;
}

.show-btn{
position:absolute;
right:18px;
top:50%;
transform:translateY(-50%);
border:none;
background:transparent;
cursor:pointer;
font-size:18px;
}

.login-btn{
margin-top:25px;
width:100%;
border:none;
background:white;
color:black;
font-weight:bold;
padding:14px;
border-radius:30px;
cursor:pointer;
}

.forgot{
margin-top:18px;
text-align:center;
font-size:14px;
cursor:pointer;
}

/* ERROR */

.error-text{
background:white;
color:#d40028;
padding:12px;
border-radius:12px;
margin-bottom:15px;
text-align:center;
font-weight:bold;
}

/* FOOTER */

.footer{
background:#f3f3f3;
padding:35px 55px 15px;
}

.footer-left{
display:flex;
gap:18px;
}

.footer-logo{
width:35px;
}

.footer-text h3{
font-size:22px;
}

.footer-text p{
margin:5px 0 15px;
}

.contact-item{
margin-bottom:10px;
}

.footer-bottom{
margin-top:25px;
font-size:13px;
}

/* RESPONSIVE */

@media(max-width:600px){

.login-card{
width:100%;
}

.header-left{
font-size:18px;
}

.footer{
padding:25px;
}

}
</style>