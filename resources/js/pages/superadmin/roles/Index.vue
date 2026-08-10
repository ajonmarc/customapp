<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { roles } from '@/routes/superadmin';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                href: roles(),
            },
        ],
    },
});
</script>

<template>

    <Head title="Roles" />
    <div class="px-4 py-6">
        <Heading title="Superadmin Roles" description="manage your superadmin roles" />

        <div class="mt-6 max-w-2xl">
            <section class="space-y-12">
                main content
            </section>
        </div>
    </div>
</template>
