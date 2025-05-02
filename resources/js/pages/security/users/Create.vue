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
import { BreadcrumbItem, Employee, Pagination, Permission, Role } from '@/types';
import { Head, router, WhenVisible } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { useForm } from 'laravel-precognition-vue-inertia';
import { LoaderCircle, Search, UserIcon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import CardPerson from './partials/CardPerson.vue';
import DialogPerson from './partials/DialogPerson.vue';

const props = defineProps<{
  filters: { [index: string]: string | undefined };
  employees: Array<Employee>;
  permissions: Array<Permission>;
  roles: Array<Role>;
  paginationPerm: Pagination<Permission>;
  paginationRole: Pagination<Role>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Usuarios',
    href: '/users',
  },
  {
    title: 'Nuevo',
    href: '',
  },
];

const buttonCancel = ref(false);
const openSheetPermissions = ref(false);
const openSheetRoles = ref(false);
const search = ref(props.filters.search);

type formUser = {
  name: string;
  email: string;
  is_external: boolean;
  id_card: string;
  names: string;
  surnames: string;
  phones: string;
  emails: string;
  position: string;
  staff_type: string;
  permissions: string[];
  roles: string[];
};

const form = useForm('post', route('users.store'), <formUser>{
  name: '',
  email: '',
  is_external: false,
  id_card: '',
  names: '',
  surnames: '',
  phones: '',
  emails: '',
  position: '',
  staff_type: '',
  permissions: [],
  roles: [],
});

const showDialogTrigger = computed(() => form.id_card ? false : true)

function submit() {
  form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
}

function index() {
  router.visit(route('users.index'), {
    onStart: () => (buttonCancel.value = true),
    onFinish: () => (buttonCancel.value = false),
  });
}

watchDebounced(
  search,
  (s) => {
    if (s === '') search.value = undefined;

    router.visit(route('users.create'), {
      data: { search: s },
      preserveScroll: true,
      preserveState: true,
    });
  },
  { debounce: 500, maxWait: 1000 },
);

watch([openSheetPermissions, openSheetRoles], ([isOpenSheetPermissions, isOpenSheetRoles]) => {
  if (!isOpenSheetRoles) {
    search.value = undefined;
    router.visit(route('users.create'), { preserveScroll: true, preserveState: true });
  }
  if (!isOpenSheetPermissions) {
    search.value = undefined;
    router.visit(route('users.create'), { preserveScroll: true, preserveState: true });
  }
});

function handlePermissionSelecction(permission: Permission) {
  if (!form.permissions.includes(permission.description)) {
    form.permissions.push(permission.description);
  } else {
    form.permissions.splice(form.permissions.indexOf(permission.description), 1);
  }
}

function handleRoleSelecction(role: Role) {
  if (!form.roles.includes(role.name)) {
    form.roles.push(role.name);
  } else {
    form.roles.splice(form.roles.indexOf(role.name), 1);
  }
}

function handleEmployeeSelected(employe: { [index: string]: any }) {
  console.log('employee', employe);
  
  form.id_card = employe.id_card;
  form.names = employe.names;
  form.surnames = employe.surnames;
  form.position = employe.position;
  form.staff_type = employe.staff_type;
  form.is_external = employe.is_external
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Crear Nuevo Usuario" />
    <ContentLayout title="Crear Nuevo Usuario">
      <template #icon>
        <UserIcon />
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
                  <Label class="is-required" for="name">Nombre de Usuario</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    maxlength="255"
                    autocomplete="on"
                    placeholder="ej.: pedro.p"
                    required
                    autofocus
                    @change="form.validate('name')"
                  />
                  <InputError :message="form.errors.name" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label class="is-required" for="email">Correo Electrónico</Label>
                  <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    maxlength="255"
                    autocomplete="email"
                    placeholder="ej.: pedro.p@correo.com"
                    required
                    @change="form.validate('email')"
                  />
                  <InputError :message="form.errors.email" />
                </div>
                <br />
                <DialogPerson :employees :search="props.filters.search" :show-dialog-trigger="showDialogTrigger" @employee-selected="(employee) => handleEmployeeSelected(employee)" @external-person="(employee) => handleEmployeeSelected(employee)" />
                <CardPerson
                  :is-external="form.is_external"
                  :idCard="form.id_card"
                  :names="form.names"
                  :surnames="form.surnames"
                  :phones="form.phones"
                  :emails="form.emails"
                  :position="form.position"
                  :staffType="form.staff_type"
                  @quit-person="form.reset()"
                />
                <br />
                <div class="5 flex flex-col space-y-1">
                  <Label for="roles">Roles</Label>
                  <TagsInput id="roles" v-model="form.roles">
                    <TagsInputItem v-for="role in form.roles" :key="role" :value="role">
                      <TagsInputItemText />
                      <TagsInputItemDelete />
                    </TagsInputItem>
                    <TagsInputInput placeholder="Roles seleccionados..." @click="openSheetRoles = true" />
                  </TagsInput>
                  <InputError :message="form.errors.roles" />
                </div>
                <div class="5 flex flex-col space-y-1">
                  <Label for="permissions">Permisos Directos</Label>
                  <TagsInput id="permissions" v-model="form.permissions">
                    <TagsInputItem v-for="permission in form.permissions" :key="permission" :value="permission">
                      <TagsInputItemText />
                      <TagsInputItemDelete />
                    </TagsInputItem>
                    <TagsInputInput placeholder="Permisos seleccionados..." @click="openSheetPermissions = true" />
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

        <Sheet v-model:open="openSheetRoles">
          <SheetContent>
            <SheetHeader>
              <SheetTitle>Seleccionar Roles</SheetTitle>
              <SheetDescription>
                Haga clic en el rol que necesite ser asignado al usuario. El usuario puede pertenecer a más de un rol a la vez, por lo que puede
                seleccionar varios. Haga clic en Seleccionar cuando haya terminado.
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
                <div v-for="(role, i) in roles" :key="i">
                  <Label :for="role.name" class="flex items-center space-x-3">
                    <Checkbox :id="role.name" :model-value="form.roles.includes(role.name)" @update:model-value="handleRoleSelecction(role)" />
                    <span>{{ role.name }}</span>
                  </Label>
                  <Separator class="my-2" />
                </div>
                <WhenVisible
                  :params="{
                    data: { page: paginationRole?.current_page + 1 },
                    only: ['roles', 'paginationRole'],
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

        <Sheet v-model:open="openSheetPermissions">
          <SheetContent>
            <SheetHeader>
              <SheetTitle>Seleccionar Permisos</SheetTitle>
              <SheetDescription>
                Haga clic en los permisos que necesiten ser asignados directamente al usuario, sin tomar en cuenta el rol (o roles) al que pertenezca.
                Haga clic en Seleccionar cuando haya terminado.
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
                    data: { page: paginationPerm?.current_page + 1 },
                    only: ['permissions', 'paginationPerm'],
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
