<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { LogOut } from '@lucide/vue';
import { computed } from 'vue';

import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

import { logout } from '@/routes';
import { useRole } from '@/composables/useRole';

import {
    superadminNavItems,
    adminNavItems,
    userNavItems,
} from '@/navigation';

const { isSuperadmin, isAdmin, isUser } = useRole();

const homeHref = computed(() => {
    if (isSuperadmin.value) return '/superadmin/dashboard';
    if (isAdmin.value) return '/admin/dashboard';
    if (isUser.value) return '/user/dashboard';

    return '/';
});

const handleLogout = () => {
    router.flushAll();
};

</script>

<template>
    <Sidebar collapsible="offcanvas" variant="sidebar">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="homeHref">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
   
            <template v-if="isSuperadmin">
                <NavMain label="Super Admin" :items="superadminNavItems" />
                <NavMain label="Admin" :items="adminNavItems" />
                <NavMain label="User" :items="userNavItems" />
            </template>

      
            <template v-else-if="isAdmin">
                <NavMain label="Admin" :items="adminNavItems" />
            </template>

        
            <template v-else-if="isUser">
                <NavMain label="User" :items="userNavItems" />
            </template>
        </SidebarContent>

        <SidebarFooter>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child>
                        <Link class="block w-full cursor-pointer" :href="logout()" @click="handleLogout" as="button"
                            data-test="logout-button">
                            <LogOut class="mr-2 h-4 w-4" />
                            <span>Log out</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>