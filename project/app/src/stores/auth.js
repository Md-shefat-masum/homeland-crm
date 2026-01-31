import { defineStore } from 'pinia'
import { api } from 'boot/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token && state.isAuthenticated,
  },

  actions: {
    // Initialize auth from localStorage
    initAuth() {
      if (process.env.CLIENT) {
        const token = localStorage.getItem('auth_token')
        const user = localStorage.getItem('auth_user')

        if (token && user) {
          this.token = token
          this.user = JSON.parse(user)
          this.isAuthenticated = true
          this.setAxiosToken(token)
        }
      }
    },

    // Set token in axios headers
    setAxiosToken(token) {
      if (token) {
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`
      } else {
        delete api.defaults.headers.common['Authorization']
      }
    },

    // Login
    async login(credentials) {
      try {
        const response = await api.post('/api/v1/login', credentials)
        
        if (response.data.success) {
          const { token, user } = response.data.data
          
          this.token = token
          this.user = user
          this.isAuthenticated = true
          
          // Save to localStorage
          localStorage.setItem('auth_token', token)
          localStorage.setItem('auth_user', JSON.stringify(user))
          
          // Set axios token
          this.setAxiosToken(token)
          
          return { success: true, data: response.data.data }
        }
        
        return { success: false, message: response.data.message }
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || 'Login failed',
          errors: error.response?.data?.errors,
        }
      }
    },

    // Register
    async register(userData) {
      try {
        const response = await api.post('/api/v1/register', userData)
        
        if (response.data.success) {
          return { success: true, data: response.data.data }
        }
        
        return { success: false, message: response.data.message }
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || 'Registration failed',
          errors: error.response?.data?.errors,
        }
      }
    },

    // Send password reset code
    async sendPasswordResetCode(email) {
      try {
        const response = await api.post('/api/v1/forgot-password', { email })
        return {
          success: response.data.success,
          message: response.data.message,
        }
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || 'Failed to send reset code',
          errors: error.response?.data?.errors,
        }
      }
    },

    // Reset password
    async resetPassword(data) {
      try {
        const response = await api.post('/api/v1/reset-password', data)
        return {
          success: response.data.success,
          message: response.data.message,
        }
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || 'Failed to reset password',
          errors: error.response?.data?.errors,
        }
      }
    },

    // Get current user
    async fetchUser() {
      try {
        const response = await api.get('/api/v1/me')
        
        if (response.data.success) {
          this.user = response.data.data.user
          localStorage.setItem('auth_user', JSON.stringify(this.user))
          return { success: true, data: response.data.data }
        }
        
        return { success: false }
      } catch {
        this.logout()
        return { success: false }
      }
    },

    // Refresh token
    async refreshToken() {
      try {
        const response = await api.post('/api/v1/refresh')
        
        if (response.data.success) {
          const { token } = response.data.data
          this.token = token
          localStorage.setItem('auth_token', token)
          this.setAxiosToken(token)
          return { success: true }
        }
        
        return { success: false }
      } catch {
        this.logout()
        return { success: false }
      }
    },

    // Logout
    async logout() {
      try {
        await api.post('/api/v1/logout')
      } catch {
        // Ignore logout errors
      } finally {
        this.token = null
        this.user = null
        this.isAuthenticated = false
        
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
        
        this.setAxiosToken(null)
      }
    },
  },
})

