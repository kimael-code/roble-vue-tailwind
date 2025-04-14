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
import { Eye, FileDown, MoreHorizontal, Pencil, ToggleLeft, ToggleRight, Trash2 } from 'lucide-vue-next';

defineProps<{
  row: object;
  can: Can;
}>();

defineEmits<{
  read: [row: object];
  update: [row: object];
  destroy: [row: object];
  export: [row: object];
  enable: [row: object];
  disable: [row: object];
}>();
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="h-8 w-8 p-0">
        <span class="sr-only">...</span>
        <MoreHorizontal class="h-4 w-4" />
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
      <DropdownMenuItem v-if="can.delete" @click="$emit('destroy', row)">
        <Trash2 />
        <span>Eliminar</span>
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
