import { mount } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createRouter, createMemoryHistory } from 'vue-router'
import ForgotPassword from '../views/ForgotPassword.vue'

vi.mock('../services/api', () => ({
  default: {
    post: vi.fn(),
  },
}))

import api from '../services/api.js'

describe('Integrasi Komponen dalam Modul Lupa Password', () => {
  let router
  let wrapper

  beforeEach(() => {
    vi.clearAllMocks()
    localStorage.clear()
    vi.spyOn(console, 'error').mockImplementation(() => {})

    router = createRouter({
      history: createMemoryHistory(),
      routes: [
        { path: '/', component: { template: '<div>Home</div>' } },
        { path: '/forgot-password', component: { template: '<div>Forgot Password</div>' } },
      ],
    })

  wrapper = mount(ForgotPassword, {
      global: {
        plugins: [router],
      },
    })
  })

    it('Menunjukkan pesan error ketika email kosong saat mengisi data reset password', async () => {
    const mockError = {
      response: {
        data: {
          message: 'The email field is required.'
        }
      }
    }
    vi.mocked(api.post).mockRejectedValue(mockError)

    await wrapper.find('input[type="email"]').setValue('')
    await wrapper.find('.reset-btn').trigger('click')
    await wrapper.vm.$nextTick()

    expect(api.post).toHaveBeenCalled()
    expect(wrapper.text()).toContain('The email field is required.')
  })

    it('Berhasil mengirim link dan menampilkan pesan sukses ketika field email diisi', async () => {
    vi.mocked(api.post).mockResolvedValue({ 
      data: { message: 'Link reset berhasil dikirim' } 
    })

    await wrapper.find('input[type="email"]').setValue('newadmin@email.com')
    
    await wrapper.find('.reset-btn').trigger('click')
    await wrapper.vm.$nextTick()

    expect(api.post).toHaveBeenCalledWith('/forgot-password', { 
      email: 'newadmin@email.com' 
    })
    expect(wrapper.text()).toContain('Link reset berhasil dikirim')
  })
})
  