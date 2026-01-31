import { registerPlugin } from '@capacitor/core'

const CallDetection = registerPlugin('CallDetection', {
  web: async () => {
    const webModule = await import('./callDetection.web.js')
    return new webModule.CallDetectionWeb()
  },
})

export default CallDetection

