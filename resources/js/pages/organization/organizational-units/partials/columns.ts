import DataTableActions from '@/components/DataTableActions.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Can, OrganizationalUnit } from '@/types';
import { createColumnHelper } from '@tanstack/vue-table';
import { ChevronDown, ChevronsUpDown, ChevronUp } from 'lucide-vue-next';
import { h, ref } from 'vue';

const columnHelper = createColumnHelper<OrganizationalUnit>();

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
    id: 'select',
    header: ({ table }) =>
      h(TooltipProvider, () =>
        h(Tooltip, { delayDuration: 200 }, () => [
          h(TooltipTrigger, { asChild: true }, () =>
            h(Checkbox, {
              modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
              'onUpdate:modelValue': (value) => table.toggleAllPageRowsSelected(!!value),
              ariaLabel: 'Marcar/Desmarcar todo',
            }),
          ),
          h(TooltipContent, () => h('p', 'Marcar/Desmarcar todos los registros de la página')),
        ]),
      ),
    cell: ({ row }) =>
      h(TooltipProvider, () =>
        h(Tooltip, { delayDuration: 200 }, () => [
          h(TooltipTrigger, { asChild: true }, () =>
            h(Checkbox, {
              modelValue: row.getIsSelected(),
              'onUpdate:modelValue': (value) => row.toggleSelected(!!value),
              ariaLabel: 'Marcar/Desmarcar registro',
            }),
          ),
          h(TooltipContent, () => h('p', 'Marcar/Desmarcar este registro')),
        ]),
      ),
    enableSorting: false,
    enableHiding: false,
  }),
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
          'Unidad Administrativa',
          isSorted
            ? isSortedDesc
              ? h(ChevronDown, { class: 'ml-2 h-4 w-4' })
              : h(ChevronUp, { class: 'ml-2 h-4 w-4' })
            : h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' }),
        ],
      );
    },
    cell: ({ row }) => h('div', row.getValue('name')),
  }),
  columnHelper.accessor((row) => row.organization?.name, {
    id: 'organization',
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
          'Ente',
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
  columnHelper.accessor('status', {
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
          'Estatus',
          isSorted
            ? isSortedDesc
              ? h(ChevronDown, { class: 'ml-2 h-4 w-4' })
              : h(ChevronUp, { class: 'ml-2 h-4 w-4' })
            : h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' }),
        ],
      );
    },
    cell: (info) => {
      const cssClass = info.getValue() === 'INACTIVO' ? 'text-red-500' : 'text-green-500';

      return h('div', { class: cssClass }, info.getValue());
    },
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
