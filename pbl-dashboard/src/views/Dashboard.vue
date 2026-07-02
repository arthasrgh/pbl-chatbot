<template>
  <div class="dashboard">

    <!-- Sidebar -->
    <Sidebar active="dashboard" />

    <!-- Main -->
    <main class="main-content">

      <Topbar
        title="Dashboard"
        subtitle="Monitoring Chatbot Bangkesbangpol Boyolali"
      />

      <!-- ===================== -->
      <!-- STATISTIK -->
      <!-- ===================== -->

      <div class="stats-grid">

        <StatCard
          title="Total Chat"
          :value="stats.total_chat"
          :icon="MessageSquareMore"
          color="red"
        />

        <StatCard
            title="Total User"
            :value="stats.total_users"
            :icon="Users"
            color="blue"
            @click="router.push('/users-bot')"
        />

        <StatCard
          title="Chat Hari Ini"
          :value="stats.today_chat"
          :icon="BarChart3"
          color="green"
        />

        <StatCard
          title="Hot Leads"
          :value="stats.hot_leads"
          :icon="Flame"
          color="orange"
          @click="router.push('/hot-leads')"
        />

        <!-- ADMIN -->

        <StatCard
          title="Administrator"
          :value="stats.total_admin"
          :icon="Shield"
          color="purple"
        />

        <!-- CUSTOMER SERVICE -->

        <StatCard
          title="Customer Service"
          :value="stats.total_cs"
          :icon="Headset"
          color="teal"
        />

        <StatCard
          title="User AI"
          :value="aiUsage.total_ai_users"
          :icon="Users"
          color="indigo"
        />

        <StatCard
          title="AI Request"
          :value="aiUsage.total_ai_request"
          :icon="BarChart3"
          color="pink"
        />

      </div>

      <!-- ===================== -->
      <!-- CONTENT -->
      <!-- ===================== -->

      <div class="content-grid">

        <!-- ================= LEFT ================= -->

        <div class="left-panel">

          <!-- Grafik -->

          <div class="card">

            <div class="card-header">

              <h3>Grafik Chat Harian</h3>

              <span>Realtime</span>

            </div>

            <div class="chart-wrapper">

              <canvas ref="chartCanvas"></canvas>

            </div>

          </div>

          <div class="card">

            <div class="card-header">

                <h3>Grafik Penggunaan AI</h3>

                    <span>Realtime</span>

            </div>

            <div class="chart-wrapper">

                <canvas ref="aiChartCanvas"></canvas>

            </div>

        </div>

          <!-- Statistik Progress -->

          <div class="card">

            <div class="card-header">

              <h3>Statistik Sistem</h3>

            </div>

            <!-- USER -->

            <div class="progress-item">

              <span>Total User</span>

              <strong>{{ stats.total_users }}</strong>

            </div>

            <div class="progress">

              <div
                class="progress-fill blue"
                :style="{
                  width: Math.min(stats.total_users * 2,100) + '%'
                }"
              ></div>

            </div>

            <!-- CHAT -->

            <div class="progress-item">

              <span>Chat Hari Ini</span>

              <strong>{{ stats.today_chat }}</strong>

            </div>

            <div class="progress">

              <div
                class="progress-fill green"
                :style="{
                  width: Math.min(stats.today_chat * 5,100) + '%'
                }"
              ></div>

            </div>

            <!-- HOT LEADS -->

            <div class="progress-item">

              <span>Hot Leads</span>

              <strong>{{ stats.hot_leads }}</strong>

            </div>

            <div class="progress">

              <div
                class="progress-fill orange"
                :style="{
                  width: Math.min(stats.hot_leads * 10,100) + '%'
                }"
              ></div>

            </div>

            <!-- ADMIN -->

            <div class="progress-item">

              <span>Administrator</span>

              <strong>{{ stats.total_admin }}</strong>

            </div>

            <div class="progress">

              <div
                class="progress-fill purple"
                :style="{
                  width: Math.min(stats.total_admin * 50,100) + '%'
                }"
              ></div>

            </div>

            <!-- CS -->

            <div class="progress-item">

              <span>Customer Service</span>

              <strong>{{ stats.total_cs }}</strong>

            </div>

            <div class="progress">

              <div
                class="progress-fill teal"
                :style="{
                  width: Math.min(stats.total_cs * 50,100) + '%'
                }"
              ></div>

            </div>

          </div>

        </div>

        <!-- ================= RIGHT ================= -->

        <div class="right-panel">

          <div class="card">

            <div class="card-header">

              <h3>Chat Terbaru</h3>

              <button
                class="refresh-btn"
                @click="loadRecentChat"
              >

                Refresh

              </button>

            </div>

            <div
              v-if="recentChat.length"
              class="recent-list"
            >

              <div
                v-for="chat in recentChat"
                :key="chat.created_at"
                class="chat-item"
                @click="openHistory(chat.nomor)"
              >

                <div class="avatar">

                  {{ chat.nomor.slice(-2) }}

                </div>

                <div class="chat-info">

                  <div class="chat-top">

                    <strong>

                      {{ chat.nomor }}

                    </strong>

                    <span
                      class="badge"
                      :class="
                        chat.sender == 'bot'
                          ? 'bot'
                          : 'user'
                      "
                    >

                      {{ chat.sender }}

                    </span>

                  </div>

                  <p>

                    {{ chat.pesan }}

                  </p>

                  <small>

                    {{ formatTime(chat.created_at) }}

                  </small>

                </div>

              </div>

            </div>

            <div
              v-else
              class="empty"
            >

              Belum ada chat.

            </div>

            <button
              class="view-btn"
              @click="router.push('/history')"
            >

              Lihat Semua Chat

            </button>

          </div>

        </div>

      </div>

    </main>

  </div>
