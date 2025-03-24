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
import { MoreHorizontal } from 'lucide-vue-next';

defineProps<{
  id: string | number;
  can: Can;
}>();

defineEmits<{
  read   : [id: string | number],
  update : [id: string | number],
  destroy: [id: string | number],
  export : [id: string | number],
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
      <DropdownMenuItem v-if="can.read" @click="$emit('read', id)"> Ver </DropdownMenuItem>
      <DropdownMenuItem v-if="can.update" @click="$emit('update', id)"> Editar </DropdownMenuItem>
      <DropdownMenuItem v-if="can.delete" @click="$emit('destroy', id)"> Eliminar </DropdownMenuItem>
      <DropdownMenuSeparator v-if="can.export" />
      <DropdownMenuItem v-if="can.export" @click="$emit('export', id)">Exportar</DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
