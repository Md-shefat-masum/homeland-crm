<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary">
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />

        <q-toolbar-title> CRM Homland </q-toolbar-title>

        <q-space />

        <q-btn-dropdown flat dense :label="authStore.user?.name || 'User'" icon="account_circle">
          <q-list>
            <q-item clickable v-close-popup @click="handleLogout">
              <q-item-section avatar>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Logout</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <q-list>
        <q-item-label header> Navigation </q-item-label>

        <q-item clickable v-ripple to="/dashboard">
          <q-item-section avatar>
            <q-icon name="dashboard" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
          </q-item-section>
        </q-item>

        <q-expansion-item icon="location_on" label="Address Management" v-model="addressExpanded">
          <q-item clickable v-ripple to="/crm/address" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>All Addresses</q-item-label>
            </q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/crm/address/create" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Add Address</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-expansion-item icon="work" label="Professions" v-model="professionExpanded">
          <q-item clickable v-ripple to="/crm/customer_professions" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>All Professions</q-item-label>
            </q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/crm/customer_professions/create" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Add Profession</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-expansion-item icon="group" label="Customer Groups" v-model="groupExpanded">
          <q-item clickable v-ripple to="/crm/customer_groups" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>All Groups</q-item-label>
            </q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/crm/customer_groups/create" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Add Group</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-expansion-item icon="people" label="Customers" v-model="customerExpanded">
          <q-item clickable v-ripple to="/crm/customer" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>All Customers</q-item-label>
            </q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/crm/customer/create" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Add Customer</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-expansion-item icon="business" label="Projects" v-model="projectExpanded">
          <q-item clickable v-ripple to="/crm/projects" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>All Projects</q-item-label>
            </q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/crm/projects/create" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Add Project</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-expansion-item icon="source" label="Lead Sources" v-model="leadSourceExpanded">
          <q-item clickable v-ripple to="/crm/lead_sources" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>All Lead Sources</q-item-label>
            </q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/crm/lead_sources/create" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Add Lead Source</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-expansion-item icon="favorite" label="Interested Types" v-model="interestedTypeExpanded">
          <q-item clickable v-ripple to="/crm/interested_types" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>All Interested Types</q-item-label>
            </q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/crm/interested_types/create" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Add Interested Type</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-expansion-item icon="trending_up" label="Leads" v-model="leadExpanded">
          <q-item clickable v-ripple to="/crm/leads" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>All Leads</q-item-label>
            </q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/crm/leads/create" style="padding-left: 50px;">
            <q-item-section avatar>
              <q-icon name="add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Add Lead</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <q-item clickable v-ripple to="/crm/new_contact">
          <q-item-section avatar>
            <q-icon name="person_add" />
          </q-item-section>
          <q-item-section>
            <q-item-label>New Contact Entry</q-item-label>
            <q-item-label caption>Search & view customer history</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { useAuthStore } from 'stores/auth'

export default defineComponent({
  name: 'MainLayout',

  setup() {
    const router = useRouter()
    const $q = useQuasar()
    const authStore = useAuthStore()

    // Helper function to show notifications
    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
      }
    }

    const handleLogout = async () => {
      await authStore.logout()
      showNotify({
        type: 'positive',
        message: 'Logged out successfully',
        position: 'top',
      })
      router.push('/login')
    }

    return {
      authStore,
      handleLogout,
    }
  },

  data() {
    return {
      leftDrawerOpen: false,
      addressExpanded: false,
      professionExpanded: false,
      groupExpanded: false,
      customerExpanded: false,
      projectExpanded: false,
      leadSourceExpanded: false,
      interestedTypeExpanded: false,
      leadExpanded: false,
    }
  },

  methods: {
    toggleLeftDrawer() {
      this.leftDrawerOpen = !this.leftDrawerOpen
    },
  },
})
</script>
<style scoped>
.q-item__section--avatar {
  padding-right: 10px;
}
</style>
