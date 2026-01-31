<template>
  <div>
    <div v-if="store.loading.history" class="text-center q-pa-lg">
      <q-spinner color="primary" size="3em" />
      <div class="q-mt-md text-grey-7">Loading notes...</div>
    </div>

    <div v-else-if="notes.length === 0" class="text-center q-pa-xl text-grey-6">
      <q-icon name="note" size="48px" />
      <div class="text-h6 q-mt-md">No Notes Found</div>
      <div class="text-body2 q-mt-sm">This customer has no notes yet.</div>
    </div>

    <div v-else>
      <q-list separator>
        <q-item v-for="note in notes" :key="note.id" class="q-pa-md">
          <q-item-section avatar>
            <q-avatar color="secondary" text-color="white" icon="note" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ note.title || 'Untitled Note' }}</q-item-label>
            <q-item-label caption>
              {{ note.content }}
            </q-item-label>
            <q-item-label caption class="q-mt-xs">
              <span v-if="note.creator">By: {{ note.creator.name }}</span>
              <span v-if="note.created_at"> • {{ formatDate(note.created_at) }}</span>
            </q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </div>
</template>

<script>
import { useNewContactStore } from 'stores/newContact'

export default {
  name: 'TabNotes',
  computed: {
    store() {
      return useNewContactStore()
    },
    notes() {
      return this.store.customerHistory.notes || []
    }
  },
  methods: {
    formatDate(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString()
    }
  }
}
</script>

