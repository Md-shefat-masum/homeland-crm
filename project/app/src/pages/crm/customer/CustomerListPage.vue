<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Customers</div>
        <div class="text-body2 text-grey-7">Manage customers and leads</div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          label=""
          icon="add"
          @click="$router.push('/crm/customer/create')"
          unelevated
        />
      </div>
    </div>

    <q-card>
      <q-card-section>
        <div class="row" style="gap: 10px; margin-bottom: 16px;">
          <div class="col-12 col-md-3">
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
          <div class="col-12 col-md-2">
            <q-select
              v-model="filters.customer_group_id"
              :options="customerGroupOptions"
              option-label="name"
              option-value="id"
              label="Group"
              outlined
              dense
              clearable
              use-input
              input-debounce="400"
              @filter="onGroupSearch"
              @update:model-value="onFilterChange"
            />
          </div>
          <div class="col-12 col-md-2">
            <q-select
              v-model="filters.profession_id"
              :options="professionOptions"
              option-label="displayName"
              option-value="id"
              label="Profession"
              outlined
              dense
              clearable
              use-input
              input-debounce="400"
              @filter="onProfessionSearch"
              @update:model-value="onFilterChange"
            />
          </div>
          <div class="col-12 col-md-2">
            <q-select
              v-model="filters.project_id"
              :options="projectOptions"
              option-label="name"
              option-value="id"
              label="Project"
              outlined
              dense
              clearable
              use-input
              input-debounce="400"
              @filter="onProjectSearch"
              @focus="onProjectFocus"
              @update:model-value="onFilterChange"
              :loading="loadingProjects"
            >
              <template v-slot:no-option>
                <div class="text-center text-grey-7 q-pa-md">
                  {{ projectSearchQuery ? 'No projects found' : 'Start typing to search projects' }}
                </div>
              </template>
              <template v-slot:option="scope">
                <q-item v-bind="scope.itemProps">
                  <q-item-section>
                    <q-item-label>{{ scope.opt.name }}</q-item-label>
                    <q-item-label v-if="scope.opt.project_type" caption>
                      {{ scope.opt.project_type }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
          <div class="col-12 col-md-2">
            <q-select
              v-model="filters.current_address_id"
              :options="addressOptions"
              option-label="displayLabel"
              option-value="id"
              label="Address"
              outlined
              dense
              clearable
              use-input
              input-debounce="400"
              @filter="onAddressSearch"
              @focus="onAddressFocus"
              @update:model-value="onFilterChange"
              :loading="loadingAddresses"
            >
              <template v-slot:no-option>
                <div class="text-center text-grey-7 q-pa-md">
                  {{ addressSearchQuery ? 'No addresses found' : 'Start typing to search addresses' }}
                </div>
              </template>
              <template v-slot:option="scope">
                <q-item v-bind="scope.itemProps">
                  <q-item-section>
                    <q-item-label>{{ scope.opt.displayLabel }}</q-item-label>
                    <q-item-label v-if="scope.opt.type" caption>
                      Type: {{ scope.opt.type }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
          <div class="col-12 col-md-2">
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
          <div class="q-mt-md">Loading customers...</div>
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
                v-for="customer in customers"
                :key="customer.id"
                style="border-bottom: 1px solid #eee"
                class="table-row-hover"
              >
                <td style="padding: 12px">{{ customer.id }}</td>
                <td style="padding: 12px">
                  <div style="display: flex; align-items: center; gap: 8px">
                    <AppAvatar :image="customer.image" :alt="customer.name" size="32px" />
                    <span>{{ customer.name }}</span>
                  </div>
                </td>
                <td style="padding: 12px">{{ customer.mobile }}</td>
                <td style="padding: 12px">{{ customer.email || '-' }}</td>
                <td style="padding: 12px">
                  <q-badge
                    v-if="customer.customer_group"
                    :style="{ backgroundColor: customer.customer_group.color || '#9e9e9e', color: '#fff' }"
                    :label="customer.customer_group.name"
                  />
                  <span v-else style="color: #999">-</span>
                </td>
                <td style="padding: 12px">
                  <span v-if="customer.profession">
                    {{ getProfessionDisplayName(customer.profession) }}
                  </span>
                  <span v-else style="color: #999">-</span>
                </td>
                <td style="padding: 12px">
                  {{ customer.current_address?.name || customer.current_address_text || '-' }}
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    :color="customer.is_active ? 'positive' : 'negative'"
                    :label="customer.is_active ? 'Active' : 'Inactive'"
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
                    @click="$router.push(`/crm/customer/${customer.id}`)"
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
                    @click="$router.push(`/crm/customer/${customer.id}/edit`)"
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
                    @click="confirmDelete(customer)"
                  >
                    <q-tooltip>Delete</q-tooltip>
                  </q-btn>
                </td>
              </tr>
              <tr v-if="customers.length === 0">
                <td :colspan="columns.length" style="padding: 40px; text-align: center; color: #999">
                  No customers found
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
          <span class="q-ml-sm">Are you sure you want to delete this customer?</span>
        </q-card-section>

        <q-card-section v-if="selectedCustomer">
          <div><strong>Name:</strong> {{ selectedCustomer.name }}</div>
          <div><strong>Mobile:</strong> {{ selectedCustomer.mobile }}</div>
          <div v-if="selectedCustomer.email"><strong>Email:</strong> {{ selectedCustomer.email }}</div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn flat label="Delete" color="negative" @click="deleteCustomer" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue'
import { useQuasar, Notify } from 'quasar'
import { api } from 'boot/axios'
import debounce from 'debounce'
import AppAvatar from 'components/AppAvatar.vue'

export default defineComponent({
  name: 'CustomerListPage',
  components: {
    AppAvatar,
  },
  setup() {
    const $q = useQuasar()

    const customers = ref([])
    const loading = ref(false)
    const deleteDialog = ref(false)
    const selectedCustomer = ref(null)
    const sort_by = ref('created_at')
    const descending = ref(true)

    const filters = ref({
      search: '',
      customer_group_id: null,
      project_id: null,
      profession_id: null,
      current_address_id: null,
      is_active: null,
    })

    const customerGroupOptions = ref([])
    const projectOptions = ref([])
    const professionOptions = ref([])
    const addressOptions = ref([])
    const loadingProjects = ref(false)
    const loadingAddresses = ref(false)
    const projectSearchQuery = ref('')
    const addressSearchQuery = ref('')
    const statusOptions = [
      { label: 'Active', value: true },
      { label: 'Inactive', value: false },
    ]

    const columns = [
      { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
      { name: 'name', label: 'Name', field: 'name', align: 'left', sortable: true },
      { name: 'mobile', label: 'Mobile', field: 'mobile', align: 'left', sortable: true },
      { name: 'email', label: 'Email', field: 'email', align: 'left', sortable: true },
      {
        name: 'customer_group',
        label: 'Group',
        field: 'customer_group',
        align: 'left',
        sortable: false,
      },
      {
        name: 'profession',
        label: 'Profession',
        field: 'profession',
        align: 'left',
        sortable: false,
      },
      {
        name: 'address',
        label: 'Address',
        field: 'current_address',
        align: 'left',
        sortable: false,
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
      sortBy: 'created_at',
      descending: true,
      page: 1,
      rowsPerPage: 15,
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

    const getProfessionDisplayName = (profession) => {
      if (!profession) return '-'
      if (profession.type === 'job' && profession.job_title) {
        return profession.job_title
      }
      if (profession.type === 'business' && profession.business_type) {
        return profession.business_type
      }
      return profession.type || '-'
    }

    const formatAddressLabel = (address) => {
      // Format: "Parent > Address" or just "Address"
      if (address.parent && address.parent.name) {
        return `${address.parent.name} > ${address.name}`
      }
      return address.name
    }

    const loadCustomerGroups = async (search = '') => {
      try {
        const params = { is_active: true, per_page: 50 }
        if (search) {
          params.search = search
        }
        const response = await api.get('/api/v1/customer-groups', { params })
        if (response.data.success) {
          customerGroupOptions.value = response.data.data || []
        }
      } catch (error) {
        console.error('Failed to load customer groups:', error)
      }
    }

    const loadProjects = async (search = '') => {
      loadingProjects.value = true
      try {
        const params = { is_active: true, per_page: 50 }
        if (search) {
          params.search = search
        }
        const response = await api.get('/api/v1/crm-projects', { params })
        if (response.data.success) {
          projectOptions.value = response.data.data || []
        }
      } catch (error) {
        console.error('Failed to load projects:', error)
        projectOptions.value = []
      } finally {
        loadingProjects.value = false
      }
    }

    const loadProfessions = async (search = '') => {
      try {
        const params = { per_page: 50 }
        if (search) {
          params.search = search
        }
        const response = await api.get('/api/v1/professions', { params })
        if (response.data.success) {
          const professions = response.data.data || []
          professionOptions.value = professions.map((prof) => ({
            ...prof,
            displayName: getProfessionDisplayName(prof),
          }))
        }
      } catch (error) {
        console.error('Failed to load professions:', error)
      }
    }

    const loadAddresses = async (searchQuery = '') => {
      loadingAddresses.value = true
      try {
        const params = {
          is_active: true,
          per_page: 50,
        }

        // Only add search parameter if searchQuery is provided and not empty
        if (searchQuery !== undefined && searchQuery !== null && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }

        const response = await api.get('/api/v1/addresses', { params })
        if (response.data.success) {
          const addresses = response.data.data || []
          // Format addresses with display label showing parent hierarchy
          addressOptions.value = addresses.map((address) => ({
            ...address,
            displayLabel: formatAddressLabel(address),
          }))
        }
      } catch (error) {
        console.error('Failed to load addresses:', error)
        addressOptions.value = []
      } finally {
        loadingAddresses.value = false
      }
    }

    // Debounced version of loadProjects (400ms)
    const debouncedLoadProjects = debounce((searchQuery) => {
      loadProjects(searchQuery)
    }, 400)

    const onProjectSearch = (val, update) => {
      projectSearchQuery.value = val
      update(() => {
        if (val === '') {
          loadProjects('')
        } else {
          debouncedLoadProjects(val)
        }
      })
    }

    const onProjectFocus = () => {
      if (projectOptions.value.length === 0 && !loadingProjects.value) {
        loadProjects('')
      }
    }

    // Debounced version of loadAddresses (400ms)
    const debouncedLoadAddresses = debounce((searchQuery) => {
      loadAddresses(searchQuery)
    }, 400)

    const onAddressSearch = (val, update) => {
      addressSearchQuery.value = val
      update(() => {
        // Load addresses with search query (debounced)
        if (val === '') {
          loadAddresses('')
        } else {
          debouncedLoadAddresses(val)
        }
      })
    }

    const onAddressFocus = () => {
      // Load all addresses when field is focused (if not already loaded)
      if (addressOptions.value.length === 0 && !loadingAddresses.value) {
        loadAddresses('')
      }
    }

    const onGroupSearch = (val, update) => {
      update(() => {
        if (val === '') {
          loadCustomerGroups()
        } else {
          loadCustomerGroups(val)
        }
      })
    }

    const onProfessionSearch = (val, update) => {
      update(() => {
        if (val === '') {
          loadProfessions()
        } else {
          loadProfessions(val)
        }
      })
    }

    const loadCustomers = async (requestProps) => {
      loading.value = true
      try {
        const currentPagination = requestProps?.pagination || pagination.value

        const params = {
          page: currentPagination.page || 1,
          per_page: currentPagination.rowsPerPage || 15,
          sort_by: sort_by.value,
          sort_order: descending.value ? 'desc' : 'asc',
        }

        if (filters.value.search) {
          params.search = filters.value.search
        }
        if (filters.value.customer_group_id) {
          params.customer_group_id = filters.value.customer_group_id
        }
        if (filters.value.project_id) {
          params.project_id = filters.value.project_id && typeof filters.value.project_id === 'object'
            ? filters.value.project_id.id
            : filters.value.project_id
        }
        if (filters.value.profession_id) {
          params.profession_id = filters.value.profession_id
        }
        if (filters.value.current_address_id) {
          params.current_address_id = filters.value.current_address_id && typeof filters.value.current_address_id === 'object'
            ? filters.value.current_address_id.id
            : filters.value.current_address_id
        }
        if (filters.value.is_active !== null && filters.value.is_active !== '') {
          params.is_active = filters.value.is_active
        }

        const response = await api.get('/api/v1/customers', { params })

        if (response.data.success) {
          customers.value = response.data.data || []
          if (response.data.pagination) {
            currentPage.value = response.data.pagination.current_page || 1
            lastPage.value = response.data.pagination.last_page || 1
            pagination.value = {
              sortBy: sort_by.value,
              descending: descending.value,
              page: response.data.pagination.current_page || 1,
              rowsPerPage: response.data.pagination.per_page || 15,
              rowsNumber: response.data.pagination.total || 0,
            }
          } else {
            // Fallback if pagination format is different
            currentPage.value = 1
            lastPage.value = 1
            pagination.value = {
              sortBy: sort_by.value,
              descending: descending.value,
              page: 1,
              rowsPerPage: 15,
              rowsNumber: customers.value.length,
            }
          }
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load customers',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    const handleSort = (columnName) => {
      if (sort_by.value === columnName) {
        descending.value = !descending.value
      } else {
        sort_by.value = columnName
        descending.value = true
      }

      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        sortBy: sort_by.value,
        descending: descending.value,
        page: 1,
      }

      loadCustomers({ pagination: pagination.value })
    }

    const onPageChange = (page) => {
      currentPage.value = page
      pagination.value = {
        ...pagination.value,
        page: page,
      }
      loadCustomers({ pagination: pagination.value })
    }

    const debouncedLoadCustomers = debounce((requestProps) => {
      loadCustomers(requestProps)
    }, 400)

    const onFilterChange = () => {
      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        page: 1,
      }
      debouncedLoadCustomers({ pagination: pagination.value })
    }

    const confirmDelete = (customer) => {
      selectedCustomer.value = customer
      deleteDialog.value = true
    }

    const deleteCustomer = async () => {
      if (!selectedCustomer.value) {
        return
      }

      loading.value = true
      try {
        const response = await api.delete(`/api/v1/customers/${selectedCustomer.value.id}`)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Customer deleted successfully',
            position: 'top',
          })
          deleteDialog.value = false
          selectedCustomer.value = null
          loadCustomers({ pagination: pagination.value })
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to delete customer',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        sortBy: sort_by.value,
        descending: descending.value,
        page: 1,
      }
      loadCustomerGroups()
      loadProjects()
      loadProfessions()
      loadAddresses('')
      loadCustomers({ pagination: pagination.value })
    })

    return {
      customers,
      loading,
      filters,
      customerGroupOptions,
      projectOptions,
      professionOptions,
      statusOptions,
      columns,
      pagination,
      deleteDialog,
      selectedCustomer,
      sort_by,
      descending,
      currentPage,
      lastPage,
      pagesNumber,
      getProfessionDisplayName,
      formatAddressLabel,
      loadCustomerGroups,
      loadProjects,
      loadProfessions,
      loadAddresses,
      onGroupSearch,
      onProjectSearch,
      onProjectFocus,
      onProfessionSearch,
      onAddressSearch,
      onAddressFocus,
      loadingProjects,
      loadingAddresses,
      projectSearchQuery,
      addressOptions,
      addressSearchQuery,
      loadCustomers,
      handleSort,
      onPageChange,
      onFilterChange,
      confirmDelete,
      deleteCustomer,
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

