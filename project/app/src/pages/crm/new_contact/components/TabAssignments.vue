<template>
  <div>
    <div v-if="store.loading.history" class="text-center q-pa-lg">
      <q-spinner color="primary" size="3em" />
      <div class="q-mt-md text-grey-7">Loading assignments...</div>
    </div>

    <div v-else-if="assignments.length === 0" class="text-center q-pa-xl text-grey-6">
      <q-icon name="assignment" size="48px" />
      <div class="text-h6 q-mt-md">No Assignments Found</div>
      <div class="text-body2 q-mt-sm">This customer has no assignments yet.</div>
    </div>

    <div v-else>
      <q-list separator>
        <q-item v-for="assignment in assignments" :key="assignment.id" class="q-pa-md">
          <q-item-section avatar>
            <q-avatar color="purple" text-color="white" icon="assignment" />
          </q-item-section>
          <q-item-section>
            <q-item-label>
              Assigned to: {{ assignment.employee ? assignment.employee.name : 'N/A' }}
            </q-item-label>
            <q-item-label caption>
              <span v-if="assignment.assigned_date">Date: {{ formatDate(assignment.assigned_date) }}</span>
              <span v-if="assignment.assigned_by"> • Assigned by: {{ assignment.assigned_by.name }}</span>
            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <div class="column q-gutter-xs">
              <q-badge :color="getStatusColor(assignment.status)" :label="assignment.status || 'pending'" />
              <q-badge v-if="assignment.priority" :color="getPriorityColor(assignment.priority)" :label="assignment.priority" />
            </div>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </div>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'

export default {
  name: 'TabAssignments',
  computed: {
    store() {
      return useNewContactStore()
    },
    assignments() {
      return this.store.customerHistory.assignments || []
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
        cancelled: 'negative',
        skipped: 'grey'
      }
      return colors[status] || 'grey'
    },
    getPriorityColor(priority) {
      const colors = {
        low: 'blue',
        medium: 'orange',
        high: 'red'
      }
      return colors[priority] || 'grey'
    }
  }
}
</script>

