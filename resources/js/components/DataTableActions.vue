<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Can } from '@/types';
import { EllipsisIcon, Eye, FileDown, LoaderCircleIcon, Pencil, RotateCcwIcon, ToggleLeft, ToggleRight, Trash2, XIcon } from 'lucide-vue-next';

defineProps<{
  row: {
    [index: string]: any;
  };
  can: Can;
  loading?: boolean;
}>();

defineEmits<{
  read: [row: object];
  update: [row: object];
  destroy: [row: object];
  forceDestroy: [row: object];
  export: [row: object];
  enable: [row: object];
  disable: [row: object];
  restore: [row: object];
}>();
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="h-8 w-8 p-0" :disabled="loading">
        <LoaderCircleIcon v-if="loading" class="animate-spin" />
        <EllipsisIcon v-else />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuLabel>Acciones</DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuItem v-if="can.read" @click="$emit('read', row)">
        <Eye />
        <span>Ver</span>
      </DropdownMenuItem>
      <DropdownMenuItem v-if="can.update" @click="$emit('update', row)">
        <Pencil />
        <span>Editar</span>
      </DropdownMenuItem>
      <DropdownMenuItem v-if="can.restore" :disabled="!row?.deleted_at" @click="$emit('restore', row)">
        <RotateCcwIcon />
        <span>Restaurar</span>
      </DropdownMenuItem>
      <DropdownMenuItem v-if="can.delete" :disabled="row?.deleted_at ? true : false" @click="$emit('destroy', row)">
        <Trash2 />
        <span>Eliminar</span>
      </DropdownMenuItem>
      <DropdownMenuItem v-if="can.f_delete" :disabled="!row?.deleted_at" @click="$emit('forceDestroy', row)">
        <XIcon />
        <span>Eliminar permanentemente</span>
      </DropdownMenuItem>
      <DropdownMenuSeparator v-if="can.enable || can.disable" />
      <DropdownMenuItem v-if="can.enable" @click="$emit('destroy', row)">
        <ToggleLeft />
        <span>Habilitar</span>
      </DropdownMenuItem>
      <DropdownMenuItem v-if="can.disable" @click="$emit('destroy', row)">
        <ToggleRight />
        <span>Deshabilitar</span>
      </DropdownMenuItem>
      <DropdownMenuSeparator v-if="can.export" />
      <DropdownMenuItem v-if="can.export" @click="$emit('export', row)">
        <FileDown />
        <span>Exportar</span>
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
