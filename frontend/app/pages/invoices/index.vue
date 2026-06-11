<script setup lang="ts">
import type { Invoice } from '~/types/invoice'

const { data: invoices, pending, error } = await useFetch<Invoice[]>('/api/invoices')
</script>

<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h2 class="text-base font-semibold leading-6 text-gray-900">Invoices</h2>
        <p class="mt-2 text-sm text-gray-700">A list of all invoices including supplier name, amount, and status.</p>
      </div>
    </div>
    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <div v-if="pending" class="text-center py-4">
            Loading invoices...
          </div>
          <div v-else-if="error" class="text-center py-4 text-red-600">
            Error loading invoices: {{ error.message }}
          </div>
          <div v-else-if="!invoices || invoices.length === 0" class="text-center py-4 text-gray-500">
            No invoices found.
          </div>
          <table v-else class="min-w-full divide-y divide-gray-300">
            <thead>
              <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Number</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Supplier Name</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gross Amount</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Due Date</th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                  <span class="sr-only">View</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="invoice in invoices" :key="invoice.id">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                  {{ invoice.number }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ invoice.supplier_name }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ invoice.gross_amount }} {{ invoice.currency }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                    :class="{
                      'bg-green-50 text-green-700 ring-green-600/20': invoice.status === 'paid',
                      'bg-yellow-50 text-yellow-800 ring-yellow-600/20': invoice.status === 'pending',
                      'bg-gray-50 text-gray-600 ring-gray-500/10': !['paid', 'pending'].includes(invoice.status)
                    }">
                    {{ invoice.status }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ formatDate(invoice.due_date) }}
                </td>
                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                  <NuxtLink :to="`/invoices/${invoice.id}`" class="text-indigo-600 hover:text-indigo-900">
                    View<span class="sr-only">, invoice {{ invoice.number }}</span>
                  </NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
