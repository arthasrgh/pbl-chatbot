<template>
  <div class="dashboard">

    <!-- Sidebar -->
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

        <button class="menu" @click="goRiwayat">
          <MessageCircle size="18" />
          Riwayat Chat
        </button>

        <button class="menu active">
          <Users size="18" />
          Manajemen User
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
      <div class="top-header">
        <h1>Manajemen Admin</h1>
      </div>

      <!-- CONTENT -->
      <div class="content-area">
        <div class="table-wrapper">

          <!-- ACTION -->
          <div class="table-top">
            <button class="add-btn" @click="openModal">
              + Tambah Admin
            </button>

            <input
              type="text"
              placeholder="Cari username..."
              class="search-input"
              v-model="search"
            />
          </div>

          <!-- ERROR / SUCCESS MESSAGE -->
          <div v-if="errorMessage" class="alert-error">
            {{ errorMessage }}
          </div>
          <div v-if="successMessage" class="alert-success">
            {{ successMessage }}
          </div>

          <!-- TABLE -->
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Username</th>
                <th>Email</th>
                <th>Aksi</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="admin in filteredAdmins" :key="admin.id">
                <td>{{ filteredAdmins.indexOf(admin) + 1 }}</td>
                <td>{{ admin.username }}</td>
                <td>{{ admin.email || '-' }}</td>
                <td>
                  <button class="edit-btn" @click="editAdmin(admin)">Edit</button>
                  <button class="delete-btn" @click="deleteAdmin(admin)">Hapus</button>
                </td>
              </tr>
              <tr v-if="filteredAdmins.length === 0">
                <td colspan="4" style="text-align: center; padding: 20px;">
                  Tidak ada data admin
                </td>
              </tr>
            </tbody>
          </table>

        </div>
      </div>

    </main>

    <!-- MODAL OVERLAY (di luar main, agar tidak terpotong) -->
    <div class="modal-overlay" v-if="showModal" @click.self="closeModal">
      <div class="modal">
        <h2>{{ isEdit ? 'Edit Admin' : 'Tambah Admin' }}</h2>

        <!-- ERROR inside modal -->
        <p v-if="errorMessage" class="error-text">
          {{ errorMessage }}
        </p>

        <!-- FORM -->
          <form @submit.prevent="saveAdmin">
            <div class="form-group">
              <label>Username</label>
              <input
                v-model="form.username"
                type="text"
                placeholder="Masukkan username"
                required
                :disabled="isSubmitting"
              />
          </div>

          <div class="form-group">
            <label>Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="contoh@email.com"
              :disabled="isSubmitting"
            />
          </div>


          <div class="form-group">
            <label>Password</label>
            <div class="password-box">
              <input
                :type="showPassword ? 'text' : 'password'"
                :placeholder="isEdit ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 8 karakter'"
                v-model="form.password"
                :required="!isEdit"
                :disabled="isSubmitting"
              />
              <button type="button" class="show-btn" @click="showPassword = !showPassword" :disabled="isSubmitting">
                <i :class="showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
              </button>
            </div>
            <small v-if="!isEdit">Minimal 8 karakter, huruf besar, angka, dan simbol</small>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn-secondary" :disabled="isSubmitting">
              Batal
            </button>
            <button type="submit" class="btn-primary" :disabled="isSubmitting">
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import {
  LayoutDashboard, MessageCircle, Users, LogOut
} from "lucide-vue-next"

import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from "../services/api"
import logo from "../assets/Logo_Boyo2.png"

const router = useRouter()
const search = ref('')

/* MODAL */
const showModal = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const showPassword = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const isSubmitting = ref(false)  // ✅ Loading state

/* DATA ADMIN dari API */
const admins = ref([])

/* FORM */
const form = ref({
  username: '',
  email: '', 
  password: ''
})

/* FILTER SEARCH */
const filteredAdmins = computed(() => {
  return admins.value.filter(admin =>
    admin.username?.toLowerCase().includes(search.value.toLowerCase())
  )
})

/* FORMAT DATE */
const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

/* ✅ LOAD ADMIN DARI API */
const loadAdmins = async () => {
  try {
    const res = await api.get('/admins')  // ✅ Pastikan route ini sesuai di api.php
    admins.value = res.data.data || res.data || []
  } catch (err) {
    console.error('Gagal load admins:', err)
    errorMessage.value = 'Gagal memuat data admin'
  }
}

/* OPEN MODAL */
const openModal = () => {
  showModal.value = true
  isEdit.value = false
  editId.value = null
  showPassword.value = false
  errorMessage.value = ''
  successMessage.value = ''
  form.value = { username: '', password: '' }
}

/* CLOSE MODAL */
const closeModal = () => {
  showModal.value = false
  errorMessage.value = ''
  successMessage.value = ''
  showPassword.value = false
  isSubmitting.value = false
}

