<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Create New Address</div>
        <div class="text-body2 text-grey-7">Add a new address to the system</div>
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

    <q-card>
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
                @blur="onNameBlur"
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
                @update:model-value="onCodeChange"
                :loading="checkingCode"
              >
                <template v-slot:append>
                  <q-btn
                    flat
                    dense
                    round
                    icon="refresh"
                    @click="generateCodeFromName"
                    :disable="!form.name || checkingCode"
                  >
                    <q-tooltip>Auto-generate code from name</q-tooltip>
                  </q-btn>
                </template>
              </q-input>
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
                label="Create Address"
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
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { api } from 'boot/axios'
import debounce from 'debounce'

export default defineComponent({
  name: 'AddressCreatePage',
  setup() {
    const router = useRouter()
    const $q = useQuasar()

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
    const checkingCode = ref(false)
    const codeManuallyChanged = ref(false)

    const typeOptions = ['country', 'division', 'district', 'upazila', 'union', 'village', 'area', 'road', 'other', 'home name'];

    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
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

      // if (!form.value.type) {
      //   parentOptions.value = []
      //   return
      // }

      loadingParents.value = true
      try {
        // Use GetAllAddressesAction which already loads parent relationships
        const params = {
          // type: parentType,
          is_active: true,
          per_page: 10, // Limit results for better performance
        }

        // Only add search parameter if searchQuery is provided and not empty
        if (searchQuery !== undefined && searchQuery !== null && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }

        const response = await api.get('/api/v1/addresses', { params })
        if (response.data.success) {
          const addresses = response.data.data || []
          // Format addresses with display label showing parent hierarchy
          parentOptions.value = addresses.map((address) => ({
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

    const generateCodeFromName = async () => {
      if (!form.value.name || !form.value.name.trim()) {
        return
      }

      checkingCode.value = true
      try {
        // Generate base code from name (lowercase, remove spaces, special chars)
        let baseCode = form.value.name
          .toLowerCase()
          .trim()
          .replace(/[^a-z0-9]/g, '')
          .substring(0, 20) // Limit length

        if (!baseCode) {
          // Fallback: use first 3 chars
          baseCode = form.value.name.toLowerCase().trim().substring(0, 3).replace(/[^a-z0-9]/g, '')
        }

        if (!baseCode) {
          return
        }

        // Check if code exists and find next available number
        let code = baseCode.toUpperCase()
        let counter = 1

        // Check up to 100 iterations to avoid infinite loop
        for (let i = 0; i < 100; i++) {
          const response = await api.get('/api/v1/addresses', {
            params: {
              search: code,
              per_page: 100, // Get more results to check code exactly
            },
          })

          if (response.data.success) {
            const existing = response.data.data.find((addr) => addr.code && addr.code.toUpperCase() === code.toUpperCase())
            if (!existing) {
              // Code is available
              break
            }
            // Code exists, try next number
            counter++
            code = `${baseCode}${counter}`.toUpperCase()
          } else {
            break
          }
        }

        form.value.code = code
        codeManuallyChanged.value = false
      } catch (error) {
        console.error('Failed to generate code:', error)
      } finally {
        checkingCode.value = false
      }
    }

    const onNameBlur = () => {
      // Auto-generate code only if user hasn't manually changed it and name exists
      if (!codeManuallyChanged.value && form.value.name && form.value.name.trim()) {
        generateCodeFromName()
      }
    }

    const checkCodeUniqueness = async () => {
      if (!form.value.code || !form.value.code.trim()) {
        return
      }

      checkingCode.value = true
      try {
        const response = await api.get('/api/v1/addresses', {
          params: {
            search: form.value.code.trim(),
            per_page: 100,
          },
        })

        if (response.data.success) {
          const codeToCheck = form.value.code.trim().toUpperCase()
          const existing = response.data.data.find(
            (addr) => addr.code && addr.code.toUpperCase() === codeToCheck
          )
          if (existing) {
            errors.value.code = 'This code already exists. Please use a different code.'
          } else {
            // Remove error if code is unique
            if (errors.value.code && errors.value.code.includes('already exists')) {
              delete errors.value.code
            }
          }
        }
      } catch (error) {
        console.error('Failed to check code uniqueness:', error)
      } finally {
        checkingCode.value = false
      }
    }

    // Debounced version of checkCodeUniqueness (400ms)
    const debouncedCheckCodeUniqueness = debounce(checkCodeUniqueness, 400)

    const onCodeChange = () => {
      // Mark that user manually changed the code
      codeManuallyChanged.value = true
      
      // Validate code uniqueness when user changes it (debounced)
      if (form.value.code && form.value.code.trim()) {
        debouncedCheckCodeUniqueness()
      }
    }

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        form.value.parent_id = form.value.parent_id ? form.value.parent_id.id : null;
        const response = await api.post('/api/v1/addresses', form.value)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Address created successfully',
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
          message: error.response?.data?.message || 'Failed to create address',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadParentAddresses()
    })

      return {
      form,
      errors,
      loading,
      loadingParents,
      parentOptions,
      parentSearchQuery,
      typeOptions,
      checkingCode,
      codeManuallyChanged,
      loadParentAddresses,
      onParentSearch,
      onParentFocus,
      generateCodeFromName,
      onNameBlur,
      onCodeChange,
      checkCodeUniqueness,
      onSubmit,
    }
  },
})
</script>

