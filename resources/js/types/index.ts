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
  id: string;
  name: string;
  guard_name: string;
  created_at?: string;
  updated_at?: string;
  set_menu: boolean;
  description: string;
}

export interface Can {
  create: boolean;
  read: boolean;
  update: boolean;
  delete: boolean;
  export?: boolean;
}

export interface PaginatedLink {
    url: string | URL,
    label: number;
    active: boolean;
}

export interface Pagination<T> {
    current_page: number;
    data: Array<T>;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<PaginatedLink>;
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: string;
    to: number;
    total: number;
}

export interface PaginatedCollectionLinks {
    first: string;
    last: string;
    prev: string;
    next: string;
}

export interface PaginatedCollectionMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: Array<PaginatedLink>;
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface PaginatedCollection<T> {
    data: Array<T>;
    links: PaginatedCollectionLinks;
    meta: PaginatedCollectionMeta;
}

export type BreadcrumbItemType = BreadcrumbItem;
