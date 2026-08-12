// columns.ts
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Pencil, Trash2 } from '@lucide/vue';
import type { AppTableFeatures } from '@/lib/tableFeatures';

export type UserRow = {
    id: number;
    name: string;
    email: string;
    role: { id: number; name: string } | null;
};

export function createColumns(
    onEdit: (user: UserRow) => void,
    onDelete: (user: UserRow) => void,
): ColumnDef<AppTableFeatures, UserRow>[] {
    return [
        {
            id: 'select',
            header: ({ table }) =>
                h(Checkbox, {
                    checked: table.getIsAllPageRowsSelected(),
                    'onUpdate:checked': (value: boolean) => {
                        table.toggleAllPageRowsSelected(!!value);
                    },
                    indeterminate: table.getIsSomePageRowsSelected(),
                    'aria-label': 'Select all',
                }),
            cell: ({ row }) =>
                h(Checkbox, {
                    checked: row.getIsSelected(),
                    'onUpdate:checked': (value: boolean) => {
                        row.toggleSelected(!!value);
                    },
                    'aria-label': 'Select row',
                }),
            enableSorting: false,
            // Remove enableHiding as it doesn't exist in your version
        },
        { 
            accessorKey: 'name', 
            header: 'Name', 
            enableSorting: true 
        },
        { 
            accessorKey: 'email', 
            header: 'Email', 
            enableSorting: true 
        },
        {
            id: 'role',
            header: 'Role',
            accessorFn: (row) => row.role?.name ?? '—',
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