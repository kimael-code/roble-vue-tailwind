<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, OrganizationalUnit } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import { LoaderCircleIcon, Workflow } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  organizationalUnits: Array<OrganizationalUnit>;
  organizationalUnit: OrganizationalUnit;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Unidades Administrativas',
    href: '/organizational-units',
  },
  {
    title: 'Editar',
    href: '',
  },
];

const buttonCancel = ref(false);

type OrganizationalUnitForm = {
  organization_id: string;
  organizational_unit_id: string;
  code: string;
  name: string;
  acronym: string;
  floor: string;
};

const form = useForm('put', route('organizational-units.update', props.organizationalUnit.id), <OrganizationalUnitForm>{
  organization_id: props.organizationalUnit.organization_id,
  organizational_unit_id: props.organizationalUnit.organizational_unit_id,
  code: props.organizationalUnit.code,
  name: props.organizationalUnit.name,
  acronym: props.organizationalUnit.acronym,
  floor: props.organizationalUnit.floor,
});

function submit() {
  form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
}

function index() {
  router.visit(route('organizational-units.index'), {
    onStart: () => (buttonCancel.value = true),
    onFinish: () => (buttonCancel.value = false),
  });
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Editar Unidad Administrativa" />
    <ContentLayout title="Editar Unidad Administrativa">
      <template #icon>
        <Workflow />
      </template>
      <section class="mx-auto w-full">
        <Card class="container">
          <CardHeader>
            <CardDescription>Los campos con asterisco rojo son requeridos.</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit">
              <div class="grid w-full items-center gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label class="is-required" for="organization_id">Ente</Label>
                  <Input
                    id="organization_id"
                    :value="organizationalUnit.organization.name"
                    type="text"
                    maxlength="255"
                    autocomplete="on"
                    placeholder="ej.: Dirección de Tecnología"
                    required
                    disabled
                  />
                  <InputError :message="form.errors.organization_id" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label class="is-required" for="name">Nombre</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    maxlength="255"
                    autocomplete="on"
                    placeholder="ej.: Dirección de Tecnología"
                    required
                    autofocus
                    @change="form.validate('name')"
                  />
                  <InputError :message="form.errors.name" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="acronym">Acrónimo</Label>
                  <Input id="acronym" v-model="form.acronym" type="text" maxlength="20" placeholder="ej.: ACME" @change="form.validate('acronym')" />
                  <InputError :message="form.errors.acronym" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="code">Código</Label>
                  <Input id="code" v-model="form.code" type="text" maxlength="20" placeholder="ej.: 000000009999" @change="form.validate('code')" />
                  <InputError :message="form.errors.code" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="floor">Piso</Label>
                  <Input id="floor" v-model="form.floor" type="text" maxlength="5" placeholder="ej.: PB" @change="form.validate('floor')" />
                  <InputError :message="form.errors.floor" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="organizational_unit_id">Unidad Administrativa de Adscripción</Label>
                  <Select
                    id="organizational_unit_id"
                    v-model="form.organizational_unit_id"
                    required
                    autofocus
                    @update:model-value="form.validate('organizational_unit_id')"
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Seleccione Unidad Administrativa de Adscripción" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectGroup>
                        <SelectLabel>Unidades Administrativas</SelectLabel>
                        <SelectItem v-for="(ou, i) in organizationalUnits" :value="ou.id" :key="i">
                          {{ ou.name }}
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <InputError :message="form.errors.organizational_unit_id" />
                </div>
              </div>
            </form>
          </CardContent>
          <CardFooter class="flex justify-between px-6 pb-6">
            <Button variant="outline" :disabled="buttonCancel" @click="index">
              <LoaderCircleIcon v-if="buttonCancel" class="h-4 w-4 animate-spin" />
              Cancelar
            </Button>
            <Button :disabled="buttonCancel || form.processing" @click="submit">
              <LoaderCircleIcon v-if="form.processing" class="h-4 w-4 animate-spin" />
              Guardar
            </Button>
          </CardFooter>
        </Card>
      </section>
    </ContentLayout>
  </AppLayout>
</template>