/* ✅ SAVE ADMIN KE API */
const saveAdmin = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  // Validasi frontend
  if (!form.value.username || (!isEdit.value && !form.value.password)) {
    errorMessage.value = 'Isi semua field'
    return
  }

  // Validasi password
  if (form.value.password) {
    const pass = form.value.password
    if (pass.length < 8) {
      errorMessage.value = 'Password minimal 8 karakter'
      return
    }
    if (!/[A-Z]/.test(pass)) {
      errorMessage.value = 'Password harus memiliki huruf kapital'
      return
    }
    if (!/[0-9]/.test(pass)) {
      errorMessage.value = 'Password harus memiliki angka'
      return
    }
    if (!/[!@#$%^&*(),.?":{}|<>_]/.test(pass)) {
      errorMessage.value = 'Password harus memiliki karakter spesial'
      return
    }
  }

  isSubmitting.value = true

  try {
    if (isEdit.value) {
      await api.put(`/admins/${editId.value}`, form.value)
      successMessage.value = 'Admin berhasil diupdate'
    } else {
      await api.post('/admins', form.value)
      successMessage.value = 'Admin berhasil ditambahkan'
    }

    await loadAdmins()
    setTimeout(() => closeModal(), 1500)
    
  } catch (err) {
    if (err.response?.data?.message) {
      errorMessage.value = err.response.data.message
    } else if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      errorMessage.value = Object.values(errors).flat().join(', ')
    } else {
      errorMessage.value = 'Terjadi kesalahan pada server'
    }
    console.error('Error:', err)
  } finally {
    isSubmitting.value = false
  }
}

const editAdmin = (admin) => {
  isEdit.value = true
  showModal.value = true
  showPassword.value = false
  errorMessage.value = ''
  successMessage.value = ''
  
  editId.value = admin.id
  
  form.value = {
    username: admin.username || '',
    email: admin.email || '',   // Tambahkan field email jika ada
    password: ''  // Kosong = tidak diubah
  }
}

const deleteAdmin = async (admin) => {
  if (!confirm(`Hapus admin "${admin.username}"?`)) return

  try {
    await api.delete(`/admins/${admin.id}`)
    successMessage.value = 'Admin berhasil dihapus'
    await loadAdmins()
    setTimeout(() => { successMessage.value = '' }, 2000)
  } catch (err) {
    console.error('Gagal hapus admin:', err)
    errorMessage.value = err.response?.data?.message || 'Gagal menghapus admin'
  }
}

/* ROUTER */
const logout = () => {
  localStorage.removeItem("isLogin")
  router.push("/")
}

const goDashboard = () => router.push('/dashboard')
const goRiwayat = () => router.push('/history')

/* ✅ Load data saat component mounted */
onMounted(() => {
  loadAdmins()
})
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

/* SIDEBAR */
.sidebar {
  width: 260px;
  background: #111827;
  padding: 25px 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.logo-area {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 40px;
}

.logo {
  width: 50px;
  height: 50px;
}

.logo-area h2 {
  color: white;
  font-size: 18px;
}

.logo-area p {
  color: #9ca3af;
  font-size: 13px;
}

.menu-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.menu {
  border: none;
  padding: 14px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  background: transparent;
  color: #d1d5db;
  transition: 0.3s;
}

.menu:hover {
  background: #1f2937;
}

.active {
  background: #ef4444;
  color: white;
}

.logout-btn {
  border: none;
  padding: 14px;
  border-radius: 14px;
  background: #ef4444;
  color: white;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
}

/* MAIN */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #8692a3;
}

.top-header {
  position: fixed;
  top: 0;
  left: 260px;
  right: 0;
  background: #eeeeee;
  padding: 20px 40px;
  z-index: 100;
}

.top-header h1 {
  font-size: 26px;
  font-weight: 700;
}

.content-area {
  margin-top: 80px;
  padding: 30px 40px;
  width: 100%;
}

/* TABLE */
.table-wrapper {
  background: #f4f4f4;
  border-radius: 35px;
  padding: 25px;
  width: 100%;
  min-height: 75vh;
}

.table-top {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 10px;
}

.add-btn {
  background: #ef0033;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  font-weight: bold;
  cursor: pointer;
}

.add-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.search-input {
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ccc;
  width: 220px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #242741;
  color: white;
}

th,
td {
  padding: 15px;
  text-align: left;
}

tbody tr {
  border-bottom: 1px solid #ccc;
}

tbody tr:hover {
  background: #e5e7eb;
}

/* BUTTON */
.edit-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  margin-right: 8px;
  cursor: pointer;
}

.edit-btn:hover {
  background: #2563eb;
}

.delete-btn {
  background: #ef4444;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
}

.delete-btn:hover {
  background: #dc2626;
}

/* ALERT */
.alert-error,
.alert-success,
.error-text {
  padding: 10px 15px;
  border-radius: 10px;
  margin-bottom: 15px;
  font-size: 14px;
}

.alert-error,
.error-text {
  background: #fee2e2;
  color: #dc2626;
}

.alert-success {
  background: #d1fae5;
  color: #059669;
}

/* MODAL */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 20px;
}

.modal {
  background: white;
  width: 100%;
  max-width: 450px;
  padding: 30px;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal h2 {
  text-align: center;
  margin-bottom: 10px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  font-size: 14px;
}

.form-group input {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #ccc;
  font-size: 14px;
}

.form-group input:disabled {
  background: #f3f4f6;
  cursor: not-allowed;
}

.form-group small {
  color: #6b7280;
  font-size: 12px;
}

/* PASSWORD */
.password-box {
  position: relative;
}

.password-box input {
  padding-right: 50px;
}

.show-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 16px;
  color: #6b7280;
}

.show-btn:hover {
  color: #374151;
}

.show-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* ACTION */
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 10px;
}

.btn-primary {
  background: #ef0033;
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
}

.btn-primary:hover:not(:disabled) {
  background: #c20029;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #9ca3af;
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
}

.btn-secondary:hover:not(:disabled) {
  background: #6b7280;
}

.btn-secondary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .sidebar {
    width: 220px;
  }

  .table-top {
    flex-direction: column;
  }

  .search-input {
    width: 100%;
  }

  table {
    font-size: 14px;
  }

  th, td {
    padding: 12px 8px;
  }
}
</style>