<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import { valueUpdater } from '@/components/ui/table/utils';
import { useRequestActions } from '@/composables';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { ActivityLog, BreadcrumbItem, Can, PaginatedCollection, SearchFilter, User } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import {
  ColumnFiltersState,
  ExpandedState,
  getCoreRowModel,
  getFilteredRowModel,
  getSortedRowModel,
  RowSelectionState,
  SortingState,
  useVueTable,
} from '@tanstack/vue-table';
import { LogsIcon } from 'lucide-vue-next';
import { ref, watch, watchEffect } from 'vue';
import { columns, permissions, processingRowId } from './partials/columns';

const props = defineProps<{
  can: Can;
  filters: SearchFilter;
  users?: Array<User>;
  events?: Array<string>;
  logs: PaginatedCollection<ActivityLog>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Trazas',
    href: '/activity-logs',
  },
];

const { requestRead, requestingRead } = useRequestActions('activity-logs');

permissions.value = props.can;
const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const globalFilter = ref('');
const rowSelection = ref<RowSelectionState>({});
const expanded = ref<ExpandedState>({});

function handleSortingChange(item: any) {
  if (typeof item === 'function') {
    const sortValue = item(sorting.value);
    const data: { [index: string]: any } = {};

    sortValue.forEach((element: any) => {
      const sortBy = element?.id ? element.id : '';
      const sortDirection = sortBy ? (element?.desc ? 'desc' : 'asc') : '';
      data[sortBy] = sortDirection;
    });

    router.visit(route('activity-logs.index'), {
      data: { sortBy: data },
      only: ['logs'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}

const table = useVueTable({
  data: props.logs.data,
  columns: columns,
  manualPagination: true,
  pageCount: props.logs.meta.per_page,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getRowId: (row) => row.id,
  onSortingChange: (updaterOrValue) => handleSortingChange(updaterOrValue),
  onColumnFiltersChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnFilters),
  onGlobalFilterChange: (updaterOrValue) => valueUpdater(updaterOrValue, globalFilter),
  onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
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
    get rowSelection() {
      return rowSelection.value;
    },
    get expanded() {
      return expanded.value;
    },
  },
});

watch(
  () => props.logs.data,
  (newData) => table.setOptions((prev) => ({ ...prev, data: newData })),
);
watchEffect(() => (requestingRead.value === false ? (processingRowId.value = null) : false));
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Trazas" />

    <ContentLayout title="Trazas">
      <template #icon>
        <LogsIcon />
      </template>
      <DataTable
        :can="can"
        :columns="columns"
        :data="logs"
        :filters="filters"
        :search-only="['logs']"
        :search-route="route('activity-logs.index')"
        :table="table"
        @search="(s) => (globalFilter = s)"
        @read="(row) => (requestRead(row.id), (processingRowId = row.id))"
      />
    </ContentLayout>
  </AppLayout>
</template>