</template>

<script setup>

import { ref, onMounted, nextTick, onBeforeUnmount } from "vue"
import { useRouter } from "vue-router"
import Chart from "chart.js/auto"

import api from "../services/api"

import Sidebar from "../components/Sidebar.vue"
import Topbar from "../components/Topbar.vue"
import StatCard from "../components/StatCard.vue"

import {

    MessageSquareMore,
    Users,
    BarChart3,
    Flame,
    Shield,
    Headset

} from "lucide-vue-next"

const lastUpdate = ref("");

const router = useRouter()

// =======================================
// STATE
// =======================================

const stats = ref({

    total_chat: 0,

    total_users: 0,

    today_chat: 0,

    hot_leads: 0,

    total_admin: 0,

    total_cs: 0

})

const aiUsage = ref({
    total_ai_users: 0,
    total_ai_request: 0
})

const chart = ref([])
const recentChat = ref([])
const chartCanvas = ref(null)
const aiChart = ref([]);
const aiChartCanvas = ref(null);

let aiChartObject = null;

let myChart = null

let interval = null

// =======================================
// LOAD STATISTIK
// =======================================

const loadStats = async () => {

    try {

        const res = await api.get("/stats")

        stats.value = {
            total_chat: res.data.total_chat || 0,
            total_users: res.data.total_users || 0,
            today_chat: res.data.today_chat || 0,
            hot_leads: res.data.hot_leads || 0,
            total_admin: res.data.total_admin || 0,
            total_cs: res.data.total_cs || 0
        }

    } catch (err) {

        console.error("Load Stats Error :", err)

    }

}

    const loadAiUsage = async () => {

    try {

        const res = await api.get("/ai/usages");

        aiUsage.value.total_ai_users = res.data.length;

        aiUsage.value.total_ai_request =
            res.data.reduce(
                (a, b) => a + Number(b.jumlah),
                0
            );

    } catch (err) {

        console.log(err);

    }

}

// =======================================
// LOAD CHART
// =======================================

const loadChart = async () => {

    try {

        const res = await api.get("/chart")

        chart.value = res.data

    }

    catch (err) {

        console.error(err)

    }

}

// =======================================
// LOAD RECENT CHAT
// =======================================

