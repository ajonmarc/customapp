<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { LogOut, LayoutGrid, Users, Anchor, MapPin, ClipboardCheck, CreditCard, FileBarChart, Activity, Sailboat, CalendarClock, CloudSun, ClipboardList } from '@lucide/vue';
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
import type { NavItem } from '@/types';

const { isSuperadmin, isAdmin, isUser } = useRole();

const homeHref = computed(() => {
    if (isSuperadmin.value) return '/superadmin/dashboard';
    if (isAdmin.value) return '/admin/dashboard';
    if (isUser.value) return '/user/dashboard';
    return '/';
});

const superadminNavItems: NavItem[] = [
    { title: 'Dashboard', href: '/superadmin/dashboard', icon: LayoutGrid },

];

const adminNavItems: NavItem[] = [
    { title: 'Dashboard', href: '/admin/dashboard', icon: LayoutGrid },

];

const userNavItems: NavItem[] = [
    { title: 'Dashboard', href: '/user/dashboard', icon: LayoutGrid },

];

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
            <!-- Super Administrator -->
            <template v-if="isSuperadmin">
                <NavMain label="Super Administrator" :items="superadminNavItems" />
                <NavMain label="Administrator" :items="adminNavItems" />
                <NavMain label="User" :items="userNavItems" />
            </template>

            <!-- Administrator -->
            <template v-else-if="isAdmin">
                <NavMain label="Administrator" :items="adminNavItems" />
            </template>

            <!-- User -->
            <NavMain v-else-if="isUser" label="User" :items="userNavItems" />
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