import { defineRouter } from '#q-app/wrappers'
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router'
import routes from './routes'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default defineRouter(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.VUE_ROUTER_BASE),
  })

  // Add auth guard to all routes
  Router.beforeEach(async (to, from, next) => {
    if (process.env.CLIENT) {
      try {
        const { useAuthStore } = await import('stores/auth')
        const authStore = useAuthStore()
        
        // Initialize auth from localStorage
        authStore.initAuth()
        
        // Public routes that don't require authentication
        const publicRoutes = ['/login', '/register', '/forgot-password', '/reset-password']
        const isPublicRoute = publicRoutes.includes(to.path)
        
        // If route is public, allow access
        if (isPublicRoute) {
          // If already logged in, redirect to dashboard
          if (authStore.isLoggedIn) {
            return next('/dashboard')
          }
          return next()
        }
        
        // Protected routes - require authentication
        if (!authStore.isLoggedIn) {
          // Redirect to login if not authenticated
          return next('/login')
        }
      } catch (error) {
        console.error('Auth guard error:', error)
        return next('/login')
      }
    }
    
    // User is authenticated or server-side, allow access
    next()
  })

  return Router
})
