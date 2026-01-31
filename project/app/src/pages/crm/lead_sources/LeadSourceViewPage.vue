<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Lead Source Details</div>
        <div class="text-body2 text-grey-7">View lead source information and activities</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/lead_sources')"
        />
        <q-btn
          color="primary"
          label="Edit"
          icon="edit"
          @click="$router.push(`/crm/lead_sources/${leadSource?.id}/edit`)"
          class="q-ml-sm"
          unelevated
        />
      </div>
    </div>

    <div v-if="!loading && leadSource" class="row q-gutter-md">
      <!-- Lead Source Information -->
      <div class="col-12 col-md-8">
        <q-card>
          <q-card-section>
            <div class="row items-center q-mb-md">
              <div>
                <div class="text-h5">{{ leadSource.title }}</div>
              </div>
            </div>

            <q-separator class="q-mb-md" />

            <div class="text-h6 q-mb-md">Basic Information</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Title</div>
                <div class="text-body1 q-mb-md">{{ leadSource.title }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Status</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="leadSource.is_active ? 'positive' : 'negative'"
                    :label="leadSource.is_active ? 'Active' : 'Inactive'"
                  />
                </div>
              </div>
            </div>

            <q-separator class="q-my-md" v-if="leadSource.description" />

            <div v-if="leadSource.description">
              <div class="text-h6 q-mb-md">Description</div>
              <div class="text-body1 q-mb-md">{{ leadSource.description }}</div>
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
                  <q-badge color="info" :label="leadSource.leads?.length || leadSource.leads_count || 0" />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <q-card class="q-mt-md">
          <q-card-section>
            <div class="text-h6 q-mb-md">Created Information</div>
            <div class="q-gutter-md">
              <div v-if="leadSource.creator">
                <div class="text-body2 text-grey-7">Created By</div>
                <div class="text-body1">{{ leadSource.creator.name }}</div>
              </div>
              <div v-if="leadSource.created_at">
                <div class="text-body2 text-grey-7">Created At</div>
                <div class="text-body1">{{ formatDate(leadSource.created_at) }}</div>
              </div>
              <div v-if="leadSource.updater">
                <div class="text-body2 text-grey-7">Updated By</div>
                <div class="text-body1">{{ leadSource.updater.name }}</div>
              </div>
              <div v-if="leadSource.updated_at">
                <div class="text-body2 text-grey-7">Updated At</div>
                <div class="text-body1">{{ formatDate(leadSource.updated_at) }}</div>
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
  name: 'LeadSourceViewPage',
  setup() {
    const route = useRoute()

    const leadSource = ref(null)
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

    const loadLeadSource = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/lead-sources/${route.params.id}`)
        if (response.data.success) {
          leadSource.value = response.data.data
        }
      } catch (error) {
        console.error('Failed to load lead source:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadLeadSource()
    })

    return {
      leadSource,
      loading,
      formatDate,
    }
  },
})
</script>

