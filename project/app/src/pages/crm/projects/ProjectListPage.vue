<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Project Management</div>
        <div class="text-body2 text-grey-7">Manage projects</div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          label=""
          icon="add"
          @click="$router.push('/crm/projects/create')"
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
              v-model="filters.status"
              :options="statusOptions"
              label="Status"
              outlined
              dense
              clearable
              @update:model-value="onFilterChange"
            />
          </div>
          <div class="col-12 col-md-3">
            <q-select
              v-model="filters.project_type"
              :options="projectTypeOptions"
              label="Project Type"
              outlined
              dense
              clearable
              @update:model-value="onFilterChange"
            />
          </div>
          <div class="col-12 col-md-2">
            <q-select
              v-model="filters.is_active"
              :options="activeOptions"
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
          <div class="q-mt-md">Loading projects...</div>
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
                v-for="project in projects"
                :key="project.id"
                style="border-bottom: 1px solid #eee"
                class="table-row-hover"
              >
                <td style="padding: 12px">{{ project.id }}</td>
                <td style="padding: 12px">
                  <div class="text-weight-medium">{{ project.name }}</div>
                  <div class="text-caption text-grey-7">{{ project.slug }}</div>
                </td>
                <td style="padding: 12px">{{ project.description || '-' }}</td>
                <td style="padding: 12px">
                  <div v-if="project.address">
                    {{ formatAddressLabel(project.address) }}
                  </div>
                  <div v-else-if="project.address_text">{{ project.address_text }}</div>
                  <span v-else class="text-grey-7">-</span>
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    :color="getProjectTypeColor(project.project_type)"
                    :label="project.project_type || '-'"
                  />
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    :color="getStatusColor(project.status)"
                    :label="formatStatus(project.status)"
                  />
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge color="info" :label="project.leads_count || 0" />
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    :color="project.is_active ? 'positive' : 'negative'"
                    :label="project.is_active ? 'Active' : 'Inactive'"
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
                    @click="$router.push(`/crm/projects/${project.id}`)"
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
                    @click="$router.push(`/crm/projects/${project.id}/edit`)"
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
                    :disable="project.leads_count > 0"
                    @click="confirmDelete(project)"
                  >
                    <q-tooltip>
                      {{ project.leads_count > 0 ? 'Cannot delete: Has leads' : 'Delete' }}
                    </q-tooltip>
                  </q-btn>
                </td>
              </tr>
              <tr v-if="projects.length === 0">
                <td :colspan="columns.length" style="padding: 40px; text-align: center; color: #999">
                  No projects found
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
          <span class="q-ml-sm">Are you sure you want to delete this project?</span>
        </q-card-section>

        <q-card-section v-if="selectedProject">
          <div><strong>Name:</strong> {{ selectedProject.name }}</div>
          <div v-if="selectedProject.description">
            <strong>Description:</strong> {{ selectedProject.description }}
          </div>
          <div v-if="selectedProject.leads_count > 0" class="text-negative q-mt-sm">
            <q-icon name="error" /> This project has {{ selectedProject.leads_count }} lead(s)
            connected. Cannot delete.
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            flat
            label="Delete"
            color="negative"
            @click="deleteProject"
            :disable="selectedProject?.leads_count > 0"
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
  name: 'ProjectListPage',
  setup() {
    const $q = useQuasar()

    const projects = ref([])
    const loading = ref(false)
    const deleteDialog = ref(false)
    const selectedProject = ref(null)
    const sort_by = ref('id')
    const descending = ref(true)

    const filters = ref({
      search: '',
      status: null,
      project_type: null,
      is_active: null,
    })

    const statusOptions = [
      { label: 'Planning', value: 'planning' },
      { label: 'Ongoing', value: 'ongoing' },
      { label: 'Completed', value: 'completed' },
      { label: 'On Hold', value: 'on_hold' },
    ]

    const projectTypeOptions = [
      { label: 'Apartment', value: 'apartment' },
      { label: 'Land', value: 'land' },
      { label: 'Commercial', value: 'commercial' },
      { label: 'Other', value: 'other' },
    ]

    const activeOptions = [
      { label: 'Active', value: true },
      { label: 'Inactive', value: false },
    ]

    const columns = [
      { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
      { name: 'name', label: 'Name', field: 'name', align: 'left', sortable: true },
      {
        name: 'description',
        label: 'Description',
        field: 'description',
        align: 'left',
        sortable: false,
      },
      { name: 'address', label: 'Address', field: 'address', align: 'left', sortable: false },
      {
        name: 'project_type',
        label: 'Type',
        field: 'project_type',
        align: 'center',
        sortable: true,
      },
      { name: 'status', label: 'Status', field: 'status', align: 'center', sortable: true },
      {
        name: 'leads_count',
        label: 'Leads',
        field: 'leads_count',
        align: 'center',
        sortable: true,
      },
      {
        name: 'is_active',
        label: 'Active',
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

    const formatAddressLabel = (address) => {
      if (!address) return '-'
      const parts = []
      if (address.name) parts.push(address.name)
      if (address.parent && address.parent.name) parts.push(address.parent.name)
      return parts.join(', ') || '-'
    }

    const formatStatus = (status) => {
      const statusMap = {
        planning: 'Planning',
        ongoing: 'Ongoing',
        completed: 'Completed',
        on_hold: 'On Hold',
      }
      return statusMap[status] || status
    }

    const getStatusColor = (status) => {
      const colorMap = {
        planning: 'blue',
        ongoing: 'orange',
        completed: 'positive',
        on_hold: 'grey',
      }
      return colorMap[status] || 'grey'
    }

    const getProjectTypeColor = (type) => {
      const colorMap = {
        apartment: 'purple',
        land: 'green',
        commercial: 'blue',
        other: 'grey',
      }
      return colorMap[type] || 'grey'
    }

    const loadProjects = async (requestProps) => {
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
        if (filters.value.status !== null && filters.value.status !== '') {
          params.status = filters.value.status
        }
        if (filters.value.project_type !== null && filters.value.project_type !== '') {
          params.project_type = filters.value.project_type
        }
        if (filters.value.is_active !== null && filters.value.is_active !== '') {
          params.is_active = filters.value.is_active
        }

        const response = await api.get('/api/v1/crm-projects', { params })

        if (response.data.success) {
          projects.value = response.data.data || []
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
          message: error.response?.data?.message || 'Failed to load projects',
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

      loadProjects({ pagination: pagination.value })
    }

    const onPageChange = (page) => {
      currentPage.value = page
      pagination.value = {
        ...pagination.value,
        page: page,
      }
      loadProjects({ pagination: pagination.value })
    }

    const debouncedLoadProjects = debounce((requestProps) => {
      loadProjects(requestProps)
    }, 400)

    const onFilterChange = () => {
      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        page: 1,
      }
      debouncedLoadProjects({ pagination: pagination.value })
    }

    const confirmDelete = (project) => {
      selectedProject.value = project
      deleteDialog.value = true
    }

    const deleteProject = async () => {
      if (!selectedProject.value || selectedProject.value.leads_count > 0) {
        return
      }

      loading.value = true
      try {
        const response = await api.delete(`/api/v1/crm-projects/${selectedProject.value.id}`)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Project deleted successfully',
            position: 'top',
          })
          deleteDialog.value = false
          selectedProject.value = null
          loadProjects({ pagination: pagination.value })
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to delete project',
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
      loadProjects({ pagination: pagination.value })
    })

    return {
      projects,
      loading,
      filters,
      statusOptions,
      projectTypeOptions,
      activeOptions,
      columns,
      pagination,
      deleteDialog,
      selectedProject,
      sort_by,
      descending,
      currentPage,
      lastPage,
      pagesNumber,
      formatAddressLabel,
      formatStatus,
      getStatusColor,
      getProjectTypeColor,
      loadProjects,
      handleSort,
      onPageChange,
      onFilterChange,
      confirmDelete,
      deleteProject,
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


