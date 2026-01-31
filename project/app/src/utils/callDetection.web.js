export class CallDetectionWeb {
  async requestPermissions() {
    return { granted: false, message: 'Not available on web' }
  }

  async checkOverlayPermission() {
    return { granted: true }
  }

  async hasOverlayPermission() {
    return { granted: true }
  }

  async getLastIncomingCallNumber() {
    return { found: false, number: '', date: 0, duration: 0, name: '' }
  }
}

