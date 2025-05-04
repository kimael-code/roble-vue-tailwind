<script setup lang="ts">
import AlertDialog from '@/components/AlertDialog.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, PaginatedCollection, Permission, Role, SearchFilter, User } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Plus, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Permisos from './partials/Permisos.vue';
import Roles from './partials/Roles.vue';

interface Props {
  can: Can;
  filters: SearchFilter;
  user: User;
  pagePerm: number;
  permissions: PaginatedCollection<Permission>;
  roles: PaginatedCollection<Role>;
}
const props = defineProps<Props>();
console.log(props);

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Usuarios',
    href: '/users',
  },
  {
    title: 'Ver',
    href: '',
  },
];

const deleteForm = useForm({});
const isProcessing = ref(false);

const title = computed(() =>
  props.user.deleted_at ? `¿Eliminar el/la usuario/a «${props.user.name}» permanentemente?` : `¿Eliminar el/la usuario/a «${props.user.name}»?`,
);
const description = computed(() =>
  props.user.deleted_at
    ? 'Este usuario/a perderá el acceso al sistema. Sus datos serán eliminados permanentemente.'
    : 'Este usuario/a perderá el acceso al sistema. Sus datos no serán eliminados.',
);

function deleteData() {
  deleteForm.delete(route('users.destroy', props.user.id), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}

function editData() {
  router.visit(route('users.edit', props.user.id), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}

function newData() {
  router.visit(route('users.create'), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Usuarios: Ver" />
    <ContentLayout :title="user.name" :description="user.email">
      <template #icon>
        <Users />
      </template>
      <section class="grid gap-4 md:grid-cols-4">
        <div class="col-1 col-span-3 md:col-span-1">
          <Card class="container">
            <CardHeader>
              <CardTitle>Detalles</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm font-medium">Creado</p>
              <p class="text-muted-foreground text-sm">{{ user.created_at_human }}</p>
              <br />
              <p class="text-sm font-medium">Modificado</p>
              <p class="text-muted-foreground text-sm">{{ user.updated_at_human }}</p>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
              <Button @click="editData" :disabled="isProcessing">
                <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
                Editar
              </Button>
              <AlertDialog :title="title" :description="description" @continue="deleteData">
                <Button variant="destructive" :disabled="isProcessing">
                  <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
                  Eliminar
                </Button>
              </AlertDialog>
            </CardFooter>
          </Card>
        </div>
        <div class="col-1 col-span-3">
          <div class="flex items-center justify-end">
            <Button v-if="can.create" class="mb-3" @click="newData" :disabled="isProcessing">
              <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
              <Plus v-else class="mr-2 h-4 w-4" />
              Nuevo
            </Button>
          </div>
          <Tabs default-value="roles" class="w-auto">
            <TabsList class="grid w-full grid-cols-2">
              <TabsTrigger value="roles">Roles</TabsTrigger>
              <TabsTrigger value="permissions">Permisos</TabsTrigger>
            </TabsList>
            <TabsContent value="roles">
              <Roles :filters :user-id="user.id" :roles></Roles>
            </TabsContent>
            <TabsContent value="permissions">
              <Permisos :filters :user-id="user.id" :page-perm="pagePerm" :permissions></Permisos>
            </TabsContent>
          </Tabs>
        </div>
      </section>
    </ContentLayout>
  </AppLayout>
</template>
