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
import { useConfirmAction, useRequestActions } from '@/composables';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, OperationType, PaginatedCollection, User } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { getCoreRowModel, RowSelectionState, SortingState, TableOptions, useVueTable } from '@tanstack/vue-table';
import { UserIcon } from 'lucide-vue-next';
import { reactive, ref, watch, watchEffect } from 'vue';
import { columns, permissions, processingRowId } from './partials/columns';

const props = defineProps<{
  can: Can;
  filters: object;
  users: PaginatedCollection<User>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Usuarios',
    href: '/users',
  },
];

const { action, resourceID, requestingCreate, requestAction, requestRead, requestEdit, requestCreate } = useRequestActions('users');
const { alertOpen, alertAction, alertActionCss, alertTitle, alertDescription, alertData } = useConfirmAction();

permissions.value = props.can;
const dropdownBtn = ref(false);
const sorting = ref<SortingState>([]);
const globalFilter = ref('');
const rowSelection = ref<RowSelectionState>({});

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
  dropdownBtn.value = true;

  router.post(route('batch-deletion', { resource: 'users' }), rowSelection.value, {
    preserveState: false,
    onFinish: () => {
      dropdownBtn.value = false;
      rowSelection.value = {};
    },
  });
}

function handleAction(act: OperationType, rowData: Record<string, any>) {
  alertData.value = rowData;
  action.value = act;
  processingRowId.value = rowData.id;
}

const tableOptions = reactive<TableOptions<User>>({
  get data() {
    return props.users.data;
  },
  get columns() {
    return columns;
  },
  manualPagination: true,
  manualSorting: true,
  get meta() {
    return {
      currentPage: props.users.meta.current_page,
      pageSize: props.users.meta.per_page,
    };
  },
  getCoreRowModel: getCoreRowModel(),
  getRowId: (row) => row.id,
  onSortingChange: handleSortingChange,
  onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
  state: {
    get sorting() {
      return sorting.value;
    },
    get globalFilter() {
      return globalFilter.value;
    },
    get rowSelection() {
      return rowSelection.value;
    },
  },
});

const table = useVueTable(tableOptions);

watch(action, () => {
  switch (action.value) {
    case 'destroy':
      alertAction.value = 'Eliminar';
      alertActionCss.value = 'bg-destructive text-destructive-foreground hover:bg-destructive/90';
      alertTitle.value = `¿Eliminar usuario(a) «${alertData.value?.name}»?`;
      alertDescription.value = `«${alertData.value?.name}» perderá el acceso al sistema. Sus datos se conservarán.`;
      alertOpen.value = true;
      break;
    case 'restore':
      alertAction.value = 'Restaurar';
      alertActionCss.value = '';
      alertTitle.value = `¿Restaurar usuario(a) «${alertData.value?.name}»?`;
      alertDescription.value = `«${alertData.value?.name}» recuperará el acceso al sistema. Sus datos se restaurarán.`;
      alertOpen.value = true;
      break;
    case 'force_destroy':
      alertAction.value = 'Eliminar permanentemente';
      alertActionCss.value = 'bg-destructive text-destructive-foreground hover:bg-destructive/90';
      alertTitle.value = `¿Eliminar usuario(a) «${alertData.value?.name}» permanentemente?`;
      alertDescription.value = `Esta acción no podrá revertirse. «${alertData.value?.name}» perderá el acceso al sistema. Sus datos se eliminarán.`;
      alertOpen.value = true;
      break;
    case 'enable':
      alertAction.value = 'Activar';
      alertActionCss.value = '';
      alertTitle.value = `Activar usuario(a) «${alertData.value?.name}»?`;
      alertDescription.value = `«${alertData.value?.name}» recuperará el acceso al sistema. Sus datos se restaurarán.`;
      alertOpen.value = true;
      break;
    case 'disable':
      alertAction.value = 'Desactivar';
      alertActionCss.value = 'bg-amber-500 text-foreground hover:bg-amber-500/90';
      alertTitle.value = `Desactivar usuario(a) «${alertData.value?.name}»?`;
      alertDescription.value = `«${alertData.value?.name}» perderá el acceso al sistema. Sus datos se conservarán.`;
      alertOpen.value = true;
      break;

    default:
      break;
  }
});
watchEffect(() => (resourceID.value === null ? (processingRowId.value = null) : false));
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
        :is-loading-new="requestingCreate"
        :is-loading-dropdown="dropdownBtn"
        @batch-destroy="handleBatchDeletion"
        @search="(s) => (globalFilter = s)"
        @new="requestCreate"
        @read="(row) => (requestRead(row.id), (processingRowId = row.id))"
        @update="(row) => (requestEdit(row.id), (processingRowId = row.id))"
        @destroy="(row) => handleAction('destroy', row)"
        @force-destroy="(row) => handleAction('force_destroy', row)"
        @restore="(row) => handleAction('restore', row)"
        @activate="(row) => handleAction('enable', row)"
        @deactivate="(row) => handleAction('disable', row)"
      />

      <AlertDialog v-model:open="alertOpen">
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>{{ alertTitle }}</AlertDialogTitle>
            <AlertDialogDescription>{{ alertDescription }}</AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel @click="((action = null), (processingRowId = null))">Cancelar</AlertDialogCancel>
            <AlertDialogAction :class="alertActionCss" @click="requestAction(alertData.id, { preserveState: false })">
              {{ alertAction }}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </ContentLayout>
  </AppLayout>
</template>
