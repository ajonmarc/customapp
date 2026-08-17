<?php

namespace App\Http\Controllers\Superadmin\Concerns;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

trait HasSessionFilters
{
    protected function getFilters(): array
    {
        $sort = $this->sanitizeSort(
            session("{$this->filterSessionKey}.sort"),
            $this->sortableColumns
        );
        $search = session("{$this->filterSessionKey}.search");
        $perPage = (int) session("{$this->filterSessionKey}.per_page", 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $page = (int) session("{$this->filterSessionKey}.page", 1);

        return compact('sort', 'search', 'perPage', 'page');
    }

    public function updateFilters(Request $request): Response
    {
        $sort = $this->sanitizeSort($request->input('sort'), $this->sortableColumns);

        $search = $request->input('search');
        $search = is_string($search) && $search !== '' ? $search : null;

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $page = max(1, (int) $request->input('page', 1));

        session([
            "{$this->filterSessionKey}.sort" => $sort,
            "{$this->filterSessionKey}.search" => $search,
            "{$this->filterSessionKey}.per_page" => $perPage,
            "{$this->filterSessionKey}.page" => $page,
        ]);

        // Render the index page directly - no redirect, no second round trip
        return $this->index();
    }

    private function sanitizeSort(mixed $sort, array $allowed): ?string
    {
        if (!is_string($sort) || $sort === '' || !preg_match('/^-?[a-zA-Z]+(,-?[a-zA-Z]+)*$/', $sort)) {
            return null;
        }

        $parts = collect(explode(',', $sort))
            ->filter(fn ($f) => in_array(ltrim($f, '-'), $allowed, true))
            ->values();

        return $parts->isNotEmpty() ? $parts->implode(',') : null;
    }
}