<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\StoreRoleRequest;
use App\Http\Requests\Superadmin\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $roles = Role::query()
            ->when(
                $request->input('search'),
                function (Builder $query, string $search) {
                    $query->where(function (Builder $q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                $request->input('sort'),
                function (Builder $query, string $sort) {
                    foreach (explode(',', $sort) as $field) {
                        $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
                        $column = ltrim($field, '-');

                        if (in_array($column, ['name', 'description'])) {
                            $query->orderBy($column, $direction);
                        }
                    }
                },
                fn(Builder $query) => $query->orderBy('name')
            )
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('superadmin/roles/Index', [
            'roles' => $roles,
            'filters' => $request->only('sort', 'search', 'per_page'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('superadmin/roles/Create');
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Role::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Role created successfully.']);

        return redirect()->route('superadmin.roles.index');
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('superadmin/roles/Edit', [
            'role' => $role,
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Role updated successfully.']);

        return redirect()->route('superadmin.roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Role deleted successfully.']);

        return redirect()->route('superadmin.roles.index');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'No roles selected for deletion.']);
            return redirect()->back();
        }

        $deleted = Role::whereIn('id', $ids)->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => $deleted . ' role(s) deleted successfully.']);

        return redirect()->route('superadmin.roles.index');
    }
}