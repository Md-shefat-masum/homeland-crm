<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Lead Source Management</div>
        <div class="text-body2 text-grey-7">Manage lead sources</div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          label=""
          icon="add"
          @click="$router.push('/crm/lead_sources/create')"
          unelevated
        />
      </div>
    </div>

    <q-card>
      <q-card-section>
        <div class="row" style="gap: 10px; margin-bottom: 16px;">
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
              v-model="filters.is_active"
              :options="statusSelectOptions"
              option-label="label"
              option-value="value"
              emit-value
              map-options
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
          <div class="q-mt-md">Loading lead sources...</div>
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
                v-for="leadSource in leadSources"
                :key="leadSource.id"
                style="border-bottom: 1px solid #eee"
                class="table-row-hover"
              >
                <td style="padding: 12px">{{ leadSource.id }}</td>
                <td style="padding: 12px">
                  <div class="text-weight-medium">{{ leadSource.title }}</div>
                </td>
                <td style="padding: 12px">{{ leadSource.description || '-' }}</td>
                <td style="padding: 12px; text-align: center">
                  <q-badge color="info" :label="leadSource.leads_count || 0" />
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    :color="leadSource.is_active ? 'positive' : 'negative'"
                    :label="leadSource.is_active ? 'Active' : 'Inactive'"
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
                    @click="$router.push(`/crm/lead_sources/${leadSource.id}`)"
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
                    @click="$router.push(`/crm/lead_sources/${leadSource.id}/edit`)"
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
                    :disable="leadSource.leads_count > 0"
                    @click="confirmDelete(leadSource)"
                  >
                    <q-tooltip>
                      {{ leadSource.leads_count > 0 ? 'Cannot delete: Has leads' : 'Delete' }}
                    </q-tooltip>
                  </q-btn>
                </td>
              </tr>
              <tr v-if="leadSources.length === 0">
                <td :colspan="columns.length" style="padding: 40px; text-align: center; color: #999">
                  No lead sources found
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
          <span class="q-ml-sm">Are you sure you want to delete this lead source?</span>
        </q-card-section>

        <q-card-section v-if="selectedLeadSource">
          <div><strong>Title:</strong> {{ selectedLeadSource.title }}</div>
          <div v-if="selectedLeadSource.description">
            <strong>Description:</strong> {{ selectedLeadSource.description }}
          </div>
          <div v-if="selectedLeadSource.leads_count > 0" class="text-negative q-mt-sm">
            <q-icon name="error" /> This lead source has {{ selectedLeadSource.leads_count }} lead(s)
            connected. Cannot delete.
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            flat
            label="Delete"
            color="negative"
            @click="deleteLeadSource"
            :disable="selectedLeadSource?.leads_count > 0"
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
import debounce from 'debounce'

export default defineComponent({
  name: 'LeadSourceListPage',
  setup() {
    const $q = useQuasar()

    const leadSources = ref([])
    const loading = ref(false)
    const deleteDialog = ref(false)
    const selectedLeadSource = ref(null)
    const sort_by = ref('id')
    const descending = ref(true)

    const filters = ref({
      search: '',
      is_active: null,
    })

    const statusOptions = [
      { label: 'Active', value: true },
      { label: 'Inactive', value: false },
    ]

    // Map statusOptions for q-select
    const statusSelectOptions = [
      { label: 'Active', value: true },
      { label: 'Inactive', value: false },
    ]

    const columns = [
      { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
      { name: 'title', label: 'Title', field: 'title', align: 'left', sortable: true },
      {
        name: 'description',
        label: 'Description',
        field: 'description',
        align: 'left',
        sortable: false,
      },
      {
        name: 'leads_count',
        label: 'Leads',
        field: 'leads_count',
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

    const loadLeadSources = async (requestProps) => {
      loading.value = true
      try {
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
        if (filters.value.is_active !== null && filters.value.is_active !== '') {
          params.is_active = filters.value.is_active
        }

        const response = await api.get('/api/v1/lead-sources', { params })

        if (response.data.success) {
          leadSources.value = response.data.data || []
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
          message: error.response?.data?.message || 'Failed to load lead sources',
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

      loadLeadSources({ pagination: pagination.value })
    }

    const onPageChange = (page) => {
      currentPage.value = page
      pagination.value = {
        ...pagination.value,
        page: page,
      }
      loadLeadSources({ pagination: pagination.value })
    }

    const debouncedLoadLeadSources = debounce((requestProps) => {
      loadLeadSources(requestProps)
    }, 400)

    const onFilterChange = () => {
      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        page: 1,
      }
      debouncedLoadLeadSources({ pagination: pagination.value })
    }

    const confirmDelete = (leadSource) => {
      selectedLeadSource.value = leadSource
      deleteDialog.value = true
    }

    const deleteLeadSource = async () => {
      if (!selectedLeadSource.value || selectedLeadSource.value.leads_count > 0) {
        return
      }

      loading.value = true
      try {
        const response = await api.delete(`/api/v1/lead-sources/${selectedLeadSource.value.id}`)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Lead source deleted successfully',
            position: 'top',
          })
          deleteDialog.value = false
          selectedLeadSource.value = null
          loadLeadSources({ pagination: pagination.value })
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to delete lead source',
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
      loadLeadSources({ pagination: pagination.value })
    })

    return {
      leadSources,
      loading,
      filters,
      statusOptions,
      statusSelectOptions,
      columns,
      pagination,
      deleteDialog,
      selectedLeadSource,
      sort_by,
      descending,
      currentPage,
      lastPage,
      pagesNumber,
      loadLeadSources,
      handleSort,
      onPageChange,
      onFilterChange,
      confirmDelete,
      deleteLeadSource,
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

