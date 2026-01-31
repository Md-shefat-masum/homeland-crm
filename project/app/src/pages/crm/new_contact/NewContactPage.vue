<template>
  <q-page class="q-pa-md">
    <q-card>
      <q-card-section>
        <div class="text-h6 q-mb-md">New Contact Entry</div>

        <!-- Customer Search -->
        <CustomerSearch />

        <!-- Last 10 Call Numbers -->
        <CallLogSelect />

        <!-- Customer Info & Tabs (shown when customer selected) -->
        <div v-if="store.selectedCustomer" class="q-mt-lg">
          <CustomerInfo />
          <CustomerTabs />
        </div>


        <!-- First Call Log Info (after line 25) -->
        <div v-if="firstCallLog && !store.selectedCustomer" class="q-mt-lg">
          <q-card>
            <q-card-section>
              <div class="text-h6 q-mb-md">Last Call Information</div>
              <div class="row q-gutter-md">
                <div class="col-12 col-md-4">
                  <div class="text-caption text-grey-7">Name</div>
                  <div class="text-body1">{{ firstCallLog.name }}</div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="text-caption text-grey-7">Phone Number</div>
                  <div class="text-body1">{{ firstCallLog.number }}</div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="text-caption text-grey-7">Time</div>
                  <div class="text-body1">{{ firstCallLog.time }}</div>
                </div>
              </div>
              <div class="q-mt-md">
                <!-- Show "Check Customer" button if not checked yet -->
                <q-btn
                  v-if="!isCheckingCustomer && !store.showCreateForm"
                  color="primary"
                  label="Check Customer"
                  @click="checkFirstCallLogCustomer"
                  :loading="isCheckingCustomer"
                  unelevated
                />
                <!-- Show loading state -->
                <q-btn
                  v-if="isCheckingCustomer"
                  color="primary"
                  label="Checking..."
                  :loading="isCheckingCustomer"
                  unelevated
                  disable
                />
                <!-- Show "New Customer" button if customer doesn't exist -->
                <q-btn
                  v-if="store.showCreateForm && !isCheckingCustomer"
                  color="positive"
                  label="New Customer"
                  icon="person_add"
                  @click="goToCreateCustomerPage"
                  unelevated
                />
              </div>
            </q-card-section>
          </q-card>
        </div>

        <!-- Empty State -->
        <div v-if="!firstCallLog && !store.selectedCustomer" class="text-center q-pa-xl text-grey-6">
          <q-icon name="person_search" size="64px" />
          <div class="text-h6 q-mt-md">Search for a customer to view their history</div>
          <div class="text-body2 q-mt-sm">Enter customer name, mobile, or email to search</div>
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'
import { CallLog } from 'src/plugins/callLog'
import CustomerSearch from './components/CustomerSearch.vue'
import CustomerInfo from './components/CustomerInfo.vue'
import CustomerTabs from './components/CustomerTabs.vue'
import CallLogSelect from './components/CallLogSelect.vue'

export default {
  name: 'NewContactPage',
  components: {
    CustomerSearch,
    CustomerInfo,
    CustomerTabs,
    CallLogSelect
  },
  data() {
    return {
      firstCallLog: null,
      isCheckingCustomer: false
    }
  },
  computed: {
    store() {
      return useNewContactStore()
    },
    selectedCustomer() {
      return this.store.selectedCustomer
    }
  },
  watch: {
    '$route.query.phone_number'(newVal) {
      if (newVal) {
        this.handlePhoneNumberParam(newVal)
      }
    }
  },
  async created() {
    try {
      const res = await CallLog.get_call_log()
      if (res && res.calls && res.calls.length > 0) {
        // Format call logs for display
        const formattedLogs = res.calls.map(call => ({
          ...call,
          display: `${call.name}<br>${call.number}`
        }))
        this.store.setCallLogs(formattedLogs)
        console.log('CallLog: Loaded', formattedLogs.length, 'call logs')
        
        // Auto-fetch first call log (index 0)
        this.firstCallLog = formattedLogs[0]
        console.log('CallLog: First call log:', this.firstCallLog)
        
        // Auto-check customer for first call log
        await this.checkFirstCallLogCustomer()
      }
    } catch (e) {
      console.error('CallLog error:', e)
    }
  },
  async mounted() {
    // Check for phone_number query param first
    const phoneNumber = this.$route.query.phone_number
    if (phoneNumber) {
      await this.handlePhoneNumberParam(phoneNumber)
    }
  },
  methods: {
    async checkFirstCallLogCustomer() {
      if (!this.firstCallLog || !this.firstCallLog.number) {
        return
      }

      this.isCheckingCustomer = true
      const query = this.firstCallLog.number.trim()

      try {
        console.log('NewContactPage: Checking customer for first call log:', query)
        
        // Check if customer exists in database
        const result = await this.store.searchCustomers(query)
        
        if (result.success && result.data && result.data.length > 0) {
          // Customer exists - get full customer details
          const customer = result.data[0]
          console.log('NewContactPage: Customer found, loading details for ID:', customer.id)
          
          // Call customer detail API: /api/v1/customers/{id}
          await this.store.selectCustomer(customer)
          console.log('NewContactPage: Customer details loaded:', customer.id)
        } else {
          // Customer doesn't exist - show create form button
          console.log('NewContactPage: No customer found for phone:', query)
          this.store.showCustomerCreateForm(query)
        }
      } catch (error) {
        console.error('NewContactPage: Failed to check customer:', error)
      } finally {
        this.isCheckingCustomer = false
      }
    },
    goToCreateCustomerPage() {
      // Navigate to customer create page with phone number
      const phoneNumber = this.firstCallLog?.number || this.store.createFormPhoneNumber
      const query = phoneNumber ? { phone_number: phoneNumber } : {}
      this.$router.push({
        path: '/crm/customer/create',
        query
      })
    },
    async handlePhoneNumberParam(phoneNumber) {
      if (!phoneNumber || phoneNumber.trim() === '') {
        return
      }

      try {
        console.log('CallLog: Searching customer by phone:', phoneNumber)
        // Search customer by phone number
        const result = await this.store.searchCustomers(phoneNumber.trim())
        if (result.success && result.data && result.data.length > 0) {
          // Auto-select first result
          await this.store.selectCustomer(result.data[0])
        } else {
          console.log('CallLog: No customer found for phone:', phoneNumber)
        }
      } catch (error) {
        console.error('CallLog: Failed to search by phone number:', error)
      }
    }
  }
}
</script>

