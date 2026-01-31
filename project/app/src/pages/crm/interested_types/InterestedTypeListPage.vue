<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Interested Types</div>
        <div class="text-body2 text-grey-7">Manage customer interest types</div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          label="Add Interested Type"
          icon="add"
          @click="$router.push('/crm/interested_types/create')"
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
              :options="statusOptions"
              label="Status"
              outlined
              dense
              clearable
              emit-value
              map-options
              @update:model-value="onFilterChange"
            />
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center q-pa-lg">
          <q-spinner color="primary" size="3em" />
          <div class="q-mt-md">Loading interested types...</div>
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
                v-for="interestedType in interestedTypes"
                :key="interestedType.id"
                style="border-bottom: 1px solid #eee"
                class="table-row-hover"
              >
                <td style="padding: 12px">{{ interestedType.id }}</td>
                <td style="padding: 12px">
                  <div style="display: flex; align-items: center; gap: 8px">
                    <q-badge
                      v-if="interestedType.color"
                      :style="{ backgroundColor: interestedType.color, color: '#fff', width: '20px', height: '20px', borderRadius: '50%' }"
                    />
                    <span class="text-weight-medium">{{ interestedType.name }}</span>
                  </div>
                </td>
                <td style="padding: 12px">{{ interestedType.description || '-' }}</td>
                <td style="padding: 12px; text-align: center">
                  <q-badge color="info" :label="interestedType.leads_count || 0" />
                </td>
                <td style="padding: 12px; text-align: center">{{ interestedType.sort_order || 0 }}</td>
                <td style="padding: 12px; text-align: center">
                  <q-badge
                    :color="interestedType.is_active ? 'positive' : 'negative'"
                    :label="interestedType.is_active ? 'Active' : 'Inactive'"
                  />
                </td>
                <td style="padding: 12px">{{ formatDate(interestedType.created_at) }}</td>
                <td style="padding: 12px; text-align: center">
                  <q-btn
                    flat
                    round
                    icon="visibility"
                    color="info"
                    size="sm"
                    @click="$router.push(`/crm/interested_types/${interestedType.id}`)"
                  >
                    <q-tooltip>View</q-tooltip>
                  </q-btn>
                  <q-btn
                    flat
                    round
                    icon="edit"
                    color="primary"
                    size="sm"
                    @click="$router.push(`/crm/interested_types/${interestedType.id}/edit`)"
                  >
                    <q-tooltip>Edit</q-tooltip>
                  </q-btn>
                  <q-btn
                    flat
                    round
                    icon="delete"
                    color="negative"
                    size="sm"
                    :disable="interestedType.leads_count > 0"
                    @click="confirmDelete(interestedType)"
                  >
                    <q-tooltip>
                      {{ interestedType.leads_count > 0 ? 'Cannot delete: Has leads' : 'Delete' }}
                    </q-tooltip>
                  </q-btn>
                </td>
              </tr>
              <tr v-if="interestedTypes.length === 0">
                <td :colspan="columns.length" style="padding: 40px; text-align: center; color: #999">
                  No interested types found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="q-mt-md row justify-center">
          <q-pagination
            v-model="pagination.current_page"
            :max="pagination.last_page"
            :input="true"
            input-class="text-orange-10"
            @update:model-value="onPageChange"
          />
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from 'vue'
import { api } from 'boot/axios'
import { useQuasar } from 'quasar'
import { debounce } from 'lodash'

export default defineComponent({
  name: 'InterestedTypeListPage',
  setup() {
    const $q = useQuasar()
    const interestedTypes = ref([])
    const loading = ref(false)
    const filters = ref({
      search: '',
      is_active: null,
    })
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
    })
    const sort_by = ref('sort_order')
    const sort_order = ref('asc')

    const statusOptions = [
      { label: 'Active', value: true },
      { label: 'Inactive', value: false },
    ]

    const columns = [
      { name: 'id', label: 'ID', align: 'left', field: 'id', sortable: true },
      { name: 'name', label: 'Name', align: 'left', field: 'name', sortable: true },
      { name: 'description', label: 'Description', align: 'left', field: 'description', sortable: false },
      { name: 'leads_count', label: 'Leads', align: 'center', field: 'leads_count', sortable: true },
      { name: 'sort_order', label: 'Sort Order', align: 'center', field: 'sort_order', sortable: true },
      { name: 'is_active', label: 'Status', align: 'center', field: 'is_active', sortable: true },
      { name: 'created_at', label: 'Created At', align: 'left', field: 'created_at', sortable: true },
      { name: 'actions', label: 'Actions', align: 'center', field: 'actions', sortable: false },
    ]

    const formatDate = (dateString) => {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      })
    }

    const loadInterestedTypes = async () => {
      loading.value = true
      try {
        const params = {
          ...filters.value,
          page: pagination.value.current_page,
          per_page: pagination.value.per_page,
          sort_by: sort_by.value,
          sort_order: sort_order.value,
        }
        const response = await api.get('/api/v1/interested-types', { params })
        if (response.data.success) {
          interestedTypes.value = response.data.data
          pagination.value = response.data.pagination
        }
      } catch (error) {
        console.error('Failed to load interested types:', error)
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load interested types',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    const debouncedLoadInterestedTypes = debounce(loadInterestedTypes, 400)

    const onFilterChange = () => {
      pagination.value.current_page = 1
      debouncedLoadInterestedTypes()
    }

    const onPageChange = (page) => {
      pagination.value.current_page = page
      loadInterestedTypes()
    }

    const handleSort = (columnName) => {
      if (sort_by.value === columnName) {
        sort_order.value = sort_order.value === 'asc' ? 'desc' : 'asc'
      } else {
        sort_by.value = columnName
        sort_order.value = 'asc'
      }
      loadInterestedTypes()
    }

    const confirmDelete = (interestedType) => {
      $q.dialog({
        title: 'Confirm Delete',
        message: `Are you sure you want to delete "${interestedType.name}"?`,
        cancel: true,
        persistent: true,
      }).onOk(() => {
        deleteInterestedType(interestedType.id)
      })
    }

    const deleteInterestedType = async (id) => {
      try {
        const response = await api.delete(`/api/v1/interested-types/${id}`)
        if (response.data.success) {
          $q.notify({
            type: 'positive',
            message: response.data.message,
            position: 'top',
          })
          loadInterestedTypes()
        } else {
          $q.notify({
            type: 'negative',
            message: response.data.message || 'Failed to delete interested type',
            position: 'top',
          })
        }
      } catch (error) {
        console.error('Failed to delete interested type:', error)
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to delete interested type',
          position: 'top',
        })
      }
    }

    onMounted(() => {
      loadInterestedTypes()
    })

    watch(filters, debouncedLoadInterestedTypes, { deep: true })

    return {
      interestedTypes,
      loading,
      filters,
      pagination,
      sort_by,
      sort_order,
      statusOptions,
      columns,
      formatDate,
      onFilterChange,
      onPageChange,
      handleSort,
      confirmDelete,
    }
  },
})
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
}
.q-table th {
  font-weight: bold;
}
.table-row-hover:hover {
  background-color: #f0f0f0;
}
</style>

