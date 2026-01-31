<template>
  <div>
    <div v-if="store.loading.history" class="text-center q-pa-lg">
      <q-spinner color="primary" size="3em" />
      <div class="q-mt-md text-grey-7">Loading leads...</div>
    </div>

    <div v-else-if="leads.length === 0" class="text-center q-pa-xl text-grey-6">
      <q-icon name="inbox" size="48px" />
      <div class="text-h6 q-mt-md">No Leads Found</div>
      <div class="text-body2 q-mt-sm">This customer has no leads yet.</div>
    </div>

    <div v-else>
      <q-list separator>
        <q-item v-for="lead in leads" :key="lead.id" class="q-pa-md">
          <q-item-section avatar>
            <q-avatar color="primary" text-color="white" icon="trending_up" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ lead.title || 'Lead #' + lead.id }}</q-item-label>
            <q-item-label caption>
              <span v-if="lead.project">Project: {{ lead.project.name }}</span>
              <span v-if="lead.interested_type"> • Interested Type: {{ lead.interested_type.name }}</span>
            </q-item-label>
            <q-item-label v-if="lead.next_contact_date" caption>
              Next Contact: {{ formatDate(lead.next_contact_date) }}
            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-badge :color="getStatusColor(lead.status)" :label="lead.status || 'pending'" />
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </div>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'

export default {
  name: 'TabLeads',
  computed: {
    store() {
      return useNewContactStore()
    },
    leads() {
      return this.store.customerHistory.leads || []
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
        active: 'primary',
        converted: 'positive',
        lost: 'negative'
      }
      return colors[status] || 'grey'
    }
  }
}
</script>

