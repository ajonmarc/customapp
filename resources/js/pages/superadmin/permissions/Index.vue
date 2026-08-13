<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { index } from '@/routes/superadmin/permissions';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Permissions', href: index() }],
    },
});
</script>

<template>

    <Head title="Permissions" />
    <div class="px-4 py-6">
        <Heading title="Superadmin Permissions" description="manage your superadmin permissions" />

        <div class="mt-6 max-w-2xl">
            <section class="space-y-12">
                main content
            </section>
        </div>
    </div>
</template>
