<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Pagination, Permission, Role } from '@/types';
import { Head, router, WhenVisible } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { useForm } from 'laravel-precognition-vue-inertia';
import { LoaderCircle, Search, Users } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
  filters: { [index: string]: string | undefined };
  permissions: Array<Permission>;
  pagination: Pagination<Permission>;
  role: Role;
  rolePermissions: Array<string>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Roles',
    href: '/roles',
  },
  {
    title: 'Nuevo',
    href: '',
  },
];

const buttonCancel = ref(false);
const openSheet = ref(false);
const search = ref(props.filters.search);

type formRole = {
  name: string;
  description: string;
  guard_name: string;
  permissions: string[];
};

const form = useForm('put', route('roles.update', props.role.id), <formRole>{
  name: props.role.name,
  description: props.role.description,
  guard_name: props.role.guard_name,
  permissions: props.rolePermissions,
});

function submit() {
  form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
}

function index() {
  router.visit(route('roles.index'), {
    onStart: () => (buttonCancel.value = true),
    onFinish: () => (buttonCancel.value = false),
  });
}

watchDebounced(
  search,
  (s) => {
    if (s === '') search.value = undefined;

    router.visit(route('roles.edit', props.role.id), {
      data: { search: s },
      preserveScroll: true,
      preserveState: true,
    });
  },
  { debounce: 500, maxWait: 1000 },
);

watch(openSheet, (isOpen) => {
  if (!isOpen) {
    search.value = undefined;
    router.visit(route('roles.edit', props.role.id), { preserveScroll: true, preserveState: true });
  }
});

function handlePermissionSelecction(permission: Permission) {
  if (!form.permissions.includes(permission.description)) {
    form.permissions.push(permission.description);
  } else {
    form.permissions.splice(form.permissions.indexOf(permission.description), 1);
  }
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Editar Rol" />
    <ContentLayout title="Editar Rol">
      <template #icon>
        <Users />
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
                  <Label class="is-required" for="name">Nombre</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    maxlength="255"
                    autocomplete="on"
                    placeholder="ej.: Analista de Datos"
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
                    placeholder="ej.: administra los datos de la aplicación"
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
                    placeholder="ej.: web"
                    readonly
                    required
                    @change="form.validate('guard_name')"
                  />
                  <InputError :message="form.errors.guard_name" />
                </div>
                <div class="5 flex flex-col space-y-1">
                  <Label for="permissions">Permisos</Label>
                  <TagsInput id="permissions" v-model="form.permissions">
                    <TagsInputItem v-for="permission in form.permissions" :key="permission" :value="permission">
                      <TagsInputItemText />
                      <TagsInputItemDelete />
                    </TagsInputItem>
                    <TagsInputInput placeholder="Permisos seleccionados..." @click="openSheet = true" />
                  </TagsInput>
                  <InputError :message="form.errors.permissions" />
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

        <Sheet v-model:open="openSheet">
          <SheetContent>
            <SheetHeader>
              <SheetTitle>Seleccionar Permisos</SheetTitle>
              <SheetDescription>
                Haga clic en los permisos que necesiten ser asignados al Rol. Haga clic en Seleccionar cuando haya terminado.
              </SheetDescription>
            </SheetHeader>
            <div class="relative w-full max-w-sm items-center p-4">
              <Input id="search" type="text" placeholder="Buscar..." class="pl-10" v-model="search" />
              <span class="absolute inset-y-0 start-0 flex items-center justify-center px-5">
                <Search class="text-muted-foreground size-6" />
              </span>
            </div>
            <ScrollArea class="m-3 h-75 rounded-md border">
              <div class="p-4">
                <div v-for="(permission, i) in permissions" :key="i">
                  <Label :for="permission.description" class="flex items-center space-x-3">
                    <Checkbox
                      :id="permission.description"
                      :model-value="form.permissions.includes(permission.description)"
                      @update:model-value="handlePermissionSelecction(permission)"
                    />
                    <span>{{ permission.description }}</span>
                  </Label>
                  <Separator class="my-2" />
                </div>
                <WhenVisible
                  :params="{
                    data: { page: pagination?.current_page + 1 },
                    only: ['permissions', 'pagination'],
                  }"
                  always
                >
                  <div class="text-muted">No hay más registros</div>
                </WhenVisible>
              </div>
            </ScrollArea>
            <SheetFooter>
              <SheetClose as-child>
                <Button>Cerrar</Button>
              </SheetClose>
            </SheetFooter>
          </SheetContent>
        </Sheet>
      </section>
    </ContentLayout>
  </AppLayout>
</template>
