<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use App\Models\User;
use App\Services\Billing\PriceListService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        // Build query with conditional search and role filters
        $query = User::with(['department:id,name', 'roles', 'shifts'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role && $role !== 'all', function ($query) use ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->latest();

        return Inertia::render('Users/Index', [
            // Paginate users (e.g., 10 per page) and append search parameters to page links
            'users' => $query->paginate(10)->withQueryString(),
            'departments' => Department::select('id', 'name')->get(),
            'roles' => Role::select('id', 'name')->get(),
            // Keep stats global rather than limited to the paginated subset
            'stats' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
            ],
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $role = $validated['role'];
        unset($validated['role']);

        $user = User::create($validated);
        $user->assignRole($role);

        if ($request->hasFile('profile_picture')) {
            $user->addMediaFromRequest('profile_picture')
                ->toMediaCollection('profile_picture');
        }

        return back()->with('success', 'User created successfully.');
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'departments' => Department::select('id', 'name')->get(),
            'roles' => Role::select('id', 'name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $role = $validated['role'] ?? null;
        $shifts = $validated['shifts'] ?? [];
        unset($validated['role'], $validated['shifts']);

        $user->update($validated);

        if ($role) {
            $user->syncRoles($role);
        }

        // Sync shifts: replace the stored set with the submitted one. The
        // submitted shift keys are optional, so read them defensively.
        $user->shifts()->delete();
        foreach ($shifts as $shift) {
            if (! empty($shift['shift_date']) && ! empty($shift['shift_type'])) {
                $user->shifts()->create([
                    'shift_date' => $shift['shift_date'],
                    'shift_type' => $shift['shift_type'],
                    'start_time' => ($shift['start_time'] ?? null) ?: '08:00',
                    'end_time' => ($shift['end_time'] ?? null) ?: '17:00',
                ]);
            }
        }

        if ($request->hasFile('profile_picture')) {
            $user->addMediaFromRequest('profile_picture')
                ->toMediaCollection('profile_picture');
        }

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion if necessary
        if (auth()->id() === $user->id) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }

    /**
     * Price list of one doctor: catalogue default vs what this doctor charges.
     *
     * Doctors do not share tariffs, so their prices live beside their account
     * instead of forcing one hospital-wide rate.
     */
    public function pricing(User $user, PriceListService $prices)
    {
        Gate::authorize('manage-users');

        $user->load('roles');

        return Inertia::render('Users/Pricing', [
            'doctor' => [
                'id' => $user->id,
                'name' => $user->name,
                'roles' => $user->getRoleNames()->values()->toArray(),
            ],
            'prices' => $prices->priceListFor($user),
            'consultationService' => $prices->consultationService()?->only(['id', 'name', 'code']),
        ]);
    }

    /**
     * Save the doctor's overrides. Sending the catalogue price removes the
     * override, so the list only shows real differences.
     */
    public function updatePricing(Request $request, User $user, PriceListService $prices)
    {
        Gate::authorize('manage-users');

        $validated = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $prices->syncPrices($user, $validated['prices']);

        return redirect()
            ->route('users.pricing', $user->id)
            ->with('success', 'Doctor prices updated.');
    }
}
