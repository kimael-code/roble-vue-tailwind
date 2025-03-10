import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
    menu: Array<string>;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
    hasPermission?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Permission {
  id: number;
  name: string;
  guard_name: string;
  set_menu: boolean;
  description: string;
}

export interface Can {
  create: string,
  read: string,
  update: string,
  delete: string,
  export?: string,
}

export type BreadcrumbItemType = BreadcrumbItem;
