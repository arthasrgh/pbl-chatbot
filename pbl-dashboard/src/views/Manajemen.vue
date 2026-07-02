<template>
  <div class="dashboard">

    <Sidebar active="admin" />

    <main class="main-content">

      <Topbar
        title="Manajemen Admin"
        subtitle="Kelola akun admin dashboard chatbot Bangkesbangpol Boyolali"
      />

      <div class="toolbar">
        <button
          class="add-btn"
          @click="openModal"
        >
          + Tambah Admin
        </button>

        <input
          v-model="search"
          class="search-input"
          type="text"
          placeholder="Cari username admin..."
        />
      </div>

      <div v-if="errorMessage && !showModal" class="alert alert-error">
        {{ errorMessage }}
      </div>
      <div v-if="successMessage && !showModal" class="alert alert-success">
        {{ successMessage }}
      </div>

      <div class="table-card">
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
            <tr v-if="filteredAdmins.length === 0">
              <td colspan="4" class="empty">
                Tidak ada data admin.
              </td>
            </tr>

            <tr
              v-for="(admin, index) in filteredAdmins"
              :key="admin.id"
            >
              <td>{{ index + 1 }}</td>
              <td><strong>{{ admin.username }}</strong><br><small style="color:#6b7280">ID: {{ admin.id }}</small></td>
              <td>{{ admin.email || '-' }}</td>
              <td>
                <div class="action-group">
                  <button
                    class="edit-btn"
                    @click="editAdmin(admin)"
                  >
                    Edit
                  </button>
                  <button
                    class="delete-btn"
                    @click="deleteAdmin(admin)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        v-if="showModal"
        class="modal-overlay"
      >
        <div class="modal">
          <h2>{{ isEdit ? 'Edit Admin' : 'Tambah Admin' }}</h2>

          <div v-if="errorMessage" class="alert alert-error" style="margin-bottom: 15px;">
            {{ errorMessage }}
          </div>

          <div class="form-group">
            <label>Username</label>
            <input
              v-model="form.username"
              type="text"
              placeholder="Masukkan username"
              :disabled="isSubmitting"
            >
          </div>

          <div class="form-group">
            <label>Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="contoh@email.com"
              :disabled="isSubmitting"
            >
          </div>

          <div class="form-group">
            <label>Password</label>
            <div style="position: relative; display: flex; align-items: center;">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                :placeholder="isEdit ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 8 karakter'"
                :disabled="isSubmitting"
                style="padding-right: 80px;"
              >
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                style="position: absolute; right: 10px; background: #e5e7eb; border: none; padding: 4px 8px; border-radius: 6px; font-size: 11px; cursor: pointer;"
              >
                {{ showPassword ? 'Sembunyikan' : 'Lihat' }}
              </button>
            </div>
            <small v-if="!isEdit" style="color: #6b7280; font-size: 12px; display: block; margin-top: 5px;">
              Password minimal 8 karakter (Huruf besar, angka, & simbol).
            </small>
          </div>

          <div class="modal-footer">
            <button
              class="cancel-btn"
              @click="closeModal"
              :disabled="isSubmitting"
            >
              Batal
            </button>
            <button
              class="save-btn"
              @click="saveAdmin"
              :disabled="isSubmitting"
            >
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>

        </div>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import Sidebar from "../components/Sidebar.vue"
import Topbar from "../components/Topbar.vue"
import api from "../services/api"

// ======================================
// STATE (Mengadopsi Logika Lengkap Kode 2)
// ======================================
const search = ref("")
const admins = ref([])

const showModal = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const showPassword = ref(false)
const errorMessage = ref("")
const successMessage = ref("")
const isSubmitting = ref(false)

const form = ref({
  username: "",
  email: "",
  password: ""
})

// ======================================
// LOAD DATA
// ======================================
const loadAdmins = async () => {
  try {
    const res = await api.get("/admins")
    admins.value = res.data.data || res.data || []
  } catch (err) {
    console.error(err)
    errorMessage.value = "Gagal memuat data admin"
  }
}

// ======================================
// FILTER (Mencari berdasarkan username seperti Kode 2)
// ======================================
const filteredAdmins = computed(() => {
  return admins.value.filter(admin =>
    admin.username?.toLowerCase().includes(search.value.toLowerCase())
  )
})

// ======================================
// MODAL ACTIONS
// ======================================
const openModal = () => {
  showModal.value = true
  isEdit.value = false
  editId.value = null
  showPassword.value = false
  errorMessage.value = ""
  successMessage.value = ""
  form.value = { username: "", email: "", password: "" }
}

const closeModal = () => {
  showModal.value = false
  errorMessage.value = ""
  successMessage.value = ""
  showPassword.value = false
  isSubmitting.value = false
}

const editAdmin = (admin) => {
  isEdit.value = true
  showModal.value = true
  showPassword.value = false
  errorMessage.value = ""
  successMessage.value = ""
  editId.value = admin.id

  form.value = {
    username: admin.username || "",
    email: admin.email || "",
    password: ""
  }
}

