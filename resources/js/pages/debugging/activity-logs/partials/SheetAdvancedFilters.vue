<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { User } from '@/types';
import { useForm } from '@inertiajs/vue3';
import ComboboxEvents from './ComboboxEvents.vue';
import ComboboxLogNames from './ComboboxLogNames.vue';
import ComboboxUsers from './ComboboxUsers.vue';
import DatePicker from './DatePicker.vue';

defineProps<{
  show: boolean;
  users?: Array<User>;
  events?: Array<string>;
  logNames?: Array<string>;
}>();

defineEmits(['close']);

const form = useForm({
  selected_users: [],
  selected_events: [],
  selected_modules: [],
});

function submitSearch() {
  console.log(form.data());
}
</script>

<template>
  <div class="grid grid-cols-2 gap-2">
    <Sheet :open="show" @update:open="$emit('close')">
      <SheetContent side="right">
        <SheetHeader>
          <SheetTitle>Filtros de Búsqueda Avanzados</SheetTitle>
          <SheetDescription>Parametrice la consulta de registros haciendo uso de los siguientes controles.</SheetDescription>
        </SheetHeader>
        <Tabs default-value="users" class="pr-4 pl-4" :unmount-on-hide="false">
          <TabsList class="grid w-full grid-cols-4">
            <TabsTrigger value="users">Usuarios</TabsTrigger>
            <TabsTrigger value="events">Eventos</TabsTrigger>
            <TabsTrigger value="logNames">Módulos</TabsTrigger>
            <TabsTrigger value="dateTime">Tiempo</TabsTrigger>
          </TabsList>
          <TabsContent value="users">
            <ComboboxUsers :users @selected="(u) => (form.selected_users = u)" />
          </TabsContent>
          <TabsContent value="events">
            <ComboboxEvents :events @selected="(e) => (form.selected_events = e)" />
          </TabsContent>
          <TabsContent value="logNames">
            <ComboboxLogNames :log-names="logNames" @selected="(users) => (form.selected_modules = users)" />
          </TabsContent>
          <TabsContent value="dateTime"><DatePicker /></TabsContent>
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
