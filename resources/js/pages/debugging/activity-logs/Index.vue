<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogDescription, DialogFooter, DialogHeader, DialogScrollContent, DialogTitle } from '@/components/ui/dialog';
import { valueUpdater } from '@/components/ui/table/utils';
import { useRequestActions } from '@/composables';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { ActivityLog, BreadcrumbItem, Can, PaginatedCollection, SearchFilter, User } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { getCoreRowModel, RowSelectionState, SortingState, TableOptions, useVueTable } from '@tanstack/vue-table';
import { LogsIcon } from 'lucide-vue-next';
import { reactive, ref, watchEffect } from 'vue';
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
const showPdf = ref(false);

permissions.value = props.can;
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

    router.visit(route('activity-logs.index'), {
      data: { sortBy: data },
      only: ['logs'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}

const tableOptions = reactive<TableOptions<ActivityLog>>({
  get data() {
    return props.logs.data;
  },
  get columns() {
    return columns;
  },
  manualPagination: true,
  manualSorting: true,
  get meta() {
    return {
      currentPage: props.logs.meta.current_page,
      pageSize: props.logs.meta.per_page,
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
        @export="showPdf = true"
      />

      <Dialog v-model:open="showPdf">
        <DialogScrollContent>
          <DialogHeader>
            <DialogTitle>Exportar a PDF</DialogTitle>
            <DialogDescription> Reporte de las trazas de actividades de los usuarios </DialogDescription>
          </DialogHeader>
          <div class="h-[300dvh]">
            <iframe :src="route('data.export', { resource: 'activity-logs' })" frameborder="0" width="100%" height="100%"></iframe>
          </div>
          <DialogFooter>
            <DialogClose as-child>
              <Button type="button"> Cerrar </Button>
            </DialogClose>
          </DialogFooter>
        </DialogScrollContent>
      </Dialog>
    </ContentLayout>
  </AppLayout>
</template>
