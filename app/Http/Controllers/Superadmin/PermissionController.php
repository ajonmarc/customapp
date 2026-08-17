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
use App\Http\Controllers\Superadmin\Concerns\HasSessionFilters;

class PermissionController extends Controller
{
    use HasSessionFilters;

    protected string $filterSessionKey = 'permissions';
    protected array $sortableColumns = ['name', 'label', 'group'];

    protected string $filterIndexRoute = 'superadmin.permissions.index';

    public function index(): Response
    {
        $sort = $this->sanitizeSort(session('permissions.sort'), ['name', 'label', 'group']);
        $search = session('permissions.search');
        $perPage = (int) session('permissions.per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $page = (int) session('permissions.page', 1);

        $permissions = Permission::query()
            ->when($search, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('label', 'like', "%{$search}%")
                        ->orWhere('group', 'like', "%{$search}%");
                });
            })
            ->when($sort, function (Builder $query) use ($sort) {
                foreach (explode(',', $sort) as $field) {
                    $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
                    $query->orderBy(ltrim($field, '-'), $direction);
                }
            }, fn (Builder $query) => $query->orderBy('group')->orderBy('label'))
            ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('superadmin/permissions/Index', [
            'permissions' => $permissions,
            'filters' => [
                'sort' => $sort,
                'search' => $search,
                'per_page' => $perPage,
            ],
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