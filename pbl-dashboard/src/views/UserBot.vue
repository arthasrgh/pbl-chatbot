<template>
  <div class="dashboard">

    <!-- Sidebar -->
    <Sidebar active="users" />

    <!-- Main -->
    <main class="main-content">

      <Topbar
        title="Data Pengguna Chatbot"
        subtitle="Daftar seluruh pengguna chatbot Bangkesbangpol Boyolali"
      />

      <!-- =========================
           SUMMARY CARD
      ========================== -->

      <div class="summary-grid">

        <div class="summary-card red">

          <div>

            <h4>Total User</h4>

            <h2>{{ summary.total_users }}</h2>

          </div>

          <Users :size="38"/>

        </div>

        <div class="summary-card blue">

          <div>

            <h4>Hari Ini</h4>

            <h2>{{ summary.today_users }}</h2>

          </div>

          <CalendarDays :size="38"/>

        </div>

        <div class="summary-card green">

          <div>

            <h4>Minggu Ini</h4>

            <h2>{{ summary.week_users }}</h2>

          </div>

          <CalendarRange :size="38"/>

        </div>

        <div class="summary-card orange">

          <div>

            <h4>Bulan Ini</h4>

            <h2>{{ summary.month_users }}</h2>

          </div>

          <Calendar :size="38"/>

        </div>

      </div>

      <!-- =========================
           FILTER
      ========================== -->

      <div class="filter-card">

        <div class="search-box">

          <Search
            :size="18"
            class="search-icon"
          />

          <input

            v-model="search"

            type="text"

            placeholder="Cari nomor..."

            @keyup.enter="loadUsers"

          />

        </div>

        <select
          v-model="filter"
          @change="loadUsers"
        >

          <option value="">
            Semua
          </option>

          <option value="today">
            Hari Ini
          </option>

          <option value="week">
            Minggu Ini
          </option>

          <option value="month">
            Bulan Ini
          </option>

        </select>

        <button
          class="refresh-btn"
          @click="loadUsers"
        >

          Refresh

        </button>

      </div>

      <!-- =========================
           TABLE
      ========================== -->

      <div class="table-card">

        <div class="table-header">

          <h3>Daftar Pengguna</h3>

          <span>

            {{ users.length }} User

          </span>

        </div>

        <table>

          <thead>

            <tr>

              <th>No</th>

              <th>Nomor</th>

              <th>Total Chat</th>

              <th>Pertama Chat</th>

              <th>Terakhir Chat</th>

              <th>Status</th>

              <th>Aksi</th>

            </tr>

          </thead>

          <tbody>

            <tr
              v-for="(user,index) in users"
              :key="user.nomor"
            >

              <td>

                {{ index+1 }}

              </td>

              <td>

                {{ user.nomor }}

              </td>

              <td>

                {{ user.total_chat }}

              </td>

              <td>

                {{ formatDate(user.first_chat) }}

              </td>

              <td>

                {{ formatDate(user.last_chat) }}

              </td>

              <td>

                <span
                  class="status"
                  :class="statusClass(user.status)"
                >

                  {{ user.status }}

                </span>

              </td>

              <td>

                <button

                  class="detail-btn"

                  @click="openDetail(user.nomor)"

                >

                  Detail

                </button>

              </td>

            </tr>

            <tr v-if="users.length===0">

              <td
                colspan="7"
                class="empty"
              >

                Tidak ada data.

              </td>

            </tr>

          </tbody>

        </table>

      </div>

    </main>

  </div>
</template>

<script setup>

import { ref, onMounted } from "vue"
import { useRouter } from "vue-router"

import api from "../services/api"

import Sidebar from "../components/Sidebar.vue"
import Topbar from "../components/Topbar.vue"

import {

    Users,
    Calendar,
    CalendarDays,
    CalendarRange,
    Search

} from "lucide-vue-next"

const router = useRouter()

// ======================================
// STATE
// ======================================

const users = ref([])

const summary = ref({

    total_users: 0,

    today_users: 0,

    week_users: 0,

    month_users: 0

})

const search = ref("")

const filter = ref("")

const loading = ref(false)

// ======================================
// LOAD SUMMARY
// ======================================

const loadSummary = async () => {

    try {

        const res = await api.get("/users-bot/summary")

        summary.value = res.data

    }

    catch (err) {

        console.log(err)

    }

}

// ======================================
// LOAD USERS
// ======================================

const loadUsers = async () => {

    loading.value = true

    try {

        const res = await api.get("/users-bot", {

            params: {

                search: search.value,

                filter: filter.value

            }

        })

        users.value = res.data

    }

    catch (err) {

        console.log(err)

    }

    finally {

        loading.value = false

    }

}

// ======================================
// FORMAT TANGGAL
// ======================================

