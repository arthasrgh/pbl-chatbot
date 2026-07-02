<template>
  <header class="topbar">

    <div>

      <h1>{{ title }}</h1>

      <p>
        {{ subtitle }}
      </p>

    </div>

    <div class="topbar-right">

      <div class="date-box">

        📅 {{ currentDate }}

      </div>

      <div class="admin-box">

        <div class="avatar">

          {{ initial }}

        </div>

        <div>

          <h4>{{ adminNama }}</h4>

          <span>{{ adminEmail }}</span>

          <small>{{ adminRole }}</small>

        </div>

      </div>

    </div>

  </header>
</template>

<script setup>

import {
  computed,
  ref,
  onMounted,
  onBeforeUnmount
} from "vue"

const props = defineProps({

  title: String,

  subtitle: String

})

const user = JSON.parse(

  localStorage.getItem("user") || "{}"

)

const adminNama = computed(() => {

  return user.nama || "Administrator"

})

const adminEmail = computed(() => {

  return user.email || "-"

})

const adminRole = computed(() => {

  return user.role === "admin"

    ? "Administrator"

    : "Customer Service"

})

const initial = computed(() => {

  return adminNama.value
    .charAt(0)
    .toUpperCase()

})

const currentDate = ref("")

let timer = null

const updateDate = () => {

  currentDate.value =

    new Date().toLocaleString(

      "id-ID",

      {

        weekday: "long",

        day: "numeric",

        month: "long",

        year: "numeric",

        hour: "2-digit",

        minute: "2-digit"

      }

    )

}

onMounted(() => {

  updateDate()

  timer = setInterval(

    updateDate,

    60000

  )

})

onBeforeUnmount(() => {

  clearInterval(timer)

})

</script>

<style scoped>

.topbar{

  display:flex;

  justify-content:space-between;

  align-items:center;

  margin-bottom:30px;

}

.topbar h1{

  font-size:32px;

  font-weight:700;

  color:#991b1b;

  margin-bottom:5px;

}

.topbar p{

  color:#6b7280;

  font-size:15px;

}

.topbar-right{

  display:flex;

  align-items:center;

  gap:18px;

}

.date-box{

  background:white;

  padding:12px 18px;

  border-radius:14px;

  box-shadow:0 8px 20px rgba(0,0,0,.06);

  font-size:14px;

  color:#374151;

}

.admin-box{

  display:flex;

  align-items:center;

  gap:12px;

  background:white;

  padding:10px 18px;

  border-radius:16px;

  box-shadow:0 8px 20px rgba(0,0,0,.06);

}

.avatar{

  width:48px;

  height:48px;

  border-radius:50%;

  background:#b71c1c;

  display:flex;

  justify-content:center;

  align-items:center;

  font-size:18px;

  font-weight:700;

  color:white;

}

.admin-box h4{

  font-size:15px;

  margin-bottom:2px;

  color:#111827;

}

.admin-box span{

  display:block;

  font-size:12px;

  color:#6b7280;

}

.admin-box small{

  display:block;

  margin-top:2px;

  font-size:11px;

  color:#b91c1c;

  font-weight:600;

}

@media(max-width:900px){

  .topbar{

    flex-direction:column;

    align-items:flex-start;

    gap:20px;

  }

  .topbar-right{

    width:100%;

    flex-direction:column;

    align-items:flex-start;

  }

  .date-box,

  .admin-box{

    width:100%;

  }

}

</style>