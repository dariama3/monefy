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
}
