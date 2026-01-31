<template>
  <div>
    <div v-if="store.loading.history" class="text-center q-pa-lg">
      <q-spinner color="primary" size="3em" />
      <div class="q-mt-md text-grey-7">Loading call logs...</div>
    </div>

    <div v-else-if="callLogs.length === 0" class="text-center q-pa-xl text-grey-6">
      <q-icon name="phone" size="48px" />
      <div class="text-h6 q-mt-md">No Call Logs Found</div>
      <div class="text-body2 q-mt-sm">This customer has no call logs yet.</div>
    </div>

    <div v-else>
      <q-list separator>
        <q-item v-for="callLog in callLogs" :key="callLog.id" class="q-pa-md">
          <q-item-section avatar>
            <q-avatar color="green" text-color="white" icon="phone" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ callLog.title || 'Call' }}</q-item-label>
            <q-item-label caption>
              {{ callLog.notes || 'No notes' }}
            </q-item-label>
            <q-item-label caption class="q-mt-xs">
              <span v-if="callLog.call_date">Date: {{ formatDateTime(callLog.call_date) }}</span>
              <span v-if="callLog.duration"> • Duration: {{ callLog.duration }}s</span>
              <span v-if="callLog.creator"> • By: {{ callLog.creator.name }}</span>
            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-badge :color="getStatusColor(callLog.status)" :label="callLog.status || 'completed'" />
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </div>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'

export default {
  name: 'TabCallLogs',
  computed: {
    store() {
      return useNewContactStore()
    },
    callLogs() {
      return this.store.customerHistory.callLogs || []
    }
  },
  methods: {
    formatDateTime(date) {
      if (!date) return ''
      const d = new Date(date)
      return d.toLocaleString()
    },
    getStatusColor(status) {
      const colors = {
        completed: 'positive',
        missed: 'negative',
        busy: 'orange',
        no_answer: 'grey'
      }
      return colors[status] || 'primary'
    }
  }
}
</script>

