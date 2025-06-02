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
import { useRunAction } from '@/composables/useRunAction';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, PaginatedCollection, User } from '@/types';
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
import { UserIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { columns, permissions } from './partials/columns';

interface Props {
  can: Can;
  filters: object;
  users: PaginatedCollection<User>;
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Usuarios',
    href: '/users',
  },
];

const { action, subject, runAction } = useRunAction('users');
const dialogOpen = ref(false);
const dialogAction = ref('Continuar');
const dialogTitle = ref('¿Está seguro?');
const dialogDescription = ref('Si realmente está seguro haga clic en el botón "Continuar"');

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

    router.visit(route('users.index'), {
      data: { sortBy: data },
      only: ['users'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}

function handleBatchDeletion() {
  router.post(route('batch-deletion', { resource: 'users' }), rowSelection.value, {
    preserveState: false,
    onFinish: () => (rowSelection.value = {}),
  });
}

const table = useVueTable({
  data: props.users.data,
  columns: columns,
  manualPagination: true,
  pageCount: props.users.meta.per_page,
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
  () => props.users.data,
  (newData) => table.setOptions((prev) => ({ ...prev, data: newData })),
);
watch(action, () => {
  switch (action.value) {
    case 'delete':
      dialogAction.value = 'Eliminar';
      dialogTitle.value = `¿Eliminar usuario(a) «${subject.value?.name}»?`;
      dialogDescription.value = `«${subject.value?.name}» perderá el acceso al sistema. Sus datos se conservarán.`;
      dialogOpen.value = true;
      break;
    case 'restore':
      dialogAction.value = 'Restaurar';
      dialogTitle.value = `¿Restaurar usuario(a) «${subject.value?.name}»?`;
      dialogDescription.value = `«${subject.value?.name}» recuperará el acceso al sistema. Sus datos se restaurarán.`;
      dialogOpen.value = true;
      break;
    case 'f_delete':
      dialogAction.value = 'Eliminar permanentemente';
      dialogTitle.value = `¿Eliminar usuario(a) «${subject.value?.name}» permanentemente?`;
      dialogDescription.value = `Esta acción no podrá revertirse. «${subject.value?.name}» perderá el acceso al sistema. Sus datos se eliminarán.`;
      dialogOpen.value = true;
      break;

    default:
      break;
  }
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Usuarios" />

    <ContentLayout title="Usuarios">
      <template #icon>
        <UserIcon />
      </template>
      <DataTable
        :can="can"
        :columns="columns"
        :data="users"
        :filters="filters"
        :search-only="['users']"
        :search-route="route('users.index')"
        :table="table"
        @batch-destroy="handleBatchDeletion"
        @search="(s) => (globalFilter = s)"
        @new="router.get(route('users.create'))"
        @read="(row) => router.get(route('users.show', row?.id))"
        @update="(row) => router.get(route('users.edit', row?.id))"
        @destroy="(row) => ((action = 'delete'), (subject = row))"
        @force-destroy="(row) => ((action = 'f_delete'), (subject = row))"
        @restore="(row) => ((action = 'restore'), (subject = row))"
      />

      <AlertDialog v-model:open="dialogOpen">
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>{{ dialogTitle }}</AlertDialogTitle>
            <AlertDialogDescription>{{ dialogDescription }}</AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel @click="action = ''">Cancelar</AlertDialogCancel>
            <AlertDialogAction @click="runAction(subject.id)">
              {{ dialogAction }}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </ContentLayout>
  </AppLayout>
</template>
