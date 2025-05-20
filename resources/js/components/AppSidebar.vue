<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { SharedData, type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Bug, Building, Folder, Footprints, KeySquare, LayoutGrid, User, Users, Workflow } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import NavCompany from './NavCompany.vue';
import NavDebug from './NavDebug.vue';
import NavSecurity from './NavSecurity.vue';

const page = usePage<SharedData>();

const mainNavItems: NavItem[] = [
  {
    title: 'Tablero',
    href: '/dashboard',
    icon: LayoutGrid,
  },
];
const companyNavItems: NavItem[] = [
  {
    title: 'Entes',
    href: '/organizations',
    icon: Building,
    hasPermission: page.props.auth?.menu.includes('read any organization'),
  },
  {
    title: 'Unidades Administrativas',
    href: '/organizational-units',
    icon: Workflow,
    hasPermission: page.props.auth?.menu.includes('read any organizational unit'),
  },
];
const securityNavItems: NavItem[] = [
  {
    title: 'Permisos',
    href: '/permissions',
    icon: KeySquare,
    hasPermission: page.props.auth?.menu.includes('read any permission'),
  },
  {
    title: 'Roles',
    href: '/roles',
    icon: Users,
    hasPermission: page.props.auth?.menu.includes('read any role'),
  },
  {
    title: 'Usuarios',
    href: '/users',
    icon: User,
    hasPermission: page.props.auth?.menu.includes('read any user'),
  },
];
const debugNavItems: NavItem[] = [
  {
    title: 'Trazas',
    href: '/dashboard',
    icon: Footprints,
    hasPermission: page.props.auth?.menu.includes('read any activity trace'),
  },
  {
    title: 'Logs',
    href: '/log-files',
    icon: Bug,
    hasPermission: page.props.auth?.menu.includes('read any system log'),
  },
];

const footerNavItems: NavItem[] = [
  {
    title: 'Github Repo',
    href: 'https://github.com/laravel/vue-starter-kit',
    icon: Folder,
  },
  {
    title: 'Documentation',
    href: 'https://laravel.com/docs/starter-kits',
    icon: BookOpen,
  },
];
</script>

<template>
  <Sidebar collapsible="icon" variant="floating">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
      <NavCompany :items="companyNavItems" />
      <NavSecurity :items="securityNavItems" />
      <NavDebug :items="debugNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