const loadRecentChat = async () => {

    try {

        const res = await api.get("/recent-chat")

        recentChat.value = res.data

    }

    catch (err) {

        console.error(err)

    }

}

// =======================================
// RENDER CHART
// =======================================

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

                    borderColor: "#b71c1c",

                    backgroundColor: "rgba(183,28,28,.12)",

                    fill: true,

                    tension: .4,

                    pointRadius: 4,

                    pointBackgroundColor: "#b71c1c",

                    borderWidth: 3

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    display: true

                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        precision: 0

                    }

                }

            }

        }

    })

}

const renderAiChart = async () => {

    await nextTick();

    if (!aiChartCanvas.value) return;

    if (aiChartObject) {
        aiChartObject.destroy();
    }

    aiChartObject = new Chart(aiChartCanvas.value, {

        type: "bar",

        data: {

            labels: aiChart.value.map(i => i.date),

            datasets: [
                {
                    label: "AI Request",
                    data: aiChart.value.map(i => i.total),
                    backgroundColor: "#4f46e5"
                }
            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false

        }

    });

}

// =======================================
// FORMAT WAKTU
// =======================================

const formatTime = (datetime) => {

    if (!datetime) return "-"

    const now = new Date()

    const date = new Date(datetime)

    const diff = Math.floor((now - date) / 1000)

    if (diff < 60) {

        return diff + " detik lalu"

    }

    if (diff < 3600) {

        return Math.floor(diff / 60) + " menit lalu"

    }

    if (diff < 86400) {

        return Math.floor(diff / 3600) + " jam lalu"

    }

    return date.toLocaleDateString("id-ID")

}

// =======================================
// BUKA HISTORY
// =======================================

const openHistory = (nomor) => {

    router.push({

        path: "/history",

        query: {

            nomor

        }

    })

}

// =======================================
// AUTO REFRESH
// =======================================

const refreshDashboard = async () => {

    await loadStats();
    await loadAiUsage();
    await loadChart();
    await loadAiChart();
    await loadRecentChat();

    await renderChart();
    await renderAiChart();

}

// =======================================
// MOUNT
// =======================================

let refreshInterval = null;

onMounted(async () => {

    await refreshDashboard();

    refreshInterval = setInterval(async () => {

        await refreshDashboard();

    }, 10000); // refresh setiap 10 detik

    lastUpdate.value = new Date().toLocaleTimeString("id-ID");

});

// =======================================
// DESTROY
// =======================================

onBeforeUnmount(() => {

    if (myChart) {
        myChart.destroy();
    }

    if (aiChartObject) {
        aiChartObject.destroy();
    }

    if (refreshInterval) {
        clearInterval(refreshInterval);
    }

});

const loadAiChart = async () => {

    try {

        const res = await api.get("/ai-chart");

        aiChart.value = res.data;

    } catch (err) {

        console.log(err);

    }

}

</script>

<style scoped>

/* ==========================================
   DASHBOARD LAYOUT
========================================== */

.dashboard{
    display:flex;
    min-height:100vh;
    background:#f4f6fb;
}

.main-content{
    flex:1;
    padding:30px;
    overflow-y:auto;
}

/* ==========================================
   STAT CARD GRID
========================================== */

.stats-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:22px;
    margin-bottom:30px;
}

/* ==========================================
   CONTENT GRID
========================================== */

.content-grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:24px;
    align-items:start;
}

.left-panel,
.right-panel{
    display:flex;
    flex-direction:column;
    gap:24px;
}

/* ==========================================
   CARD
========================================== */

.card{
    background:#fff;
    border-radius:20px;
    padding:24px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    transition:.3s;
    animation:fadeUp .5s ease;
}

.card:hover{
    transform:translateY(-3px);
    box-shadow:0 18px 40px rgba(0,0,0,.08);
}

/* ==========================================
   CARD HEADER
========================================== */

.card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:22px;
}

.card-header h3{
    font-size:20px;
    font-weight:700;
    color:#991b1b;
}

.card-header span{
    font-size:13px;
    color:#6b7280;
}

