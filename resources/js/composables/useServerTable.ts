import { computed, type Ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useTable, type ColumnDef, type SortingState, type RowData } from '@tanstack/vue-table';
import { appTableFeatures, type AppTableFeatures } from '@/lib/tableFeatures';

type ServerTableOptions<T extends RowData> = {
    data: Ref<T[]>;
    columns: ColumnDef<AppTableFeatures, T>[];
    baseUrl: string;
    sort: Ref<string | undefined>; // e.g. "name,-email"
    search: Ref<string | undefined>;
};

function parseSorting(sort?: string | string[] | null): SortingState {
    if (!sort || typeof sort !== 'string') return [];
    return sort.split(',').map((part) => ({
        id: part.startsWith('-') ? part.slice(1) : part,
        desc: part.startsWith('-'),
    }));
}

// [{ id: 'name', desc: false }, { id: 'email', desc: true }] -> "name,-email"
function serializeSorting(sorting: SortingState): string | undefined {
    if (sorting.length === 0) return undefined;
    return sorting.map((s) => (s.desc ? `-${s.id}` : s.id)).join(',');
}

export function useServerTable<T extends RowData>({
    data,
    columns,
    baseUrl,
    sort,
    search,
}: ServerTableOptions<T>) {
    const sorting = computed<SortingState>(() => parseSorting(sort.value));

    const table = useTable({
        features: appTableFeatures,
        data,
        columns,
        state: {
            sorting: sorting.value,
        },
        enableMultiSort: true,
        manualSorting: true,
        onSortingChange: (updater) => {
            const next = typeof updater === 'function' ? updater(sorting.value) : updater;

            router.get(
                baseUrl,
                { sort: serializeSorting(next), search: search.value },
                { preserveState: true, preserveScroll: true, replace: true },
            );
        },
    });

    const runSearch = (value: string) => {
        router.get(
            baseUrl,
            { sort: sort.value, search: value || undefined },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    };

    return { table, runSearch };
}