<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input';
import { User } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import ComboboxEvents from './ComboboxEvents.vue';
import ComboboxLogNames from './ComboboxLogNames.vue';
import ComboboxUsers from './ComboboxUsers.vue';
import DateTimePickers from './DateTimePickers.vue';

defineProps<{
  show: boolean;
  users?: Array<User>;
  events?: Array<string>;
  logNames?: Array<string>;
}>();

const emit = defineEmits(['close', 'advancedSearch']);

const form = useForm({
  date: '',
  date_range: {},
  ip_dirs: [],
  selected_users: [],
  selected_events: [],
  selected_modules: [],
  time: '',
  time_from: '',
  time_until: '',
  sort_by: {
    created_at: 'desc',
  },
});

function submitSearch() {
  router.reload({
    data: form.data(),
    only: ['logs'],
    onSuccess: () => emit('advancedSearch', form.data),
  });
}
</script>

<template>
  <div class="grid grid-cols-2 gap-2">
    <Sheet :open="show" @update:open="$emit('close')">
      <SheetContent side="top">
        <SheetHeader>
          <SheetTitle>Filtros de Búsqueda Avanzados</SheetTitle>
          <SheetDescription>Parametrice la consulta de registros haciendo uso de los siguientes controles.</SheetDescription>
        </SheetHeader>
        <Tabs default-value="users" class="pr-4 pl-4" :unmount-on-hide="false">
          <TabsList class="grid w-full grid-cols-5">
            <TabsTrigger value="users">Usuarios</TabsTrigger>
            <TabsTrigger value="events">Eventos</TabsTrigger>
            <TabsTrigger value="logNames">Módulos</TabsTrigger>
            <TabsTrigger value="dateTime">Tiempo</TabsTrigger>
            <TabsTrigger value="ipAddrs">Direcciones IP</TabsTrigger>
          </TabsList>
          <TabsContent value="users">
            <ComboboxUsers :users @selected="(u) => (form.selected_users = u)" />
          </TabsContent>
          <TabsContent value="events">
            <ComboboxEvents :events @selected="(e) => (form.selected_events = e)" />
          </TabsContent>
          <TabsContent value="logNames">
            <ComboboxLogNames :log-names="logNames" @selected="(l) => (form.selected_modules = l)" />
          </TabsContent>
          <TabsContent value="dateTime">
            <DateTimePickers
              @date-range-set="(dr) => (form.date_range = dr)"
              @date-set="(d) => (form.date = d)"
              @time-from-set="(tf) => (form.time_from = tf)"
              @time-set="(t) => (form.time = t)"
              @time-until-set="(tu) => (form.time_until = tu)"
            />
          </TabsContent>
          <TabsContent value="ipAddrs">
            <TagsInput v-model="form.ip_dirs">
              <TagsInputItem v-for="item in form.ip_dirs" :key="item" :value="item">
                <TagsInputItemText />
                <TagsInputItemDelete />
              </TagsInputItem>

              <TagsInputInput id="ip-addrs" :auto-focus="true" placeholder="Direcciones IP..." />
            </TagsInput>
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
                  <SelectItem :value="{ created_at: 'asc' }"> Fecha Creado asc. </SelectItem>
                  <SelectItem :value="{ created_at: 'desc' }"> Fecha Creado desc. </SelectItem>
                  <SelectItem :value="{ causer: 'asc' }"> Usuario asc. </SelectItem>
                  <SelectItem :value="{ causer: 'desc' }"> Usuario desc. </SelectItem>
                  <SelectItem :value="{ event: 'asc' }"> Actividad asc. </SelectItem>
                  <SelectItem :value="{ event: 'desc' }"> Actividad desc. </SelectItem>
                  <SelectItem :value="{ log_name: 'asc' }"> Módulo/Func. asc. </SelectItem>
                  <SelectItem :value="{ log_name: 'desc' }"> Módulo/Func. desc. </SelectItem>
                  <SelectItem :value="{ description: 'asc' }"> Descripción asc. </SelectItem>
                  <SelectItem :value="{ description: 'desc' }"> Descripción desc. </SelectItem>
                  <SelectItem :value="{ subject_id: 'asc' }"> ID asc. </SelectItem>
                  <SelectItem :value="{ subject_id: 'desc' }"> ID desc. </SelectItem>
                  <SelectItem :value="{ ip_address: 'asc' }"> Dirección IP asc. </SelectItem>
                  <SelectItem :value="{ ip_address: 'desc' }"> Dirección IP desc. </SelectItem>
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