/* ==========================================
   CHART
========================================== */

.chart-wrapper{
    width:100%;
    height:360px;
}

.chart-wrapper canvas{
    width:100%!important;
    height:100%!important;
}

/* ==========================================
   PROGRESS
========================================== */

.progress-item{
    display:flex;
    justify-content:space-between;
    margin-top:20px;
    margin-bottom:8px;
    font-size:14px;
    color:#374151;
}

.progress{
    width:100%;
    height:10px;
    border-radius:20px;
    background:#eceff4;
    overflow:hidden;
    margin-bottom:18px;
}

.progress-fill{
    height:100%;
    border-radius:20px;
    transition:width .8s ease;
}

.progress-fill.blue{
    background:#2563eb;
}

.progress-fill.green{
    background:#16a34a;
}

.progress-fill.orange{
    background:#ea580c;
}

.progress-fill.purple{
    background:#7c3aed;
}

.progress-fill.teal{
    background:#0f766e;
}

/* ==========================================
   RECENT CHAT
========================================== */

.recent-list{
    display:flex;
    flex-direction:column;
    gap:16px;
    max-height:650px;
    overflow-y:auto;
    padding-right:5px;
}

.recent-list::-webkit-scrollbar{
    width:6px;
}

.recent-list::-webkit-scrollbar-thumb{
    background:#d1d5db;
    border-radius:20px;
}

.chat-item{
    display:flex;
    gap:14px;
    padding:14px;
    border-radius:14px;
    transition:.25s;
    cursor:pointer;
    border:1px solid #eef2f7;
}

.chat-item:hover{
    background:#fef2f2;
    transform:translateX(4px);
}

.avatar{
    width:50px;
    height:50px;
    min-width:50px;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#b71c1c;
    color:#fff;
    font-weight:700;
    font-size:16px;
}

.chat-info{
    flex:1;
}

.chat-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:6px;
}

.chat-top strong{
    color:#111827;
    font-size:15px;
}

.chat-info p{
    font-size:13px;
    color:#6b7280;
    margin:6px 0;
    overflow:hidden;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
}

.chat-info small{
    color:#9ca3af;
    font-size:12px;
}

/* ==========================================
   BADGE
========================================== */

.badge{
    padding:4px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
}

.badge.user{
    background:#dcfce7;
    color:#15803d;
}

.badge.bot{
    background:#dbeafe;
    color:#2563eb;
}

/* ==========================================
   BUTTON
========================================== */

.refresh-btn{
    border:none;
    background:#b71c1c;
    color:#fff;
    padding:8px 14px;
    border-radius:10px;
    cursor:pointer;
    font-size:13px;
    font-weight:600;
    transition:.3s;
}

.refresh-btn:hover{
    background:#991b1b;
}

.view-btn{
    width:100%;
    margin-top:20px;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#b71c1c;
    color:#fff;
    font-weight:700;
    cursor:pointer;
    transition:.3s;
}

.view-btn:hover{
    background:#991b1b;
    transform:translateY(-2px);
}

/* ==========================================
   EMPTY
========================================== */

.empty{
    text-align:center;
    color:#9ca3af;
    padding:60px 20px;
    font-size:14px;
}

/* ==========================================
   ANIMATION
========================================== */

@keyframes fadeUp{

    from{
        opacity:0;
        transform:translateY(20px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }

}

/* ==========================================
   RESPONSIVE
========================================== */

@media(max-width:1200px){

    .stats-grid{
        grid-template-columns:repeat(2,1fr);
    }

    .content-grid{
        grid-template-columns:1fr;
    }

    .right-panel{
        order:-1;
    }

}

@media(max-width:768px){

    .main-content{
        padding:20px;
    }

    .stats-grid{
        grid-template-columns:1fr;
    }

    .chart-wrapper{
        height:280px;
    }

    .chat-item{
        flex-direction:column;
        align-items:flex-start;
    }

    .chat-top{
        flex-direction:column;
        align-items:flex-start;
        gap:6px;
    }

}

</style>