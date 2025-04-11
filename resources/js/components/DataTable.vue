<script setup lang="ts">
import { Can, PaginatedCollection } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { ColumnDef, FlexRender, Table as TanstackTable } from '@tanstack/vue-table';
import { watchDebounced } from '@vueuse/core';
import { Delete, Plus } from 'lucide-vue-next';
import { Button } from './ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from './ui/dropdown-menu';
import { Input } from './ui/input';
import { Pagination, PaginationFirst, PaginationLast, PaginationList, PaginationListItem, PaginationNext, PaginationPrev } from './ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from './ui/table';

interface Props {
  can?: Can;
  columns: ColumnDef<any>[];
  data: PaginatedCollection<any>;
  filters: { [key: string]: any };
  searchOnly: Array<string>;
  searchRoute: string;
  searchRouteData?: { [key: string]: any };
  table: TanstackTable<any>;
  hasNewButton?: boolean;
  hasBatchActionsButton?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
  hasNewButton: true,
  hasBatchActionsButton: true,
});
const emit = defineEmits(['batchDestroy', 'export', 'search', 'new', 'read', 'update', 'destroy', 'export']);

const form = useForm({
  search: props.filters?.search || undefined,
  ...props.searchRouteData,
});
form.defaults({
  search: undefined,
});

watchDebounced(
  () => form.search,
  (s) => {
    if (s === '') form.reset('search');

    router.visit(props.searchRoute, {
      data: form.data(),
      only: props.searchOnly,
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => emit('search', s),
    });
  },
  { debounce: 500, maxWait: 1000 },
);
</script>

<template>
  <div class="w-full">
    <div class="flex items-center justify-start px-2 py-4">
      <div class="mr-3 text-sm text-muted-foreground">{{ data.meta.from }} a {{ data.meta.to }} de {{ data.meta.total }} registros</div>
      <div class="relative w-full max-w-xs items-center">
        <Transition>
          <span v-if="form.search" class="absolute inset-y-0 end-0 flex items-center justify-center px-2" @click="form.reset('search')">
            <Delete class="size-6 text-muted-foreground" />
          </span>
        </Transition>
        <Input id="search" type="text" class="max-w-xs pr-10" placeholder="Buscar rápido..." v-model:model-value="form.search" />
      </div>
      <DropdownMenu v-if="can && (can.delete || can.export)">
        <DropdownMenuTrigger as-child>
          <Button variant="outline" class="ml-auto"> ... </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuGroup>
            <DropdownMenuItem @click="$emit('export', 'pdf')">Exportar a PDF</DropdownMenuItem>
          </DropdownMenuGroup>
          <DropdownMenuSeparator v-if="can.delete && table.getFilteredSelectedRowModel().rows.length > 1" />
          <DropdownMenuGroup v-if="can.delete && table.getFilteredSelectedRowModel().rows.length > 1">
            <DropdownMenuItem @click="$emit('batchDestroy')">Eliminar</DropdownMenuItem>
          </DropdownMenuGroup>
        </DropdownMenuContent>
      </DropdownMenu>
      <Button v-if="can && can.create" class="ml-3" @click="$emit('new')">
        <Plus class="mr-2 h-4 w-4" />
        Nuevo
      </Button>
    </div>
    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead v-for="header in headerGroup.headers" :key="header.id" @click="header.column.getToggleSortingHandler()?.($event)">
              <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="table.getRowModel().rows?.length">
            <template v-for="row in table.getRowModel().rows" :key="row.id">
              <TableRow :data-state="row.getIsSelected() && 'selected'">
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <FlexRender
                    :render="cell.column.columnDef.cell"
                    :props="cell.getContext()"
                    @read="(id) => $emit('read', id)"
                    @update="(id) => $emit('update', id)"
                    @destroy="(id) => $emit('destroy', id)"
                    @export="(id) => $emit('export', id)"
                  />
                </TableCell>
              </TableRow>
              <TableRow v-if="row.getIsExpanded()">
                <TableCell :colspan="row.getAllCells().length">
                  {{ JSON.stringify(row.original) }}
                </TableCell>
              </TableRow>
            </template>
          </template>

          <TableRow v-else>
            <TableCell :colspan="columns.length" class="h-24 text-center"> No hay registros. </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <div class="flex items-center justify-end space-x-2 py-4">
      <div v-if="table.getFilteredSelectedRowModel().rows.length" class="flex-1 text-sm text-muted-foreground">
        {{ table.getFilteredSelectedRowModel().rows.length }} de {{ table.getFilteredRowModel().rows.length }} fila(s) seleccionadas.
      </div>
      <div class="space-x-2">
        <Pagination :page="data.meta.current_page" :items-per-page="data.meta.per_page" :total="data.meta.total">
          <PaginationList class="flex items-center gap-1">
            <PaginationFirst @click="router.visit(data.links.first, { preserveScroll: true })" />
            <PaginationPrev @click="router.visit(data.links.prev, { preserveScroll: true })" />
            <template v-for="(item, i) in data.meta.links" :key="i">
              <PaginationListItem :value="item.label" as-child>
                <Button
                  class="h-10 w-10 p-0"
                  :variant="item?.active ? 'default' : 'outline'"
                  @click="router.visit(item.url, { preserveScroll: true })"
                >
                  {{ item?.label }}
                </Button>
              </PaginationListItem>
            </template>
            <PaginationNext @click="router.visit(data.links.next, { preserveScroll: true })" />
            <PaginationLast @click="router.visit(data.links.last, { preserveScroll: true })" />
          </PaginationList>
        </Pagination>
      </div>
    </div>
  </div>
</template>
