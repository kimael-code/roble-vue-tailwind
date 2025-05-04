import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

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
  flash: { message: { message: string; title: string; type: string } };
  ziggy: Config & { location: string };
  sidebarOpen: boolean;
}

export interface User {
  id: string;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
  is_password_set: boolean;
  created_at_human?: string | null;
  updated_at_human?: string | null;
  deleted_at_human?: string | null;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Role {
  id: string;
  name: string;
  guard_name: string;
  created_at: string;
  updated_at: string;
  created_at_human?: string;
  updated_at_human?: string;
  deleted_at?: string | null;
  description: string;
}

export interface Permission extends Role {
  set_menu: boolean;
  pivot?: { [index: string]: string | number };
}

export interface Employee {
  company_code?: string;
  nationality?: string;
  id_card: string;
  rif: string;
  names: string;
  surnames: string;
  staff_type_code: string;
  org_unit_code: string;
  position: string;
  staff_type_name: string;
  org_unit_name: string;
}

export interface Can {
  create: boolean;
  read?: boolean;
  update: boolean;
  delete: boolean;
  enable?: boolean;
  disable?: boolean;
  export?: boolean;
}

export interface PaginatedLink {
  url: string | URL;
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

export interface SearchFilter {
  [index: string]: string | undefined;
}
