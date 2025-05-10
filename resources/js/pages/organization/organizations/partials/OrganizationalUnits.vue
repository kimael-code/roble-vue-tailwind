<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import { valueUpdater } from '@/components/ui/table/utils';
import { OrganizationalUnit, PaginatedCollection } from '@/types';
import { router } from '@inertiajs/vue3';
import { ColumnFiltersState, getCoreRowModel, getFilteredRowModel, getSortedRowModel, SortingState, useVueTable } from '@tanstack/vue-table';
import { ref, watch } from 'vue';
import { columns } from './columnsOrganizationalUnit';

interface Props {
  filters: object;
  resourceId: string | number;
  ous: PaginatedCollection<OrganizationalUnit>;
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

    router.visit(route('organizations.show', props.resourceId), {
      data,
      only: ['ous'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}

const table = useVueTable({
  columns: cols,
  data: props.ous.data,
  manualPagination: true,
  pageCount: props.ous.meta.per_page,
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
  () => props.ous.data,
  (newData) => table.setOptions((prev) => ({ ...prev, data: newData })),
);
</script>

<template>
  <DataTable
    :columns="cols"
    :data="ous"
    :filters
    :search-only="['ous']"
    :search-route="route('organizations.show', resourceId)"
    :table
    @search="(s) => (globalFilter = s)"
  />
</template>
