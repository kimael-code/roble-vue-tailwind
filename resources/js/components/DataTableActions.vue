<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuPortal,
  DropdownMenuSeparator,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Can } from '@/types';
import {
  EllipsisIcon,
  Eye,
  FileDown,
  LoaderCircleIcon,
  Pencil,
  RotateCcwIcon,
  ToggleLeftIcon,
  ToggleRightIcon,
  Trash2,
  XIcon,
} from 'lucide-vue-next';

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
  activate: [row: object];
  deactivate: [row: object];
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
      <DropdownMenuGroup>
        <DropdownMenuItem v-if="can.read" @click="$emit('read', row)">
          <Eye />
          <span>Ver</span>
        </DropdownMenuItem>
        <DropdownMenuItem v-if="can.update" @click="$emit('update', row)">
          <Pencil />
          <span>Editar</span>
        </DropdownMenuItem>

        <DropdownMenuSub v-if="can.activate || can.deactivate">
          <DropdownMenuSubTrigger>
            <span>Activación</span>
          </DropdownMenuSubTrigger>
          <DropdownMenuPortal>
            <DropdownMenuSubContent>
              <DropdownMenuItem v-if="can.activate" :disabled="row?.disabled_at === null" @click="$emit('activate', row)">
                <ToggleRightIcon />
                <span>Activar</span>
              </DropdownMenuItem>
              <DropdownMenuItem v-if="can.deactivate" :disabled="row?.disabled_at !== null" @click="$emit('deactivate', row)">
                <ToggleLeftIcon />
                <span>Desactivar</span>
              </DropdownMenuItem>
            </DropdownMenuSubContent>
          </DropdownMenuPortal>
        </DropdownMenuSub>

        <DropdownMenuSub>
          <DropdownMenuSubTrigger> Eliminación </DropdownMenuSubTrigger>
          <DropdownMenuPortal>
            <DropdownMenuSubContent>
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
            </DropdownMenuSubContent>
          </DropdownMenuPortal>
        </DropdownMenuSub>
      </DropdownMenuGroup>

      <DropdownMenuSeparator v-if="can.export" />
      <DropdownMenuItem v-if="can.export" @click="$emit('export', row)">
        <FileDown />
        <span>Exportar</span>
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
