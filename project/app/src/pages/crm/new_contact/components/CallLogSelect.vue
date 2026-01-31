<template>
  <div>
    <q-select
      v-if="store.callLogs && store.callLogs.length > 0"
      v-model="selectedCallLog"
      :options="store.callLogs"
      option-label="number"
      label="Last 10 call numbers"
      filled
      class="q-mt-md"
      @update:model-value="handleCallLogSelect"
    >
      <template v-slot:option="scope">
        <q-item v-bind="scope.itemProps">
          <q-item-section>
            <q-item-label>{{ scope.opt.name }}</q-item-label>
            <q-item-label caption>{{ scope.opt.number }}</q-item-label>
          </q-item-section>
        </q-item>
      </template>
      <template v-slot:selected>
        <div v-if="selectedCallLogObject">
          <div>{{ selectedCallLogObject.name }}</div>
          <div class="text-caption text-grey-7">{{ selectedCallLogObject.number }}</div>
        </div>
      </template>
    </q-select>

    <!-- Create New Customer Button (if customer not found) -->
    <div v-if="selectedCallLog && showCreateButton" class="q-mt-md">
      <q-btn
        color="positive"
        label="Create New Customer"
        icon="person_add"
        @click="goToCreateCustomerPage"
        unelevated
        class="full-width"
      />
    </div>
  </div>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'

export default {
  name: 'CallLogSelect',
  data() {
    return {
      selectedCallLog: null,
      customerNotFound: false
    }
  },
  computed: {
    store() {
      return useNewContactStore()
    },
    selectedCallLogObject() {
      if (!this.selectedCallLog) return null
      // Handle both object and string cases
      if (typeof this.selectedCallLog === 'object') {
        return this.selectedCallLog
      }
      return this.store.callLogs.find(log => log.number === this.selectedCallLog) || null
    },
    showCreateButton() {
      // Show button if customer not found and create form is shown for this number
      if (!this.selectedCallLog || !this.store.showCreateForm) return false
      
      const selectedNumber = typeof this.selectedCallLog === 'object' 
        ? this.selectedCallLog.number 
        : this.selectedCallLog
      
      return this.store.createFormPhoneNumber === selectedNumber
    }
  },
  methods: {
    async handleCallLogSelect(selectedValue) {
      console.log('CallLogSelect: Selected call log:', selectedValue)
      
      // Handle both object and string cases
      let callLog = null
      if (typeof selectedValue === 'object' && selectedValue !== null) {
        // If it's already an object, use it directly
        callLog = selectedValue
      } else if (selectedValue) {
        // If it's a string/number, find the object
        callLog = this.store.callLogs.find(log => log.number === selectedValue)
      }
      
      if (!callLog || !callLog.number) {
        console.log('CallLogSelect: Invalid call log selection')
        return
      }
      
      const query = callLog.number.trim()
      
      try {
        console.log('CallLogSelect: Searching for customer with phone:', query)
        
        // Step 1: Check if customer exists in database
        const result = await this.store.searchCustomers(query)
        
        if (result.success && result.data && result.data.length > 0) {
          // Customer exists - get full customer details
          const customer = result.data[0]
          console.log('CallLogSelect: Customer found, loading details for ID:', customer.id)
          
          // Reset customer not found flag
          this.customerNotFound = false
          
          // Call customer detail API: /api/v1/customers/{id}
          // This is done via selectCustomer which calls loadCustomerHistory
          await this.store.selectCustomer(customer)
          console.log('CallLogSelect: Customer details loaded:', customer.id)
        } else {
          // Customer doesn't exist - show create form
          console.log('CallLogSelect: No customer found, showing create form for phone:', query)
          this.customerNotFound = true
          this.store.showCustomerCreateForm(query)
        }
      } catch (error) {
        console.error('CallLogSelect: Failed to search by phone number:', error)
      }
    },
    goToCreateCustomerPage() {
      if (!this.selectedCallLog) return
      
      // Get phone number from selected call log
      const phoneNumber = typeof this.selectedCallLog === 'object'
        ? this.selectedCallLog.number
        : this.selectedCallLog
      
      if (!phoneNumber) return
      
      // Navigate to customer create page with phone number
      this.$router.push({
        path: '/crm/customer/create',
        query: { phone_number: phoneNumber }
      })
    }
  }
}
</script>

