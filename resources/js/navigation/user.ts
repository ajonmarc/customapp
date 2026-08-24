import type { NavItem } from '@/types';
import {
    LayoutGrid,
    Sailboat,
    ClipboardList,
} from '@lucide/vue';

export const userNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/user/dashboard',
        icon: LayoutGrid,
    },

    {
        title: 'Island Hopping',
        href: '/user/packages',
        icon: Sailboat,
    },

    {
        title: 'My Bookings',
        href: '/user/bookings',
        icon: ClipboardList,
    },
];