<template>
  <q-card class="q-mt-md">
    <q-card-section>
      <div class="row items-center q-gutter-md">
        <!-- Avatar -->
        <div>
          <AppAvatar
            :image="customer.photo"
            :alt="customer.name"
            size="80px"
          />
        </div>

        <!-- Customer Details -->
        <div class="col">
          <div class="text-h6">{{ customer.name }}</div>
          <div class="row q-gutter-md q-mt-sm">
            <div v-if="customer.mobile" class="text-body2">
              <q-icon name="phone" size="16px" class="q-mr-xs" />
              {{ customer.mobile }}
            </div>
            <div v-if="customer.email" class="text-body2">
              <q-icon name="email" size="16px" class="q-mr-xs" />
              {{ customer.email }}
            </div>
            <div v-if="customer.alternative_mobile" class="text-body2">
              <q-icon name="phone_android" size="16px" class="q-mr-xs" />
              {{ customer.alternative_mobile }}
            </div>
          </div>
        </div>

        <!-- Status Badge -->
        <div>
          <q-badge
            :color="customer.is_active ? 'positive' : 'negative'"
            :label="customer.is_active ? 'Active' : 'Inactive'"
          />
        </div>
      </div>

      <!-- Additional Info -->
      <q-separator class="q-mt-md q-mb-md" />

      <div class="row q-gutter-md">
        <!-- Customer Group -->
        <div v-if="customer.customer_group" class="col-12 col-md-3">
          <div class="text-caption text-grey-7">Customer Group</div>
          <div class="q-mt-xs">
            <q-badge
              :style="{ backgroundColor: customer.customer_group.color || '#1976d2', color: 'white' }"
              :label="customer.customer_group.name"
            />
          </div>
        </div>

        <!-- Profession -->
        <div v-if="customer.profession" class="col-12 col-md-3">
          <div class="text-caption text-grey-7">Profession</div>
          <div class="text-body2 q-mt-xs">{{ getProfessionDisplayName(customer.profession) }}</div>
        </div>

        <!-- Address -->
        <div v-if="customer.current_address" class="col-12 col-md-6">
          <div class="text-caption text-grey-7">Current Address</div>
          <div class="text-body2 q-mt-xs">
            {{ formatAddressLabel(customer.current_address) }}
          </div>
          <div v-if="customer.current_address_text" class="text-body2 text-grey-7 q-mt-xs">
            {{ customer.current_address_text }}
          </div>
        </div>
      </div>

      <!-- Additional Fields -->
      <div v-if="customer.nearest_market || customer.preferred_area || customer.target_real_estate" class="row q-gutter-md q-mt-md">
        <div v-if="customer.nearest_market" class="col-12 col-md-4">
          <div class="text-caption text-grey-7">Nearest Market</div>
          <div class="text-body2 q-mt-xs">{{ customer.nearest_market }}</div>
        </div>
        <div v-if="customer.preferred_area" class="col-12 col-md-4">
          <div class="text-caption text-grey-7">Preferred Area</div>
          <div class="text-body2 q-mt-xs">{{ customer.preferred_area }}</div>
        </div>
        <div v-if="customer.target_real_estate" class="col-12 col-md-4">
          <div class="text-caption text-grey-7">Target Real Estate</div>
          <div class="text-body2 q-mt-xs">{{ customer.target_real_estate }}</div>
        </div>
      </div>
    </q-card-section>
  </q-card>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'
import AppAvatar from 'components/AppAvatar.vue'

export default {
  name: 'CustomerInfo',
  components: {
    AppAvatar
  },
  computed: {
    store() {
      return useNewContactStore()
    },
    customer() {
      return this.store.selectedCustomer
    }
  },
  methods: {
    formatAddressLabel(address) {
      // Format: "Parent > Address" or just "Address"
      if (address.parent && address.parent.name) {
        return `${address.parent.name} > ${address.name}`
      }
      return address.name || ''
    },

    getProfessionDisplayName(profession) {
      if (!profession) return ''
      
      if (profession.type === 'job' && profession.job_title) {
        return profession.job_title
      } else if (profession.type === 'business' && profession.business_type) {
        return profession.business_type
      } else if (profession.type === 'student') {
        return 'Student'
      } else if (profession.type === 'housewife') {
        return 'Housewife'
      }
      
      return profession.name || ''
    }
  }
}
</script>

