<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import Form from './Form.vue';
import { index, update } from '@/routes/superadmin/users';

const props = defineProps<{
    user: { id: number; name: string; email: string; role_id: number };
    roles: { id: number; name: string }[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Users', href: index() },
            { title: 'Edit' },
        ],
    },
});
</script>

<template>
    <Head title="Edit User" />
    <div class="px-4 py-6">
        <Heading title="Edit User" :description="`Editing ${user.name}`" />

        <div class="mt-6 max-w-2xl">
            <Form
                :user="user"
                :roles="roles"
                :submit-action="update(user.id)"
                submit-label="Save Changes"
            />
        </div>
    </div>
</template>