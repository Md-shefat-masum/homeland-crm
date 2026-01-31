<template>
  <q-avatar :size="size" :class="avatarClass">
    <img :src="imageUrl" :alt="alt" @error="onImageError" />
  </q-avatar>
</template>

<script>
import { defineComponent, computed } from 'vue'

export default defineComponent({
  name: 'AppAvatar',
  props: {
    image: {
      type: String,
      default: 'avatar.png',
    },
    alt: {
      type: String,
      default: 'Avatar',
    },
    size: {
      type: String,
      default: '32px',
    },
    avatarClass: {
      type: String,
      default: '',
    },
  },
  setup(props) {
    const getApiUrl = () => {
      // Get API URL from environment variable or use default
      const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
      return apiUrl.replace(/\/$/, '') // Remove trailing slash if exists
    }

    const imageUrl = computed(() => {
      const apiUrl = getApiUrl()
      const imagePath = props.image || 'avatar.png'
      
      // If image path already contains http/https, use it as is
      if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
        return imagePath
      }
      
      // Otherwise, prepend API URL (images are in public directory)
      return `${apiUrl}/${imagePath}`
    })

    const onImageError = (event) => {
      // Fallback to default avatar if image fails to load
      const apiUrl = getApiUrl()
      const fallbackUrl = `${apiUrl}/avatar.png`
      if (event.target.src !== fallbackUrl) {
        event.target.src = fallbackUrl
      }
    }

    return {
      imageUrl,
      onImageError,
    }
  },
})
</script>

