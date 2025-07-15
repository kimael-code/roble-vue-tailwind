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
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { valueUpdater } from '@/components/ui/table/utils';
import { useConfirmAction, useRequestActions } from '@/composables';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, OperationType, PaginatedCollection, Permission, Role, SearchFilter, User } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { getCoreRowModel, RowSelectionState, SortingState, TableOptions, useVueTable } from '@tanstack/vue-table';
import { KeySquare } from 'lucide-vue-next';
import { computed, reactive, ref, watch, watchEffect } from 'vue';
import { columns, permissions as DTpermissions, processingRowId } from './partials/columns';
import SheetAdvancedFilters from './partials/SheetAdvancedFilters.vue';

const props = defineProps<{
  can: Can;
  filters: SearchFilter;
  users?: Array<User>;
  roles?: Array<Role>;
  operations?: Array<string>;
  permissions: PaginatedCollection<Permission>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Permisos',
    href: '/permissions',
  },
];

const { action, resourceID, requestingCreate, requestAction, requestRead, requestEdit, requestCreate } = useRequestActions('permissions');
const { alertOpen, alertAction, alertActionCss, alertTitle, alertDescription, alertData } = useConfirmAction();
const showPdf = ref(false);
const showAdvancedFilters = ref(false);
const advancedSearchApplied = ref(false);
const advancedFilters = ref({});
const page = usePage();

const urlQueryString = computed(() => {
  const queryString = page.url.indexOf('?');

  return queryString >= 0 ? page.url.substring(queryString) : '';
});

DTpermissions.value = props.can;
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

    if (Object.keys(data).length) {
      router.reload({
        data: { ...advancedFilters.value, sort_by: data, per_page: table.getState().pagination.pageSize },
        only: ['permissions'],
        onSuccess: () => (sorting.value = sortValue),
      });
    } else {
      const url = page.url.replace(/&sort_by%5B[^%]+%5D=(?:asc|desc)(?=(?:&|$))/g, '');

      router.visit(url, {
        data: { ...advancedFilters.value },
        only: ['permissions'],
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (sorting.value = sortValue),
      });
    }
  }
}

function handleBatchDeletion() {
  dropdownBtn.value = true;

  router.post(route('batch-deletion', { resource: 'permissions' }), rowSelection.value, {
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

const tableOptions = reactive<TableOptions<Permission>>({
  get data() {
    return props.permissions.data;
  },
  get columns() {
    return columns;
  },
  manualPagination: true,
  manualSorting: true,
  get meta() {
    return {
      currentPage: props.permissions.meta.current_page,
      pageSize: props.permissions.meta.per_page,
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
      alertTitle.value = `¿Eliminar permiso «${alertData.value.name}» permanentemente?`;
      alertDescription.value = `Esta acción no podrá revertirse. Los datos de «${alertData.value.name}» se perderán permanentemente.`;
      alertOpen.value = true;
      break;

    default:
      break;
  }
});

watchEffect(() => (resourceID.value === null ? (processingRowId.value = null) : false));

function handleAdvancedSearch() {
  router.reload({
    only: ['users', 'roles', 'operations'],
    onSuccess: () => (showAdvancedFilters.value = true),
  });
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Permisos" />

    <ContentLayout title="Permisos">
      <template #icon>
        <KeySquare />
      </template>
      <DataTable
        :can="can"
        :columns="columns"
        :data="permissions"
        :filters="filters"
        :search-only="['permissions']"
        :search-route="route('permissions.index')"
        :table="table"
        :is-advanced-search="advancedSearchApplied"
        :is-loading-new="requestingCreate"
        :is-loading-dropdown="dropdownBtn"
        @batch-destroy="handleBatchDeletion"
        @search="(s) => (globalFilter = s)"
        @new="requestCreate"
        @read="(row) => (requestRead(row.id), (processingRowId = row.id))"
        @update="(row) => (requestEdit(row.id), (processingRowId = row.id))"
        @destroy="(row) => handleAction('destroy', row)"
        @export="showPdf = true"
        @advanced-search="handleAdvancedSearch"
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

      <Sheet v-model:open="showPdf">
        <SheetContent side="bottom">
          <SheetHeader>
            <SheetTitle>Exportar a PDF</SheetTitle>
            <SheetDescription>Reporte: Permisos</SheetDescription>
          </SheetHeader>
          <div class="h-[70dvh]">
            <iframe :src="`${route('export-permissions-pdf.index')}${urlQueryString}`" frameborder="0" width="100%" height="100%"></iframe>
          </div>
        </SheetContent>
      </Sheet>

      <SheetAdvancedFilters
        :operations
        :roles
        :users
        :show="showAdvancedFilters"
        @close="showAdvancedFilters = false"
        @advanced-search="(advFilters) => ((advancedSearchApplied = true), (advancedFilters = advFilters))"
      />
    </ContentLayout>
  </AppLayout>
</template>
