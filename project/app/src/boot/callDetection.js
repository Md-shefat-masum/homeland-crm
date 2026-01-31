import { App } from '@capacitor/app'
import { Capacitor } from '@capacitor/core'

export default async ({ router }) => {
  // Only run on native platforms
  if (!Capacitor.isNativePlatform()) {
    console.log('CallDetection: Not a native platform, skipping')
    return
  }

  console.log('CallDetection: Boot file loaded')

  // Listen for phoneNumberReceived event
  window.addEventListener('phoneNumberReceived', () => {
    console.log('CallDetection: phoneNumberReceived event fired')
    
    // Allow empty string too (Android 10+ might not provide number)
    if (window.pendingPhoneNumber !== undefined) {
      const phoneNumber = window.pendingPhoneNumber ?? ''
      console.log('CallDetection: Phone number received:', phoneNumber)
      delete window.pendingPhoneNumber

      setTimeout(() => {
        navigateToNewContact(router, phoneNumber)
      }, 500)
    }
  })

  // Handle app state changes
  App.addListener('appStateChange', async (state) => {
    console.log('CallDetection: App state changed:', state.isActive ? 'active' : 'inactive')
    if (state.isActive) {
      // App became active, check for pending phone number from MainActivity
      await checkForPhoneNumberFromIntent(router)
    }
  })

  // Check for phone number when app opens
  setTimeout(() => {
    checkForPhoneNumberFromIntent(router)
  }, 2000) // Wait 2 seconds for app to fully initialize

  // Listen for app URL open (deep link)
  App.addListener('appUrlOpen', async (data) => {
    try {
      console.log('CallDetection: App URL opened:', data.url)
      const url = new URL(data.url)
      const phoneNumber = url.searchParams.get('phone_number')
      
      if (phoneNumber) {
        navigateToNewContact(router, phoneNumber)
      }
    } catch (error) {
      console.error('CallDetection: Error parsing app URL:', error)
    }
  })
}

async function checkForPhoneNumberFromIntent(router) {
  try {
    console.log('CallDetection: Checking for phone number...')
    
    // Check if there's a phone number stored in window (set by MainActivity)
    if (window.pendingPhoneNumber !== undefined) {
      const phoneNumber = window.pendingPhoneNumber ?? ''
      console.log('CallDetection: Found phone number in window:', phoneNumber)
      delete window.pendingPhoneNumber

      setTimeout(() => {
        navigateToNewContact(router, phoneNumber)
      }, 500)
      return
    }
    
    // Try to get phone number from Android interface
    if (window.Android && typeof window.Android.getPendingPhoneNumber === 'function') {
      const phoneNumber = window.Android.getPendingPhoneNumber()
      if (phoneNumber) {
        console.log('CallDetection: Found phone number from Android interface:', phoneNumber)
        setTimeout(() => {
          navigateToNewContact(router, phoneNumber)
        }, 500)
      }
    }
  } catch (error) {
    console.error('CallDetection: Error checking for phone number:', error)
  }
}

function navigateToNewContact(router, phoneNumber) {
  if (!router) {
    console.log('CallDetection: Cannot navigate - router missing')
    return
  }

  const query = {}
  if (phoneNumber && phoneNumber.trim() !== '') {
    query.phone_number = phoneNumber.trim()
  }

  console.log('CallDetection: Navigating to new_contact with:', { phoneNumber: query.phone_number || 'none' })

  router.push({
    path: '/crm/leads/create',
    query
  }).then(() => {
    console.log('CallDetection: Navigation successful to /crm/leads/create')
  }).catch(err => {
    console.log('CallDetection: Navigation error (may be expected):', err.message)
    // Fallback for hash mode
    if (window.location) {
      const qs = query.phone_number ? `?phone_number=${encodeURIComponent(query.phone_number)}` : ''
      window.location.href = `/#/crm/leads/create${qs}`
    }
  })
}

