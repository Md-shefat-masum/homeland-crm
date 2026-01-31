<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Interested Type Details</div>
        <div class="text-body2 text-grey-7">View interested type information</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/interested_types')"
        />
        <q-btn
          color="primary"
          label="Edit"
          icon="edit"
          @click="$router.push(`/crm/interested_types/${interestedType?.id}/edit`)"
          class="q-ml-sm"
          unelevated
        />
      </div>
    </div>

    <div v-if="!loading && interestedType" class="row q-gutter-md">
      <div class="col-12">
        <q-card>
          <q-card-section>
            <div class="row items-center q-mb-md">
              <div
                v-if="interestedType.color"
                :style="{ width: '40px', height: '40px', backgroundColor: interestedType.color, border: '2px solid #ddd', borderRadius: '8px', marginRight: '16px' }"
              />
              <div>
                <div class="text-h6">{{ interestedType.name }}</div>
                <div class="text-body2 text-grey-7">{{ interestedType.description || 'No description' }}</div>
              </div>
            </div>

            <q-separator class="q-mb-md" />

            <div class="text-h6 q-mb-md">Basic Information</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Name</div>
                <div class="text-body1 q-mb-md">{{ interestedType.name }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Status</div>
                <div class="q-mb-md">
                  <q-badge
                    :color="interestedType.is_active ? 'positive' : 'negative'"
                    :label="interestedType.is_active ? 'Active' : 'Inactive'"
                  />
                </div>
              </div>
              <div class="col-12">
                <div class="text-body2 text-grey-7">Description</div>
                <div class="text-body1 q-mb-md">{{ interestedType.description || '-' }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Color</div>
                <div class="q-mb-md">
                  <div v-if="interestedType.color" style="display: flex; align-items: center; gap: 8px;">
                    <div
                      :style="{ width: '30px', height: '30px', backgroundColor: interestedType.color, border: '1px solid #ccc', borderRadius: '4px' }"
                    />
                    <span>{{ interestedType.color }}</span>
                  </div>
                  <span v-else>-</span>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Sort Order</div>
                <div class="text-body1 q-mb-md">{{ interestedType.sort_order || 0 }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Total Leads</div>
                <div class="q-mb-md">
                  <q-badge color="info" :label="interestedType.leads_count || 0" />
                </div>
              </div>
            </div>

            <q-separator class="q-my-md" />

            <div class="text-h6 q-mb-md">Audit Information</div>
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6" v-if="interestedType.creator">
                <div class="text-body2 text-grey-7">Created By</div>
                <div class="text-body1 q-mb-md">{{ interestedType.creator.name }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Created At</div>
                <div class="text-body1 q-mb-md">{{ formatDate(interestedType.created_at) }}</div>
              </div>
              <div class="col-12 col-md-6" v-if="interestedType.updater">
                <div class="text-body2 text-grey-7">Updated By</div>
                <div class="text-body1 q-mb-md">{{ interestedType.updater.name }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-body2 text-grey-7">Updated At</div>
                <div class="text-body1 q-mb-md">{{ formatDate(interestedType.updated_at) }}</div>
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
import { useRoute, useRouter } from 'vue-router'
import { api } from 'boot/axios'

export default defineComponent({
  name: 'InterestedTypeViewPage',
  setup() {
    const route = useRoute()
    const router = useRouter()

    const interestedType = ref(null)
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

    const loadInterestedType = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/interested-types/${route.params.id}`)
        if (response.data.success) {
          interestedType.value = response.data.data
        }
      } catch (error) {
        console.error('Failed to load interested type:', error)
        router.push('/crm/interested_types')
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadInterestedType()
    })

    return {
      interestedType,
      loading,
      formatDate,
    }
  },
})
</script>

