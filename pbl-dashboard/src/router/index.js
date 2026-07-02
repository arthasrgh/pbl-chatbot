import { createRouter, createWebHistory } from "vue-router"

import Login from "../views/Login.vue"
import Dashboard from "../views/Dashboard.vue"
import History from "../views/History.vue"
import HotLeads from "../views/HotLeads.vue"
import Manajemen from "../views/Manajemen.vue"
import UserBot from "../views/UserBot.vue"
import AiUsage from "../views/AiUsage.vue"
// 1. Import komponen baru
import ResetPassword from "../views/ResetPassword.vue"

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            redirect: "/login"
        },
        {
            path: "/login",
            component: Login
        },
        // 2. Daftarkan path reset-password (akses publik tanpa requiresAuth)
        {
            path: "/reset-password",
            name: "reset-password",
            component: ResetPassword
        },
        {
            path: "/dashboard",
            component: Dashboard,
            meta: { requiresAuth: true }
        },
        {
            path: "/users-bot",
            component: UserBot,
            meta: { requiresAuth: true }
        },
        {
            path: "/history",
            component: History,
            meta: { requiresAuth: true }
        },
        {
            path: "/hot-leads",
            component: HotLeads,
            meta: { requiresAuth: true }
        },
        {
            path: "/ai-usage",
            component: AiUsage,
            meta: {
            requiresAuth: true,
            role: "admin"
            }
        },
        {
            path: "/manajemen",
            component: Manajemen,
            meta: { requiresAuth: true, role: "admin" }
        },
    ]
})

router.beforeEach((to, from, next) => {
    const isLogin = localStorage.getItem("isLogin")
    const user = JSON.parse(localStorage.getItem("user") || "{}")

    if (to.meta.requiresAuth && !isLogin) {
        return next("/login")
    }

    // Jika user sudah login lalu mencoba membuka '/login', lempar ke dashboard.
    // Halaman '/reset-password' aman diakses karena tidak masuk kondisi di bawah ini.
    if (to.path === "/login" && isLogin) {
        return next("/dashboard")
    }

    if (to.meta.role && user.role !== to.meta.role) {
        alert("Anda tidak memiliki hak akses.")
        return next("/dashboard")
    }

    next()
})

export default router