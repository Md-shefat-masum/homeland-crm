<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Address Details</div>
        <div class="text-body2 text-grey-7">View address information and analytics</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/address')"
        />
        <q-btn
          color="primary"
          label="Edit"
          icon="edit"
          @click="$router.push(`/crm/address/${address?.id}/edit`)"
          class="q-ml-sm"
          unelevated
        />
      </div>
    </div>

    <div v-if="!loading && address" class="row q-gutter-md">
      <!-- Address Information -->
      <div class="col-12 col-md-8">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Address Information</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Name</div>
                <div class="text-body1 q-mb-md">{{ address.name }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Code</div>
                <div class="text-body1 q-mb-md">{{ address.code }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Type</div>
                <div class="q-mb-md">
                  <q-badge :color="getTypeColor(address.type)" :label="address.type" />
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Status</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="address.is_active ? 'positive' : 'negative'"
                    :label="address.is_active ? 'Active' : 'Inactive'"
                  />
                </div>
              </div>
              <div class="col-12 col-md-6" v-if="address.parent">
                <div class="text-body2 text-grey-7">Parent Address</div>
                <div class="text-body1 q-mb-md">
                  {{ address.parent.name }} ({{ address.parent.type }})
                </div>
              </div>
              <div class="col-12 col-md-6" v-if="address.latitude && address.longitude">
                <div class="text-body2 text-grey-7">Coordinates</div>
                <div class="text-body1 q-mb-md">
                  {{ address.latitude }}, {{ address.longitude }}
                </div>
              </div>
              <div class="col-12 col-md-6" v-if="address.sort_order !== null">
                <div class="text-body2 text-grey-7">Sort Order</div>
                <div class="text-body1 q-mb-md">{{ address.sort_order }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <!-- Children Addresses -->
        <q-card v-if="children.length > 0" class="q-mt-md">
          <q-card-section>
            <div class="text-h6 q-mb-md">Child Addresses ({{ children.length }})</div>
            <q-list>
              <q-item
                v-for="child in children"
                :key="child.id"
                clickable
                @click="$router.push(`/crm/address/${child.id}`)"
              >
                <q-item-section>
                  <q-item-label>{{ child.name }}</q-item-label>
                  <q-item-label caption>{{ child.type }} - {{ child.code }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-badge
                    :color="child.is_active ? 'positive' : 'negative'"
                    :label="child.is_active ? 'Active' : 'Inactive'"
                  />
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>

      <!-- Analytics Card -->
      <div class="col-12 col-md-4">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Customer Analytics</div>
            <div class="q-gutter-md">
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Total Customers</div>
                </div>
                <div class="col-auto">
                  <q-badge color="primary" :label="customerStats.total || 0" />
                </div>
              </div>
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Active Customers</div>
                </div>
                <div class="col-auto">
                  <q-badge color="positive" :label="customerStats.active || 0" />
                </div>
              </div>
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Inactive Customers</div>
                </div>
                <div class="col-auto">
                  <q-badge color="negative" :label="customerStats.inactive || 0" />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <!-- Customer List -->
        <q-card v-if="customers.length > 0" class="q-mt-md">
          <q-card-section>
            <div class="text-h6 q-mb-md">Customers ({{ customers.length }})</div>
            <q-list>
              <q-item
                v-for="customer in customers"
                :key="customer.id"
                clickable
                @click="$router.push(`/crm/customers/${customer.id}`)"
              >
                <q-item-section>
                  <q-item-label>{{ customer.name }}</q-item-label>
                  <q-item-label caption>{{ customer.mobile || customer.email }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-badge
                    :color="customer.is_active ? 'positive' : 'negative'"
                    :label="customer.is_active ? 'Active' : 'Inactive'"
                  />
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-inner-loading :showing="loading">
      <q-spinner-gears size="50px" color="primary" />
    </q-inner-loading>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { api } from 'boot/axios'

export default defineComponent({
  name: 'AddressViewPage',
  setup() {
    const route = useRoute()

    const address = ref(null)
    const children = ref([])
    const customers = ref([])
    const customerStats = ref({
      total: 0,
      active: 0,
      inactive: 0,
    })
    const loading = ref(false)

    const getTypeColor = (type) => {
      const colors = {
        country: 'purple',
        division: 'blue',
        district: 'cyan',
        upazila: 'teal',
        union: 'green',
        village: 'orange',
        area: 'red',
      }
      return colors[type] || 'grey'
    }

    const loadAddress = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/addresses/${route.params.id}`)
        if (response.data.success) {
          address.value = response.data.data
          await loadChildren()
          await loadCustomers()
        }
      } catch (error) {
        console.error('Failed to load address:', error)
      } finally {
        loading.value = false
      }
    }

    const loadChildren = async () => {
      try {
        const response = await api.get(`/api/v1/addresses/${route.params.id}/children`)
        if (response.data.success) {
          children.value = response.data.data || []
        }
      } catch (error) {
        console.error('Failed to load children:', error)
      }
    }

    const loadCustomers = async () => {
      try {
        const response = await api.get(`/api/v1/customers`, {
          params: {
            current_address_id: route.params.id,
            per_page: 10,
          },
        })
        if (response.data.success) {
          const customerList = response.data.data.data || response.data.data || []
          customers.value = customerList
          customerStats.value = {
            total: customerList.length,
            active: customerList.filter((c) => c.is_active).length,
            inactive: customerList.filter((c) => !c.is_active).length,
          }
        }
      } catch (error) {
        console.error('Failed to load customers:', error)
      }
    }

    onMounted(() => {
      loadAddress()
    })

    return {
      address,
      children,
      customers,
      customerStats,
      loading,
      getTypeColor,
    }
  },
})
</script>

