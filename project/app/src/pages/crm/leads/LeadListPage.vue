<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Lead Management</div>
        <div class="text-body2 text-grey-7">Manage leads and track conversions</div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          label=""
          icon="add"
          @click="$router.push('/crm/leads/create')"
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
              v-model="filters.status"
              :options="statusOptions"
              label="Status"
              outlined
              dense
              clearable
              @update:model-value="onFilterChange"
            />
          </div>
          <div class="col-12 col-md-2">
            <q-select
              v-model="filters.priority"
              :options="priorityOptions"
              label="Priority"
              outlined
              dense
              clearable
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
            />
          </div>
          <div class="col-12 col-md-2">
            <q-select
              v-model="filters.lead_source_id"
              :options="leadSourceOptions"
              option-label="title"
              option-value="id"
              label="Lead Source"
              outlined
              dense
              clearable
              use-input
              input-debounce="400"
              @filter="onLeadSourceSearch"
              @focus="onLeadSourceFocus"
              @update:model-value="onFilterChange"
              :loading="loadingLeadSources"
            />
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center q-pa-lg">
          <q-spinner color="primary" size="3em" />
          <div class="q-mt-md">Loading leads...</div>
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
                v-for="lead in leads"
                :key="lead.id"
                style="border-bottom: 1px solid #eee"
                class="table-row-hover"
              >
                <td style="padding: 12px">{{ lead.id }}</td>
                <td style="padding: 12px">
                  <div class="text-weight-medium">{{ lead.customer?.name || '-' }}</div>
                  <div class="text-caption text-grey-7">{{ lead.customer?.mobile || '-' }}</div>
                </td>
                <td style="padding: 12px">{{ lead.project?.name || '-' }}</td>
                <td style="padding: 12px">{{ lead.customer_lead_source?.title || lead.lead_source || '-' }}</td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    :color="getStatusColor(lead.status)"
                    :label="formatStatus(lead.status)"
                  />
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    :color="getPriorityColor(lead.priority)"
                    :label="formatPriority(lead.priority)"
                  />
                </td>
                <td style="padding: 12px">{{ lead.next_contact_date? new Date(lead.next_contact_date).toDateString() : '-' }}</td>
                <td style="padding: 12px; text-align: center">
                  <q-btn
                    flat
                    dense
                    round
                    icon="visibility"
                    color="primary"
                    size="sm"
                    @click="$router.push(`/crm/leads/${lead.id}`)"
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
                    @click="$router.push(`/crm/leads/${lead.id}/edit`)"
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
                    @click="confirmDelete(lead)"
                  >
                    <q-tooltip>Delete</q-tooltip>
                  </q-btn>
                </td>
              </tr>
              <tr v-if="leads.length === 0">
                <td :colspan="columns.length" style="padding: 40px; text-align: center; color: #999">
                  No leads found
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
          <span class="q-ml-sm">Are you sure you want to delete this lead?</span>
        </q-card-section>

        <q-card-section v-if="selectedLead">
          <div><strong>Customer:</strong> {{ selectedLead.customer?.name }}</div>
          <div><strong>Status:</strong> {{ formatStatus(selectedLead.status) }}</div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn flat label="Delete" color="negative" @click="deleteLead" />
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
  name: 'LeadListPage',
  setup() {
    const $q = useQuasar()

    const leads = ref([])
    const loading = ref(false)
    const deleteDialog = ref(false)
    const selectedLead = ref(null)
    const sort_by = ref('id')
    const descending = ref(true)

    const filters = ref({
      search: '',
      status: null,
      priority: null,
      project_id: null,
      lead_source_id: null,
    })

    const statusOptions = [
      { label: 'New', value: 'new' },
      { label: 'Contacted', value: 'contacted' },
      { label: 'Qualified', value: 'qualified' },
      { label: 'Converted', value: 'converted' },
      { label: 'Lost', value: 'lost' },
    ]

    const priorityOptions = [
      { label: 'Low', value: 'low' },
      { label: 'Medium', value: 'medium' },
      { label: 'High', value: 'high' },
    ]

    const projectOptions = ref([])
    const loadingProjects = ref(false)
    const projectSearchQuery = ref('')

    const leadSourceOptions = ref([])
    const loadingLeadSources = ref(false)
    const leadSourceSearchQuery = ref('')

    const columns = [
      { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
      { name: 'customer', label: 'Customer', field: 'customer', align: 'left', sortable: false },
      { name: 'project', label: 'Project', field: 'project', align: 'left', sortable: false },
      { name: 'lead_source', label: 'Lead Source', field: 'lead_source', align: 'left', sortable: false },
      { name: 'status', label: 'Status', field: 'status', align: 'center', sortable: true },
      { name: 'priority', label: 'Priority', field: 'priority', align: 'center', sortable: true },
      { name: 'next_contact_date', label: 'Next Contact', field: 'next_contact_date', align: 'left', sortable: true },
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

    const formatStatus = (status) => {
      const statusMap = {
        new: 'New',
        contacted: 'Contacted',
        qualified: 'Qualified',
        converted: 'Converted',
        lost: 'Lost',
      }
      return statusMap[status] || status
    }

    const getStatusColor = (status) => {
      const colors = {
        new: 'info',
        contacted: 'primary',
        qualified: 'warning',
        converted: 'positive',
        lost: 'negative',
      }
      return colors[status] || 'grey'
    }

    const formatPriority = (priority) => {
      const priorityMap = {
        low: 'Low',
        medium: 'Medium',
        high: 'High',
      }
      return priorityMap[priority] || priority
    }

    const getPriorityColor = (priority) => {
      const colors = {
        low: 'grey',
        medium: 'warning',
        high: 'negative',
      }
      return colors[priority] || 'grey'
    }

    const loadProjects = async (searchQuery = '') => {
      loadingProjects.value = true
      try {
        const params = { is_active: true, per_page: 50 }
        if (searchQuery && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
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

    const loadLeadSources = async (searchQuery = '') => {
      loadingLeadSources.value = true
      try {
        const params = { is_active: true, per_page: 50 }
        if (searchQuery && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }
        const response = await api.get('/api/v1/lead-sources', { params })
        if (response.data.success) {
          leadSourceOptions.value = response.data.data || []
        }
      } catch (error) {
        console.error('Failed to load lead sources:', error)
        leadSourceOptions.value = []
      } finally {
        loadingLeadSources.value = false
      }
    }

    const debouncedLoadLeadSources = debounce((searchQuery) => {
      loadLeadSources(searchQuery)
    }, 400)

    const onLeadSourceSearch = (val, update) => {
      leadSourceSearchQuery.value = val
      update(() => {
        if (val === '') {
          loadLeadSources('')
        } else {
          debouncedLoadLeadSources(val)
        }
      })
    }

    const onLeadSourceFocus = () => {
      if (leadSourceOptions.value.length === 0 && !loadingLeadSources.value) {
        loadLeadSources('')
      }
    }

    const loadLeads = async (requestProps) => {
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
        if (filters.value.status) {
          params.status = filters.value.status
        }
        if (filters.value.priority) {
          params.priority = filters.value.priority
        }
        if (filters.value.project_id) {
          params.project_id = filters.value.project_id
        }
        if (filters.value.lead_source_id) {
          params.lead_source_id = filters.value.lead_source_id
        }

        const response = await api.get('/api/v1/leads', { params })

        if (response.data.success) {
          leads.value = response.data.data || []
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
          message: error.response?.data?.message || 'Failed to load leads',
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

      loadLeads({ pagination: pagination.value })
    }

    const onPageChange = (page) => {
      currentPage.value = page
      pagination.value = {
        ...pagination.value,
        page: page,
      }
      loadLeads({ pagination: pagination.value })
    }

    const debouncedLoadLeads = debounce((requestProps) => {
      loadLeads(requestProps)
    }, 400)

    const onFilterChange = () => {
      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        page: 1,
      }
      debouncedLoadLeads({ pagination: pagination.value })
    }

    const confirmDelete = (lead) => {
      selectedLead.value = lead
      deleteDialog.value = true
    }

    const deleteLead = async () => {
      if (!selectedLead.value) {
        return
      }

      loading.value = true
      try {
        const response = await api.delete(`/api/v1/leads/${selectedLead.value.id}`)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Lead deleted successfully',
            position: 'top',
          })
          deleteDialog.value = false
          selectedLead.value = null
          loadLeads({ pagination: pagination.value })
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to delete lead',
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
      loadLeads({ pagination: pagination.value })
    })

    return {
      leads,
      loading,
      filters,
      statusOptions,
      priorityOptions,
      projectOptions,
      leadSourceOptions,
      loadingProjects,
      loadingLeadSources,
      columns,
      pagination,
      deleteDialog,
      selectedLead,
      sort_by,
      descending,
      currentPage,
      lastPage,
      pagesNumber,
      formatStatus,
      getStatusColor,
      formatPriority,
      getPriorityColor,
      loadProjects,
      onProjectSearch,
      onProjectFocus,
      loadLeadSources,
      onLeadSourceSearch,
      onLeadSourceFocus,
      loadLeads,
      handleSort,
      onPageChange,
      onFilterChange,
      confirmDelete,
      deleteLead,
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

