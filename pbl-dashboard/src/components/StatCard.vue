<template>
  <div
    class="stat-card"
    :class="color"
    @click="$emit('click')"
  >
    <div class="stat-content">

      <small>{{ title }}</small>

      <h2>{{ value }}</h2>

    </div>

    <div class="stat-icon">

      <component
        :is="icon"
        :size="38"
      />

    </div>

  </div>
</template>

<script setup>

defineProps({

    title:String,

    value:[String,Number],

    icon:Object,

    color:String

})

defineEmits(["click"])

</script>

<style scoped>

/* ==========================================
   SIDEBAR
========================================== */

.sidebar{
    width:270px;
    height:100vh;
    background:#ffffff;
    border-right:1px solid #e5e7eb;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    padding:24px;
    position:sticky;
    top:0;
    transition:.3s;
}

/* ==========================================
   LOGO
========================================== */

.logo-area{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:40px;
}

.logo{
    width:55px;
    height:55px;
    object-fit:contain;
}

.logo-area h2{
    font-size:18px;
    font-weight:700;
    color:#b71c1c;
    margin-bottom:2px;
}

.logo-area p{
    font-size:13px;
    color:#6b7280;
}

/* ==========================================
   MENU
========================================== */

.menu{
    display:flex;
    flex-direction:column;
    gap:10px;
}

/* ==========================================
   MENU ITEM
========================================== */

.menu-item{
    width:100%;
    display:flex;
    align-items:center;
    gap:14px;

    padding:15px 16px;

    border:none;
    outline:none;

    background:transparent;

    border-radius:14px;

    cursor:pointer;

    font-size:15px;

    font-weight:600;

    color:#374151;

    transition:all .25s ease;
}

.menu-item svg{

    flex-shrink:0;

}

/* Hover */

.menu-item:hover{

    background:#fee2e2;

    color:#b71c1c;

    transform:translateX(5px);

}

/* Active */

.menu-item.active{

    background:#b71c1c;

    color:white;

    box-shadow:0 10px 25px rgba(183,28,28,.25);

}

/* Active Icon */

.menu-item.active svg{

    color:white;

}

/* ==========================================
   FOOTER
========================================== */

.sidebar-footer{

    border-top:1px solid #e5e7eb;

    padding-top:22px;

}

/* ==========================================
   PROFILE
========================================== */

.profile{

    display:flex;

    align-items:center;

    gap:12px;

    margin-bottom:18px;

}

.avatar{

    width:48px;

    height:48px;

    border-radius:50%;

    background:#b71c1c;

    display:flex;

    justify-content:center;

    align-items:center;

    color:white;

    font-size:18px;

    font-weight:700;

}

.profile h4{

    font-size:15px;

    font-weight:700;

    color:#111827;

    margin-bottom:2px;

}

.profile span{

    font-size:12px;

    color:#6b7280;

    word-break:break-all;

}

/* ==========================================
   LOGOUT BUTTON
========================================== */

.logout-btn{

    width:100%;

    display:flex;

    justify-content:center;

    align-items:center;

    gap:10px;

    padding:14px;

    border:none;

    border-radius:14px;

    background:#dc2626;

    color:white;

    font-size:15px;

    font-weight:600;

    cursor:pointer;

    transition:.3s;

}

.logout-btn:hover{

    background:#991b1b;

    transform:translateY(-2px);

    box-shadow:0 10px 20px rgba(220,38,38,.25);

}

.logout-btn svg{

    flex-shrink:0;

}

/* ==========================================
   SCROLLBAR
========================================== */

.sidebar::-webkit-scrollbar{

    width:6px;

}

.sidebar::-webkit-scrollbar-thumb{

    background:#d1d5db;

    border-radius:20px;

}

/* ==========================================
   RESPONSIVE
========================================== */

@media(max-width:900px){

.sidebar{

    position:relative;

    width:100%;

    height:auto;

    border-right:none;

    border-bottom:1px solid #e5e7eb;

}

.logo-area{

    justify-content:center;

}

.menu{

    margin-top:15px;

}

}

</style>