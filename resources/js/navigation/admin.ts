import type { NavItem } from '@/types';
import {
    LayoutGrid,
    Users,
    MapPin,
    CalendarClock,
    ClipboardList,
    CreditCard,
    CloudSun,
} from '@lucide/vue';

export const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },

    {
        title: 'User Management',
        href: '/admin/users',
        icon: Users,
        isActive: true,
        items: [
            {
                title: 'Users',
                href: '/admin/users',
                icon: Users,
            },
            {
                title: 'Roles',
                href: '/admin/roles',
                icon: Users,
            },
            {
                title: 'Permissions',
                href: '/admin/permissions',
                icon: Users,
            },
        ],

    },

    {
        title: 'Packages',
        href: '/admin/packages',
        icon: CalendarClock,
    },

    {
        title: 'Pickup Locations',
        href: '/admin/pickup-locations',
        icon: MapPin,
    },

    {
        title: 'Pickup Schedules',
        href: '/admin/pickup-schedules',
        icon: CalendarClock,
    },

    {
        title: 'Bookings',
        href: '/admin/bookings',
        icon: ClipboardList,
    },

    {
        title: 'Payments',
        href: '/admin/payments',
        icon: CreditCard,
    },

    {
        title: 'Weather Advisory',
        href: '/admin/weather-advisories',
        icon: CloudSun,
    },
];