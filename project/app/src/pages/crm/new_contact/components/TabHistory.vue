<template>
  <div>
    <div v-if="store.loading.history" class="text-center q-pa-lg">
      <q-spinner color="primary" size="3em" />
      <div class="q-mt-md text-grey-7">Loading history...</div>
    </div>

    <div v-else-if="allHistory.length === 0" class="text-center q-pa-xl text-grey-6">
      <q-icon name="history" size="48px" />
      <div class="text-h6 q-mt-md">No History Found</div>
      <div class="text-body2 q-mt-sm">This customer has no activity history yet.</div>
    </div>

    <div v-else>
      <q-timeline color="secondary">
        <q-timeline-entry
          v-for="item in sortedHistory"
          :key="item.id"
          :title="item.title"
          :subtitle="formatDateTime(item.date)"
          :icon="item.icon"
          :color="item.color"
        >
          <div>{{ item.description }}</div>
          <div v-if="item.meta" class="text-caption text-grey-7 q-mt-xs">
            {{ item.meta }}
          </div>
        </q-timeline-entry>
      </q-timeline>
    </div>
  </div>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'

export default {
  name: 'TabHistory',
  computed: {
    store() {
      return useNewContactStore()
    },
    allHistory() {
      const history = []
      const store = this.store.customerHistory

      // Add leads
      if (store.leads) {
        store.leads.forEach(lead => {
          history.push({
            id: `lead-${lead.id}`,
            type: 'lead',
            title: lead.title || `Lead #${lead.id}`,
            description: lead.description || '',
            date: lead.created_at || lead.updated_at,
            icon: 'trending_up',
            color: 'primary',
            meta: lead.project ? `Project: ${lead.project.name}` : null
          })
        })
      }

      // Add notes
      if (store.notes) {
        store.notes.forEach(note => {
          history.push({
            id: `note-${note.id}`,
            type: 'note',
            title: note.title || 'Note',
            description: note.content || '',
            date: note.created_at,
            icon: 'note',
            color: 'secondary',
            meta: note.creator ? `By: ${note.creator.name}` : null
          })
        })
      }

      // Add follow-ups
      if (store.followUps) {
        store.followUps.forEach(followUp => {
          history.push({
            id: `followup-${followUp.id}`,
            type: 'followup',
            title: followUp.title || 'Follow-up',
            description: followUp.notes || '',
            date: followUp.follow_up_date || followUp.created_at,
            icon: 'schedule',
            color: 'info',
            meta: followUp.status || null
          })
        })
      }

      // Add call logs
      if (store.callLogs) {
        store.callLogs.forEach(callLog => {
          history.push({
            id: `call-${callLog.id}`,
            type: 'call',
            title: callLog.title || 'Call',
            description: callLog.notes || '',
            date: callLog.call_date || callLog.created_at,
            icon: 'phone',
            color: 'green',
            meta: callLog.duration ? `Duration: ${callLog.duration}s` : null
          })
        })
      }

      // Add assignments
      if (store.assignments) {
        store.assignments.forEach(assignment => {
          history.push({
            id: `assignment-${assignment.id}`,
            type: 'assignment',
            title: `Assignment to ${assignment.employee ? assignment.employee.name : 'N/A'}`,
            description: '',
            date: assignment.assigned_date || assignment.created_at,
            icon: 'assignment',
            color: 'purple',
            meta: `${assignment.status} • ${assignment.priority || 'N/A'} priority`
          })
        })
      }

      return history
    },
    sortedHistory() {
      // Sort by date (newest first)
      return [...this.allHistory].sort((a, b) => {
        const dateA = new Date(a.date || 0)
        const dateB = new Date(b.date || 0)
        return dateB - dateA
      })
    }
  },
  methods: {
    formatDateTime(date) {
      if (!date) return ''
      return new Date(date).toLocaleString()
    }
  }
}
</script>

