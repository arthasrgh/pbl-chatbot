<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="red-strip"></div>

      <div class="sidebar-content">
        <div>
          <div class="menu-row">
            <span>All Messages</span>
            <strong>50</strong>
          </div>

          <div class="menu-row">
            <span>Unread</span>
          </div>

          <div class="line"></div>

          <!-- Menu -->
          <button class="menu-btn" @click="goDashboard">
            Home
          </button>

          <button class="menu-btn active-btn" @click="goRiwayat">
            Riwayat Chat
          </button>

          <button class="menu-btn" @click="goManage">
            Manajemen User
          </button>
        </div>

        <!-- Logout -->
        <button class="logout-btn" @click="goHome">
          Logout ↪
        </button>
      </div>
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
import { useRouter } from 'vue-router'

const router = useRouter()

const goHome = () => {
  router.push('/') 
}

const goDashboard = () => {
  router.push('/dashboard')
}

const goRiwayat = () => {
  router.push('/history')
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
}

.sidebar {
  width: 265px;
  background: #242741;
  display: flex;
  height : 100vh;
  position: sticky;
  top: 0;
}

.red-strip {
  width: 55px;
  background: #ef0033;
}

.sidebar-content {
  flex: 1;
  padding: 60px 18px 18px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* Menu */
.menu-row {
  display: flex;
  justify-content: space-between;
  color: white;
  font-size: 14px;
  margin-bottom: 12px;
}

.line {
  height: 2px;
  background: white;
  margin: 14px 0;
}

.menu-btn {
  width: 100%;
  border: none;
  padding: 10px;
  border-radius: 8px;
  font-weight: bold;
  background: white;
  cursor: pointer;
  margin-bottom: 10px;
}

/* ACTIVE BUTTON (buat halaman aktif) */
.active-btn {
  background: #ef0033;
  color: white;
}

/* Logout */
.logout-btn {
  border: none;
  background: white;
  padding: 12px;
  border-radius: 15px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
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
  left: 265px; 
  right: 0;
  background: #eeeeee;
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

/* WARNA */
.gray {
  background: #cfcfcf;
}

.green {
  background: #cfe5b6;
}

.pink {
  background: #efb1b1;
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .stats {
    flex-direction: column;
  }

  .sidebar {
    width: 220px;
  }
}
</style>