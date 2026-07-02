<template>
  <div class="dashboard">

    <!-- Sidebar -->
    <Sidebar active="history" />

    <!-- Main -->
    <main class="main-content">

      <Topbar
        title="Riwayat Chat"
        subtitle="Monitoring seluruh percakapan chatbot"
      />

      <!-- Toolbar -->
      <div class="toolbar">

        <input
          v-model="search"
          class="search-input"
          type="text"
          placeholder="Cari nomor WhatsApp..."
        />

        <button
          class="refresh-btn"
          @click="loadChats"
        >
          Refresh
        </button>

      </div>

      <!-- Table -->
      <div class="table-card">

        <table>

          <thead>

            <tr>

              <th>No</th>

              <th>Nomor</th>

              <th>Pesan</th>

              <th>Sender</th>

              <th>Waktu</th>

              <th>Aksi</th>

            </tr>

          </thead>

          <tbody>

            <tr
              v-for="(chat,index) in filteredChats"
              :key="chat.id"
            >

              <td>{{ index + 1 }}</td>

              <td>{{ chat.nomor }}</td>

              <td class="message">

                {{ chat.pesan }}

              </td>

              <td>

                <span
                  class="badge"
                  :class="chat.sender"
                >

                  {{ chat.sender }}

                </span>

              </td>

              <td>

                {{ formatTime(chat.created_at) }}

              </td>

              <td>

                <button
                  class="detail-btn"
                  @click="openChat(chat.nomor)"
                >

                  Detail

                </button>

              </td>

            </tr>

            <tr v-if="filteredChats.length==0">

              <td
                colspan="6"
                class="empty"
              >

                Tidak ada data.

              </td>

            </tr>

          </tbody>

        </table>

      </div>

      <!-- ===================== -->
      <!-- MODAL CHAT -->
      <!-- ===================== -->

      <div
        v-if="showModal"
        class="modal-overlay"
        @click="showModal=false"
      >

        <div
          class="chat-modal"
          @click.stop
        >

          <!-- Header -->

          <div class="modal-header">

            <div>

              <h3>

                {{ selectedNumber }}

              </h3>

              <small>

                Riwayat Percakapan

              </small>

            </div>

            <button
              class="close-btn"
              @click="showModal=false"
            >

              ✕

            </button>

          </div>

          <!-- Body -->

          <div class="chat-body">

            <div
              v-for="msg in conversation"
              :key="msg.id"
              :class="[
                'chat-bubble',
                msg.sender=='user'
                ?'user-chat'
                :'bot-chat'
              ]"
            >

              <p>

                {{ msg.pesan }}

              </p>

              <span>

                {{ formatTime(msg.created_at) }}

              </span>

            </div>

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
// STATE
// ======================================

const chats = ref([])

const conversation = ref([])

const showModal = ref(false)

const selectedNumber = ref("")

const search = ref("")

// ======================================
// LOAD ALL CHAT
// ======================================

const loadChats = async()=>{

    try{

        const res = await api.get("/chats")

        chats.value = res.data

    }

    catch(err){

        console.error(err)

    }

}

// ======================================
// FILTER
// ======================================

const filteredChats = computed(()=>{

    if(search.value===""){

        return chats.value

    }

    return chats.value.filter(chat=>{

        return (

            chat.nomor
            .toLowerCase()
            .includes(search.value.toLowerCase())

            ||

            chat.pesan
            .toLowerCase()
            .includes(search.value.toLowerCase())

        )

    })

})

// ======================================
// DETAIL CHAT
// ======================================

const openChat = async(nomor)=>{

    try{

        const res = await api.get(

            "/chats/"+encodeURIComponent(nomor)

        )

        conversation.value = res.data

        selectedNumber.value = nomor

        showModal.value = true

    }

    catch(err){

        console.error(err)

    }

}

// ======================================
// FORMAT TANGGAL
// ======================================

const formatTime=(datetime)=>{

    if(!datetime){

        return "-"

    }

    return new Date(datetime)

    .toLocaleString("id-ID",{

        day:"2-digit",

        month:"short",

        year:"numeric",

        hour:"2-digit",

        minute:"2-digit"

    })

}

