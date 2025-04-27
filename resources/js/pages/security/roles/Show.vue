<script setup lang="ts">
import AlertDialog from '@/components/AlertDialog.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, PaginatedCollection, Permission, Role, User } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Plus, Users } from 'lucide-vue-next';
import { ref } from 'vue';
import Permisos from './partials/Permisos.vue';
import Usuarios from './partials/Usuarios.vue';

interface Props {
  can: Can;
  filters: object;
  permissions: PaginatedCollection<Permission>;
  role: Role;
  users: PaginatedCollection<User>;
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Roles',
    href: '/roles',
  },
  {
    title: 'Ver',
    href: '',
  },
];

const deleteForm = useForm({});
const isProcessing = ref(false);

function deleteData() {
  deleteForm.delete(route('roles.destroy', { role: props.role.id }), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}
function editData() {
  router.visit(route('roles.edit', { role: props.role.id }), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}
function newData() {
  router.visit(route('roles.create'), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Roles: Ver" />
    <ContentLayout :title="role.name" :description="role.description">
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
              <p class="text-muted-foreground text-sm">{{ role.created_at_human }}</p>
              <br />
              <p class="text-sm font-medium">Modificado</p>
              <p class="text-muted-foreground text-sm">{{ role.updated_at_human }}</p>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
              <Button @click="editData" :disabled="isProcessing">
                <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
                Editar
              </Button>
              <AlertDialog
                :title="`¿Eliminar el rol «${role.name}» permanentemente?`"
                :description="`Este rol «${role.description}».`"
                @continue="deleteData"
              >
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
          <Tabs default-value="permissions" class="w-auto">
            <TabsList class="grid w-full grid-cols-2">
              <TabsTrigger value="permissions">Permisos</TabsTrigger>
              <TabsTrigger value="usuarios">Usuarios</TabsTrigger>
            </TabsList>
            <TabsContent value="permissions">
              <Permisos :filters :role-id="role.id" :permissions></Permisos>
            </TabsContent>
            <TabsContent value="usuarios">
              <Usuarios :filters :role-id="role.id" :users></Usuarios>
            </TabsContent>
          </Tabs>
        </div>
      </section>
    </ContentLayout>
  </AppLayout>
</template>
