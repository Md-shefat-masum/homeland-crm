<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Addresses</div>
        <div class="text-body2 text-grey-7">Manage addresses and locations</div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          label=""
          icon="add"
          @click="$router.push('/crm/address/create')"
          unelevated
        />
      </div>
    </div>

    <q-card>
      <q-card-section>
        <div class="row q-gutter-md q-mb-md">
          <div class="col-12 col-md-4">
            <q-input
              v-model="filters.search"
              label="Search"
              outlined
              dense
              clearable
              @update:model-value="onFilterChange"
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-12 col-md-3">
            <q-select
              v-model="filters.type"
              :options="typeOptions"
              label="Type"
              outlined
              dense
              clearable
              @update:model-value="onFilterChange"
            />
          </div>
          <div class="col-12 col-md-3">
            <q-select
              v-model="filters.is_active"
              :options="statusOptions"
              label="Status"
              outlined
              dense
              clearable
              @update:model-value="onFilterChange"
            />
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center q-pa-lg">
          <q-spinner color="primary" size="3em" />
          <div class="q-mt-md">Loading addresses...</div>
        </div>

        <!-- Table -->
        <div v-else class="table-responsive">
          <table class="q-table" style="width: 100%; border-collapse: collapse">
            <thead>
              <tr style="background-color: #f5f5f5">
                <th
                  v-for="col in columns"
                  :key="col.name"
                  :class="[
                    'q-table__col',
                    col.sortable ? 'cursor-pointer' : '',
                    `text-${col.align || 'left'}`
                  ]"
                  style="padding: 12px; border-bottom: 2px solid #ddd"
                  @click="col.sortable ? handleSort(col.name) : null"
                >
                  <div style="display: flex; align-items: center; gap: 8px">
                    <span>{{ col.label }}</span>
                    <q-icon
                      v-if="col.sortable && sort_by === col.name"
                      :name="descending ? 'arrow_downward' : 'arrow_upward'"
                      size="16px"
                    />
                    <q-icon
                      v-else-if="col.sortable"
                      name="sort"
                      size="16px"
                      style="opacity: 0.3"
                    />
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="address in addresses"
                :key="address.id"
                style="border-bottom: 1px solid #eee"
                class="table-row-hover"
              >
                <td style="padding: 12px">{{ address.id }}</td>
                <td style="padding: 12px">{{ address.name }}</td>
                <td style="padding: 12px">{{ address.code }}</td>
                <td style="padding: 12px">
                  <q-badge :color="getTypeColor(address.type)" :label="address.type" />
                </td>
                <td style="padding: 12px">
                  {{ address.parent?.name || '-' }}
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge color="info" :label="address.customers_count || 0" />
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    :color="address.is_active ? 'positive' : 'negative'"
                    :label="address.is_active ? 'Active' : 'Inactive'"
                  />
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-btn
                    flat
                    dense
                    round
                    icon="visibility"
                    color="primary"
                    size="sm"
                    @click="$router.push(`/crm/address/${address.id}`)"
                  >
                    <q-tooltip>View</q-tooltip>
                  </q-btn>
                  <q-btn
                    flat
                    dense
                    round
                    icon="edit"
                    color="primary"
                    size="sm"
                    @click="$router.push(`/crm/address/${address.id}/edit`)"
                  >
                    <q-tooltip>Edit</q-tooltip>
                  </q-btn>
                  <q-btn
                    flat
                    dense
                    round
                    icon="delete"
                    color="negative"
                    size="sm"
                    :disable="address.customers_count > 0"
                    @click="confirmDelete(address)"
                  >
                    <q-tooltip>
                      {{ address.customers_count > 0 ? 'Cannot delete: Has customers' : 'Delete' }}
                    </q-tooltip>
                  </q-btn>
                </td>
              </tr>
              <tr v-if="addresses.length === 0">
                <td :colspan="columns.length" style="padding: 40px; text-align: center; color: #999">
                  No addresses found
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="!loading && pagesNumber > 0" class="row justify-center q-mt-md">
          <q-pagination
            v-model="currentPage"
            color="grey-8"
            :max="pagesNumber"
            size="sm"
            @update:model-value="onPageChange"
          />
        </div>
      </q-card-section>
    </q-card>

    <q-dialog v-model="deleteDialog" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <q-avatar icon="warning" color="negative" text-color="white" />
          <span class="q-ml-sm">Are you sure you want to delete this address?</span>
        </q-card-section>

        <q-card-section v-if="selectedAddress">
          <div><strong>Name:</strong> {{ selectedAddress.name }}</div>
          <div><strong>Type:</strong> {{ selectedAddress.type }}</div>
          <div v-if="selectedAddress.customers_count > 0" class="text-negative q-mt-sm">
            <q-icon name="error" /> This address has {{ selectedAddress.customers_count }} customer(s)
            connected. Cannot delete.
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            flat
            label="Delete"
            color="negative"
            @click="deleteAddress"
            :disable="selectedAddress?.customers_count > 0"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue'
import { useQuasar, Notify } from 'quasar'
import { api } from 'boot/axios'

