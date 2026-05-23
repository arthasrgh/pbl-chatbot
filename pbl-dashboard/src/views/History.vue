<template>
  <div class="dashboard">
    
    <!-- SIDEBAR -->
    <aside class="sidebar">

      <div class="logo-area">
        <img :src="logo" class="logo" />
        <div>
          <h2>Bangkesbangpol</h2>
          <p>Dashboard Bot</p>
        </div>
      </div>

      <div class="menu-section">

        <button class="menu" @click="goDashboard">
          <LayoutDashboard size="18" />
          Dashboard
        </button>

        <button class="menu active" @click="goRiwayat">
          <MessageCircle size="18" />
          Riwayat Chat
        </button>

        <button class="menu" @click="goManage">
          <Users size="18" />
          Manajemen Admin
        </button>

      </div>

      <button class="logout-btn" @click="logout">
        <LogOut size="18" />
        Logout
      </button>

    </aside>

    <!-- Main -->
    <main class="main-content">
      <!-- Header FULL -->
      <div class="top-header">
        <h1>Riwayat Chat</h1>
      </div>
    </main>
  </div>

</template>

<script setup>
import {
  LayoutDashboard,
  MessageCircle,
  Users,
  LogOut,
  MessageSquareMore,
  BarChart3,
  Flame
} from "lucide-vue-next"

import { ref, onMounted, nextTick } from "vue"
import { useRouter } from "vue-router"
import Chart from "chart.js/auto"
import api from "../services/api"
import logo from "../assets/Logo_Boyo2.png"

const router = useRouter()

const logout = () => {
  localStorage.removeItem("isLogin")
  router.push("/")
}

const goDashboard = () => {
  router.push('/dashboard')
}

const goManage = () => {
  router.push('/manajemen')
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: sans-serif;
}

.dashboard {
  display: flex;
  min-height: 100vh;
  background:#f4f7fb;
}

.sidebar {
width:260px;
background:#111827;
padding:25px 20px;
display:flex;
flex-direction:column;
justify-content:space-between; 
}


.logo-area{
display:flex;
align-items:center;
gap:14px;
margin-bottom:40px;
}

.logo{
width:50px;
height:50px;
}

.logo-area h2{
color:white;
font-size:18px;
}

.logo-area p{
color:#9ca3af;
font-size:13px;
}

.menu-section{
display:flex;
flex-direction:column;
gap:12px;
}

.menu{
border:none;
padding:14px;
border-radius:14px;
display:flex;
align-items:center;
gap:12px;
font-size:15px;
font-weight:600;
cursor:pointer;
background:transparent;
color:#d1d5db;
transition:0.3s;
}

.menu:hover{
background:#1f2937;
}

.active{
background:#ef4444;
color:white;
}

/* Logout */
.logout-btn {
border:none;
padding:14px;
border-radius:14px;
background:#ef4444;
color:white;
font-weight:bold;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
cursor:pointer;
}

/* MAIN */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #8692a3;
  align-items: stretch; /* penting */
}

/* HEADER FULL WIDTH */
.top-header {
  position: fixed;
  top: 0;
  left: 260px; 
  right: 0;
  background: #ffffff;
  padding: 20px 40px;
  z-index: 100;
}

.content-area {
  margin-top: 80px; /* biar tidak ketiban */
  padding: 30px 40px;
}

.top-header h1 {
  font-size: 26px;
  font-weight: 700;
}

/* CARD LUAR (FULL, TIDAK CENTER) */
.card-wrapper {
  background: #f4f4f4;
  border-radius: 35px;
  padding: 25px;
  width: 100%;
}

/* TITLE */
.card-wrapper h2 {
  font-size: 20px;
  margin-bottom: 20px;
}

/* GRID */
.stats {
  display: flex;
  gap: 20px;
}

/* CARD DALAM */
.card {
  flex: 1;
  height: 130px;
  border-radius: 30px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* TEXT */
.card h3 {
  font-size: 32px;
  margin-bottom: 8px;
}

.card p {
  font-size: 14px;
  font-weight: 600;
}

/* RESPONSIVE */
@media(max-width:900px){

.dashboard{
flex-direction:column;
}

.sidebar{
width:100%;
}

.topbar{
flex-direction:column;
align-items:flex-start;
gap:20px;
}

}
</style>