// ======================================
// AUTO LOAD
// ======================================

onMounted(()=>{

    loadChats()

})

</script>

<style scoped>

/* =========================================
   LAYOUT
========================================= */

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

/* =========================================
   TOOLBAR
========================================= */

.toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    margin-bottom:24px;
}

.search-input{
    flex:1;
    height:48px;
    padding:0 18px;
    border:1px solid #dcdfe6;
    border-radius:12px;
    outline:none;
    font-size:14px;
    transition:.3s;
}

.search-input:focus{
    border-color:#b71c1c;
    box-shadow:0 0 0 3px rgba(183,28,28,.12);
}

.refresh-btn{
    border:none;
    background:#b71c1c;
    color:#fff;
    padding:12px 22px;
    border-radius:12px;
    cursor:pointer;
    font-weight:600;
    transition:.3s;
}

.refresh-btn:hover{
    background:#8b1111;
}

/* =========================================
   TABLE
========================================= */

.table-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#b71c1c;
    color:white;
}

thead th{
    padding:18px;
    font-size:14px;
    text-align:left;
}

tbody td{
    padding:16px 18px;
    border-bottom:1px solid #eef2f7;
    font-size:14px;
}

tbody tr{
    transition:.25s;
}

tbody tr:hover{
    background:#fff5f5;
}

.message{
    max-width:350px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;
}

.empty{
    text-align:center;
    padding:40px;
    color:#999;
}

/* =========================================
   BADGE
========================================= */

.badge{
    display:inline-block;
    padding:6px 12px;
    border-radius:30px;
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
    color:#1d4ed8;
}

/* =========================================
   BUTTON
========================================= */

.detail-btn{
    border:none;
    background:#2563eb;
    color:white;
    padding:8px 16px;
    border-radius:8px;
    cursor:pointer;
    transition:.25s;
}

.detail-btn:hover{
    background:#1d4ed8;
}

/* =========================================
   MODAL
========================================= */

.modal-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.45);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:999;
}

.chat-modal{
    width:720px;
    max-width:95%;
    height:650px;
    background:white;
    border-radius:18px;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    animation:fadeUp .3s;
}

/* =========================================
   HEADER
========================================= */

.modal-header{
    background:#b71c1c;
    color:white;
    padding:18px 22px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.modal-header h3{
    margin:0;
    font-size:18px;
}

.modal-header small{
    opacity:.8;
}

.close-btn{
    border:none;
    background:none;
    color:white;
    font-size:22px;
    cursor:pointer;
}

/* =========================================
   CHAT BODY
========================================= */

.chat-body{
    flex:1;
    overflow-y:auto;
    padding:20px;
    background:#ece5dd;
    display:flex;
    flex-direction:column;
    gap:16px;
}

/* Bubble */

.chat-bubble{
    max-width:75%;
    padding:12px 16px;
    border-radius:16px;
    display:flex;
    flex-direction:column;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
}

.chat-bubble p{
    margin:0;
    line-height:1.5;
    word-break:break-word;
}

.chat-bubble span{
    margin-top:8px;
    font-size:11px;
    color:#666;
    text-align:right;
}

/* User */

.user-chat{
    align-self:flex-end;
    background:#dcf8c6;
}

/* Bot */

.bot-chat{
    align-self:flex-start;
    background:white;
}

/* =========================================
   SCROLLBAR
========================================= */

.chat-body::-webkit-scrollbar{
    width:8px;
}

.chat-body::-webkit-scrollbar-thumb{
    background:#cbd5e1;
    border-radius:20px;
}

/* =========================================
   RESPONSIVE
========================================= */

@media(max-width:900px){

.toolbar{
    flex-direction:column;
    align-items:stretch;
}

.table-card{
    overflow-x:auto;
}

table{
    min-width:850px;
}

.chat-modal{
    width:95%;
    height:90vh;
}

.chat-bubble{
    max-width:90%;
}

}

/* =========================================
   ANIMATION
========================================= */

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

</style>