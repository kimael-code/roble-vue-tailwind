<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import { PaginatedCollection, User } from '@/types';
import { router } from '@inertiajs/vue3';
import { getCoreRowModel, SortingState, TableOptions, useVueTable } from '@tanstack/vue-table';
import { reactive, ref } from 'vue';
import { columns } from './columnsUser';

interface Props {
  filters: object;
  roleId: string | number;
  users: PaginatedCollection<User>;
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

    router.visit(route('roles.show', { role: props.roleId }), {
      data,
      only: ['users'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => (sorting.value = sortValue),
    });
  }
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
    :data="users"
    :filters
    :search-only="['users']"
    :search-route="route('permissions.show', { permission: roleId })"
    :table
    @search="(s) => (globalFilter = s)"
  />
</template>
