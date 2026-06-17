import { mount, flushPromises } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createRouter, createMemoryHistory } from 'vue-router'

import manajemen from '../views/UserManagement.vue' 

vi.mock('../services/api', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
  },
}))

import api from '../services/api'

describe('Integrasi Modul Manajemen Admin', () => {
  let router
  let wrapper

  beforeEach(() => {
    vi.clearAllMocks()
    vi.resetAllMocks()
    localStorage.clear()

    vi.spyOn(console, 'error').mockImplementation(() => {}) 

    vi.mocked(api.get).mockResolvedValue({ data: { data: [] } })
    global.confirm = vi.fn(() => true) 

    router = createRouter({
      history: createMemoryHistory(),
      routes: [
        { path: '/', component: { template: '<div>Home</div>' } },
        { path: '/dashboard', component: { template: '<div>Dashboard</div>' } },
        { path: '/history', component: { template: '<div>History</div>' } },
      ],
    })
  })

  it('Harus memuat daftar admin saat di-mount', async () => {
    // Arrange: Siapkan data palsu
    const mockAdmins = [{ id: 1, username: 'admin', email: 'admin@gmail.com' }]
    vi.mocked(api.get).mockResolvedValue({ data: { data: mockAdmins } })

    wrapper = mount(manajemen, { global: { plugins: [router] } })
    
    await flushPromises() 

    expect(wrapper.text()).toContain('admin@gmail.com')
  })

  it('Menunjukkan pesan error bila memuat admin gagal', async () => {
    vi.mocked(api.get).mockRejectedValue(new Error('Network Error'))

    wrapper = mount(manajemen, { global: { plugins: [router] } })
    await flushPromises()

    expect(wrapper.text()).toContain('Gagal memuat data admin')
  })

    it('Validasi password ketika menyimpan admin baru', async () => {
    wrapper = mount(manajemen, { global: { plugins: [router] } })
    await flushPromises()

    await wrapper.find('.add-btn').trigger('click')
    await flushPromises() // Tunggu modal ter-render

    await wrapper.find('input[placeholder="Masukkan username"]').setValue('Admin Baru')
    await wrapper.find('input[type="email"]').setValue('newadmin@email.com')
    await wrapper.find('input[type="password"]').setValue('lemah')

    await wrapper.find('form').trigger('submit')
    await flushPromises()
    
    expect(wrapper.text()).toContain('Password minimal 8 karakter')
    expect(api.post).not.toHaveBeenCalled()
  })

  it('Memanggil api.delete ketika menghapus admin', async () => {
    const mockAdmins = [{ id: 1, username: 'admin_to_delete' }]
    vi.mocked(api.get).mockResolvedValue({ data: { data: mockAdmins } })
    vi.mocked(api.delete).mockResolvedValue({ data: { success: true } })

    wrapper = mount(manajemen, { global: { plugins: [router] } })
    await flushPromises()

    const allButtons = wrapper.findAll('button')
    const btnDelete = allButtons.find(btn => btn.text().includes('Hapus'))
    await btnDelete.trigger('click')
    await flushPromises()

    expect(global.confirm).toHaveBeenCalled()
    expect(api.delete).toHaveBeenCalledWith('/admins/1')
    expect(wrapper.text()).toContain('Admin berhasil dihapus')
  })
})