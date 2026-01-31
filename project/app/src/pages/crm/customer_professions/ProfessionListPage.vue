<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Professions</div>
        <div class="text-body2 text-grey-7">Manage customer professions</div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          label=""
          icon="add"
          @click="$router.push('/crm/customer_professions/create')"
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
              v-model="filters.type"
              :options="typeOptions"
              label="Type"
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
          <div class="q-mt-md">Loading professions...</div>
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
                v-for="profession in professions"
                :key="profession.id"
                style="border-bottom: 1px solid #eee"
                class="table-row-hover"
              >
                <td style="padding: 12px">{{ profession.id }}</td>
                <td style="padding: 12px">{{ getProfessionDisplayName(profession) }}</td>
                <td style="padding: 12px">
                  <q-badge :color="getTypeColor(profession.type)" :label="profession.type" />
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    v-if="profession.company_name"
                    color="info"
                    :label="profession.company_name"
                  />
                  <span v-else style="color: #999">-</span>
                </td>
                <td style="padding: 12px; text-align: center">
                  <q-btn
                    flat
                    dense
                    round
                    icon="edit"
                    color="primary"
                    size="sm"
                    @click="$router.push(`/crm/customer_professions/${profession.id}/edit`)"
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
                    @click="confirmDelete(profession)"
                  >
                    <q-tooltip>Delete</q-tooltip>
                  </q-btn>
                </td>
              </tr>
              <tr v-if="professions.length === 0">
                <td :colspan="columns.length" style="padding: 40px; text-align: center; color: #999">
                  No professions found
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
          <span class="q-ml-sm">Are you sure you want to delete this profession?</span>
        </q-card-section>

        <q-card-section v-if="selectedProfession">
          <div><strong>Title/Type:</strong> {{ getProfessionDisplayName(selectedProfession) }}</div>
          <div><strong>Category:</strong> {{ selectedProfession.type }}</div>
          <div v-if="selectedProfession.company_name"><strong>Company:</strong> {{ selectedProfession.company_name }}</div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn flat label="Delete" color="negative" @click="deleteProfession" />
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
  name: 'ProfessionListPage',
  setup() {
    const $q = useQuasar()

    const professions = ref([])
    const loading = ref(false)
    const deleteDialog = ref(false)
    const selectedProfession = ref(null)
    const sort_by = ref('id')
    const descending = ref(true)

    const filters = ref({
      search: '',
      type: null,
    })

    const typeOptions = ['job', 'business', 'student', 'housewife']

    const columns = [
      { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
      { name: 'name', label: 'Title/Type', field: 'name', align: 'left', sortable: true },
      { name: 'type', label: 'Category', field: 'type', align: 'left', sortable: true },
      {
        name: 'company',
        label: 'Company',
        field: 'company_name',
        align: 'center',
        sortable: false,
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
        job: 'blue',
        business: 'green',
        student: 'orange',
        housewife: 'purple',
      }
      return colors[type] || 'grey'
    }

    const getProfessionDisplayName = (profession) => {
      if (profession.type === 'job' && profession.job_title) {
        return profession.job_title
      }
      if (profession.type === 'business' && profession.business_type) {
        return profession.business_type
      }
      if (profession.type === 'student') {
        return 'Student'
      }
      if (profession.type === 'housewife') {
        return 'Housewife'
      }
      return profession.type || '-'
    }

    const loadProfessions = async (requestProps) => {
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

        const response = await api.get('/api/v1/professions', { params })

        if (response.data.success) {
          // Handle both paginated and non-paginated responses
          if (response.data.data && Array.isArray(response.data.data)) {
            professions.value = response.data.data
            // If response has pagination metadata, use it
            if (response.data.current_page !== undefined) {
              currentPage.value = response.data.current_page || 1
              lastPage.value = response.data.last_page || 1
              pagination.value = {
                sortBy: sort_by.value,
                descending: descending.value,
                page: response.data.current_page || 1,
                rowsPerPage: response.data.per_page || 10,
                rowsNumber: response.data.total || response.data.data.length,
              }
            } else {
              // Non-paginated response - handle client-side pagination if needed
              professions.value = response.data.data
              pagination.value = {
                sortBy: sort_by.value,
                descending: descending.value,
                page: 1,
                rowsPerPage: 10,
                rowsNumber: response.data.data.length,
              }
              currentPage.value = 1
              lastPage.value = Math.ceil(response.data.data.length / 10) || 1
            }
          } else {
            professions.value = []
          }
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load professions',
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

      loadProfessions({ pagination: pagination.value })
    }

    const onPageChange = (page) => {
      currentPage.value = page
      pagination.value = {
        ...pagination.value,
        page: page,
      }
      loadProfessions({ pagination: pagination.value })
    }

    // Debounced filter change handler (400ms)
    const debouncedLoadProfessions = debounce((requestProps) => {
      loadProfessions(requestProps)
    }, 400)

    const onFilterChange = () => {
      // Reset to page 1 when filters change, but keep current sort
      currentPage.value = 1
      pagination.value = {
        ...pagination.value,
        page: 1,
      }
      debouncedLoadProfessions({ pagination: pagination.value })
    }

    const confirmDelete = (profession) => {
      selectedProfession.value = profession
      deleteDialog.value = true
    }

    const deleteProfession = async () => {
      if (!selectedProfession.value) {
        return
      }

      loading.value = true
      try {
        const response = await api.delete(`/api/v1/professions/${selectedProfession.value.id}`)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Profession deleted successfully',
            position: 'top',
          })
          deleteDialog.value = false
          selectedProfession.value = null
          loadProfessions({ pagination: pagination.value })
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to delete profession',
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
      loadProfessions({ pagination: pagination.value })
    })

    return {
      professions,
      loading,
      filters,
      typeOptions,
      columns,
      pagination,
      deleteDialog,
      selectedProfession,
      sort_by,
      descending,
      currentPage,
      lastPage,
      pagesNumber,
      getTypeColor,
      getProfessionDisplayName,
      loadProfessions,
      handleSort,
      onPageChange,
      onFilterChange,
      confirmDelete,
      deleteProfession,
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
