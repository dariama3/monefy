<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query();

        // default sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $query->orderBy($sortBy, $sortDir);

        // optional pagination
        $perPage = $request->input('per_page');

        if ($perPage) {
            return $query->paginate((int) $perPage);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:255|unique:invoices,number',
            'supplier_name' => 'required|string|max:255',
            'supplier_tax_id' => 'required|string|max:255',
            'net_amount' => 'required|numeric|gt:0',
            'vat_amount' => 'required|numeric|gte:0',
            'currency' => 'required|string|max:3',
            'status' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
        ]);

        $validated['gross_amount'] = $validated['net_amount'] + $validated['vat_amount'];

        return Invoice::create($validated);
    }

    public function show(Invoice $invoice)
    {
        return $invoice;
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return response()->json(['message' => 'Only invoices with pending status can be updated.'], 403);
        }

        $validated = $request->validate([
            'number' => 'sometimes|required|string|max:255|unique:invoices,number,' . $invoice->id,
            'supplier_name' => 'sometimes|required|string|max:255',
            'supplier_tax_id' => 'sometimes|required|string|max:255',
            'net_amount' => 'sometimes|required|numeric|gt:0',
            'vat_amount' => 'sometimes|required|numeric|gte:0',
            'currency' => 'sometimes|required|string|max:3',
            'status' => 'sometimes|required|string|max:255',
            'issue_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after_or_equal:issue_date',
        ]);

        if (isset($validated['net_amount']) || isset($validated['vat_amount'])) {
            $net = $validated['net_amount'] ?? $invoice->net_amount;
            $vat = $validated['vat_amount'] ?? $invoice->vat_amount;
            $validated['gross_amount'] = $net + $vat;
        }

        $invoice->update($validated);

        return $invoice;
    }
}
