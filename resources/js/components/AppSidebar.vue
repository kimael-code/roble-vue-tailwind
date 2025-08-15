<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Bug, Building, Construction, FolderGit2, KeySquare, LayoutGrid, LogsIcon, User, Users, Workflow } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import NavCompany from './NavCompany.vue';
import NavDebug from './NavDebug.vue';
import NavSecurity from './NavSecurity.vue';

const page = usePage();

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
    hasPermission: page.props.auth?.menu.includes('read any organization') || page.props.auth?.user?.roles?.some((r) => r.name === 'Superusuario'),
  },
  {
    title: 'Unidades Administrativas',
    href: '/organizational-units',
    icon: Workflow,
    hasPermission:
      page.props.auth?.menu.includes('read any organizational unit') || page.props.auth?.user?.roles?.some((r) => r.name === 'Superusuario'),
  },
];
const securityNavItems: NavItem[] = [
  {
    title: 'Permisos',
    href: '/permissions',
    icon: KeySquare,
    hasPermission: page.props.auth?.menu.includes('read any permission') || page.props.auth?.user?.roles?.some((r) => r.name === 'Superusuario'),
  },
  {
    title: 'Roles',
    href: '/roles',
    icon: Users,
    hasPermission: page.props.auth?.menu.includes('read any role') || page.props.auth?.user?.roles?.some((r) => r.name === 'Superusuario'),
  },
  {
    title: 'Usuarios',
    href: '/users',
    icon: User,
    hasPermission: page.props.auth?.menu.includes('read any user') || page.props.auth?.user?.roles?.some((r) => r.name === 'Superusuario'),
  },
];
const debugNavItems: NavItem[] = [
  {
    title: 'Trazas',
    href: '/activity-logs',
    icon: LogsIcon,
    hasPermission: page.props.auth?.menu.includes('read any activity trace') || page.props.auth?.user?.roles?.some((r) => r.name === 'Superusuario'),
  },
  {
    title: 'Depuración',
    href: '/log-files',
    icon: Bug,
    hasPermission: page.props.auth?.menu.includes('read any system log') || page.props.auth?.user?.roles?.some((r) => r.name === 'Superusuario'),
  },
  {
    title: 'Modo Mantenimiento',
    href: '/maintenance-mode',
    icon: Construction,
    hasPermission: page.props.auth?.menu.includes('manage maintenance mode') || page.props.auth?.user?.roles?.some((r) => r.name === 'Superusuario'),
  },
];

const footerNavItems: NavItem[] = [
  {
    title: 'Github Repo',
    href: 'https://github.com/kimael-code/roble-vue-tailwind',
    icon: FolderGit2,
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

<style lang="css" scoped>
/* Sólo para navegadores basados en Chromium (Chrome, Opera, Brave, Edge...) */

/* Estilo general para la barra de desplazamiento */
::-webkit-scrollbar {
  width: 4px;
  height: 8px;
}

/* Fondo de la barra de desplazamiento */
::-webkit-scrollbar-track {
  border-radius: 10px;
}

/* El "pulgar" o la parte arrastrable de la barra de desplazamiento */
::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

/* El "pulgar" al pasar el ratón por encima */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
