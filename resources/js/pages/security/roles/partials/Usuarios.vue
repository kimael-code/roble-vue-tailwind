<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import { valueUpdater } from '@/components/ui/table/utils';
import { PaginatedCollection, User } from '@/types';
import { router } from '@inertiajs/vue3';
import { ColumnFiltersState, getCoreRowModel, getFilteredRowModel, getSortedRowModel, SortingState, useVueTable } from '@tanstack/vue-table';
import { ref, watch } from 'vue';
import { columns } from './columnsUser';

interface Props {
  filters: object;
  roleId: string | number;
  users: PaginatedCollection<User>;
}
const props = defineProps<Props>();

const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const globalFilter = ref('');

function handleSortingChange(item: any) {
  if (typeof item === 'function') {
    const sortValue = item(sorting.value);
    const data: { [index: string]: any } = {};

    sortValue.forEach((element: any) => {
      const sortBy = element?.id ? element.id : '';
      const sortDirection = sortBy ? (element?.desc ? 'desc' : 'asc') : '';
      data[sortBy] = sortDirection;
    });

    router.visit(route('roles.show', { role: props.roleId }), {
      data,
      only: ['users'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}

const table = useVueTable({
  columns: columns,
  data: props.users.data,
  manualPagination: true,
  pageCount: props.users.meta.per_page,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  // getRowId: (row) => row.id,
  onSortingChange: (updateOrValue) => handleSortingChange(updateOrValue),
  onColumnFiltersChange: (updateOrValue) => valueUpdater(updateOrValue, columnFilters),
  onGlobalFilterChange: (updateOrValue) => valueUpdater(updateOrValue, globalFilter),
  state: {
    get sorting() {
      return sorting.value;
    },
    get columnFilters() {
      return columnFilters.value;
    },
    get globalFilter() {
      return globalFilter.value;
    },
  },
});

watch(
  () => props.users.data,
  (newData) => table.setOptions((prev) => ({ ...prev, data: newData })),
);
</script>

<template>
  <DataTable
    :columns="columns"
    :data="users"
    :filters
    :search-only="['users']"
    :search-route="route('permissions.show', { permission: roleId })"
    :table
    @search="(s) => (globalFilter = s)"
  />
</template>
