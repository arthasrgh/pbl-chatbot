<template>
  <div class="page">
    <!-- HEADER -->
    <header class="header">
      <div class="header-left">
        <img :src="logo" alt="logo" class="header-logo" />
        <span>Bakesbangpol Boyolali</span>
      </div>

      <button class="header-btn" @click="goLogin">Login</button>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="satu">
        <div class="hero-text-container">
        <h1>SELAMAT DATANG</h1>
        </div>
      </div>
    </section>

    <!-- CONTENT -->
    <section class="content">
      <div class="pahlawan">
      <h3>Bagi Operator, silakan Login.</h3>
      </div>
    </section>
  

    <!-- FOOTER -->
    <footer class="footer">
      <div class="footer-container">
        <div class="footer-left">
          <img :src="logo" alt="logo" class="footer-logo" />

          <div class="footer-text">
            <h3>Badan Kesatuan Bangsa dan Politik</h3>
            <p>Kabupaten Boyolali</p>

            <div class="contact-item">
              <span class="icon">📞</span>
              <span>(0276) 321087</span>
            </div>

            <div class="contact-item">
              <span class="icon">✉️</span>
              <span>bakesbangpol@boyolali.go.id</span>
            </div>
          </div>
        </div>

        <div class="footer-bottom">
          Copyright@2026 Boyolali. Developed by System Hub
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import logo from "../assets/Logo_Boyo2.png"
import { useRouter } from "vue-router"

const router = useRouter()

const goLogin = () => {
  router.push("/login")
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Alfa+Slab+One&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Rubik+Mono+One&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Rubik+Mono+One&display=swap');
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
}

/* PAGE */
.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #8d96a6;
}

/* HEADER (SAMA DENGAN LOGIN) */
.header {
  height: 55px;
  background: #252742;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 22px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
  font-size: 22px;
  font-weight: bold;
}

.header-logo {
  width: 30px;
  height: 30px;
  object-fit: contain;
}

.header-btn {
  border: none;
  background: white;
  color: black;
  padding: 8px 22px;
  border-radius: 20px;
  font-weight: bold;
  cursor: pointer;
  font-size: 14px;
}

/* HERO */
.hero-text-container {
  background: rgba(255, 255, 255, 0.92);
  padding: 5px 0;
  width: 100%;
}

.hero {
  height: 520px;
  background: url("../assets/KESBANGPOL2.jpg") no-repeat center center/cover;
  display: flex;
  justify-content: center;
  align-items: flex-end;
  padding: 60px 0;
  position: relative;
}

.hero::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
}

.overlay {
  position: relative;
  z-index: 1;
}

.hero-text-container h1 {
  font-family: "Rubik Mono One", monospace;
  font-weight: 350;
  font-style: normal;
  font-size: 50px;
  color: #252742;
  letter-spacing: 1px;
  text-align: center;
  margin: 0 0 0 0;
  padding: 5px 0;
}

.pahlawan h3 {
  font-family: "Arial", serif;
  font-weight: 1000;
  font-style: italic;
  font-size: 25px;
  color: #252742;
  letter-spacing: 1px;
  text-align: center;
  margin: 0px 0 0 0;
  padding: 0px 0;
}

.satu {
  padding: 40px 0px;
  position: relative;
  top: 60px;
  width: 100%;
  background: #252742;
  opacity: 0.65;
}

/* CONTENT */
.content {
  flex: 1;
  background: #5e5f6f;
  min-height: 160px;
  padding: 15px 0;
  margin-top: 0px;
}

/* FOOTER (SAMA LOGIN) */
.footer {
  background: #f3f3f3;
  padding: 35px 55px 15px;
  border-top: 1px solid #ddd;
}

.footer-container {
  display: flex;
  flex-direction: column;
}

.footer-left {
  display: flex;
  align-items: flex-start;
  gap: 18px;
}

.footer-logo {
  width: 34px;
  height: 34px;
  object-fit: contain;
  margin-top: 4px;
}

.footer-text h3 {
  margin: 0;
  font-size: 22px;
  font-weight: 700;
  color: #111;
}

.footer-text p {
  margin: 2px 0 18px;
  font-size: 14px;
  color: #444;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
  font-size: 14px;
  color: #222;
}

.icon {
  width: 18px;
  text-align: center;
}

.footer-bottom {
  margin-top: 35px;
  font-size: 13px;
  color: #333;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .header-left {
    font-size: 18px;
  }

  .overlay h1 {
    font-size: 22px;
    padding: 14px 24px;
  }

  .footer {
    padding: 25px;
  }

  .footer-text h3 {
    font-size: 18px;
  }
}
</style>