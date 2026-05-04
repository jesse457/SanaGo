<?php

namespace App\Http\Controllers\Api\Tenants\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    protected $service;

    public function __construct(SettingsService $service)
    {
        $this->service = $service;
    }

    /**
     * List items (Departments, Beds, etc.)
     */
    public function index(Request $request, string $type)
    {
        $query = $this->service->getEntityQuery($type, $request->search ?? '');

        return response()->json($query->paginate($request->per_page ?? 10));
    }

    /**
     * Create new setting item
     */
    public function store(Request $request, string $type)
    {
        // Add dynamic validation here based on $type
        $item = $this->service->createItem($type, $request->all());

        return response()->json($item, 201);
    }

    /**
     * Update existing setting item
     */
    public function update(Request $request, string $type, int $id)
    {
        $item = $this->service->updateItem($type, $id, $request->all());

        return response()->json($item);
    }

    /**
     * Delete setting item
     */
    public function destroy(string $type, int $id)
    {
        $this->service->deleteItem($type, $id);

        return response()->json(null, 204);
    }

    /**
     * Get form options (Dropdowns)
     */
    public function options()
    {
        return response()->json($this->service->getFormOptions());
    }
}
