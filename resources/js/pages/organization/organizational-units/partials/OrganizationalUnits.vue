<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import { OrganizationalUnit, PaginatedCollection } from '@/types';
import { router } from '@inertiajs/vue3';
import { getCoreRowModel, SortingState, TableOptions, useVueTable } from '@tanstack/vue-table';
import { reactive, ref } from 'vue';
import { columns } from './columnsOrganizationalUnit';

interface Props {
  filters: object;
  resourceId: string | number;
  ous: PaginatedCollection<OrganizationalUnit>;
}
const props = defineProps<Props>();

const sorting = ref<SortingState>([]);
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

    router.visit(route('organizational-units.show', props.resourceId), {
      data,
      only: ['organizationalUnits'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
}

const tableOptions = reactive<TableOptions<OrganizationalUnit>>({
  get data() {
    return props.ous.data;
  },
  get columns() {
    return columns;
  },
  manualPagination: true,
  manualSorting: true,
  get meta() {
    return {
      currentPage: props.ous.meta.current_page,
      pageSize: props.ous.meta.per_page,
    };
  },
  getCoreRowModel: getCoreRowModel(),
  getRowId: (row) => row.id,
  onSortingChange: handleSortingChange,
  state: {
    get sorting() {
      return sorting.value;
    },
    get globalFilter() {
      return globalFilter.value;
    },
  },
});

const table = useVueTable(tableOptions);
</script>

<template>
  <DataTable
    :columns="columns"
    :data="ous"
    :filters
    :search-only="['organizationalUnits']"
    :search-route="route('organizational-units.show', resourceId)"
    :table
    @search="(s) => (globalFilter = s)"
  />
</template>
