import DataTableActions from '@/components/DataTableActions.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { ActivityLog, Can } from '@/types';
import { createColumnHelper } from '@tanstack/vue-table';
import { ChevronDown, ChevronsUpDown, ChevronUp } from 'lucide-vue-next';
import { h, ref } from 'vue';

const columnHelper = createColumnHelper<ActivityLog>();

export const permissions = ref<Can>({
  create: false,
  read: false,
  update: false,
  delete: false,
  enable: false,
  disable: false,
  export: false,
});

export const columns = [
  columnHelper.display({
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
  }),
  columnHelper.accessor('description', {
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
    cell: (info) => h('div', info.getValue()),
  }),
  columnHelper.accessor((row) => row.causer?.name, {
    id: 'causer',
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
          'Usuario',
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
      const data = row.original;
      const can = permissions.value;

      return h(DataTableActions, {
        row: data,
        can,
        onExpand: row.toggleExpanded,
      });
    },
  }),
];

// export const columns = (permissions: Can): ColumnDef<OrganizationalUnit>[] => [
//   {
//     id: 'select',
//     header: ({ table }) =>
//       h(Checkbox, {
//         checked: table.getIsAllPageRowsSelected(),
//         'onUpdate:checked': (value: boolean) => table.toggleAllPageRowsSelected(!!value),
//         ariaLabel: 'Select all',
//       }),
//     cell: ({ row }) =>
//       h(Checkbox, {
//         checked: row.getIsSelected(),
//         disabled: !row.getCanSelect(),
//         'onUpdate:checked': (value: boolean) => row.toggleSelected(!!value),
//         ariaLabel: 'Select row',
//       }),
//     enableSorting: false,
//     enableHiding: false,
//   },
//   {
//     accessorKey: 'name',
//     header: ({ column }) => {
//       const isSorted = column.getIsSorted();
//       const isSortedDesc = column.getIsSorted() === 'desc';

//       return h(
//         Button,
//         {
//           variant: isSorted ? 'default' : 'ghost',
//           class: 'ml-auto',
//         },
//         () => [
//           'Unidad Administrativa',
//           isSorted
//             ? isSortedDesc
//               ? h(ChevronDown, { class: 'ml-2 h-4 w-4' })
//               : h(ChevronUp, { class: 'ml-2 h-4 w-4' })
//             : h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' }),
//         ],
//       );
//     },
//     cell: ({ row }) => h('div', row.getValue('name')),
//   },
//   {
//     accessorKey: 'organization.name',
//     id: 'organization',
//     header: ({ column }) => {
//       const isSorted = column.getIsSorted();
//       const isSortedDesc = column.getIsSorted() === 'desc';

//       return h(
//         Button,
//         {
//           variant: isSorted ? 'default' : 'ghost',
//           class: 'ml-auto',
//         },
//         () => [
//           'Ente',
//           isSorted
//             ? isSortedDesc
//               ? h(ChevronDown, { class: 'ml-2 h-4 w-4' })
//               : h(ChevronUp, { class: 'ml-2 h-4 w-4' })
//             : h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' }),
//         ],
//       );
//     },
//     cell: ({ row }) => h('div', row.getValue('organization')),
//   },
//   {
//     accessorKey: 'disabled_at',
//     header: ({ column }) => {
//       const isSorted = column.getIsSorted();
//       const isSortedDesc = column.getIsSorted() === 'desc';

//       return h(
//         Button,
//         {
//           variant: isSorted ? 'default' : 'ghost',
//           class: 'ml-auto',
//         },
//         () => [
//           'Estatus',
//           isSorted
//             ? isSortedDesc
//               ? h(ChevronDown, { class: 'ml-2 h-4 w-4' })
//               : h(ChevronUp, { class: 'ml-2 h-4 w-4' })
//             : h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' }),
//         ],
//       );
//     },
//     cell: ({ row }) => {
//       const isDisabled = row.getValue('disabled_at') ? true : false;
//       const cssClass = isDisabled ? 'text-red-500' : 'text-green-500';
//       const text = isDisabled ? 'INACTIVO' : 'ACTIVO';

//       return h('div', { class: cssClass }, text);
//     },
//   },
//   {
//     id: 'actions',
//     enableHiding: false,
//     cell: ({ row }) => {
//       const data = row.original;
//       const can = permissions;

//       return h(DataTableActions, {
//         row: data,
//         can,
//         onExpand: row.toggleExpanded,
//       });
//     },
//   },
// ];
