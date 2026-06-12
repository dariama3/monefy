<script setup lang="ts">
import type { Invoice } from 'frontend/app/types/invoice'

const route = useRoute()

const { data: invoice, pending, error } = await useFetch<Invoice>(`/api/invoices/${route.params.id}`)
</script>

<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div v-if="pending" class="text-center py-4">
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
          <h3 class="text-base font-semibold leading-7 text-gray-900">Invoice Details</h3>
          <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Full information for invoice {{ invoice.number }}.</p>
        </div>
        <div class="flex gap-2">
          <NuxtLink v-if="invoice.status === 'pending'" :to="`/invoices/${invoice.id}/edit`" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Edit
          </NuxtLink>
          <NuxtLink to="/invoices" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            Back to list
          </NuxtLink>
        </div>
      </div>
      <div class="mt-6 border-t border-gray-100">
        <dl class="divide-y divide-gray-100">
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Invoice Number</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ invoice.number }}</dd>
          </div>
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Supplier Name</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ invoice.supplier_name }}</dd>
          </div>
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Supplier Tax ID</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ invoice.supplier_tax_id }}</dd>
          </div>
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Issue Date</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ formatDate(invoice.issue_date) }}</dd>
          </div>
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Due Date</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ formatDate(invoice.due_date) }}</dd>
          </div>
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Net Amount</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ invoice.net_amount }} {{ invoice.currency }}</dd>
          </div>
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">VAT Amount</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ invoice.vat_amount }} {{ invoice.currency }}</dd>
          </div>
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Gross Amount</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ invoice.gross_amount }} {{ invoice.currency }}</dd>
          </div>
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Status</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
               <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                    :class="{
                      'bg-green-50 text-green-700 ring-green-600/20': invoice.status === 'paid',
                      'bg-yellow-50 text-yellow-800 ring-yellow-600/20': invoice.status === 'pending',
                      'bg-gray-50 text-gray-600 ring-gray-500/10': !['paid', 'pending'].includes(invoice.status)
                    }">
                    {{ invoice.status }}
                  </span>
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>
