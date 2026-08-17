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
use App\Http\Controllers\Superadmin\Concerns\HasSessionFilters;

class RoleController extends Controller
{
    use HasSessionFilters;

    protected string $filterSessionKey = 'roles';
    protected array $sortableColumns = ['name', 'description'];
    protected string $filterIndexRoute = 'superadmin.roles.index';

    public function index(): Response
    {
        $sort = $this->sanitizeSort(session('roles.sort'), ['name', 'description']);
        $search = session('roles.search');
        $perPage = (int) session('roles.per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $page = (int) session('roles.page', 1);

        $roles = Role::query()
            ->when($search, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($sort, function (Builder $query) use ($sort) {
                foreach (explode(',', $sort) as $field) {
                    $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
                    $query->orderBy(ltrim($field, '-'), $direction);
                }
            }, fn (Builder $query) => $query->orderBy('name'))
            ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('superadmin/roles/Index', [
            'roles' => $roles,
            'filters' => [
                'sort' => $sort,
                'search' => $search,
                'per_page' => $perPage,
            ],
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