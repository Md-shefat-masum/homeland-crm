<template>
  <div>
    <div v-if="store.loading.history" class="text-center q-pa-lg">
      <q-spinner color="primary" size="3em" />
      <div class="q-mt-md text-grey-7">Loading follow-ups...</div>
    </div>

    <div v-else-if="followUps.length === 0" class="text-center q-pa-xl text-grey-6">
      <q-icon name="schedule" size="48px" />
      <div class="text-h6 q-mt-md">No Follow-ups Found</div>
      <div class="text-body2 q-mt-sm">This customer has no follow-ups yet.</div>
    </div>

    <div v-else>
      <q-list separator>
        <q-item v-for="followUp in followUps" :key="followUp.id" class="q-pa-md">
          <q-item-section avatar>
            <q-avatar color="info" text-color="white" icon="schedule" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ followUp.title || 'Follow-up' }}</q-item-label>
            <q-item-label caption>
              {{ followUp.notes || 'No description' }}
            </q-item-label>
            <q-item-label caption class="q-mt-xs">
              <span v-if="followUp.follow_up_date">Date: {{ formatDate(followUp.follow_up_date) }}</span>
              <span v-if="followUp.creator"> • By: {{ followUp.creator.name }}</span>
            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-badge :color="getStatusColor(followUp.status)" :label="followUp.status || 'pending'" />
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </div>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'

export default {
  name: 'TabFollowUps',
  computed: {
    store() {
      return useNewContactStore()
    },
    followUps() {
      return this.store.customerHistory.followUps || []
    }
  },
  methods: {
    formatDate(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString()
    },
    getStatusColor(status) {
      const colors = {
        pending: 'orange',
        completed: 'positive',
        cancelled: 'negative'
      }
      return colors[status] || 'grey'
    }
  }
}
</script>

