import { defineStore } from 'pinia'
import { api } from 'boot/axios'

export const useNewContactStore = defineStore('newContact', {
  state: () => ({
    selectedCustomer: null,
    customerHistory: {
      leads: [],
      notes: [],
      followUps: [],
      callLogs: [],
      assignments: [],
      smsMessages: [],
      predictions: []
    },
    callLogs: [], // Last 10 call logs from device
    showCreateForm: false, // Show customer create form
    createFormPhoneNumber: null, // Phone number for create form
    loading: {
      customer: false,
      history: false,
      search: false,
      callLogs: false
    },
    searchQuery: '',
    searchResults: [],
    activeTab: 'info'
  }),

  getters: {
    hasCustomer: (state) => !!state.selectedCustomer,
    hasHistory: (state) => {
      return state.customerHistory.leads.length > 0 ||
        state.customerHistory.notes.length > 0 ||
        state.customerHistory.followUps.length > 0 ||
        state.customerHistory.callLogs.length > 0 ||
        state.customerHistory.assignments.length > 0 ||
        state.customerHistory.smsMessages.length > 0 ||
        state.customerHistory.predictions.length > 0
    }
  },

  actions: {
    // Search customers
    async searchCustomers(query) {
      if (!query || query.trim() === '') {
        this.searchResults = []
        return { success: true, data: [] }
      }

      this.loading.search = true
      this.searchQuery = query

      try {
        const response = await api.get('/api/v1/customers/search', {
          params: {
            q: query.trim(),
            limit: 20
          }
        })

        if (response.data.success) {
          this.searchResults = response.data.data || []
          return { success: true, data: this.searchResults }
        }

        return { success: false, message: response.data.message }
      } catch (error) {
        console.error('Search customers error:', error)
        return {
          success: false,
          message: error.response?.data?.message || 'Failed to search customers'
        }
      } finally {
        this.loading.search = false
      }
    },

    // Select customer and load history
    async selectCustomer(customer) {
      this.selectedCustomer = customer
      this.activeTab = 'info'

      if (customer && customer.id) {
        await this.loadCustomerHistory(customer.id)
      }
    },

    // Load customer with all relationships
    async loadCustomerHistory(customerId) {
      if (!customerId) return

      this.loading.history = true

      try {
        const response = await api.get(`/api/v1/customers/${customerId}`)

        if (response.data.success) {
          const customer = response.data.data

          // Update selected customer with full data
          this.selectedCustomer = customer

          // Extract history from relationships
          this.customerHistory = {
            leads: customer.leads || [],
            notes: customer.notes || [],
            followUps: customer.follow_ups || [],
            callLogs: customer.call_logs || [],
            assignments: customer.customer_assignments || [],
            smsMessages: customer.sms_messages || [],
            predictions: customer.predictions || []
          }

          return { success: true, data: customer }
        }

        return { success: false, message: response.data.message }
      } catch (error) {
        console.error('Load customer history error:', error)
        return {
          success: false,
          message: error.response?.data?.message || 'Failed to load customer history'
        }
      } finally {
        this.loading.history = false
      }
    },

    // Clear selection
    clearSelection() {
      this.selectedCustomer = null
      this.customerHistory = {
        leads: [],
        notes: [],
        followUps: [],
        callLogs: [],
        assignments: [],
        smsMessages: [],
        predictions: []
      }
      this.searchQuery = ''
      this.searchResults = []
      this.activeTab = 'info'
    },

    // Set active tab
    setActiveTab(tab) {
      this.activeTab = tab
    },

    // Save call logs from device
    setCallLogs(callLogs) {
      this.callLogs = callLogs || []
    },

    // Show customer create form with phone number
    showCustomerCreateForm(phoneNumber) {
      this.showCreateForm = true
      this.createFormPhoneNumber = phoneNumber
    },

    // Hide customer create form
    hideCustomerCreateForm() {
      this.showCreateForm = false
      this.createFormPhoneNumber = null
    }
  }
})

