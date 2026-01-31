<template>
  <div>
    <q-card>
      <q-card-section>
        <div class="text-h6 q-mb-md">Create New Customer</div>
        <div class="text-body2 text-grey-7 q-mb-md">
          Customer not found. Please create a new customer profile.
          <span v-if="phoneNumber">Phone: {{ phoneNumber }}</span>
        </div>

        <!-- Redirect to customer create page with phone number -->
        <q-btn
          color="primary"
          label="Go to Create Customer Form"
          @click="goToCreatePage"
          unelevated
        />
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'

export default {
  name: 'CustomerCreateForm',
  props: {
    phoneNumber: {
      type: String,
      default: null
    }
  },
  computed: {
    store() {
      return useNewContactStore()
    }
  },
  methods: {
    goToCreatePage() {
      // Hide create form
      this.store.hideCustomerCreateForm()
      
      // Navigate to customer create page with phone number
      const query = this.phoneNumber ? { phone_number: this.phoneNumber } : {}
      this.$router.push({
        path: '/crm/customer/create',
        query
      })
    }
  }
}
</script>

