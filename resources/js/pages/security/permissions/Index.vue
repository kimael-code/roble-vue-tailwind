<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import IndexLayout from '@/layouts/IndexLayout.vue';
import { valueUpdater } from '@/lib/utils';
import { BreadcrumbItem, Can, PaginatedCollection, Permission } from '@/types';
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
import { KeySquare } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { columns } from './partials/columns';

interface Props {
  can: Can;
  filters: object;
  permissions: PaginatedCollection<Permission>;
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Permisos',
    href: '/dashboard',
  },
];

const cols = columns(props.can);
const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const globalFilter = ref('');
const rowSelection = ref<RowSelectionState>({});
const expanded = ref<ExpandedState>({});

function handleSortingChange(item: any) {
  if (typeof item === 'function') {
    const sortValue = item();
    const sortBy = sortValue[0]?.id ? sortValue[0].id : '';
    const sortDirection = sortBy ? (sortValue[0]?.desc ? 'desc' : 'asc') : '';
    const data: { [index: string]: any } = {};
    data[sortBy] = sortDirection;
    router.visit(route('permissions.index'), {
      data,
      only: ['permissions'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}
function handleBulkDeletion() {
  router.post(route('bulk-deletion', { resource: 'permissions' }), rowSelection.value, {
    preserveState: false,
    onFinish: () => (rowSelection.value = {}),
  });
}

const table = useVueTable({
  data: props.permissions.data,
  columns: cols,
  manualPagination: true,
  pageCount: props.permissions.meta.per_page,
  enableRowSelection: true,
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
  () => props.permissions.data,
  (newData) => table.setOptions((prev) => ({ ...prev, data: newData })),
);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Permisos" />

    <IndexLayout title="Permisos">
      <template #icon>
        <KeySquare />
      </template>
      <DataTable
        :can="can"
        :columns="cols"
        :data="permissions"
        :filters="filters"
        :search-only="['permissions']"
        :search-route="route('permissions.index')"
        :table="table"
        @bulk-delete="handleBulkDeletion"
        @search="(s) => globalFilter = s"
      />
    </IndexLayout>
  </AppLayout>
</template>
