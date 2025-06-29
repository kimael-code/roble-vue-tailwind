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
import { BreadcrumbItem, Can, OperationType, Organization, PaginatedCollection } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { getCoreRowModel, RowSelectionState, SortingState, TableOptions, useVueTable } from '@tanstack/vue-table';
import { Building } from 'lucide-vue-next';
import { reactive, ref, watch, watchEffect } from 'vue';
import { columns, permissions, processingRowId } from './partials/columns';

const props = defineProps<{
  can: Can;
  filters: object;
  organizations: PaginatedCollection<Organization>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Entes',
    href: '/organizations',
  },
];

const { action, resourceID, requestingCreate, requestAction, requestRead, requestEdit, requestCreate } = useRequestActions('organizations');
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

    router.visit(route('organizations.index'), {
      data: { sortBy: data, per_page: table.getState().pagination.pageSize },
      only: ['organizations'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}

function handleBatchDeletion() {
  dropdownBtn.value = true;

  router.post(route('batch-deletion', { resource: 'organizations' }), rowSelection.value, {
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

const tableOptions = reactive<TableOptions<Organization>>({
  get data() {
    return props.organizations.data;
  },
  get columns() {
    return columns;
  },
  manualPagination: true,
  manualSorting: true,
  get meta() {
    return {
      currentPage: props.organizations.meta.current_page,
      pageSize: props.organizations.meta.per_page,
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
      alertAction.value = 'Eliminar permanentemente';
      alertActionCss.value = 'bg-destructive text-destructive-foreground hover:bg-destructive/90';
      alertTitle.value = `¿Eliminar ente «${alertData.value.name}» permanentemente?`;
      alertDescription.value = `Esta acción no podrá revertirse. Los datos de «${alertData.value.name}» se perderán permanentemente.`;
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
    <Head title="Entes" />

    <ContentLayout title="Entes">
      <template #icon>
        <Building />
      </template>
      <DataTable
        :can="can"
        :columns="columns"
        :data="organizations"
        :filters="filters"
        :search-only="['organizations']"
        :search-route="route('organizations.index')"
        :table="table"
        :is-loading-new="requestingCreate"
        :is-loading-dropdown="dropdownBtn"
        @batch-destroy="handleBatchDeletion"
        @search="(s) => (globalFilter = s)"
        @new="requestCreate"
        @read="(row) => (requestRead(row.id), (processingRowId = row.id))"
        @update="(row) => (requestEdit(row.id), (processingRowId = row.id))"
        @destroy="(row) => handleAction('destroy', row)"
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