export default defineComponent({
  name: 'AddressListPage',
  setup() {
    const $q = useQuasar()

    const addresses = ref([])
    const loading = ref(false)
    const deleteDialog = ref(false)
    const selectedAddress = ref(null)
    const sort_by = ref('id')
    const descending = ref(true)

    const filters = ref({
      search: '',
      type: null,
      is_active: null,
    })

    const typeOptions = ['country', 'division', 'district', 'upazila', 'union', 'village', 'area']
    const statusOptions = [
      { label: 'Active', value: true },
      { label: 'Inactive', value: false },
    ]

    const columns = [
      { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
      { name: 'name', label: 'Name', field: 'name', align: 'left', sortable: true },
      { name: 'code', label: 'Code', field: 'code', align: 'left', sortable: true },
      { name: 'type', label: 'Type', field: 'type', align: 'left', sortable: true },
      {
        name: 'parent',
        label: 'Parent',
        field: (row) => row.parent?.name || '-',
        align: 'left',
        sortable: false,
      },
      {
        name: 'customers_count',
        label: 'Customers',
        field: 'customers_count',
        align: 'center',
        sortable: true,
      },
      {
        name: 'is_active',
        label: 'Status',
        field: 'is_active',
        align: 'center',
        sortable: true,
      },
      { name: 'actions', label: 'Actions', field: 'actions', align: 'center', sortable: false },
    ]

    const pagination = ref({
      sortBy: 'id',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
    })

    const currentPage = ref(1)
    const lastPage = ref(1)

    const pagesNumber = computed(() => lastPage.value)

    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
      }
    }

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

    const loadAddresses = async (requestProps) => {
      loading.value = true
      try {
        // Use request props if provided, otherwise use current pagination state
        const currentPagination = requestProps?.pagination || pagination.value
        
        const params = {
          page: currentPagination.page || 1,
          per_page: currentPagination.rowsPerPage || 10,
          sort_by: sort_by.value,
          descending: descending.value,
        }

        if (filters.value.search) {
          params.search = filters.value.search
        }
        if (filters.value.type) {
          params.type = filters.value.type
        }
        if (filters.value.is_active !== null && filters.value.is_active !== '') {
          params.is_active = filters.value.is_active
        }

        const response = await api.get('/api/v1/addresses', { params })

        if (response.data.success) {
          addresses.value = response.data.data || []
          // Update pagination: use sort_by and descending refs, update metadata from API
          currentPage.value = response.data.current_page || 1
          lastPage.value = response.data.last_page || 1
          pagination.value = {
            sortBy: sort_by.value,
            descending: descending.value,
            page: response.data.current_page || 1,
            rowsPerPage: response.data.per_page || 10,
            rowsNumber: response.data.total || 0,
          }
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load addresses',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    const handleSort = (columnName) => {
      // Manual sorting toggle logic:
      // If same column is clicked -> toggle descending
      // If different column -> set descending = true
      if (sort_by.value === columnName) {
        // Same column clicked, toggle descending
        descending.value = !descending.value
      } else {
        // Different column clicked, set descending = true
        sort_by.value = columnName
        descending.value = true
      }
      
      // Reset to page 1 when sorting changes
      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        sortBy: sort_by.value,
        descending: descending.value,
        page: 1,
      }
      
      loadAddresses({ pagination: pagination.value })
    }

    const onPageChange = (page) => {
      currentPage.value = page
      pagination.value = {
        ...pagination.value,
        page: page,
      }
      loadAddresses({ pagination: pagination.value })
    }

    const onFilterChange = () => {
      // Reset to page 1 when filters change, but keep current sort
      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        page: 1,
      }
      loadAddresses({ pagination: pagination.value })
    }

    const confirmDelete = (address) => {
      selectedAddress.value = address
      deleteDialog.value = true
    }

    const deleteAddress = async () => {
      if (!selectedAddress.value || selectedAddress.value.customers_count > 0) {
        return
      }

      loading.value = true
      try {
        const response = await api.delete(`/api/v1/addresses/${selectedAddress.value.id}`)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Address deleted successfully',
            position: 'top',
          })
          deleteDialog.value = false
          selectedAddress.value = null
          loadAddresses({ pagination: pagination.value })
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to delete address',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      // Initialize sort state
      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        sortBy: sort_by.value,
        descending: descending.value,
        page: 1,
      }
      loadAddresses({ pagination: pagination.value })
    })

      return {
      addresses,
      loading,
      filters,
      typeOptions,
      statusOptions,
      columns,
      pagination,
      deleteDialog,
      selectedAddress,
      sort_by,
      descending,
      currentPage,
      lastPage,
      pagesNumber,
      getTypeColor,
      loadAddresses,
      handleSort,
      onPageChange,
      onFilterChange,
      confirmDelete,
      deleteAddress,
    }
  },
})
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
}

.table-row-hover:hover {
  background-color: #f9f9f9;
}

.cursor-pointer {
  cursor: pointer;
}

.cursor-pointer:hover {
  background-color: #ebebeb;
}
</style>

