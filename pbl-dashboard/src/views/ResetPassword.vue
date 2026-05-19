<script setup>
import { ref } from 'vue'
import { useRoute,useRouter } from 'vue-router'
import api from '../services/api'
import logo from '../assets/Logo_Boyo2.png'

const route = useRoute()
const router = useRouter()

const token = route.query.token

const password = ref('')
const confirmPassword = ref('')

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const errorMessage = ref('')
const successMessage = ref('')

const resetPassword = async () => {

  errorMessage.value=''
  successMessage.value=''

  if(!password.value || !confirmPassword.value){

    errorMessage.value =
      'Semua field wajib diisi'

    return
  }

  if(password.value !== confirmPassword.value){

    errorMessage.value =
      'Konfirmasi password tidak cocok'

    return
  }

  if(password.value.length < 8){

    errorMessage.value =
      'Password minimal 8 karakter'

    return
  }

  try{

    const res =
      await api.post('/reset-password',{

        token:token,
        password:password.value

      })

    successMessage.value =
      res.data.message

    setTimeout(()=>{

      router.push('/login')

    },2000)

  }catch(err){

    errorMessage.value =
      err.response?.data?.message
      || 'Reset password gagal'
  }
}

const goLogin=()=>{

  router.push('/login')
}
</script>

<template>

<div class="page">

<header class="header">

<div class="header-left">

<img
:src="logo"
class="header-logo"
/>

<span>
Bakesbangpol Boyolali
</span>

</div>

<button
class="back-btn"
@click="goLogin"
>
Back
</button>

</header>

<main class="main-content">

<div class="reset-card">

<h2>
Reset Password
</h2>

<p class="desc">

Masukkan password baru akun anda.

</p>

<p
v-if="successMessage"
class="success-text"
>

{{successMessage}}

</p>

<p
v-if="errorMessage"
class="error-text"
>

{{errorMessage}}

</p>

<label>Password Baru</label>

<div class="password-box">

<input
v-model="password"
:type="showPassword ? 'text':'password'"
placeholder="Masukkan password baru"
/>

<button
class="show-btn"
type="button"
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

<label>Konfirmasi Password</label>

<div class="password-box">

<input
v-model="confirmPassword"
:type="showConfirmPassword ? 'text':'password'"
placeholder="Masukkan ulang password"
/>

<button
class="show-btn"
type="button"
@click="
showConfirmPassword=
!showConfirmPassword
"
>

<i
:class="
showConfirmPassword
? 'fa-solid fa-eye-slash'
: 'fa-solid fa-eye'
"
></i>

</button>

</div>

<button
class="reset-btn"
@click="resetPassword"
>

Reset Password

</button>

<p
class="login-link"
@click="goLogin"
>

Kembali ke Login

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

Badan Kesatuan Bangsa
dan Politik

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

<style scoped>

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

.main-content{
flex:1;
display:flex;
justify-content:center;
align-items:center;
padding:50px 20px;
}

.reset-card{
width:420px;
background:#f3163a;
border-radius:28px;
padding:35px;
color:white;
}

.reset-card h2{
text-align:center;
margin-bottom:10px;
}

.desc{
text-align:center;
margin-bottom:20px;
font-size:14px;
}

.reset-card label{
display:block;
margin-bottom:8px;
margin-top:14px;
}

.password-box{
position:relative;
}

.reset-card input{
width:100%;
padding:14px 18px;
border:none;
border-radius:30px;
outline:none;
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

.reset-btn{
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

.login-link{
margin-top:18px;
text-align:center;
font-size:14px;
cursor:pointer;
}

.error-text{
background:#ffd4d4;
color:#a40000;
padding:10px;
border-radius:12px;
margin-bottom:15px;
text-align:center;
}

.success-text{
background:#d4ffd9;
color:#006b1b;
padding:10px;
border-radius:12px;
margin-bottom:15px;
text-align:center;
}

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

@media(max-width:600px){

.reset-card{
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