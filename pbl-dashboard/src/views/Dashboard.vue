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

        <button class="menu active">
          <LayoutDashboard size="18" />
          Dashboard
        </button>

        <button class="menu" @click="goRiwayat">
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

    <!-- MAIN -->
    <main class="main-content">

      <!-- HEADER -->
      <div class="topbar">

        <div>
          <h1>Dashboard</h1>
          <p>Monitoring chatbot Bangkesbangpol Boyolali</p>
        </div>

        <div class="admin-box">
          <div class="admin-avatar">
            N
          </div>

          <div>
            <h4>Admin</h4>
            <span>Online</span>
          </div>
        </div>

      </div>

      <!-- STATS -->
      <div class="stats-grid">

        <div class="stat-card red">
          <div>
            <h3>{{ stats.total_chat }}</h3>
            <p>Total Chat</p>
          </div>

          <MessageSquareMore class="icon" />
        </div>

        <div class="stat-card blue">
          <div>
            <h3>{{ stats.total_users }}</h3>
            <p>Total User</p>
          </div>

          <Users class="icon" />
        </div>

        <div class="stat-card green">
          <div>
            <h3>{{ stats.today_chat }}</h3>
            <p>Chat Hari Ini</p>
          </div>

          <BarChart3 class="icon" />
        </div>

        <div class="stat-card orange">
          <div>
            <h3>{{ stats.hot_leads }}</h3>
            <p>Hot Leads</p>
          </div>

          <Flame class="icon" />
        </div>

      </div>

      <!-- CHART -->
      <div class="chart-card">

        <div class="chart-header">
          <h2>Grafik Chat Harian</h2>
        </div>

        <div class="chart-wrapper">
          <canvas ref="chartCanvas"></canvas>
        </div>

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

const stats = ref({
  total_chat: 0,
  total_users: 0,
  today_chat: 0,
  hot_leads: 0
})

const chart = ref([])

const chartCanvas = ref(null)

let myChart = null

const loadStats = async () => {
  const res = await api.get("/stats")
  stats.value = res.data
}

const loadChart = async () => {
  const res = await api.get("/chart")
  chart.value = res.data
}

const renderChart = async () => {

  await nextTick()

  if (!chartCanvas.value) return

  if (myChart) {
    myChart.destroy()
  }

  myChart = new Chart(chartCanvas.value, {
    type: "line",

    data: {
      labels: chart.value.map(i => i.date),

      datasets: [
        {
          label: "Jumlah Chat",
          data: chart.value.map(i => i.total),
          fill: true,
          tension: 0.4
        }
      ]
    },

    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  })
}

onMounted(async () => {

  await loadStats()
  await loadChart()
  renderChart()

  setInterval(async () => {
    await loadStats()
    await loadChart()
    renderChart()
  }, 5000)

})

const logout = () => {
  localStorage.removeItem("isLogin")
  router.push("/")
}

const goRiwayat = () => {
  router.push("/history")
}

const goManage = () => {
  router.push("/manajemen")
}
</script>

<style scoped>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

.dashboard{
display:flex;
min-height:100vh;
background:#f4f7fb;
}

/* SIDEBAR */
.sidebar{
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

.logout-btn{
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
.main-content{
flex:1;
padding:30px;
}

/* TOPBAR */
.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.topbar h1{
font-size:30px;
color:#111827;
}

.topbar p{
color:#6b7280;
margin-top:5px;
}

.admin-box{
display:flex;
align-items:center;
gap:12px;
background:white;
padding:10px 16px;
border-radius:14px;
box-shadow:0 4px 15px rgba(0,0,0,0.05);
margin-right: 20px;
margin-left: 20px;
}

.admin-avatar{
width:45px;
height:45px;
border-radius:50%;
background:#ef4444;
display:flex;
justify-content:center;
align-items:center;
color:white;
font-weight:bold;
}

/* STATS */
.stats-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:30px;
}

.stat-card{
padding:25px;
border-radius:22px;
color:white;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
transition:0.3s;
}

.stat-card:hover{
transform:translateY(-5px);
}

.stat-card h3{
font-size:34px;
margin-bottom:5px;
}

.stat-card p{
font-size:14px;
}

.icon{
width:45px;
height:45px;
opacity:0.9;
}

.red{
background:linear-gradient(135deg,#ef4444,#dc2626);
}

.blue{
background:linear-gradient(135deg,#3b82f6,#2563eb);
}

.green{
background:linear-gradient(135deg,#10b981,#059669);
}

.orange{
background:linear-gradient(135deg,#f59e0b,#d97706);
}

/* CHART */
.chart-card{
background:white;
padding:25px;
border-radius:25px;
box-shadow:0 4px 20px rgba(0,0,0,0.05);
margin-top: -30px;
margin-left: 20px;
margin-right: 20px;
}

.chart-header{
margin-bottom:20px;
}

.chart-header h2{
font-size:22px;
color:#111827;
}

.chart-wrapper{
height:400px;
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