import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Home from '../views/Home.vue'
import History from '../views/History.vue'
import Dashboard from '../views/Dashboard.vue'
import UserManagement from '../views/UserManagement.vue'
import ForgotPassword from '../views/ForgotPassword.vue'
import ResetPassword from '../views/ResetPassword.vue'


const routes = [
  { path: '/', component: Home },     
  { path: '/login', component: Login },
  { path: '/history', component: History },
  { path: '/dashboard', component: Dashboard },
  { path: '/manajemen', component: UserManagement},
  { path: '/forgot-password', component: ForgotPassword },
  {path:'/reset-password', component:ResetPassword}
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router