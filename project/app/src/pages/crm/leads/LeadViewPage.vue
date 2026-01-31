<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Lead Details</div>
        <div class="text-body2 text-grey-7">View lead information and activities</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/leads')"
        />
        <q-btn
          color="primary"
          label="Edit"
          icon="edit"
          @click="$router.push(`/crm/leads/${lead?.id}/edit`)"
          class="q-ml-sm"
          unelevated
        />
      </div>
    </div>

    <div v-if="!loading && lead" class="row q-gutter-md">
      <!-- Lead Information -->
      <div class="col-12 col-md-8">
        <q-card>
          <q-card-section>
            <div class="row items-center q-mb-md">
              <div>
                <div class="text-h5">Lead #{{ lead.id }}</div>
                <div class="text-body2 text-grey-7">{{ lead.customer?.name }}</div>
              </div>
            </div>

            <q-separator class="q-mb-md" />

            <div class="text-h6 q-mb-md">Basic Information</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Customer</div>
                <div class="text-body1 q-mb-md">
                  {{ lead.customer?.name }} - {{ lead.customer?.mobile }}
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Status</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="getStatusColor(lead.status)"
                    :label="formatStatus(lead.status)"
                  />
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Priority</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="getPriorityColor(lead.priority)"
                    :label="formatPriority(lead.priority)"
                  />
                </div>
              </div>
              <div class="col-12 col-md-6" v-if="lead.next_contact_date">
                <div class="text-body2 text-grey-7">Next Contact Date</div>
                <div class="text-body1 q-mb-md">{{ formatDate(lead.next_contact_date) }}</div>
              </div>
            </div>

            <q-separator class="q-my-md" />

            <div class="text-h6 q-mb-md">Lead Source & Project</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6" v-if="lead.customer_lead_source || lead.lead_source">
                <div class="text-body2 text-grey-7">Lead Source</div>
                <div class="text-body1 q-mb-md">
                  {{ lead.customer_lead_source?.title || lead.lead_source || '-' }}
                </div>
              </div>
              <div class="col-12 col-md-6" v-if="lead.project">
                <div class="text-body2 text-grey-7">Project</div>
                <div class="text-body1 q-mb-md">{{ lead.project.name }}</div>
              </div>
              <div class="col-12 col-md-6" v-if="lead.interestedType">
                <div class="text-body2 text-grey-7">Interested Type</div>
                <div class="text-body1 q-mb-md">{{ lead.interestedType.name }}</div>
              </div>
            </div>

            <q-separator class="q-my-md" v-if="lead.customer_requirement || lead.preferred_area" />

            <div v-if="lead.customer_requirement || lead.preferred_area">
              <div class="text-h6 q-mb-md">Requirements</div>
              <div class="row q-gutter-md">
                <div class="col-12" v-if="lead.customer_requirement">
                  <div class="text-body2 text-grey-7">Customer Requirement</div>
                  <div class="text-body1 q-mb-md">{{ lead.customer_requirement }}</div>
                </div>
                <div class="col-12 col-md-6" v-if="lead.preferred_area">
                  <div class="text-body2 text-grey-7">Preferred Area</div>
                  <div class="text-body1 q-mb-md">{{ lead.preferred_area }}</div>
                </div>
              </div>
            </div>

            <q-separator class="q-my-md" v-if="lead.remarks" />

            <div v-if="lead.remarks">
              <div class="text-h6 q-mb-md">Remarks</div>
              <div class="text-body1 q-mb-md">{{ lead.remarks }}</div>
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
                  <div class="text-body2 text-grey-7">Notes</div>
                </div>
                <div class="col-auto">
                  <q-badge color="info" :label="lead.notes?.length || 0" />
                </div>
              </div>
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Follow-ups</div>
                </div>
                <div class="col-auto">
                  <q-badge color="warning" :label="lead.follow_ups?.length || 0" />
                </div>
              </div>
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Call Logs</div>
                </div>
                <div class="col-auto">
                  <q-badge color="positive" :label="lead.call_logs?.length || 0" />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <q-card class="q-mt-md">
          <q-card-section>
            <div class="text-h6 q-mb-md">Created Information</div>
            <div class="q-gutter-md">
              <div v-if="lead.creator">
                <div class="text-body2 text-grey-7">Created By</div>
                <div class="text-body1">{{ lead.creator.name }}</div>
              </div>
              <div v-if="lead.created_at">
                <div class="text-body2 text-grey-7">Created At</div>
                <div class="text-body1">{{ formatDate(lead.created_at) }}</div>
              </div>
              <div v-if="lead.updater">
                <div class="text-body2 text-grey-7">Updated By</div>
                <div class="text-body1">{{ lead.updater.name }}</div>
              </div>
              <div v-if="lead.updated_at">
                <div class="text-body2 text-grey-7">Updated At</div>
                <div class="text-body1">{{ formatDate(lead.updated_at) }}</div>
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
  name: 'LeadViewPage',
  setup() {
    const route = useRoute()

    const lead = ref(null)
    const loading = ref(false)

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

    const loadLead = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/leads/${route.params.id}`)
        if (response.data.success) {
          lead.value = response.data.data
        }
      } catch (error) {
        console.error('Failed to load lead:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadLead()
    })

    return {
      lead,
      loading,
      formatDate,
      formatStatus,
      getStatusColor,
      formatPriority,
      getPriorityColor,
    }
  },
})
</script>
