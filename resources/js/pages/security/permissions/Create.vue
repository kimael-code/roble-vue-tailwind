<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import { KeySquare, LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Permisos',
    href: '/dashboard',
  },
];

const form = useForm('post', route('permissions.store'), {
  name: '',
  description: '',
  guard_name: 'web',
  set_menu: false,
});

const buttonCancel = ref(false);

function submit() {
  form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
}

function index() {
  router.visit(route('permissions.index'), {
    onStart: () => (buttonCancel.value = true),
    onFinish: () => (buttonCancel.value = false),
  });
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Permisos: Nuevo" />
    <ContentLayout title="Permisos: Nuevo">
      <template #icon>
        <KeySquare />
      </template>
      <section class="mx-auto w-full max-w-4xl">
        <Card class="container">
          <CardHeader>
            <CardTitle>Crear Nuevo Permiso</CardTitle>
          </CardHeader>
          <CardContent>
            <form>
              <div class="grid w-full items-center gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label class="is-required" for="name">Nombre</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    maxlength="255"
                    autocomplete="on"
                    placeholder="Nombre"
                    required
                    autofocus
                    @change="form.validate('name')"
                  />
                  <InputError :message="form.errors.name" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label class="is-required" for="description">Descripción</Label>
                  <Input
                    id="description"
                    v-model="form.description"
                    type="text"
                    maxlength="255"
                    autocomplete="on"
                    placeholder="Descripción"
                    required
                    @change="form.validate('description')"
                  />
                  <InputError :message="form.errors.description" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label class="is-required" for="guard_name">Autenticación</Label>
                  <Input
                    id="guard_name"
                    v-model="form.guard_name"
                    type="text"
                    placeholder="Autenticación"
                    readonly
                    required
                    @change="form.validate('guard_name')"
                  />
                  <InputError :message="form.errors.guard_name" />
                </div>
                <div class="flex items-center space-x-2">
                  <Checkbox id="set_menu" @change="form.validate('set_menu')" />
                  <Label for="set_menu">Define menú</Label>
                  <InputError :message="form.errors.set_menu" />
                </div>
              </div>
            </form>
          </CardContent>
          <CardFooter class="flex justify-between px-6 pb-6">
            <Button variant="outline" :disabled="buttonCancel" @click="index">
              <LoaderCircle v-if="buttonCancel" class="h-4 w-4 animate-spin" />
              Cancelar
            </Button>
            <Button :disabled="buttonCancel || form.processing" @click="submit">
              <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
              Guardar
            </Button>
          </CardFooter>
        </Card>
      </section>
    </ContentLayout>
  </AppLayout>
</template>
