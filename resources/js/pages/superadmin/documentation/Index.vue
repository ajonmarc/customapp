<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { documentation } from '@/routes/superadmin';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                href: documentation(),
                title: 'Documentation',
            },
        ],
    },
});

</script>

<template>

    <Head title="Documentation" />
    <div class="px-4 py-6">
        <Heading title="Superadmin Documentation" description="manage your superadmin documentation" />

        <div class="mt-6 max-w-2xl">
            <section class="space-y-12">
                main content all components here...
            </section>
        </div>
    </div>

</template>