const formatDate = (date) => {

    if (!date) return "-"

    return new Date(date).toLocaleString("id-ID", {

        day: "2-digit",

        month: "long",

        year: "numeric",

        hour: "2-digit",

        minute: "2-digit"

    })

}

// ======================================
// STATUS BADGE
// ======================================

const statusClass = (status) => {

    switch (status) {

        case "Aktif":

            return "active"

        case "Tidak Aktif":

            return "inactive"

        default:

            return "old"

    }

}

// ======================================
// DETAIL USER
// ======================================

const openDetail = (nomor) => {

    router.push({

        path: "/history",

        query: {

            nomor

        }

    })

}

// ======================================
// REFRESH
// ======================================

const refreshData = async () => {

    await loadSummary()

    await loadUsers()

}

// ======================================
// AUTO SEARCH
// ======================================

let timeout = null

const autoSearch = () => {

    clearTimeout(timeout)

    timeout = setTimeout(() => {

        loadUsers()

    }, 500)

}

// ======================================
// WATCH SEARCH
// ======================================

import { watch } from "vue"

watch(search, () => {

    autoSearch()

})

// ======================================
// MOUNT
// ======================================

onMounted(async () => {

    await refreshData()

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
   SUMMARY
========================================== */

.summary-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-bottom:25px;
}

.summary-card{
    border-radius:18px;
    color:#fff;
    padding:24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    transition:.3s;
}

.summary-card:hover{
    transform:translateY(-4px);
}

.summary-card h4{
    font-size:15px;
    font-weight:500;
    margin-bottom:8px;
}

.summary-card h2{
    font-size:32px;
    font-weight:700;
}

.red{
    background:linear-gradient(135deg,#b71c1c,#ef5350);
}

.blue{
    background:linear-gradient(135deg,#1565c0,#42a5f5);
}

.green{
    background:linear-gradient(135deg,#2e7d32,#66bb6a);
}

.orange{
    background:linear-gradient(135deg,#ef6c00,#ffb74d);
}

/* ==========================================
   FILTER
========================================== */

.filter-card{
    display:flex;
    gap:16px;
    align-items:center;
    background:#fff;
    padding:20px;
    border-radius:18px;
    margin-bottom:25px;
    box-shadow:0 8px 25px rgba(0,0,0,.06);
}

.search-box{
    flex:1;
    position:relative;
}

.search-icon{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:#9ca3af;
}

.search-box input{
    width:100%;
    padding:14px 14px 14px 45px;
    border:1px solid #d1d5db;
    border-radius:12px;
    outline:none;
    transition:.3s;
    font-size:14px;
}

.search-box input:focus{
    border-color:#b71c1c;
    box-shadow:0 0 0 3px rgba(183,28,28,.12);
}

.filter-card select{
    padding:14px 16px;
    border-radius:12px;
    border:1px solid #d1d5db;
    background:#fff;
    cursor:pointer;
}

.refresh-btn{
    border:none;
    padding:14px 20px;
    background:#b71c1c;
    color:white;
    border-radius:12px;
    cursor:pointer;
    font-weight:600;
    transition:.3s;
}

.refresh-btn:hover{
    background:#991b1b;
}

/* ==========================================
   TABLE
========================================== */

.table-card{
    background:white;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.table-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 24px;
    border-bottom:1px solid #eee;
}

.table-header h3{
    color:#991b1b;
    font-size:20px;
}

.table-header span{
    color:#6b7280;
    font-size:14px;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#f8fafc;
}

thead th{
    padding:16px;
    text-align:left;
    font-size:14px;
    color:#374151;
    font-weight:700;
}

tbody td{
    padding:16px;
    border-top:1px solid #f1f5f9;
    font-size:14px;
    color:#374151;
}

tbody tr{
    transition:.2s;
}

tbody tr:hover{
    background:#fff5f5;
}

/* ==========================================
   STATUS
========================================== */

.status{
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
}

.active{
    background:#dcfce7;
    color:#15803d;
}

.inactive{
    background:#fef3c7;
    color:#b45309;
}

.old{
    background:#fee2e2;
    color:#b91c1c;
}

/* ==========================================
   BUTTON
========================================== */

.detail-btn{
    border:none;
    background:#2563eb;
    color:white;
    padding:8px 16px;
    border-radius:10px;
    cursor:pointer;
    transition:.3s;
    font-weight:600;
}

.detail-btn:hover{
    background:#1d4ed8;
}

/* ==========================================
   EMPTY
========================================== */

.empty{
    text-align:center;
    padding:40px;
    color:#9ca3af;
}

/* ==========================================
   RESPONSIVE
========================================== */

@media(max-width:1200px){

.summary-grid{

grid-template-columns:repeat(2,1fr);

}

}

@media(max-width:768px){

.main-content{

padding:20px;

}

.summary-grid{

grid-template-columns:1fr;

}

.filter-card{

flex-direction:column;

align-items:stretch;

}

table{

display:block;
overflow-x:auto;

}

}

</style>