<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\StorePermissionRequest;
use App\Http\Requests\Superadmin\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $permissions = Permission::query()
            ->when(
                $request->input('search'),
                function (Builder $query, string $search) {
                    $query->where(function (Builder $q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('label', 'like', "%{$search}%")
                            ->orWhere('group', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                $request->input('sort'),
                function (Builder $query, string $sort) {
                    foreach (explode(',', $sort) as $field) {
                        $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
                        $column = ltrim($field, '-');

                        if (in_array($column, ['name', 'label', 'group'])) {
                            $query->orderBy($column, $direction);
                        }
                    }
                },
                fn(Builder $query) => $query->orderBy('group')->orderBy('label')
            )
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('superadmin/permissions/Index', [
            'permissions' => $permissions,
            'filters' => $request->only('sort', 'search', 'per_page'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('superadmin/permissions/Create');
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        Permission::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Permission created successfully.']);

        return redirect()->route('superadmin.permissions.index');
    }

    public function edit(Permission $permission): Response
    {
        return Inertia::render('superadmin/permissions/Edit', [
            'permission' => $permission,
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $permission->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Permission updated successfully.']);

        return redirect()->route('superadmin.permissions.index');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Permission deleted successfully.']);

        return redirect()->route('superadmin.permissions.index');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'No permissions selected for deletion.']);
            return redirect()->back();
        }

        $deleted = Permission::whereIn('id', $ids)->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => $deleted . ' permission(s) deleted successfully.']);

        return redirect()->route('superadmin.permissions.index');
    }
}