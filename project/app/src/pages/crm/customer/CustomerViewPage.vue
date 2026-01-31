<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Customer Details</div>
        <div class="text-body2 text-grey-7">View customer information and activities</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/customer')"
        />
        <q-btn
          color="primary"
          label="Edit"
          icon="edit"
          @click="$router.push(`/crm/customer/${customer?.id}/edit`)"
          class="q-ml-sm"
          unelevated
        />
      </div>
    </div>

    <div v-if="!loading && customer" class="row q-gutter-md">
      <!-- Customer Information -->
      <div class="col-12 col-md-8">
        <q-card>
          <q-card-section>
            <div class="row items-center q-mb-md">
              <AppAvatar :image="customer.image" :alt="customer.name" size="80px" avatar-class="q-mr-md" />
              <div>
                <div class="text-h5">{{ customer.name }}</div>
                <div class="text-body2 text-grey-7">{{ customer.mobile }}</div>
                <div v-if="customer.email" class="text-body2 text-grey-7">{{ customer.email }}</div>
              </div>
            </div>

            <q-separator class="q-mb-md" />

            <div class="text-h6 q-mb-md">Basic Information</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Name</div>
                <div class="text-body1 q-mb-md">{{ customer.name }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Mobile</div>
                <div class="text-body1 q-mb-md">{{ customer.mobile }}</div>
              </div>
              <div class="col-12 col-md-6" v-if="customer.email">
                <div class="text-body2 text-grey-7">Email</div>
                <div class="text-body1 q-mb-md">{{ customer.email }}</div>
              </div>
              <div class="col-12 col-md-6" v-if="customer.alternative_mobile">
                <div class="text-body2 text-grey-7">Alternative Mobile</div>
                <div class="text-body1 q-mb-md">{{ customer.alternative_mobile }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Status</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="customer.is_active ? 'positive' : 'negative'"
                    :label="customer.is_active ? 'Active' : 'Inactive'"
                  />
                </div>
              </div>
            </div>

            <q-separator class="q-my-md" />

            <div class="text-h6 q-mb-md">Group, Project & Profession</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-4" v-if="customer.customer_group">
                <div class="text-body2 text-grey-7">Customer Group</div>
                <div class="q-mb-md">
                  <q-badge
                    :style="{ backgroundColor: customer.customer_group.color || '#9e9e9e', color: '#fff' }"
                    :label="customer.customer_group.name"
                  />
                </div>
              </div>
              <div class="col-12 col-md-4" v-if="customer.project">
                <div class="text-body2 text-grey-7">Project</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="getProjectTypeColor(customer.project.project_type)"
                    :label="customer.project.name"
                  />
                  <q-badge
                    :color="getStatusColor(customer.project.status)"
                    :label="formatStatus(customer.project.status)"
                    class="q-ml-sm"
                  />
                </div>
              </div>
              <div class="col-12 col-md-4" v-if="customer.profession">
                <div class="text-body2 text-grey-7">Profession</div>
                <div class="text-body1 q-mb-md">
                  {{ getProfessionDisplayName(customer.profession) }}
                  <q-badge :color="getProfessionTypeColor(customer.profession.type)" :label="customer.profession.type" class="q-ml-sm" />
                </div>
              </div>
            </div>

            <q-separator class="q-my-md" />

            <div class="text-h6 q-mb-md">Address Information</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6" v-if="customer.current_address">
                <div class="text-body2 text-grey-7">Current Address</div>
                <div class="text-body1 q-mb-md">{{ customer.current_address.name }}</div>
              </div>
              <div class="col-12 col-md-6" v-if="customer.current_address_text">
                <div class="text-body2 text-grey-7">Address Details</div>
                <div class="text-body1 q-mb-md">{{ customer.current_address_text }}</div>
              </div>
              <div class="col-12 col-md-6" v-if="customer.nearest_market">
                <div class="text-body2 text-grey-7">Nearest Market</div>
                <div class="text-body1 q-mb-md">{{ customer.nearest_market }}</div>
              </div>
              <div class="col-12 col-md-6" v-if="customer.preferred_area">
                <div class="text-body2 text-grey-7">Preferred Area</div>
                <div class="text-body1 q-mb-md">{{ customer.preferred_area }}</div>
              </div>
            </div>

            <q-separator class="q-my-md" v-if="customer.target_real_estate || customer.notes" />

            <div v-if="customer.target_real_estate">
              <div class="text-h6 q-mb-md">Property Preferences</div>
              <div class="row q-gutter-md">
                <div class="col-12">
                  <div class="text-body2 text-grey-7">Target Real Estate</div>
                  <div class="text-body1 q-mb-md">{{ customer.target_real_estate }}</div>
                </div>
              </div>
            </div>

            <q-separator class="q-my-md" v-if="customer.notes" />

            <div v-if="customer.notes">
              <div class="text-h6 q-mb-md">Notes</div>
              <div class="text-body1 q-mb-md">{{ customer.notes }}</div>
            </div>

            <q-separator class="q-my-md" v-if="customer.info" />

            <div v-if="customer.info">
              <div class="text-h6 q-mb-md">Additional Information</div>
              <div class="row q-gutter-md">
                <div class="col-12 col-md-6" v-if="customer.info.date_of_birth">
                  <div class="text-body2 text-grey-7">Date of Birth</div>
                  <div class="text-body1 q-mb-md">{{ customer.info.date_of_birth }}</div>
                </div>
                <div class="col-12 col-md-6" v-if="customer.info.gender">
                  <div class="text-body2 text-grey-7">Gender</div>
                  <div class="text-body1 q-mb-md">{{ customer.info.gender }}</div>
                </div>
                <div class="col-12 col-md-6" v-if="customer.info.marital_status">
                  <div class="text-body2 text-grey-7">Marital Status</div>
                  <div class="text-body1 q-mb-md">{{ customer.info.marital_status }}</div>
                </div>
                <div class="col-12 col-md-6" v-if="customer.info.family_members">
                  <div class="text-body2 text-grey-7">Family Members</div>
                  <div class="text-body1 q-mb-md">{{ customer.info.family_members }}</div>
                </div>
                <div class="col-12 col-md-6" v-if="customer.info.income_range">
                  <div class="text-body2 text-grey-7">Income Range</div>
                  <div class="text-body1 q-mb-md">{{ customer.info.income_range }}</div>
                </div>
                <div class="col-12 col-md-6" v-if="customer.info.budget_range">
                  <div class="text-body2 text-grey-7">Budget Range</div>
                  <div class="text-body1 q-mb-md">{{ customer.info.budget_range }}</div>
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
                  <q-badge color="info" :label="customer.leads?.length || 0" />
                </div>
              </div>
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Notes</div>
                </div>
                <div class="col-auto">
                  <q-badge color="primary" :label="customer.customer_notes?.length || 0" />
                </div>
              </div>
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Follow-ups</div>
                </div>
                <div class="col-auto">
                  <q-badge color="warning" :label="customer.follow_ups?.length || 0" />
                </div>
              </div>
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Call Logs</div>
                </div>
                <div class="col-auto">
                  <q-badge color="positive" :label="customer.call_logs?.length || 0" />
                </div>
              </div>
              <div class="row items-center">
                <div class="col">
                  <div class="text-body2 text-grey-7">Assignments</div>
                </div>
                <div class="col-auto">
                  <q-badge color="purple" :label="customer.customer_assignments?.length || 0" />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <q-card class="q-mt-md">
          <q-card-section>
            <div class="text-h6 q-mb-md">Created Information</div>
            <div class="q-gutter-md">
              <div v-if="customer.creator">
                <div class="text-body2 text-grey-7">Created By</div>
                <div class="text-body1">{{ customer.creator.name }}</div>
              </div>
              <div v-if="customer.created_at">
                <div class="text-body2 text-grey-7">Created At</div>
                <div class="text-body1">{{ formatDate(customer.created_at) }}</div>
              </div>
              <div v-if="customer.updater">
                <div class="text-body2 text-grey-7">Updated By</div>
                <div class="text-body1">{{ customer.updater.name }}</div>
              </div>
              <div v-if="customer.updated_at">
                <div class="text-body2 text-grey-7">Updated At</div>
                <div class="text-body1">{{ formatDate(customer.updated_at) }}</div>
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
import AppAvatar from 'components/AppAvatar.vue'

export default defineComponent({
  name: 'CustomerViewPage',
  components: {
    AppAvatar,
  },
  setup() {
    const route = useRoute()

    const customer = ref(null)
    const loading = ref(false)

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

    const getProfessionTypeColor = (type) => {
      const colors = {
        job: 'blue',
        business: 'green',
        student: 'orange',
        housewife: 'purple',
      }
      return colors[type] || 'grey'
    }

    const getProjectTypeColor = (type) => {
      const colors = {
        apartment: 'blue',
        land: 'green',
        commercial: 'orange',
        other: 'grey',
      }
      return colors[type] || 'grey'
    }

    const formatStatus = (status) => {
      if (!status) return '-'
      const statusMap = {
        planning: 'Planning',
        ongoing: 'Ongoing',
        completed: 'Completed',
        on_hold: 'On Hold',
      }
      return statusMap[status] || status
    }

    const getStatusColor = (status) => {
      const colors = {
        planning: 'info',
        ongoing: 'positive',
        completed: 'primary',
        on_hold: 'warning',
      }
      return colors[status] || 'grey'
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

    const loadCustomer = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/customers/${route.params.id}`)
        if (response.data.success) {
          customer.value = response.data.data
        }
      } catch (error) {
        console.error('Failed to load customer:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadCustomer()
    })

    return {
      customer,
      loading,
      getProfessionDisplayName,
      getProfessionTypeColor,
      formatStatus,
      getStatusColor,
      getProjectTypeColor,
      formatDate,
    }
  },
})
</script>

