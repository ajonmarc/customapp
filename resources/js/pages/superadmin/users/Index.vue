<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { users } from '@/routes/superadmin';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                href: users(),
            },
        ],
    },
});
</script>

<template>

    <Head title="Users" />
    <div class="px-4 py-6">
        <Heading title="Superadmin Users" description="manage your superadmin users" />

        <div class="mt-6 max-w-2xl">
            <section class="space-y-12">
                main content
            </section>
        </div>
    </div>
</template>