// ======================================
// SIMPAN DENGAN VALIDASI KETAT (Kode 2)
// ======================================
const saveAdmin = async () => {
  errorMessage.value = ""
  successMessage.value = ""

  if (!form.value.username || (!isEdit.value && !form.value.password)) {
    errorMessage.value = "Isi semua field yang wajib."
    return
  }

  // Validasi Password Kompleks dari Kode 2
  if (form.value.password) {
    const pass = form.value.password
    if (pass.length < 8) { errorMessage.value = "Password minimal 8 karakter"; return }
    if (!/[A-Z]/.test(pass)) { errorMessage.value = "Password harus memiliki huruf kapital"; return }
    if (!/[0-9]/.test(pass)) { errorMessage.value = "Password harus memiliki angka"; return }
    if (!/[!@#$%^&*(),.?\":{}|<>_\-+=/\\[\]]/.test(pass)) { errorMessage.value = "Password harus memiliki karakter spesial"; return }
  }

  isSubmitting.value = true

  try {
    if (isEdit.value) {
      await api.put(`/admins/${editId.value}`, form.value)
      successMessage.value = "Admin berhasil diupdate"
    } else {
      await api.post("/admins", form.value)
      successMessage.value = "Admin berhasil ditambahkan"
    }

    await loadAdmins()
    setTimeout(() => {
      closeModal()
    }, 1200)
  } catch (err) {
    if (err.response?.data?.message) {
      errorMessage.value = err.response.data.message
    } else {
      errorMessage.value = "Terjadi kesalahan pada server"
    }
    console.error(err)
  } finally {
    isSubmitting.value = false
  }
}

// ======================================
// HAPUS
// ======================================
const deleteAdmin = async (admin) => {
  if (!confirm(`Hapus admin "${admin.username}"?`)) return

  try {
    await api.delete(`/admins/${admin.id}`)
    successMessage.value = "Admin berhasil dihapus"
    await loadAdmins()
    setTimeout(() => { successMessage.value = "" }, 2000)
  } catch (err) {
    errorMessage.value = err.response?.data?.message || "Gagal menghapus admin"
  }
}

onMounted(() => {
  loadAdmins()
})
</script>

<style scoped>
/* ==========================================================================
   STYLE MURNI DARI KODE 1 (Merah Maroon #b71c1c & Clean Layout)
   ========================================================================== */
.dashboard{ display:flex; min-height:100vh; background:#f4f6fb; font-family: 'Poppins', sans-serif;}
.main-content{ flex:1; padding:30px; overflow-y:auto; }
.toolbar{ display:flex; justify-content:space-between; align-items:center; gap:20px; margin-bottom:24px; }
.search-input{ flex:1; height:48px; border:1px solid #d1d5db; border-radius:12px; padding:0 18px; outline:none; font-size:14px; transition:.3s; }
.search-input:focus{ border-color:#b71c1c; box-shadow:0 0 0 3px rgba(183,28,28,.12); }
.add-btn{ border:none; background:#b71c1c; color:white; padding:12px 22px; border-radius:12px; cursor:pointer; font-weight:600; transition:.3s; }
.add-btn:hover{ background:#991b1b; }

/* TABLE */
.table-card{ background:white; border-radius:18px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,.06); }
table{ width:100%; border-collapse:collapse; }
thead{ background:#b71c1c; color:white; }
thead th{ padding:16px; text-align:left; font-size:14px; }
tbody td{ padding:16px; border-bottom:1px solid #eef2f7; font-size:14px; }
tbody tr:hover{ background:#fef2f2; }
.empty{ text-align:center; padding:40px; color:#999; }

/* ACTION BUTTONS */
.action-group{ display:flex; gap:10px; }
.edit-btn, .delete-btn{ border:none; padding:8px 14px; border-radius:8px; cursor:pointer; transition:.25s; color:white; }
.edit-btn{ background:#2563eb; }
.edit-btn:hover{ background:#1d4ed8; }
.delete-btn{ background:#ef4444; }
.delete-btn:hover{ background:#dc2626; }

/* ALERT (Tambahan Penyelarasan untuk pesan sukses/gagal dari Kode 2) */
.alert { padding: 14px 16px; border-radius: 12px; margin-bottom: 18px; font-size: 14px; font-weight: 500; }
.alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }

/* MODAL */
.modal-overlay{ position:fixed; inset:0; background:rgba(0,0,0,.45); display:flex; justify-content:center; align-items:center; z-index:999; }
.modal{ width:480px; max-width:95%; background:white; border-radius:18px; padding:28px; animation:fadeUp .3s ease; }
.modal h2{ margin-bottom:24px; color:#111827; }
.form-group{ margin-bottom:18px; }
.form-group label{ display:block; margin-bottom:8px; color:#374151; font-weight:600; }
.form-group input{ width:100%; height:46px; border:1px solid #d1d5db; border-radius:10px; padding:0 14px; outline:none; font-size:14px; transition:.3s; }
.form-group input:focus{ border-color:#b71c1c; }
.form-group input:disabled { background: #f3f4f6; cursor: not-allowed; }

.modal-footer{ display:flex; justify-content:flex-end; gap:12px; margin-top:24px; }
.cancel-btn{ border:none; background:#9ca3af; color:white; padding:10px 20px; border-radius:10px; cursor:pointer; }
.save-btn{ border:none; background:#b71c1c; color:white; padding:10px 20px; border-radius:10px; cursor:pointer; }
.cancel-btn:disabled, .save-btn:disabled { opacity: 0.6; cursor: not-allowed; }

@keyframes fadeUp{ from{ opacity:0; transform:translateY(20px); } to{ opacity:1; transform:translateY(0); } }
@media(max-width:900px){ .toolbar{ flex-direction:column; align-items:stretch; } .table-card{ overflow-x:auto; } table{ min-width:700px; } .modal{ width:95%; } }
</style>