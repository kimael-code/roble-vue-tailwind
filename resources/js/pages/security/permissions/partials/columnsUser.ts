import { Button } from '@/components/ui/button';
import { User } from '@/types';
import { createColumnHelper } from '@tanstack/vue-table';
import { ChevronDown, ChevronsUpDown, ChevronUp } from 'lucide-vue-next';
import { h } from 'vue';

const columnHelper = createColumnHelper<User>();

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
  columnHelper.accessor('name', {
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
          'Nombre',
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
  columnHelper.accessor('email', {
    enableSorting: false,
    header: 'Correo Electrónico',
    cell: ({ row }) => h('div', row.getValue('email')),
  }),
];
