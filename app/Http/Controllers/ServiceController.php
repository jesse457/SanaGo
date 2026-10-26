<?php

namespace App\Http\Controllers;

use App\Enums\ServiceCategory;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServicePriceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    /**
     * The priced catalogue billable at the desk.
     *
     * Search and the module tabs are applied in the query, not in the browser:
     * the list is paginated, so filtering client-side would only ever search the
     * thirty rows that happen to be on screen.
     */
    public function index(Request $request): Response
    {
        $filters = [
            'search' => trim((string) $request->string('search')),
            'module' => (string) $request->string('module'),
        ];

        $serviceQuery = Service::query();

        if ($filters['module'] !== '' && $filters['module'] !== 'all') {
            $categories = collect(ServiceCategory::cases())
                ->filter(fn (ServiceCategory $category) => $category->module() === $filters['module'])
                ->map(fn (ServiceCategory $category) => $category->value);

            $serviceQuery->whereIn('category', $categories);
        }

        $services = $serviceQuery
            ->when($filters['search'] !== '', function ($query) use ($filters): void {
                $query->where(function ($inner) use ($filters): void {
                    $inner->where('name', 'like', "%{$filters['search']}%")
                        ->orWhere('code', 'like', "%{$filters['search']}%");
                });
            })
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString()
            ->through(fn (Service $service): array => [
                ...$service->toArray(),
                'module' => $service->category?->module() ?? 'other',
            ]);

        return Inertia::render('Finance/Services/Index', [
            'services' => $services,
            'filters' => $filters,
            'modules' => ServiceCategory::modules(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Finance/Services/Create', [
            'categories' => ServiceCategory::values(),
        ]);
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        Service::create($request->validated());

        return redirect()->route('finance.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return Inertia::render('Finance/Services/Edit', [
            'service' => $service,
            'categories' => ServiceCategory::values(),
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()->route('finance.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Price change straight from the catalogue list: the price belongs to the
     * service itself, so editing it takes one request and no full form.
     */
    public function updatePrice(UpdateServicePriceRequest $request, Service $service): RedirectResponse
    {
        $service->update(['default_price' => $request->validated('default_price')]);

        return back()->with('success', 'Price updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return back()->with('success', 'Service deleted.');
    }
}
