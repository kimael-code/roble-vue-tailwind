<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import IndexLayout from '@/layouts/IndexLayout.vue';
import { BreadcrumbItem, Can, Pagination, Permission } from '@/types';
import { Head } from '@inertiajs/vue3';
import DataTable from './datatable/DataTable.vue';

interface Props {
  can: Can;
  filters: object;
  permissions: Pagination<Permission>;
}
const props = defineProps<Props>();
console.log(props);


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Permisos',
    href: '/dashboard',
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Permisos" />

    <IndexLayout title="Permisos">
        <DataTable :can="can" :paginated-data="permissions" />
    </IndexLayout>
  </AppLayout>
</template>
