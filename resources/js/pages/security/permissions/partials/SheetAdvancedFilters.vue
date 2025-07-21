<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Role, User } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import ComboboxOperations from './ComboboxOperations.vue';
import ComboboxRoles from './ComboboxRoles.vue';
import ComboboxUsers from './ComboboxUsers.vue';

defineProps<{
  show: boolean;
  operations?: Array<string>;
  roles?: Array<Role>;
  users?: Array<User>;
}>();

const emit = defineEmits(['close', 'advancedSearch']);

const form = useForm({
  operations: [],
  roles: [],
  users: [],
  sort_by: {
    id: 'asc',
  },
});

function submitSearch() {
  router.reload({
    data: form.data(),
    only: ['permissions'],
    onSuccess: () => emit('advancedSearch', form.data),
  });
}
</script>

<template>
  <div class="grid grid-cols-2 gap-2">
    <Sheet :open="show" @update:open="$emit('close')">
      <SheetContent side="top">
        <SheetHeader>
          <SheetTitle>Permisos: Filtros de Búsqueda Avanzados</SheetTitle>
          <SheetDescription>Parametrice la consulta de registros haciendo uso de los siguientes controles.</SheetDescription>
        </SheetHeader>
        <Tabs default-value="users" class="pr-4 pl-4" :unmount-on-hide="false">
          <TabsList class="grid w-full grid-cols-3">
            <TabsTrigger value="users">Usuarios</TabsTrigger>
            <TabsTrigger value="roles">Roles</TabsTrigger>
            <TabsTrigger value="operations">Operación</TabsTrigger>
          </TabsList>
          <TabsContent value="users">
            <ComboboxUsers :users @selected="(u) => (form.users = u)" />
          </TabsContent>
          <TabsContent value="roles">
            <ComboboxRoles :roles @selected="(r) => (form.roles = r)" />
          </TabsContent>
          <TabsContent value="operations">
            <ComboboxOperations :operations @selected="(o) => (form.operations = o)" />
          </TabsContent>
        </Tabs>
        <div class="grid gap-4 p-4">
          <div class="grid grid-cols-2 items-center gap-4 md:grid-cols-12">
            <Label for="order_by"> Ordenar Por </Label>
            <Select v-model="form.sort_by">
              <SelectTrigger id="order_by" class="w-full md:col-span-4 md:col-start-2">
                <SelectValue placeholder="Seleccione un orden" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Ordenar por</SelectLabel>
                  <SelectItem :value="{ id: 'asc' }"> ID asc. </SelectItem>
                  <SelectItem :value="{ id: 'desc' }"> ID desc. </SelectItem>
                  <SelectItem :value="{ name: 'asc' }"> Nombre asc. </SelectItem>
                  <SelectItem :value="{ name: 'desc' }"> Nombre desc. </SelectItem>
                  <SelectItem :value="{ description: 'asc' }"> Descripción asc. </SelectItem>
                  <SelectItem :value="{ description: 'desc' }"> Descripción desc. </SelectItem>
                  <SelectItem :value="{ created_at: 'asc' }"> Fecha Creado asc. </SelectItem>
                  <SelectItem :value="{ created_at: 'desc' }"> Fecha Creado desc. </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>
        </div>
        <SheetFooter>
          <SheetClose as-child>
            <Button type="button" @click="submitSearch"> Aplicar Filtros </Button>
          </SheetClose>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  </div>
</template>
