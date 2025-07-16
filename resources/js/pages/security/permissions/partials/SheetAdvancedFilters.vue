<script setup lang="ts">
import { Button } from '@/components/ui/button';
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
        <SheetFooter>
          <SheetClose as-child>
            <Button type="button" @click="submitSearch"> Aplicar Filtros </Button>
          </SheetClose>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  </div>
</template>
