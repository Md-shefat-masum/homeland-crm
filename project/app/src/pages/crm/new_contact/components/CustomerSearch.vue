<template>
  <div>
    <q-select
      v-model="selectedCustomer"
      :options="store.searchResults"
      option-label="displayLabel"
      option-value="id"
      label="Search Customer"
      outlined
      dense
      clearable
      use-input
      input-debounce="400"
      @filter="onSearch"
      @update:model-value="onCustomerSelect"
      :loading="store.loading.search"
      placeholder="Enter name, mobile, or email"
    >
      <template v-slot:no-option>
        <div class="text-center text-grey-7 q-pa-md">
          {{ searchQuery ? 'No customers found' : 'Start typing to search customers' }}
        </div>
      </template>
      <template v-slot:option="scope">
        <q-item v-bind="scope.itemProps">
          <q-item-section avatar>
            <AppAvatar
              :image="scope.opt.photo"
              :alt="scope.opt.name"
              size="40px"
            />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ scope.opt.name }}</q-item-label>
            <q-item-label caption>
              {{ scope.opt.displayLabel }}
            </q-item-label>
            <q-item-label v-if="scope.opt.mobile" caption>
              📱 {{ scope.opt.mobile }}
            </q-item-label>
          </q-item-section>
        </q-item>
      </template>
    </q-select>
  </div>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'
import debounce from 'debounce'
import AppAvatar from 'components/AppAvatar.vue'

export default {
  name: 'CustomerSearch',
  components: {
    AppAvatar
  },
  data() {
    return {
      selectedCustomer: null,
      searchQuery: ''
    }
  },
  computed: {
    store() {
      return useNewContactStore()
    }
  },
  methods: {
    // Debounced search function
    debouncedSearch: debounce(async function(query) {
      await this.store.searchCustomers(query)
    }, 400),

    async onSearch(val, update) {
      this.searchQuery = val
      update(() => {
        if (val === '') {
          // Clear results when empty
          this.store.searchResults = []
        } else {
          // Trigger debounced search
          this.debouncedSearch(val)
        }
      })
    },

    async onCustomerSelect(customer) {
      if (customer) {
        await this.store.selectCustomer(customer)
      }
    }
  },
  created() {
    // Format customer display label
    this.formatCustomerLabel = (customer) => {
      let label = customer.name || ''
      if (customer.current_address) {
        const address = customer.current_address
        // Get 2-step hierarchy: parent > address
        if (address.parent && address.parent.name) {
          label += `, ${address.parent.name}, ${address.name}`
        } else {
          label += `, ${address.name}`
        }
      }
      return label
    }
  },
  watch: {
    'store.searchResults': {
      handler(newResults) {
        // Format display labels for search results
        if (newResults && newResults.length > 0) {
          newResults.forEach(customer => {
            customer.displayLabel = this.formatCustomerLabel(customer)
          })
        }
      },
      immediate: true
    }
  }
}
</script>

