<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import { valueUpdater } from '@/components/ui/table/utils';
import { PaginatedCollection, Permission } from '@/types';
import { router } from '@inertiajs/vue3';
import { ColumnFiltersState, getCoreRowModel, getFilteredRowModel, getSortedRowModel, SortingState, useVueTable } from '@tanstack/vue-table';
import { ref, watch } from 'vue';
import { columns } from './columnsRole';

interface Props {
  filters: object;
  roleId: string | number;
  permissions: PaginatedCollection<Permission>;
}
const props = defineProps<Props>();

const cols = columns();
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

    router.visit(route('permissions.show', { permission: props.roleId }), {
      data,
      only: ['permissions'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}

const table = useVueTable({
  columns: cols,
  data: props.permissions.data,
  manualPagination: true,
  pageCount: props.permissions.meta.per_page,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getRowId: (row) => row.id,
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
  () => props.permissions.data,
  (newData) => table.setOptions((prev) => ({ ...prev, data: newData })),
);
</script>

<template>
  <DataTable
    :columns="cols"
    :data="permissions"
    :filters
    :search-only="['permissions']"
    :search-route="route('roles.show', { role: roleId })"
    :table
    @search="(s) => (globalFilter = s)"
  />
</template>
