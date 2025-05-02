<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { Switch } from '@/components/ui/switch';
import { Employee } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Search } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  employees: Array<Employee>;
  search: string | undefined;
  showDialogTrigger: boolean;
}>();
const emit = defineEmits(['employeeSelected', 'externalPerson']);

const open = ref(false);
const search = ref(props.search);

watchDebounced(
  search,
  (s) => {
    if (s === '') search.value = undefined;

    router.visit(route('users.create'), {
      data: { search: s },
      only: ['employees'],
      preserveScroll: true,
      preserveState: true,
    });
  },
  { debounce: 500, maxWait: 1000 },
);

const formPerson = useForm({
  is_external: false,
  id_card: '',
  names: '',
  surnames: '',
  phones: '',
  emails: '',
  position: '',
  staff_type: '',
});

function handleEmployeeSelection(data: Employee) {
  formPerson.id_card = data.id_card;
  formPerson.names = data.names;
  formPerson.surnames = data.surnames;
  formPerson.position = data.position;
  formPerson.staff_type = data.staff_type_name;

  open.value = false;
  search.value = '';
  emit('employeeSelected', data);
  formPerson.reset();
}

function handleExternalPerson() {
  open.value = false;
  search.value = '';
  emit('externalPerson', formPerson.data());
  formPerson.reset();
}
</script>

<template>
  <Dialog v-model:open="open">
    <DialogTrigger v-if="showDialogTrigger" as-child>
      <Button variant="outline"> Empleado / Persona </Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Empleado / Persona</DialogTitle>
        <DialogDescription>
          Busque y seleccione a un empleado. Si el usuario a registrar no pertenece a la organización active el suiche "Usuario Externo" para
          establecer los datos personales manualmente.
        </DialogDescription>
      </DialogHeader>
      <div class="flex items-center space-x-2">
        <Switch id="isExternal" v-model:model-value="formPerson.is_external" />
        <Label for="isExternal">Usuario Externo</Label>
      </div>
      <div v-if="!formPerson.is_external" class="relative w-full max-w-sm items-center p-4">
        <Input id="search" type="text" placeholder="Buscar..." class="pl-10" v-model="search" />
        <span class="absolute inset-y-0 start-0 flex items-center justify-center px-5">
          <Search class="text-muted-foreground size-6" />
        </span>
      </div>
      <ScrollArea v-if="!formPerson.is_external" class="m-3 h-20 rounded-md border">
        <div class="p-4">
          <div v-for="(employee, i) in employees" :key="i">
            <Label :for="employee.id_card" class="flex items-center space-x-3">
              <Checkbox :id="employee.id_card" @update:model-value="handleEmployeeSelection(employee)" />
              <span>{{ `${employee.nationality}-${employee.id_card} ${employee.names} ${employee.surnames}` }}</span>
            </Label>
            <Separator class="my-2" />
          </div>
        </div>
      </ScrollArea>
      <div v-else class="grid gap-4 py-4">
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="id_card" class="text-right"> Nro. de Cédula </Label>
          <Input id="id_card" v-model="formPerson.id_card" class="col-span-3" maxlength="8" placeholder="ej.: 12345678" />
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="names" class="text-right"> Nombres </Label>
          <Input id="names" v-model="formPerson.names" class="col-span-3" maxlength="255" placeholder="ej.: Pedro ó Pedro Luis" />
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="surnames" class="text-right"> Apellidos </Label>
          <Input id="surnames" v-model="formPerson.surnames" class="col-span-3" maxlength="255" placeholder="ej.: Pérez ó Pérez González" />
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="position" class="text-right"> Cargo </Label>
          <Input id="position" v-model="formPerson.position" class="col-span-3" maxlength="255" placeholder="ej.: Auditor Externo" />
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="phones" class="text-right"> Teléfonos </Label>
          <Input id="phones" v-model="formPerson.phones" class="col-span-3" maxlength="255" placeholder="ej.: 0416-1234567,0212-1234567" />
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="emails" class="text-right"> Correos </Label>
          <Input id="emails" v-model="formPerson.emails" class="col-span-3" maxlength="255" placeholder="ej.: usuario1@correo.com,usuario2@correo.com" />
        </div>
      </div>
      <DialogFooter v-if="formPerson.is_external">
        <Button type="button" @click="handleExternalPerson"> Guardar </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
