<script setup lang="ts">
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as zod from 'zod'
import type { Invoice } from '~/types/invoice'

const route = useRoute()
const router = useRouter()
const id = route.params.id

const { data: invoice, pending: loading, error } = await useFetch<Invoice>(`/api/invoices/${id}`)

const validationSchema = toTypedSchema(
  zod.object({
    net_amount: zod.number().min(0, 'Net amount must be positive'),
    vat_amount: zod.number().min(0, 'VAT amount must be positive'),
    due_date: zod.string().min(1, 'Due date is required'),
  })
)

const { handleSubmit, errors, isSubmitting, resetForm } = useForm({
  validationSchema,
  initialValues: {
    net_amount: invoice.value?.net_amount ? Number(invoice.value.net_amount) : 0,
    vat_amount: invoice.value?.vat_amount ? Number(invoice.value.vat_amount) : 0,
    due_date: invoice.value?.due_date ? new Date(invoice.value.due_date).toISOString().split('T')[0] : '',
  },
})

// Update form when data is loaded
watch(invoice, (newInvoice) => {
  if (newInvoice) {
    resetForm({
      values: {
        net_amount: newInvoice.net_amount ? Number(newInvoice.net_amount) : 0,
        vat_amount: newInvoice.vat_amount ? Number(newInvoice.vat_amount) : 0,
        due_date: newInvoice.due_date ? new Date(newInvoice.due_date).toISOString().split('T')[0] : '',
      },
    })
  }
}, { immediate: true })

const { value: net_amount } = useField<number>('net_amount')
const { value: vat_amount } = useField<number>('vat_amount')
const { value: due_date } = useField<string>('due_date')

const isEditable = computed(() => invoice.value?.status === 'pending')

const onSubmit = handleSubmit(async (values) => {
  try {
    await $fetch(`/api/invoices/${id}`, {
      method: 'PUT',
      body: values,
    })
    router.push(`/invoices/${id}`)
  } catch (e) {
    console.error('Failed to update invoice', e)
    alert('Failed to update invoice')
  }
})
</script>

<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div v-if="loading" class="text-center py-4">
      Loading invoice details...
    </div>
    <div v-else-if="error" class="text-center py-4 text-red-600">
      Error loading invoice: {{ error.message }}
    </div>
    <div v-else-if="!invoice" class="text-center py-4 text-gray-500">
      Invoice not found.
    </div>
    <div v-else>
      <div class="px-4 sm:px-0 flex justify-between items-center">
        <div>
          <h3 class="text-base font-semibold leading-7 text-gray-900">Edit Invoice {{ invoice.number }}</h3>
        </div>
        <NuxtLink :to="`/invoices/${id}`" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
          Cancel
        </NuxtLink>
      </div>

      <div v-if="!isEditable" class="mt-4 p-4 bg-yellow-50 text-yellow-700 rounded-md">
        This invoice cannot be edited because it is not in pending status.
      </div>

      <form @submit="onSubmit" class="mt-6 space-y-6 max-w-lg">
        <div>
          <label for="net_amount" class="block text-sm font-medium leading-6 text-gray-900">Net Amount</label>
          <div class="mt-2">
            <input
              v-model.number="net_amount"
              type="number"
              step="0.01"
              id="net_amount"
              :disabled="!isEditable"
              class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 disabled:bg-gray-100 disabled:text-gray-500"
            />
            <p v-if="errors.net_amount" class="mt-2 text-sm text-red-600">{{ errors.net_amount }}</p>
          </div>
        </div>

        <div>
          <label for="vat_amount" class="block text-sm font-medium leading-6 text-gray-900">VAT Amount</label>
          <div class="mt-2">
            <input
              v-model.number="vat_amount"
              type="number"
              step="0.01"
              id="vat_amount"
              :disabled="!isEditable"
              class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 disabled:bg-gray-100 disabled:text-gray-500"
            />
            <p v-if="errors.vat_amount" class="mt-2 text-sm text-red-600">{{ errors.vat_amount }}</p>
          </div>
        </div>

        <div>
          <label for="due_date" class="block text-sm font-medium leading-6 text-gray-900">Due Date</label>
          <div class="mt-2">
            <input
              v-model="due_date"
              type="date"
              id="due_date"
              :disabled="!isEditable"
              class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 disabled:bg-gray-100 disabled:text-gray-500"
            />
            <p v-if="errors.due_date" class="mt-2 text-sm text-red-600">{{ errors.due_date }}</p>
          </div>
        </div>

        <div class="flex justify-end pt-4">
          <button
            type="submit"
            :disabled="!isEditable || isSubmitting"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:bg-indigo-300 disabled:cursor-not-allowed"
          >
            {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
