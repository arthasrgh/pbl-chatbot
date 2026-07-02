<template>
  <aside class="sidebar">

    <!-- ================= LOGO ================= -->
    <div>

      <div class="logo-area">

        <img
          src="../assets/Logo_Boyo2.png"
          class="logo"
        />

        <div>

          <h2>Bangkesbangpol</h2>

          <p>Dashboard Chatbot</p>

        </div>

      </div>

      <!-- ================= MENU ================= -->

      <nav class="menu">

        <button
          class="menu-item"
          :class="{ active: active === 'dashboard' }"
          @click="router.push('/dashboard')"
        >
          <LayoutDashboard :size="20" />
          Dashboard
        </button>

        <button
            class="menu-item"
            :class="{active:active==='users'}"
            @click="router.push('/users-bot')"
        >

        <Smartphone :size="20"/>

         Data Pengguna

        </button>

        <button
          class="menu-item"
          :class="{ active: active === 'history' }"
          @click="router.push('/history')"
        >
          <MessageSquareMore :size="20" />
          Riwayat Chat
        </button>

        <button
          class="menu-item"
          :class="{ active: active === 'hotleads' }"
          @click="router.push('/hot-leads')"
        >
          <Flame :size="20" />
          Hot Leads
        </button>

        <!-- HANYA ADMIN -->

        <button
          v-if="isAdmin"
          class="menu-item"
          :class="{ active: active === 'admin' }"
          @click="router.push('/manajemen')"
        >
          <Users :size="20" />
          Manajemen Admin
        </button>

      </nav>

    </div>

    <!-- ================= FOOTER ================= -->

    <div class="sidebar-footer">

      <div class="profile">

        <div class="avatar">

          {{ initial }}

        </div>

        <div>

          <h4>{{ adminNama }}</h4>

          <span>{{ adminEmail }}</span>

          <small>{{ roleText }}</small>

        </div>

      </div>

      <button
        class="logout-btn"
        @click="logout"
      >

        <LogOut :size="18" />

        Logout

      </button>

    </div>

  </aside>
</template>

<script setup>

import { computed } from "vue"

import { useRouter } from "vue-router"

import {

  LayoutDashboard,
  MessageSquareMore,
  Flame,
  Users,
  LogOut

} from "lucide-vue-next"

const router = useRouter()

const props = defineProps({

  active: String

})

const user = JSON.parse(

  localStorage.getItem("user") || "{}"

)

const adminNama = computed(() => {

  return user.nama || "Administrator"

})

const adminEmail = computed(() => {

  return user.email || "-"

})

const roleText = computed(() => {

  return user.role === "admin"

    ? "Administrator"

    : "Customer Service"

})

const isAdmin = computed(() => {

  return user.role === "admin"

})

const initial = computed(() => {

  return adminNama.value.charAt(0).toUpperCase()

})

const logout = () => {

  localStorage.removeItem("isLogin")

  localStorage.removeItem("user")

  router.push("/")

}

</script>

<style scoped>

.sidebar{
width:270px;
height:100vh;
background:#ffffff;
border-right:1px solid #e5e7eb;
display:flex;
flex-direction:column;
justify-content:space-between;
padding:24px;
position:sticky;
top:0;
}

.logo-area{
display:flex;
align-items:center;
gap:15px;
margin-bottom:40px;
}

.logo{
width:55px;
height:55px;
object-fit:contain;
}

.logo-area h2{
font-size:18px;
color:#b71c1c;
margin-bottom:3px;
font-weight:700;
}

.logo-area p{
font-size:13px;
color:#6b7280;
}

.menu{
display:flex;
flex-direction:column;
gap:12px;
}

.menu-item{
display:flex;
align-items:center;
gap:14px;
padding:15px;
border:none;
background:transparent;
border-radius:14px;
cursor:pointer;
font-size:15px;
font-weight:600;
color:#374151;
transition:.3s;
}

.menu-item:hover{
background:#fee2e2;
color:#b71c1c;
transform:translateX(5px);
}

.active{
background:#b71c1c;
color:white;
box-shadow:0 10px 25px rgba(183,28,28,.25);
}

.sidebar-footer{
border-top:1px solid #e5e7eb;
padding-top:20px;
}

.profile{
display:flex;
align-items:center;
gap:12px;
margin-bottom:18px;
}

.avatar{
width:46px;
height:46px;
border-radius:50%;
background:#b71c1c;
display:flex;
justify-content:center;
align-items:center;
font-weight:700;
font-size:18px;
color:white;
}

.profile h4{
font-size:15px;
color:#111827;
margin-bottom:2px;
}

.profile span{
display:block;
font-size:12px;
color:#6b7280;
}

.profile small{
display:block;
margin-top:2px;
font-size:11px;
font-weight:600;
color:#b71c1c;
}

.logout-btn{
width:100%;
display:flex;
justify-content:center;
align-items:center;
gap:10px;
padding:14px;
border:none;
border-radius:14px;
background:#dc2626;
color:white;
font-size:15px;
font-weight:600;
cursor:pointer;
transition:.3s;
}

.logout-btn:hover{
background:#991b1b;
transform:translateY(-2px);
}

@media(max-width:900px){

.sidebar{
position:relative;
width:100%;
height:auto;
}

}
</style>