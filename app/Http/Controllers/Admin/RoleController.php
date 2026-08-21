<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(): Response
    {
        return Inertia::render('admin/roles/Index');
    }


    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    public function getData(Request $request): JsonResponse
    {
        $query = Role::query();


        // Search
        $query->when($request->input('search'), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        });


        // Sorting
        $allowedSorts = [
            'id',
            'name',
            'description',
        ];

        $sort = $request->input('sort');

        if ($sort && in_array($sort, $allowedSorts, true)) {
            $order = $request->input('order', 'asc');

            if (! in_array($order, ['asc', 'desc'], true)) {
                $order = 'asc';
            }

            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }


        // Pagination
        $perPage = (int) $request->input('per_page', 10);

        $perPage = min(max($perPage, 1), 100);


        // Response
        return response()->json(
            $query
                ->paginate($perPage)
                ->through(function (Role $role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'description' => $role->description,
                    ];
                })
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Role::create(
            $request->validated()
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Role created successfully.',
        ]);

        return redirect()->route('admin.roles.index');
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {

        $data = $request->validated();



        $role->update($data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Role updated successfully.',
        ]);

        return redirect()->route('admin.roles.index');
    }


    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(Role $role): RedirectResponse
    {


        $role->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Role deleted successfully.',
        ]);

        return redirect()->route('admin.roles.index');
    }
}
