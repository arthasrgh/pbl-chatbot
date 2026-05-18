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
        <h1>Manajemen User</h1>
      </div>

      <!-- CONTENT -->
      <div class="content-area">

        <div class="table-wrapper">

          <!-- ACTION -->
          <div class="table-top">

            <button
              class="add-btn"
              @click="openModal"
            >
              + Tambah User
            </button>

            <input
              type="text"
              placeholder="Cari user..."
              class="search-input"
              v-model="search"
            />

          </div>

          <!-- TABLE -->
          <table>

            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Aksi</th>
              </tr>
            </thead>

            <tbody>

              <tr
                v-for="(user, index) in filteredUsers"
                :key="index"
              >
                <td>{{ index + 1 }}</td>
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                

                <td>

                  <button
                    class="edit-btn"
                    @click="editUser(index)"
                  >
                    Edit
                  </button>

                  <button
                    class="delete-btn"
                    @click="deleteUser(index)"
                  >
                    Hapus
                  </button>

                </td>
              </tr>

            </tbody>

          </table>

        </div>

      </div>

      <!-- MODAL -->
      <div
        class="modal-overlay"
        v-if="showModal"
      >

        <div class="modal">

          <h2>
            {{ isEdit ? 'Edit User' : 'Tambah User' }}
          </h2>

          <!-- ERROR -->
          <p
            v-if="errorMessage"
            class="error-text"
          >
            {{ errorMessage }}
          </p>

          <!-- INPUT -->
          <input
            type="text"
            placeholder="Nama"
            v-model="form.name"
          />

          <input
            type="email"
            placeholder="Email"
            v-model="form.email"
          />

          

          <!-- PASSWORD -->
          <div class="password-box">

            <input
              :type="showPassword ? 'text' : 'password'"
              placeholder="Password"
              v-model="form.password"
            />

            <button
  type="button"
  class="show-btn"
  @click="showPassword = !showPassword"
>
  <i
    :class="showPassword
      ? 'fa-solid fa-eye-slash'
      : 'fa-solid fa-eye'"
  ></i>
</button>

          </div>

          <!-- BUTTON -->
          <div class="modal-action">

            <button
              class="save-btn"
              @click="saveUser"
            >
              Simpan
            </button>

            <button
              class="cancel-btn"
              @click="closeModal"
            >
              Batal
            </button>

          </div>

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

import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import Chart from "chart.js/auto"
import api from "../services/api"
import logo from "../assets/Logo_Boyo2.png"

const router = useRouter()

/* SEARCH */
const search = ref('')

/* MODAL */
const showModal = ref(false)
const isEdit = ref(false)
const editIndex = ref(null)
const showPassword = ref(false)

/* ERROR */
const errorMessage = ref('')

/* DATA USER */
const users = ref([
  {
    name: 'Admin',
    email: 'admin@gmail.com',
    password: 'Admin123!'
  }

  
])

/* FORM */
const form = ref({
  name: '',
  email: '',
  password: ''
})

/* FILTER SEARCH */
const filteredUsers = computed(() => {
  return users.value.filter(user =>
    user.name.toLowerCase().includes(
      search.value.toLowerCase()
    )
  )
})

/* OPEN MODAL */
const openModal = () => {

  showModal.value = true
  isEdit.value = false
  showPassword.value = false
  errorMessage.value = ''

  form.value = {
    name: '',
    email: '',
    password: ''
  }
}

/* CLOSE MODAL */
const closeModal = () => {

  showModal.value = false
  errorMessage.value = ''
  showPassword.value = false
}

/* SAVE USER */
const saveUser = () => {

  errorMessage.value = ''

  /* VALIDASI KOSONG */
  if (
    !form.value.name ||
    !form.value.email ||
    !form.value.password
  ) {
    errorMessage.value = 'Isi semua field'
    return
  }

  /* VALIDASI FORMAT EMAIL */
/* VALIDASI FORMAT EMAIL */
const allowedDomains = [
  'gmail.com',
  'yahoo.com',
  'outlook.com',
  'hotmail.com',
  'student.uns.ac.id'
]

const emailPattern =
/^[a-zA-Z0-9._%+-]+@([a-zA-Z0-9.-]+\.[A-Za-z]{2,})$/

if (!emailPattern.test(form.value.email)) {
  errorMessage.value =
    'Format email tidak valid.'
  return
}

/* AMBIL DOMAIN EMAIL */
const emailDomain =
  form.value.email.split('@')[1]

/* VALIDASI DOMAIN */
if (!allowedDomains.includes(emailDomain)) {
  errorMessage.value =
    'Domain email tidak diperbolehkan'
  return
}
  /* VALIDASI EMAIL DUPLIKAT */
  const emailExists = users.value.some((user, index) => {

    if (
      isEdit.value &&
      index === editIndex.value
    ) {
      return false
    }

    return user.email === form.value.email
  })

  if (emailExists) {
    errorMessage.value =
      'Email sudah digunakan'
    return
  }

  const password = form.value.password

  /* MINIMAL 8 */
  if (password.length < 8) {
    errorMessage.value =
      'Password minimal 8 karakter'
    return
  }



  /* HURUF KAPITAL */
  if (!/[A-Z]/.test(password)) {
    errorMessage.value =
      'Password harus memiliki huruf kapital'
    return
  }

  /* ANGKA */
  if (!/[0-9]/.test(password)) {
    errorMessage.value =
      'Password harus memiliki angka'
    return
  }

  /* SPESIAL KARAKTER */
  if (
    !/[!@#$%^&*(),.?":{}|<>_]/.test(password)
  ) {
    errorMessage.value =
      'Password harus memiliki karakter spesial'
    return
  }

  /* SAVE */
  if (isEdit.value) {

    users.value[editIndex.value] = {
      ...form.value
    }

  } else {

    users.value.push({
      ...form.value
    })

  }

  closeModal()
}

/* EDIT USER */
const editUser = (index) => {

  isEdit.value = true
  showModal.value = true
  showPassword.value = false
  errorMessage.value = ''

  editIndex.value = index

  form.value = {
    ...users.value[index]
  }
}

/* DELETE USER */
const deleteUser = (index) => {

  if (confirm('Hapus user ini?')) {
    users.value.splice(index, 1)
  }
}

/* ROUTER */
const logout = () => {
  localStorage.removeItem("isLogin")
  router.push("/")
}

const goDashboard = () => {
  router.push('/dashboard')
}

const goRiwayat = () => {
  router.push('/history')
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

/* BUTTON */
.edit-btn {
  background: #cfe5b6;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  margin-right: 8px;
  cursor: pointer;
}

.delete-btn {
  background: #efb1b1;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
}

/* MODAL */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal {
  background: white;
  width: 400px;
  padding: 25px;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.modal h2 {
  text-align: center;
}

.modal input,
.modal select {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #ccc;
}

/* PASSWORD */
.password-box {
  position: relative;
}

.password-box input {
  padding-right: 60px;
}

.show-btn {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 18px;
}

/* ERROR */
.error-text {
  background: #efb1b1;
  color: #7a0000;
  padding: 10px;
  border-radius: 10px;
  font-size: 14px;
  text-align: center;
}

/* ACTION */
.modal-action {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.save-btn {
  background: #ef0033;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  cursor: pointer;
}

.cancel-btn {
  background: #ccc;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  cursor: pointer;
}

/* RESPONSIVE */
@media (max-width: 900px) {

  .sidebar {
    width: 220px;
  }

  .table-top {
    flex-direction: column;
    gap: 15px;
  }

  .search-input {
    width: 100%;
  }

  table {
    font-size: 14px;
  }
}
</style>