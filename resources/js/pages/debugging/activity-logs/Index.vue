<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { valueUpdater } from '@/components/ui/table/utils';
import { useRequestActions } from '@/composables';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { ActivityLog, BreadcrumbItem, Can, PaginatedCollection, SearchFilter, User } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { getCoreRowModel, RowSelectionState, SortingState, TableOptions, useVueTable } from '@tanstack/vue-table';
import { LogsIcon } from 'lucide-vue-next';
import { computed, reactive, ref, watchEffect } from 'vue';
import { columns, permissions, processingRowId } from './partials/columns';
import SheetAdvancedFilters from './partials/SheetAdvancedFilters.vue';

const props = defineProps<{
  can: Can;
  filters: SearchFilter;
  users?: Array<User>;
  events?: Array<string>;
  logNames?: Array<string>;
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
const showAdvancedFilters = ref(false);
const advancedSearchApplied = ref(false);
const advancedFilters = ref({});

const urlQueryString = computed(() => {
  const queryString = usePage().url.indexOf('?');

  return queryString >= 0 ? usePage().url.substring(queryString) : '';
});

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

    if (Object.keys(data).length) {
      router.reload({
        data: { ...advancedFilters.value, sortBy: data, per_page: table.getState().pagination.pageSize },
        only: ['logs'],
        onSuccess: () => (sorting.value = sortValue),
      });
    } else {
      const url = usePage().url.replace(/&sortBy%5B[^%]+%5D=(?:asc|desc)(?=(?:&|$))/g, '');

      router.visit(url, {
        data: { ...advancedFilters.value },
        only: ['logs'],
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (sorting.value = sortValue),
      });
    }
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

function handleAdvancedSearch() {
  router.reload({
    only: ['users', 'events', 'logNames'],
    onSuccess: () => (showAdvancedFilters.value = true),
  });
}
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
        :is-advanced-search="advancedSearchApplied"
        @search="(s) => (globalFilter = s)"
        @read="(row) => (requestRead(row.id), (processingRowId = row.id))"
        @export="showPdf = true"
        @advanced-search="handleAdvancedSearch"
      />

      <Sheet v-model:open="showPdf">
        <SheetContent side="bottom">
          <SheetHeader>
            <SheetTitle>Exportar a PDF</SheetTitle>
            <SheetDescription>Reporte: Trazas de Actividades de Usuarios</SheetDescription>
          </SheetHeader>
          <div class="h-[70dvh]">
            <iframe :src="`${route('export-activity-logs-pdf.index')}${urlQueryString}`" frameborder="0" width="100%" height="100%"></iframe>
          </div>
        </SheetContent>
      </Sheet>

      <SheetAdvancedFilters
        :events
        :log-names
        :users
        :show="showAdvancedFilters"
        @close="showAdvancedFilters = false"
        @advanced-search="(advFilters) => ((advancedSearchApplied = true), (advancedFilters = advFilters))"
      />
    </ContentLayout>
  </AppLayout>
</template>
