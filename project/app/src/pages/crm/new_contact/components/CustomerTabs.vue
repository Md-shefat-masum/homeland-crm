<template>
  <q-card class="q-mt-md">
    <q-tabs
      v-model="activeTab"
      dense
      class="text-grey"
      active-color="primary"
      indicator-color="primary"
      align="justify"
      narrow-indicator
    >
      <q-tab name="info" label="Info" icon="info" />
      <q-tab name="leads" label="Leads" :badge="store.customerHistory.leads.length || null" />
      <q-tab name="notes" label="Notes" :badge="store.customerHistory.notes.length || null" />
      <q-tab name="followups" label="Follow-ups" :badge="store.customerHistory.followUps.length || null" />
      <q-tab name="calls" label="Call Logs" :badge="store.customerHistory.callLogs.length || null" />
      <q-tab name="assignments" label="Assignments" :badge="store.customerHistory.assignments.length || null" />
      <q-tab name="history" label="History" />
    </q-tabs>

    <q-separator />

    <q-tab-panels v-model="activeTab" animated>
      <q-tab-panel name="info">
        <div class="text-body2 text-grey-7">
          Customer information is displayed above. Use the tabs to view different aspects of customer history.
        </div>
      </q-tab-panel>

      <q-tab-panel name="leads">
        <TabLeads />
      </q-tab-panel>

      <q-tab-panel name="notes">
        <TabNotes />
      </q-tab-panel>

      <q-tab-panel name="followups">
        <TabFollowUps />
      </q-tab-panel>

      <q-tab-panel name="calls">
        <TabCallLogs />
      </q-tab-panel>

      <q-tab-panel name="assignments">
        <TabAssignments />
      </q-tab-panel>

      <q-tab-panel name="history">
        <TabHistory />
      </q-tab-panel>
    </q-tab-panels>
  </q-card>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'
import TabLeads from './TabLeads.vue'
import TabNotes from './TabNotes.vue'
import TabFollowUps from './TabFollowUps.vue'
import TabCallLogs from './TabCallLogs.vue'
import TabAssignments from './TabAssignments.vue'
import TabHistory from './TabHistory.vue'

export default {
  name: 'CustomerTabs',
  components: {
    TabLeads,
    TabNotes,
    TabFollowUps,
    TabCallLogs,
    TabAssignments,
    TabHistory
  },
  data() {
    return {
      activeTab: 'info'
    }
  },
  computed: {
    store() {
      return useNewContactStore()
    }
  },
  watch: {
    activeTab(newTab) {
      this.setActiveTab(newTab)
    }
  },
  methods: {
    setActiveTab(tab) {
      this.store.setActiveTab(tab)
    }
  },
  mounted() {
    // Set initial tab from store
    this.activeTab = this.store.activeTab || 'info'
  }
}
</script>

