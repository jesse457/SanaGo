<?php

namespace App\Http\Controllers\Api\Tenants\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserShift;
use App\Services\UserService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    use HttpResponses;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of staff members.
     * GET /api/admin/users
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'role' => $request->query('role'),
            'status' => $request->query('status'),
        ];

        $users = $this->userService->getUsersQuery($filters)
            ->paginate($request->query('per_page', 10));

        return UserResource::collection($users);
    }

    /**
     * Get secure preview link for an attachment.
     */
    public function previewImage($userId): JsonResponse
    {
        try {
            $preview = $this->userService->getAttachmentPreview($userId);

            return $this->success($preview, 'Preview generated.');
        } catch (\Exception $e) {
            return $this->error(null, 'File not found.', 404);
        }
    }

    /**
     * Store a newly created staff member.
     * POST /api/admin/users
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|in:doctor,nurse,receptionist,pharmacist,lab-technician',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'profile_picture' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $user = $this->userService->createUser(
            $data,
            $request->file('profile_picture'),
            tenant('id')
        );

        return (new UserResource($user))
            ->additional(['message' => 'Staff member created and invitation sent.']);
    }

    /**
     * Display the specified staff member.
     * GET /api/admin/users/{id}
     */
    public function show($id)
    {
        $user = User::with(['shifts', 'department'])->findOrFail($id);

        return new UserResource($user);
    }

    /**
     * Update the specified staff member.
     * PUT/PATCH /api/admin/users/{id}
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($id)],
            'role' => 'sometimes|required|string',
            'phone_number' => 'nullable|string',
            'is_active' => 'boolean',
            'selected_shift_id' => 'nullable|exists:user_shifts,id',
        ]);

        $user = $this->userService->updateUser($id, $data);

        return (new UserResource($user))
            ->additional(['message' => 'Staff records updated successfully.']);
    }

    /**
     * Remove the specified staff member.
     * DELETE /api/admin/users/{id}
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Optionally delete profile picture from S3 before deleting record
        if ($user->profile_picture) {
            Storage::disk('s3')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member removed from records.',
        ]);
    }

    /**
     * Resend the invitation email.
     * POST /api/admin/users/{id}/resend-invitation
     */
    public function resendInvitation($id)
    {
        $user = User::findOrFail($id);

        if ($user->email_verified_at) {
            return response()->json(['message' => 'User is already verified.'], 422);
        }

        $this->userService->sendInvitation($user);

        return response()->json(['success' => true, 'message' => 'Invitation resent.']);
    }

    /**
     * Helper to get available shifts for the UI selection.
     * GET /api/admin/available-shifts
     */
    public function availableShifts()
    {
        $shifts = UserShift::where('shift_date', '>=', now()->toDateString())
            ->orderBy('shift_date', 'asc')
            ->get();

        return response()->json($shifts);
    }
}
