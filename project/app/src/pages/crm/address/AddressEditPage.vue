<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Edit Address</div>
        <div class="text-body2 text-grey-7">Update address information</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/address')"
        />
      </div>
    </div>

    <q-card v-if="!loading && address">
      <q-card-section>
        <q-form @submit="onSubmit" class="">
          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.name"
                label="Name *"
                outlined
                :error="!!errors.name"
                :error-message="errors.name"
                required
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.code"
                label="Code *"
                outlined
                :error="!!errors.code"
                :error-message="errors.code"
                required
              />
            </div>
          </div>

          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.type"
                :options="typeOptions"
                label="Type *"
                outlined
                :error="!!errors.type"
                :error-message="errors.type"
                required
                :disable="true"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.parent_id"
                :options="parentOptions"
                option-label="displayLabel"
                option-value="id"
                label="Parent Address"
                outlined
                clearable
                use-input
                input-debounce="400"
                @filter="onParentSearch"
                @focus="onParentFocus"
                :error="!!errors.parent_id"
                :error-message="errors.parent_id"
                :loading="loadingParents"
              >
                <template v-slot:no-option>
                  <div class="text-center text-grey-7 q-pa-md">
                    {{ parentSearchQuery ? 'No addresses found' : 'Start typing to search addresses' }}
                  </div>
                </template>
                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label>{{ scope.opt.displayLabel }}</q-item-label>
                      <q-item-label v-if="scope.opt.type" caption>
                        Type: {{ scope.opt.type }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
          </div>

          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model.number="form.latitude"
                label="Latitude"
                type="number"
                step="any"
                outlined
                :error="!!errors.latitude"
                :error-message="errors.latitude"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model.number="form.longitude"
                label="Longitude"
                type="number"
                step="any"
                outlined
                :error="!!errors.longitude"
                :error-message="errors.longitude"
              />
            </div>
          </div>

          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model.number="form.sort_order"
                label="Sort Order"
                type="number"
                outlined
                :error="!!errors.sort_order"
                :error-message="errors.sort_order"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-toggle
                v-model="form.is_active"
                label="Active"
                :error="!!errors.is_active"
              />
            </div>
          </div>

          <div class="row q-mt-md">
            <div class="col">
              <q-btn
                type="submit"
                label="Update Address"
                color="primary"
                :loading="loading"
                unelevated
              />
              <q-btn
                flat
                label="Cancel"
                color="grey"
                @click="$router.push('/crm/address')"
                class="q-ml-sm"
              />
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>

    <q-inner-loading :showing="loading && !address">
      <q-spinner-gears size="50px" color="primary" />
    </q-inner-loading>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { api } from 'boot/axios'
import debounce from 'debounce'

export default defineComponent({
  name: 'AddressEditPage',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const $q = useQuasar()

    const address = ref(null)
    const form = ref({
      name: '',
      code: '',
      type: null,
      parent_id: null,
      latitude: null,
      longitude: null,
      sort_order: 0,
      is_active: true,
    })

    const errors = ref({})
    const loading = ref(false)
    const loadingParents = ref(false)
    const parentOptions = ref([])
    const parentSearchQuery = ref('')

    const typeOptions = ['country', 'division', 'district', 'upazila', 'union', 'village', 'area', 'road', 'other', 'home name']

    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
      }
    }

    const loadAddress = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/addresses/${route.params.id}`)
        if (response.data.success) {
          address.value = response.data.data
          form.value = {
            name: address.value.name,
            code: address.value.code,
            type: address.value.type,
            parent_id: address.value.parent_id,
            latitude: address.value.latitude,
            longitude: address.value.longitude,
            sort_order: address.value.sort_order || 0,
            is_active: address.value.is_active !== undefined ? address.value.is_active : true,
          }
          
          // Load parent addresses and set the selected parent in the correct format
          await loadParentAddresses('')
          
          // Find and set the parent option if it exists
          // First check if parent was loaded from API response
          if (address.value.parent && address.value.parent.id) {
            const existingParent = parentOptions.value.find(opt => opt.id === address.value.parent.id)
            if (existingParent) {
              form.value.parent_id = existingParent
            } else {
              // If parent not in options (e.g., inactive), add it manually
              const parentObj = {
                ...address.value.parent,
                displayLabel: formatAddressLabel(address.value.parent),
              }
              parentOptions.value.push(parentObj)
              form.value.parent_id = parentObj
            }
          } else if (form.value.parent_id) {
            // Fallback: find by ID if parent relationship wasn't loaded
            const parentOption = parentOptions.value.find(opt => opt.id === form.value.parent_id)
            if (parentOption) {
              form.value.parent_id = parentOption
            }
          }
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load address',
          position: 'top',
        })
        router.push('/crm/address')
      } finally {
        loading.value = false
      }
    }

    const formatAddressLabel = (address) => {
      // Format: "Parent > Address" or just "Address"
      if (address.parent && address.parent.name) {
        return `${address.parent.name} > ${address.name}`
      }
      return address.name
    }

    const loadParentAddresses = async (searchQuery = '') => {
      if (!form.value.type) {
        parentOptions.value = []
        return
      }

      loadingParents.value = true
      try {
        const typeHierarchy = {
          country: null,
          division: 'country',
          district: 'division',
          upazila: 'district',
          union: 'upazila',
          village: 'union',
          area: 'village',
        }

        const parentType = typeHierarchy[form.value.type]
        if (!parentType) {
          parentOptions.value = []
          return
        }

        // Use GetAllAddressesAction which already loads parent relationships
        const params = {
          type: parentType,
          is_active: true,
          per_page: 100, // Limit results for better performance
        }

        // Only add search parameter if searchQuery is provided and not empty
        if (searchQuery !== undefined && searchQuery !== null && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }

        const response = await api.get('/api/v1/addresses', { params })
        if (response.data.success) {
          const addresses = response.data.data || []
          // Exclude current address from parent options to prevent circular references
          const currentAddressId = route.params.id ? parseInt(route.params.id) : null
          const filteredAddresses = currentAddressId
            ? addresses.filter(addr => addr.id !== currentAddressId)
            : addresses
          
          // Format addresses with display label showing parent hierarchy
          parentOptions.value = filteredAddresses.map((address) => ({
            ...address,
            displayLabel: formatAddressLabel(address),
          }))
        }
      } catch (error) {
        console.error('Failed to load parent addresses:', error)
        parentOptions.value = []
      } finally {
        loadingParents.value = false
      }
    }

    // Debounced version of loadParentAddresses (400ms)
    const debouncedLoadParentAddresses = debounce((searchQuery) => {
      loadParentAddresses(searchQuery)
    }, 400)

    const onParentSearch = (val, update) => {
      parentSearchQuery.value = val
      update(() => {
        // Load addresses with search query (debounced)
        // Empty string means load all addresses of parent type (no search filter)
        if (val === '') {
          loadParentAddresses('')
        } else {
          debouncedLoadParentAddresses(val)
        }
      })
    }

    const onParentFocus = () => {
      // Load all parent addresses when field is focused (if not already loaded)
      if (parentOptions.value.length === 0 && !loadingParents.value) {
        loadParentAddresses('')
      }
    }

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        // Handle parent_id - if it's an object, extract the id
        const submitData = {
          ...form.value,
          parent_id: form.value.parent_id && typeof form.value.parent_id === 'object' 
            ? form.value.parent_id.id 
            : (form.value.parent_id || null),
        }
        
        const response = await api.put(`/api/v1/addresses/${route.params.id}`, submitData)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Address updated successfully',
            position: 'top',
          })
          router.push('/crm/address')
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to update address',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadAddress()
    })

      return {
      address,
      form,
      errors,
      loading,
      loadingParents,
      parentOptions,
      parentSearchQuery,
      typeOptions,
      loadParentAddresses,
      onParentSearch,
      onParentFocus,
      onSubmit,
    }
  },
})
</script>

