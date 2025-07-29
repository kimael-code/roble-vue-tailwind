<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Role } from '@/types';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ComboboxPermissions from './ComboboxPermissions.vue';
import ComboboxRoles from './ComboboxRoles.vue';
import ComboboxStatus from './ComboboxStatus.vue';

defineProps<{
  show: boolean;
  permissions?: Array<string>;
  roles?: Array<Role>;
  status?: Array<string>;
}>();

const emit = defineEmits(['close', 'advancedSearch']);

const form = ref({
  permissions: [],
  roles: [],
  status: [],
});

function submitSearch() {
  router.reload({
    data: form.value,
    only: ['users'],
    onSuccess: () => emit('advancedSearch', form.value),
  });
}
</script>

<template>
  <div class="grid grid-cols-2 gap-2">
    <Sheet :open="show" @update:open="$emit('close')">
      <SheetContent side="top">
        <SheetHeader>
          <SheetTitle>Usuarios: Filtros de Búsqueda Avanzados</SheetTitle>
          <SheetDescription>Parametrice la consulta de registros haciendo uso de los siguientes controles.</SheetDescription>
        </SheetHeader>
        <Tabs default-value="status" class="pr-4 pl-4" :unmount-on-hide="false">
          <TabsList class="grid w-full grid-cols-3">
            <TabsTrigger value="status">Estatus</TabsTrigger>
            <TabsTrigger value="roles">Roles</TabsTrigger>
            <TabsTrigger value="permissions">Permisos</TabsTrigger>
          </TabsList>
          <TabsContent value="status">
            <ComboboxStatus :status @selected="(s) => (form.status = s)" />
          </TabsContent>
          <TabsContent value="roles">
            <ComboboxRoles :roles @selected="(r) => (form.roles = r)" />
          </TabsContent>
          <TabsContent value="permissions">
            <ComboboxPermissions :permissions @selected="(o) => (form.permissions = o)" />
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
