<template>
  <div class="dashboard">

    <!-- Sidebar -->
    <Sidebar active="hotleads" />

    <!-- Main -->
    <main class="main-content">

      <Topbar
        title="Hot Leads & Analitik"
        subtitle="Analisis aktivitas chatbot Bangkesbangpol Boyolali"
      />

      <!-- ==========================
           SUMMARY CARD
      =========================== -->

      <div class="stats-grid">

        <StatCard
          title="Total Hot Leads"
          :value="stats.hot_leads"
          :icon="Flame"
          color="orange"
        />

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
        />

        <StatCard
          title="Hari Ini"
          :value="stats.today_chat"
          :icon="Calendar"
          color="green"
        />

      </div>

      <!-- ==========================
           CONTENT
      =========================== -->

      <div class="content-grid">

        <!-- LEFT -->

        <div class="left-panel">

          <!-- ==========================
               WORD CLOUD
          =========================== -->

          <div class="card">

            <div class="card-header">

              <h3>
                ☁️ Word Cloud Pertanyaan
              </h3>

              <button
                class="refresh-btn"
                @click="loadWordCloud"
              >
                Refresh
              </button>

            </div>

            <canvas
              ref="canvas"
              width="900"
              height="450"
            ></canvas>

          </div>

          <!-- ==========================
               TOP KEYWORD
          =========================== -->

          <div class="card">

            <div class="card-header">

              <h3>
                📊 Top Keyword
              </h3>

            </div>

            <div
              v-if="wordcloud.length"
              class="keyword-list"
            >

              <div
                v-for="item in wordcloud"
                :key="item.text"
                class="keyword-item"
              >

                <div class="keyword-top">

                  <strong>

                    {{ item.text }}

                  </strong>

                  <span>

                    {{ item.value }}

                  </span>

                </div>

                <div class="progress">

                  <div
                    class="progress-fill"
                    :style="{
                      width:
                      Math.min(item.value*3,100)+'%'
                    }"
                  ></div>

                </div>

              </div>

            </div>

            <div
              v-else
              class="empty"
            >

              Belum ada data.

            </div>

          </div>

        </div>

        <!-- RIGHT -->

        <div class="right-panel">

          <!-- ==========================
               HOT LEADS TABLE
          =========================== -->

          <div class="card">

            <div class="card-header">

              <h3>

                🔥 Daftar Hot Leads

              </h3>

            </div>

            <table class="table">

              <thead>

                <tr>

                  <th>No</th>

                  <th>Nomor</th>

                  <th>Total Chat</th>

                  <th>Chat Terakhir</th>

                </tr>

              </thead>

              <tbody>

                <tr
                  v-for="(item,index) in hotLeads"
                  :key="item.nomor"
                >

                  <td>

                    {{ index+1 }}

                  </td>

                  <td>

                    {{ item.nomor }}

                  </td>

                  <td>

                    {{ item.total_chat }}

                  </td>

                  <td>

                    {{ formatDate(item.last_chat) }}

                  </td>

                </tr>

              </tbody>

            </table>

          </div>

          <!-- ==========================
               USER PALING AKTIF
          =========================== -->

          <div class="card">

            <div class="card-header">

              <h3>

                👥 User Paling Aktif

              </h3>

            </div>

            <div
              v-if="hotLeads.length"
              class="user-list"
            >

              <div
                v-for="item in hotLeads.slice(0,5)"
                :key="item.nomor"
                class="user-item"
              >

                <div class="avatar">

                  {{ item.nomor.slice(-2) }}

                </div>

                <div class="user-info">

                  <strong>

                    {{ item.nomor }}

                  </strong>

                  <small>

                    {{ item.total_chat }} Chat

                  </small>

                </div>

              </div>

            </div>

            <div
              v-else
              class="empty"
            >

              Tidak ada data.

            </div>

          </div>

        </div>

      </div>

    </main>

  </div>
</template>

<script setup>

import { ref, onMounted, nextTick } from "vue"

import WordCloud from "wordcloud"

import api from "../services/api"

import Sidebar from "../components/Sidebar.vue"
import Topbar from "../components/Topbar.vue"
import StatCard from "../components/StatCard.vue"

import {
    Flame,
    Users,
    MessageSquareMore,
    Calendar
} from "lucide-vue-next"

// ====================================
// STATE
// ====================================

const stats = ref({

    total_chat:0,
    total_users:0,
    today_chat:0,
    hot_leads:0

})

const hotLeads = ref([])

const wordcloud = ref([])

const canvas = ref(null)

// ====================================
// LOAD STATS
// ====================================

const loadStats = async()=>{

    try{

        const res = await api.get("/stats")

        stats.value = res.data

    }

    catch(err){

        console.log(err)

    }

}

// ====================================
// LOAD HOT LEADS
// ====================================

const loadHotLeads = async()=>{

    try{

        const res = await api.get("/hot-leads")

        hotLeads.value = res.data

    }

    catch(err){

        console.log(err)

    }

}

// ====================================
// LOAD WORD CLOUD
// ====================================

const loadWordCloud = async()=>{

    try{

        const res = await api.get("/wordcloud")

        wordcloud.value = res.data

        await nextTick()

        renderWordCloud()

    }

    catch(err){

        console.log(err)

    }

}

