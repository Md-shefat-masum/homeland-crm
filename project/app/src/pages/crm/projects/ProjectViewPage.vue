<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Project Details</div>
        <div class="text-body2 text-grey-7">View project information and activities</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/projects')"
        />
        <q-btn
          color="primary"
          label="Edit"
          icon="edit"
          @click="$router.push(`/crm/projects/${project?.id}/edit`)"
          class="q-ml-sm"
          unelevated
        />
      </div>
    </div>

    <div v-if="!loading && project" class="row q-gutter-md">
      <!-- Project Information -->
      <div class="col-12 col-md-8">
        <q-card>
          <q-card-section>
            <div class="row items-center q-mb-md">
              <div>
                <div class="text-h5">{{ project.name }}</div>
                <div class="text-body2 text-grey-7">{{ project.slug }}</div>
              </div>
            </div>

            <q-separator class="q-mb-md" />

            <div class="text-h6 q-mb-md">Basic Information</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Name</div>
                <div class="text-body1 q-mb-md">{{ project.name }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Slug</div>
                <div class="text-body1 q-mb-md">{{ project.slug }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Project Type</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="getProjectTypeColor(project.project_type)"
                    :label="project.project_type || '-'"
                  />
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Status</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="getStatusColor(project.status)"
                    :label="formatStatus(project.status)"
                  />
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Active</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="project.is_active ? 'positive' : 'negative'"
                    :label="project.is_active ? 'Active' : 'Inactive'"
                  />
                </div>
              </div>
            </div>

            <q-separator class="q-my-md" v-if="project.description" />

            <div v-if="project.description">
              <div class="text-h6 q-mb-md">Description</div>
              <div class="text-body1 q-mb-md">{{ project.description }}</div>
            </div>

            <q-separator class="q-my-md" v-if="project.address || project.address_text" />

            <div v-if="project.address || project.address_text">
              <div class="text-h6 q-mb-md">Address Information</div>
              <div class="row q-gutter-md">
                <div class="col-12 col-md-6" v-if="project.address">
                  <div class="text-body2 text-grey-7">Address</div>
                  <div class="text-body1 q-mb-md">{{ formatAddressLabel(project.address) }}</div>
                </div>
                <div class="col-12 col-md-6" v-if="project.address_text">
                  <div class="text-body2 text-grey-7">Address Details</div>
                  <div class="text-body1 q-mb-md">{{ project.address_text }}</div>
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-md-4">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Activity Summary</div>
            <div class="q-gutter-md">
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Leads</div>
                </div>
                <div class="col-auto">
                  <q-badge color="info" :label="project.leads?.length || project.leads_count || 0" />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <q-card class="q-mt-md">
          <q-card-section>
            <div class="text-h6 q-mb-md">Created Information</div>
            <div class="q-gutter-md">
              <div v-if="project.creator">
                <div class="text-body2 text-grey-7">Created By</div>
                <div class="text-body1">{{ project.creator.name }}</div>
              </div>
              <div v-if="project.created_at">
                <div class="text-body2 text-grey-7">Created At</div>
                <div class="text-body1">{{ formatDate(project.created_at) }}</div>
              </div>
              <div v-if="project.updater">
                <div class="text-body2 text-grey-7">Updated By</div>
                <div class="text-body1">{{ project.updater.name }}</div>
              </div>
              <div v-if="project.updated_at">
                <div class="text-body2 text-grey-7">Updated At</div>
                <div class="text-body1">{{ formatDate(project.updated_at) }}</div>
              </div>
            </div>
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
  name: 'ProjectViewPage',
  setup() {
    const route = useRoute()

    const project = ref(null)
    const loading = ref(false)

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

    const loadProject = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/crm-projects/${route.params.id}`)
        if (response.data.success) {
          project.value = response.data.data
        }
      } catch (error) {
        console.error('Failed to load project:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadProject()
    })

    return {
      project,
      loading,
      formatAddressLabel,
      formatStatus,
      getStatusColor,
      getProjectTypeColor,
      formatDate,
    }
  },
})
</script>
