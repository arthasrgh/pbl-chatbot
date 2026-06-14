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

              <button class="menu active">
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
            <div class="top-header">

              <h1>Riwayat Chat</h1>

            </div>

            <!-- CONTENT -->
      <!-- CONTENT -->
      <div class="content-area">

        <div class="chat-layout">

          <!-- SIDEBAR -->
          <div class="chat-sidebar">

            <div
              v-for="chat in chats"
              :key="chat.id"
              class="chat-user"
              :class="{ 'active-chat': activeNumber === chat.nomor }"
              @click="openChat(chat)"
            >

              <div class="avatar"></div>

              <div class="chat-info">

              <div
                v-if="activeNumber === chat.nomor"
                class="active-dot"
              ></div>

                <h4>
                  {{ chat.nomor }}
                </h4>

                <p>
                  Chat terbaru
                </p>

              </div>

            </div>

          </div>

          <!-- CHAT -->
          <div class="chat-box">

            <!-- HEADER -->
            <div class="chat-header">

              {{ selectedChat.nomor }}

            </div>

            <!-- BODY -->
            <div class="chat-body">
              <div
                v-for="(msg,index) in selectedChat.messages"
                :key="msg.id"
                class="message-row"
                :class="msg.sender === 'bot' ? 'right' : 'left'"
              >

            <div class="message-wrapper">

          <!-- BUBBLE -->
            <div
              class="message"
              :class="
                msg.sender === 'bot'
                ? 'user-message'
                : 'bot-message'
              "
            >
            {{ msg.pesan }}
          </div>

          <!-- TIME -->
          <div
            class="message-time"
            :class="
              msg.sender === 'bot'
              ? 'time-right'
              : 'time-left'
            "
          >
            {{ formatTime(msg.created_at) }}
          </div>

        </div>

      </div>

            </div>

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

      const getChats = async () => {
        try {
          const response = await api.get(`/chats`)

          chats.value = response.data

          if (!activeNumber.value && chats.value.length) {
            openChat(chats.value[0])
          }

        } catch (error) {
          console.log(error)
        }

      }

      const activeNumber = ref(null)
      const chats = ref([])

      const selectedChat = ref({
        nomor: '',
        messages: []
      })

      const openChat = async (chat) => {
        activeNumber.value = chat.nomor

        try {
          console.log('nomor dipilih:', chat.nomor)

          const response = await api.get(
            `/chats/${encodeURIComponent(chat.nomor)}`
          )

          console.log('isi chat:', response.data)

          selectedChat.value = {
            nomor: chat.nomor,
            messages: response.data
          }

        } catch (error) {
          console.log(error)
        }
      }

      const formatTime = (time) => {
        return new Date(time).toLocaleTimeString('id-ID', {
          hour: '2-digit',
          minute: '2-digit'
        })
      }

          onMounted(() => {

      getChats()

      /* setInterval(async () => {

        if(activeNumber.value){

          const response = await api.get(
            `/chats/${activeNumber.value}`
          )

          selectedChat.value.messages =
            response.data

        }

      }, 3000) */

    })
      </script>

      <style scoped>
      /* CONTENT */
      .content-area{
      margin-top:80px;
      height:calc(100vh - 80px);
      width:100%;
      overflow:hidden;
      }
      /* LAYOUT */
      .chat-layout{
      display:flex;
      height:100%;
      width:100%;
      }

      /* SIDEBAR CHAT */
      .chat-sidebar{
      width:320px;
      min-width:320px;
      background:#94a0b5;
      overflow-y:auto;
      border-right:1px solid #7f8aa0;
      }

      /* ITEM */
      .chat-user{
        display:flex;
        align-items:center;
        gap:12px;
        padding:14px;
        margin:6px 8px;
        border-radius:16px;
        cursor:pointer;
        transition:all .2s ease;
      }

      .chat-user{
        margin:8px;
        border-radius:20px
      }

      .chat-user:hover{
      background:#8a96aa;
      }

      .active-chat .chat-info h4{
        color:white;
        font-weight:bold;
      }

      .active-chat .chat-info p{
        color:#f3f4f6;
      }

      .active-dot{
          width:10px;
          height:10px;
          border-radius:50%;
          background:#22c55e;
          margin-left:auto;
      }

      .chat-user{
        transition:all .2s ease;
      }

      .active-chat{
        background:#ff4338;
        border-radius:16px;
      }

      /* AVATAR */
      .avatar{
      width:40px;
      height:40px;
      border-radius:50%;
      background:#d9d9d9;
      flex-shrink:0;
      }

      /* INFO */
      .chat-info{
      flex:1;
      }

      .chat-info h4{
      font-size:14px;
      color:white;
      margin-bottom:3px;
      }

      .chat-info p{
      font-size:12px;
      color:#e5e7eb;
      }

      /* CHAT BOX */
      .chat-box{
      flex:1;
      width:100%;
      display:flex;
      flex-direction:column;
      background:#ddd8d1;
      border-radius:20px;
      overflow:hidden;
      }

      /* HEADER */
      .chat-header{
      background:#ff4338;
      color:white;
      padding:18px 22px;
      font-size:22px;
      font-weight:bold;
      border-radius:0 0 16px 16px;
      margin-top:0px;
      }


      /* BODY */
      .chat-body{
      flex:1;
      margin:0;
      padding:10px 20px;
      overflow-y:auto;
      display:flex;
      flex-direction:column;
      gap:16px;
      width:100%;
      }

      /* MESSAGE */
      .message-row{
      display:flex;
      width:100%;
      }

      .left{
      justify-content:flex-start;
      }

      .right{
      justify-content:flex-end;
      }

      .message{
      max-width:320px;
      padding:10px 14px 6px;
      border-radius:20px;
      font-size:14px;
      line-height:1.5;
      word-break:break-word;
      }

      .message-wrapper{
      display:flex;
      flex-direction:column;
      max-width:320px;
      }

      .message-time{
      font-size:11px;
      color:#555;
      margin-top:4px;
      }

      .time-right{
      text-align:right;
      }

      .time-left{
      text-align:left;
      }

      /* BOT */
      .bot-message{
      background:white;
      color:#111827;
      }

      /* USER */
      .user-message{
      background:#c9f7a9;
      color:#111827;
      }
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
      .main-content{
      flex:1;
      display:flex;
      flex-direction:column;
      background:#8692a3;
      overflow:hidden;
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

      html,
      body,
      #app{
      height:100%;
      width:100%;
      overflow:hidden;
      }
      </style>