import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Can, Permission } from '@/types';
import { ColumnDef } from '@tanstack/vue-table';
import { ChevronDown, ChevronsUpDown, ChevronUp } from 'lucide-vue-next';
import { h } from 'vue';
import DropdownAction from './DropdownAction.vue';

/**
 * Definiciones de las columnas.
 * @param permissions Acciones que se puden ejecutar.
 * @returns Arreglo de objetos.
 */
export const columns = (permissions: Can): ColumnDef<Permission>[] => [
  {
    id: 'select',
    header: ({ table }) =>
      h(Checkbox, {
        checked: table.getIsAllPageRowsSelected(),
        'onUpdate:checked': (value: boolean) => table.toggleAllPageRowsSelected(!!value),
        ariaLabel: 'Select all',
      }),
    cell: ({ row }) =>
      h(Checkbox, {
        checked: row.getIsSelected(),
        disabled: !row.getCanSelect(),
        'onUpdate:checked': (value: boolean) => row.toggleSelected(!!value),
        ariaLabel: 'Select row',
      }),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'name',
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
    cell: ({ row }) => h('div', row.getValue('name')),
  },
  {
    accessorKey: 'description',
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
          'Descripción',
          isSorted
            ? isSortedDesc
              ? h(ChevronDown, { class: 'ml-2 h-4 w-4' })
              : h(ChevronUp, { class: 'ml-2 h-4 w-4' })
            : h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' }),
        ],
      );
    },
    cell: ({ row }) => h('div', row.getValue('description')),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const id = row.original.id;
      const can = permissions;

      return h(DropdownAction, {
        id,
        can,
        onExpand: row.toggleExpanded,
      });
    },
  },
];