// ====================================
// RENDER WORD CLOUD
// ====================================

const renderWordCloud = ()=>{

    if(!canvas.value) return

    const list = wordcloud.value.map(item=>[

        item.text,
        item.value

    ])

    WordCloud(canvas.value,{

        list,

        gridSize:10,

        weightFactor:function(size){

            return size*6

        },

        rotateRatio:0.35,

        minRotation:-0.5,

        maxRotation:0.5,

        backgroundColor:"#ffffff",

        drawOutOfBound:false,

        shrinkToFit:true,

        color:function(){

            const colors=[

                "#b71c1c",
                "#2563eb",
                "#16a34a",
                "#ea580c",
                "#7c3aed",
                "#0891b2"

            ]

            return colors[
                Math.floor(
                    Math.random()*colors.length
                )
            ]

        }

    })

}

// ====================================
// FORMAT DATE
// ====================================

const formatDate=(date)=>{

    if(!date) return "-"

    return new Date(date).toLocaleString("id-ID",{

        day:"2-digit",

        month:"short",

        year:"numeric",

        hour:"2-digit",

        minute:"2-digit"

    })

}

// ====================================
// REFRESH
// ====================================

const refreshAll = async()=>{

    await loadStats()

    await loadHotLeads()

    await loadWordCloud()

}

// ====================================
// AUTO REFRESH
// ====================================

let timer = null

// ====================================
// MOUNT
// ====================================

onMounted(async()=>{

    await refreshAll()

    timer = setInterval(async()=>{

        await refreshAll()

    },10000)

})

</script>

<style scoped>

/* ==========================================
   LAYOUT
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
   GRID
========================================== */

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:22px;
    margin-bottom:30px;
}

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

}

.card:hover{

    transform:translateY(-3px);

    box-shadow:0 18px 40px rgba(0,0,0,.08);

}

.card-header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:22px;

}

.card-header h3{

    color:#991b1b;

    font-size:20px;

    font-weight:700;

}

/* ==========================================
   BUTTON
========================================== */

.refresh-btn{

    border:none;

    background:#b71c1c;

    color:white;

    padding:10px 16px;

    border-radius:10px;

    cursor:pointer;

    font-weight:600;

    transition:.3s;

}

.refresh-btn:hover{

    background:#991b1b;

}

/* ==========================================
   WORD CLOUD
========================================== */

canvas{

    width:100%;

    height:430px;

    border-radius:16px;

    background:white;

}

/* ==========================================
   KEYWORD
========================================== */

.keyword-list{

    display:flex;

    flex-direction:column;

    gap:18px;

}

.keyword-item{

    background:#fafafa;

    border-radius:12px;

    padding:14px;

}

.keyword-top{

    display:flex;

    justify-content:space-between;

    margin-bottom:8px;

}

.keyword-top strong{

    color:#374151;

}

.keyword-top span{

    color:#b71c1c;

    font-weight:700;

}

/* ==========================================
   PROGRESS
========================================== */

.progress{

    width:100%;

    height:10px;

    background:#e5e7eb;

    border-radius:999px;

    overflow:hidden;

}

.progress-fill{

    height:100%;

    background:linear-gradient(90deg,#b71c1c,#ef4444);

    border-radius:999px;

    transition:.5s;

}

/* ==========================================
   TABLE
========================================== */

.table{

    width:100%;

    border-collapse:collapse;

}

.table th{

    background:#b71c1c;

    color:white;

    padding:14px;

    font-size:14px;

    text-align:left;

}

.table td{

    padding:14px;

    border-bottom:1px solid #ececec;

    font-size:14px;

}

.table tbody tr{

    transition:.25s;

}

.table tbody tr:hover{

    background:#fff5f5;

}

/* ==========================================
   USER
========================================== */

.user-list{

    display:flex;

    flex-direction:column;

    gap:15px;

}

.user-item{

    display:flex;

    align-items:center;

    gap:15px;

    padding:12px;

    border-radius:12px;

    transition:.3s;

    cursor:pointer;

}

.user-item:hover{

    background:#fff5f5;

}

.avatar{

    width:50px;

    height:50px;

    border-radius:50%;

    display:flex;

    justify-content:center;

    align-items:center;

    background:#b71c1c;

    color:white;

    font-weight:700;

    font-size:16px;

}

.user-info{

    display:flex;

    flex-direction:column;

}

.user-info strong{

    color:#111827;

}

.user-info small{

    color:#6b7280;

}

/* ==========================================
   EMPTY
========================================== */

.empty{

    text-align:center;

    color:#9ca3af;

    padding:50px 20px;

}

/* ==========================================
   ANIMATION
========================================== */

.card{

    animation:fadeUp .5s ease;

}

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
   SCROLLBAR
========================================== */

::-webkit-scrollbar{

    width:7px;

}

::-webkit-scrollbar-thumb{

    background:#cfcfcf;

    border-radius:999px;

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

}

@media(max-width:768px){

.main-content{

padding:20px;

}

.stats-grid{

grid-template-columns:1fr;

}

.table{

display:block;

overflow-x:auto;

white-space:nowrap;

}

canvas{

height:300px;

}

.card-header{

flex-direction:column;

align-items:flex-start;

gap:12px;

}

}

</style>