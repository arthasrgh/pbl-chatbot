import { mount } from '@vue/test-utils'
import { flushPromises } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createRouter, createMemoryHistory } from 'vue-router'
import Login from '../views/Login.vue'
import ForgotPassword from '../views/ForgotPassword.vue'

vi.mock('../services/api', () => ({
  default: {
    post: vi.fn(),
  },
}))

import api from '../services/api'

describe('Integrasi Antara Modul Login dan Dashboard', () => {
  let router
  let wrapper

  beforeEach(() => {
    vi.clearAllMocks()
    localStorage.clear()
    vi.spyOn(console, 'error').mockImplementation(() => {})

    router = createRouter({
      history: createMemoryHistory(),
      routes: [{ path: '/', component: { template: '<div>Home</div>' } },
        { path: '/forgot-password', component: { template: '<div>Forgot Password</div>' } },
        { path: '/dashboard', component: { template: '<div>Dashboard</div>' } },
      ],
    })

    wrapper = mount(Login, {
      global: {
        plugins: [router],
      },
    })
  })

  it('Menunjukkan pesan error ketika email dan password kosong', async () => {
    await wrapper.find('.login-btn').trigger('click')

    expect(wrapper.text()).toContain('Email dan password wajib diisi')
    expect(api.post).not.toHaveBeenCalled()
  })

  it('Berhasil login, set localStorage, dan redirect ke dashboard', async () => {
    vi.mocked(api.post).mockResolvedValue({
      data: { success: true }
    })

    await wrapper.find('input[type="email"]').setValue('test@example.com')
    await wrapper.find('input[type="password"]').setValue('password123')
    await wrapper.find('.login-btn').trigger('click')

    await vi.waitFor(() => {
      expect(api.post).toHaveBeenCalledWith('/login', {
        email: 'test@example.com',
        password: 'password123'
      })

      expect(localStorage.getItem('isLogin')).toBe('true')

      expect(router.currentRoute.value.path).toBe('/dashboard')
      
      expect(wrapper.text()).not.toContain('Email dan password wajib diisi')
    })
  })

  it('Menunjukkan pesan error secara spesifik ketika API mengembalikan error', async () => {
    const mockError = {
      response: {
        data: { message: 'Email atau password salah' }
      }
    }
    vi.mocked(api.post).mockRejectedValue(mockError)

    await wrapper.find('input[type="email"]').setValue('test@example.com')
    await wrapper.find('input[type="password"]').setValue('wrongpassword')
    await wrapper.find('.login-btn').trigger('click')

    await vi.waitFor(() => {
      expect(wrapper.text()).toContain('Email atau password salah')
      expect(router.currentRoute.value.path).not.toBe('/dashboard')
      expect(localStorage.getItem('isLogin')).toBeNull()
    })
  })

  it('Menunjukkan pesan error ketika server tidak dapat dijangkau', async () => {
    const mockNetworkError = {
      message: 'Network Error'
    }
    vi.mocked(api.post).mockRejectedValue(mockNetworkError)

    await wrapper.find('input[type="email"]').setValue('test@example.com')
    await wrapper.find('input[type="password"]').setValue('password123')
    await wrapper.find('.login-btn').trigger('click')

    await vi.waitFor(() => {
      expect(wrapper.text()).toContain('Server backend tidak terhubung')
    })
  })
})

  describe('Integrasi Modul Login dan Modul Lupa Password', () => {
  let router
  let wrapper

  beforeEach(() => {
    vi.clearAllMocks()
    localStorage.clear()
    vi.spyOn(console, 'error').mockImplementation(() => {})

    router = createRouter({
      history: createMemoryHistory(),
      routes: [{ path: '/', component: { template: '<div>Home</div>' } },
        { path: '/forgot-password', component: { template: '<div>Forgot Password</div>' } },
        { path: '/dashboard', component: { template: '<div>Dashboard</div>' } },
      ],
    })

    wrapper = mount(Login, {
      global: {
        plugins: [router],
      },
    })
  })

  it('Navigasi ke halaman Lupa Password saat link "Lupa Password? Klik di Sini" diklik', async () => {
    await wrapper.find('.forgot').trigger('click')
    await flushPromises() 

    expect(router.currentRoute.value.path).toBe('/forgot-password')
  })
})
