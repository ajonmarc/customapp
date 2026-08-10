<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';

type RoleOption = { id: number; name: string };

type UserFormValues = {
    id?: number;
    name: string;
    email: string;
    role_id: number | string;
};

const props = defineProps<{
    roles: RoleOption[];
    user?: UserFormValues;
    submitAction: { url: string; method: 'post' | 'put' };
    submitLabel: string;
}>();
</script>

<template>
    <Form
        :action="submitAction.url"
        :method="submitAction.method"
        class="flex flex-col gap-6"
        v-slot="{ errors, processing }"
    >
        <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input
                id="name"
                name="name"
                :default-value="user?.name"
                required
                autocomplete="name"
                placeholder="Full name"
            />
            <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="email">Email address</Label>
            <Input
                id="email"
                type="email"
                name="email"
                :default-value="user?.email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />
            <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
            <Label for="password">
                Password
                <span v-if="user" class="text-muted-foreground">(leave blank to keep current)</span>
            </Label>
            <Input
                id="password"
                type="password"
                name="password"
                :required="!user"
                autocomplete="new-password"
                placeholder="Password"
            />
            <InputError :message="errors.password" />
        </div>

        <div class="grid gap-2">
            <Label for="password_confirmation">Confirm password</Label>
            <Input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="Confirm password"
            />
            <InputError :message="errors.password_confirmation" />
        </div>

        <div class="grid gap-2">
            <Label for="role_id">Role</Label>
            <Select :name="'role_id'" :default-value="user?.role_id?.toString()">
                <SelectTrigger id="role_id" class="w-full">
                    <SelectValue placeholder="Select a role" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem
                        v-for="role in roles"
                        :key="role.id"
                        :value="role.id.toString()"
                    >
                        {{ role.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="errors.role_id" />
        </div>

        <div class="flex items-center gap-4">
            <Button type="submit" :disabled="processing">
                <Spinner v-if="processing" />
                {{ submitLabel }}
            </Button>
        </div>
    </Form>
</template>