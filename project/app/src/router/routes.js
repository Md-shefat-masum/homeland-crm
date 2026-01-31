const routes = [
  // Public routes (no auth required)
  {
    path: '/login',
    component: () => import('pages/LoginPage.vue'),
  },
  {
    path: '/register',
    component: () => import('pages/RegisterPage.vue'),
  },
  {
    path: '/forgot-password',
    component: () => import('pages/ForgotPasswordPage.vue'),
  },

  // Protected routes (require auth)
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('pages/DashboardPage.vue'),
      },
      // Address Management
      {
        path: 'crm/address',
        name: 'address-list',
        component: () => import('pages/crm/address/AddressListPage.vue'),
      },
      {
        path: 'crm/address/create',
        name: 'address-create',
        component: () => import('pages/crm/address/AddressCreatePage.vue'),
      },
      {
        path: 'crm/address/:id',
        name: 'address-view',
        component: () => import('pages/crm/address/AddressViewPage.vue'),
      },
      {
        path: 'crm/address/:id/edit',
        name: 'address-edit',
        component: () => import('pages/crm/address/AddressEditPage.vue'),
      },
      // Customer Professions Management
      {
        path: 'crm/customer_professions',
        name: 'profession-list',
        component: () => import('pages/crm/customer_professions/ProfessionListPage.vue'),
      },
      {
        path: 'crm/customer_professions/create',
        name: 'profession-create',
        component: () => import('pages/crm/customer_professions/ProfessionCreatePage.vue'),
      },
      {
        path: 'crm/customer_professions/:id/edit',
        name: 'profession-edit',
        component: () => import('pages/crm/customer_professions/ProfessionEditPage.vue'),
      },
      // Customer Groups Management
      {
        path: 'crm/customer_groups',
        name: 'customer-group-list',
        component: () => import('pages/crm/customer_groups/CustomerGroupListPage.vue'),
      },
      {
        path: 'crm/customer_groups/create',
        name: 'customer-group-create',
        component: () => import('pages/crm/customer_groups/CustomerGroupCreatePage.vue'),
      },
      {
        path: 'crm/customer_groups/:id/edit',
        name: 'customer-group-edit',
        component: () => import('pages/crm/customer_groups/CustomerGroupEditPage.vue'),
      },
      // Customer Management
      {
        path: 'crm/customer',
        name: 'customer-list',
        component: () => import('pages/crm/customer/CustomerListPage.vue'),
      },
      {
        path: 'crm/customer/create',
        name: 'customer-create',
        component: () => import('pages/crm/customer/CustomerCreatePage.vue'),
      },
      {
        path: 'crm/customer/:id',
        name: 'customer-view',
        component: () => import('pages/crm/customer/CustomerViewPage.vue'),
      },
      {
        path: 'crm/customer/:id/edit',
        name: 'customer-edit',
        component: () => import('pages/crm/customer/CustomerEditPage.vue'),
      },
      // New Contact Entry
      {
        path: 'crm/new_contact',
        name: 'new-contact',
        component: () => import('pages/crm/new_contact/NewContactPage.vue'),
      },
      // Projects Management
      {
        path: 'crm/projects',
        name: 'project-list',
        component: () => import('pages/crm/projects/ProjectListPage.vue'),
      },
      {
        path: 'crm/projects/create',
        name: 'project-create',
        component: () => import('pages/crm/projects/ProjectCreatePage.vue'),
      },
      {
        path: 'crm/projects/:id',
        name: 'project-view',
        component: () => import('pages/crm/projects/ProjectViewPage.vue'),
      },
      {
        path: 'crm/projects/:id/edit',
        name: 'project-edit',
        component: () => import('pages/crm/projects/ProjectEditPage.vue'),
      },
      // Lead Sources Management
      {
        path: 'crm/lead_sources',
        name: 'lead-source-list',
        component: () => import('pages/crm/lead_sources/LeadSourceListPage.vue'),
      },
      {
        path: 'crm/lead_sources/create',
        name: 'lead-source-create',
        component: () => import('pages/crm/lead_sources/LeadSourceCreatePage.vue'),
      },
      {
        path: 'crm/lead_sources/:id',
        name: 'lead-source-view',
        component: () => import('pages/crm/lead_sources/LeadSourceViewPage.vue'),
      },
      {
        path: 'crm/lead_sources/:id/edit',
        name: 'lead-source-edit',
        component: () => import('pages/crm/lead_sources/LeadSourceEditPage.vue'),
      },
      // Interested Types Management
      {
        path: 'crm/interested_types',
        name: 'interested-type-list',
        component: () => import('pages/crm/interested_types/InterestedTypeListPage.vue'),
      },
      {
        path: 'crm/interested_types/create',
        name: 'interested-type-create',
        component: () => import('pages/crm/interested_types/InterestedTypeCreatePage.vue'),
      },
      {
        path: 'crm/interested_types/:id',
        name: 'interested-type-view',
        component: () => import('pages/crm/interested_types/InterestedTypeViewPage.vue'),
      },
      {
        path: 'crm/interested_types/:id/edit',
        name: 'interested-type-edit',
        component: () => import('pages/crm/interested_types/InterestedTypeEditPage.vue'),
      },
      // Leads Management
      {
        path: 'crm/leads',
        name: 'lead-list',
        component: () => import('pages/crm/leads/LeadListPage.vue'),
      },
      {
        path: 'crm/leads/create',
        name: 'lead-create',
        component: () => import('pages/crm/leads/LeadCreatePage.vue'),
      },
      {
        path: 'crm/leads/:id',
        name: 'lead-view',
        component: () => import('pages/crm/leads/LeadViewPage.vue'),
      },
      {
        path: 'crm/leads/:id/edit',
        name: 'lead-edit',
        component: () => import('pages/crm/leads/LeadEditPage.vue'),
      },
      {
        path: '',
        redirect: '/dashboard',
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
