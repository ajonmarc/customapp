<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\StoreUserRequest;
use App\Http\Requests\Superadmin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $users = User::query()
            ->with('role') // Make sure role is loaded
            ->when(
                $request->input('search'),
                function (Builder $query, string $search) {
                    $query->where(function (Builder $q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                $request->input('sort'),
                function (Builder $query, string $sort) {
                    foreach (explode(',', $sort) as $field) {
                        $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
                        $column = ltrim($field, '-');

                        if (in_array($column, ['name', 'email'])) {
                            $query->orderBy($column, $direction);
                        }
                    }
                },
                fn(Builder $query) => $query->latest()
            )
            ->paginate($perPage)
            ->withQueryString();

        // Add roles to the index page as well (for the edit modal)
        $roles = Role::select('id', 'name')->get();

        return Inertia::render('superadmin/users/Index', [
            'users' => $users,
            'roles' => $roles, // Pass roles to the index page
            'filters' => $request->only('sort', 'search', 'per_page'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('superadmin/users/Create', [
            'roles' => Role::select('id', 'name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            ...$request->validated(),
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('superadmin/users/Edit', [
            'user' => $user->load('role'), // Load the role relationship
            'roles' => Role::select('id', 'name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($user->is_protected) {
            return redirect()
                ->back()
                ->with('error', 'This account is protected and cannot be modified.');
        }

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is_protected) {
            return redirect()
                ->back()
                ->with('error', 'This account is protected and cannot be deleted.');
        }

        $user->delete();

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()
                ->back()
                ->with('error', 'No users selected for deletion.');
        }

        // Validate that all IDs exist AND are not protected
        $validIds = User::whereIn('id', $ids)
            ->where('is_protected', false)
            ->pluck('id')
            ->toArray();

        if (empty($validIds)) {
            return redirect()
                ->back()
                ->with('error', 'No valid users found for deletion.');
        }

        // Delete the users
        $deleted = User::whereIn('id', $validIds)->delete();

        $skipped = count($ids) - count($validIds);
        $message = $deleted . ' user(s) deleted successfully.';
        if ($skipped > 0) {
            $message .= " {$skipped} protected account(s) were skipped.";
        }

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', $message);
    }
}
