import DataTableActions from '@/components/DataTableActions.vue';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuLabel,
  DropdownMenuRadioGroup,
  DropdownMenuRadioItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ActivityLog, Can } from '@/types';
import { createColumnHelper, SortingState } from '@tanstack/vue-table';
import { ChevronDown, ChevronsUpDown, ChevronUp } from 'lucide-vue-next';
import { computed, h, ref } from 'vue';

const columnHelper = createColumnHelper<ActivityLog>();

export const permissions = ref<Can>({
  create: false,
  read: false,
  update: false,
  delete: false,
  activate: false,
  deactivate: false,
  export: false,
});

export const processingRowId = ref<number | string | null>(null);

export const columns = [
  columnHelper.display({
    id: 'serial',
    header: '#',
    cell: ({ row, table }) => {
      const meta = table.options.meta as { currentPage: number; pageSize: number };

      const startingIndex = (meta.currentPage - 1) * meta.pageSize;
      const trueIndex = startingIndex + row.index + 1;

      return h('div', trueIndex);
    },
    enableSorting: false,
    enableHiding: false,
  }),
  columnHelper.accessor('description', {
    header: ({ column, table }) => {
      const isSorted = column.getIsSorted();
      const direction = computed(() => {
        const sorted = column.getIsSorted();
        if (sorted === 'asc') return 'asc';
        if (sorted === 'desc') return 'desc';
        return 'none';
      });

      return h(DropdownMenu, () => [
        h(DropdownMenuTrigger, { asChild: true }, () => [
          h(Button, { variant: isSorted ? 'default' : 'ghost', class: 'ml-auto' }, () => [
            'Descripción',
            isSorted === 'desc'
              ? h(ChevronDown, { class: 'ml-2 h-4 w-4' })
              : isSorted === 'asc'
                ? h(ChevronUp, { class: 'ml-2 h-4 w-4' })
                : h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' }),
          ]),
        ]),
        h(DropdownMenuContent, { align: 'start' }, () => [
          h(DropdownMenuLabel, 'Ordenar'),
          h(DropdownMenuSeparator),
          h(DropdownMenuRadioGroup, { modelValue: direction.value }, [
            h(DropdownMenuRadioItem, { value: 'asc', onSelect: () => column.toggleSorting(false) }, 'Ascendente'),
            h(DropdownMenuRadioItem, { value: 'desc', onSelect: () => column.toggleSorting(true) }, 'Descendente'),
            h(DropdownMenuRadioItem, { value: 'none', onSelect: () => table.setSorting(() => <SortingState>[]) }, 'Restablecer'),
          ]),
        ]),
      ]);
    },
    cell: (info) => h('div', info.getValue()),
  }),
  columnHelper.accessor('created_at_human', {
    header: ({ column }) => {
      const isSorted = column.getIsSorted();
      const isSortedDesc = column.getIsSorted() === 'desc';

      return h(
        Button,
        {
          variant: isSorted ? 'default' : 'ghost',
          class: 'ml-auto',
        },
        () => [
          'Fecha',
          isSorted
            ? isSortedDesc
              ? h(ChevronDown, { class: 'ml-2 h-4 w-4' })
              : h(ChevronUp, { class: 'ml-2 h-4 w-4' })
            : h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' }),
        ],
      );
    },
    cell: (info) => h('div', info.getValue()),
  }),
  columnHelper.display({
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      return h(DataTableActions, {
        row: row.original,
        can: permissions.value,
        loading: processingRowId.value === row.original.id,
        onExpand: row.toggleExpanded,
      });
    },
  }),
];
