<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { index, create, edit, destroy } from '@/routes/superadmin/users';

type UserRow = {
    id: number;
    name: string;
    email: string;
    role: { id: number; name: string } | null;
};

const props = defineProps<{
    users: {
        data: UserRow[];
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Users', href: index() }],
    },
});

const handleDelete = (id: number, name: string) => {
    if (confirm(`Delete user "${name}"? This cannot be undone.`)) {
        router.delete(destroy(id));
    }
};
</script>

<template>
    <Head title="Users" />
    <div class="px-4 py-6">
        <div class="flex items-center justify-between">
            <Heading title="Superadmin Users" description="Manage your superadmin users" />
            <Button as-child>
                <Link :href="create()">
                    <Plus class="mr-2 h-4 w-4" />
                    New User
                </Link>
            </Button>
        </div>

        <div class="mt-6">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="row in props.users.data" :key="row.id">
                        <TableCell>{{ row.name }}</TableCell>
                        <TableCell>{{ row.email }}</TableCell>
                        <TableCell>{{ row.role?.name ?? '—' }}</TableCell>
                        <TableCell class="flex justify-end gap-2">
                            <Button variant="ghost" size="icon" as-child>
                                <Link :href="edit(row.id)">
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon"
                                @click="handleDelete(row.id, row.name)"
                            >
                                <Trash2 class="h-4 w-4 text-destructive" />
                            </Button>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="props.users.data.length === 0">
                        <TableCell colspan="4" class="text-center text-muted-foreground">
                            No users found.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <div v-if="props.users.links.length > 3" class="mt-4 flex gap-1">
                <Link
                    v-for="(link, i) in props.users.links"
                    :key="i"
                    :href="link.url ?? '#'"
                    :class="[
                        'rounded px-3 py-1 text-sm',
                        link.active ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted',
                        !link.url && 'pointer-events-none opacity-50',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </div>
</template>