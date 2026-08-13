<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import Form from './Form.vue';
import { index, store } from '@/routes/superadmin/permissions';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Permissions', href: index() },
            { title: 'Create' },
        ],
    },
});
</script>

<template>
    <Head title="Create Permission" />
    <div class="px-4 py-6">
        <div class="mx-auto max-w-lg">
            <Button as-child variant="ghost" size="sm" class="mb-4 -ml-2">
                <Link :href="index()">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Permissions
                </Link>
            </Button>

            <Card>
                <CardHeader>
                    <Heading title="Create Permission" description="Add a new permission to the system" />
                </CardHeader>
                <CardContent>
                    <Form
                        :submit-action="store()"
                        :cancel-href="index().url"
                        submit-label="Create Permission"
                    />
                </CardContent>
            </Card>
        </div>
    </div>
</template>