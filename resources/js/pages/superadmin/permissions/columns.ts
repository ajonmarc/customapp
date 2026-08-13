// resources/js/pages/superadmin/permissions/columns.ts
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Pencil, Trash2 } from '@lucide/vue';
import type { AppTableFeatures } from '@/lib/tableFeatures';

export type PermissionRow = {
    id: number;
    name: string;
    label: string;
    group: string;
    description: string | null;
};

export function createColumns(
    onEdit: (permission: PermissionRow) => void,
    onDelete: (permission: PermissionRow) => void,
): ColumnDef<AppTableFeatures, PermissionRow>[] {
    return [
        {
            id: 'select',
            header: ({ table }) =>
                h(Checkbox, {
                    modelValue: table.getIsAllPageRowsSelected()
                        ? true
                        : table.getIsSomePageRowsSelected()
                            ? 'indeterminate'
                            : false,
                    'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
                        table.toggleAllPageRowsSelected(!!value);
                    },
                    'aria-label': 'Select all',
                }),
            cell: ({ row }) =>
                h(Checkbox, {
                    modelValue: row.getIsSelected(),
                    'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
                        row.toggleSelected(!!value);
                    },
                    'aria-label': 'Select row',
                }),
            enableSorting: false,
        },
        {
            accessorKey: 'label',
            header: 'Label',
            enableSorting: true,
        },
        {
            accessorKey: 'name',
            header: 'Name',
            enableSorting: true,
        },
        {
            accessorKey: 'group',
            header: 'Group',
            enableSorting: true,
        },
        {
            id: 'description',
            header: 'Description',
            accessorFn: (row) => row.description ?? '—',
            enableSorting: false,
        },
        {
            id: 'actions',
            header: 'Actions',
            enableSorting: false,
            cell: ({ row }) =>
                h('div', { class: 'flex justify-start gap-2' }, [
                    h(
                        Button,
                        {
                            variant: 'default',
                            size: 'sm',
                            class: 'h-8 w-8 p-0 bg-blue-600 hover:bg-blue-700',
                            onClick: () => onEdit(row.original),
                        },
                        () => h(Pencil, { class: 'h-4 w-4 text-white' }),
                    ),
                    h(
                        Button,
                        {
                            variant: 'destructive',
                            size: 'sm',
                            class: 'h-8 w-8 p-0',
                            onClick: () => onDelete(row.original),
                        },
                        () => h(Trash2, { class: 'h-4 w-4' }),
                    ),
                ]),
        },
    ];
}