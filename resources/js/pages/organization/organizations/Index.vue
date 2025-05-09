<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { valueUpdater } from '@/components/ui/table/utils';
import { useConfirmAction } from '@/composables/useConfirmAction';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, Organization, PaginatedCollection } from '@/types';
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
import { Building } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { columns } from './partials/columns';

interface Props {
  can: Can;
  filters: object;
  organizations: PaginatedCollection<Organization>;
}
const props = defineProps<Props>();
console.log(props);


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Entes',
    href: '/organizations',
  },
];

const { confirmAction, dataRow, openDialog } = useConfirmAction();

const cols = columns(props.can);
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

    router.visit(route('organizations.index'), {
      data,
      only: ['organizations'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}

function handleBatchDeletion() {
  router.post(route('batch-deletion', { resource: 'organizations' }), rowSelection.value, {
    preserveState: false,
    onFinish: () => (rowSelection.value = {}),
  });
}

const table = useVueTable({
  data: props.organizations.data,
  columns: cols,
  manualPagination: true,
  pageCount: props.organizations.meta.per_page,
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
  () => props.organizations.data,
  (newData) => table.setOptions((prev) => ({ ...prev, data: newData })),
);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Entes" />

    <ContentLayout title="Entes">
      <template #icon>
        <Building />
      </template>
      <DataTable
        :can="can"
        :columns="cols"
        :data="organizations"
        :filters="filters"
        :search-only="['organizations']"
        :search-route="route('organizations.index')"
        :table="table"
        @batch-destroy="handleBatchDeletion"
        @search="(s) => (globalFilter = s)"
        @new="router.get(route('organizations.create'))"
        @read="(row) => router.get(route('organizations.show', row?.id))"
        @update="(row) => router.get(route('organizations.edit', row?.id))"
        @destroy="(row) => confirmAction(row)"
      />

      <AlertDialog v-model:open="openDialog">
        <AlertDialogContent>
          <AlertDialogHeader v-if="!dataRow?.deleted_at">
            <AlertDialogTitle>{{ `¿Eliminar el usuario «${dataRow.name}»?` }}</AlertDialogTitle>
            <AlertDialogDescription>
              {{ `Este usuario no podrá ingresar al sistema. Sus datos no se perderán.` }}
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogHeader v-else>
            <AlertDialogTitle>{{ `¿Eliminar el usuario «${dataRow.name}» permanentemente?` }}</AlertDialogTitle>
            <AlertDialogDescription>
              {{ `Este usuario no podrá ingresar al sistema. Sus datos se borrarán permanentemente.` }}
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
              @click="router.delete(route('organizations.destroy', dataRow.id), { preserveState: false })"
            >
              Continuar
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </ContentLayout>
  </AppLayout>
</template>
