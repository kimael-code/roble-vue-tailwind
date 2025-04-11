<script setup lang="ts">
import AlertDialog from '@/components/AlertDialog.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, PaginatedCollection, Permission, Role, User } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { KeySquare, LoaderCircle, Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import Roles from './partials/Roles.vue';
import Usuarios from './partials/Usuarios.vue';

interface Props {
  can: Can;
  filters: object;
  permission: Permission;
  roles: PaginatedCollection<Role>;
  users: PaginatedCollection<User>;
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Permisos',
    href: '/permissions',
  },
  {
    title: 'Ver',
    href: '',
  },
];

const deleteForm = useForm({});
const isProcessing = ref(false);

function deleteData() {
  deleteForm.delete(route('permissions.destroy', { permission: props.permission.id }), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}
function editData() {
  router.get(
    route('permissions.edit', { permission: props.permission.id }),
    {},
    {
      onFinish: () => (isProcessing.value = false),
      onStart: () => (isProcessing.value = true),
    },
  );
}
function newData() {
  router.get(
    route('permissions.create'),
    {},
    {
      onFinish: () => (isProcessing.value = false),
      onStart: () => (isProcessing.value = true),
    },
  );
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Permisos: Ver" />
    <ContentLayout :title="permission.name" :description="permission.description">
      <template #icon>
        <KeySquare />
      </template>
      <div class="grid gap-4 md:grid-cols-4">
        <div class="col-1 col-span-3 md:col-span-1">
          <Card class="container">
            <CardHeader>
              <CardTitle>Detalles</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm font-medium">Permite definir el Menú</p>
              <p class="text-sm text-muted-foreground">{{ permission.set_menu }}</p>
              <br />
              <p class="text-sm font-medium">Creado</p>
              <p class="text-sm text-muted-foreground">{{ permission.created_at }}</p>
              <br />
              <p class="text-sm font-medium">Modificado</p>
              <p class="text-sm text-muted-foreground">{{ permission.updated_at }}</p>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
              <Button @click="editData" :disabled="isProcessing">
                <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
                Editar
              </Button>
              <AlertDialog
                :title="`¿Eliminar el permiso «${permission.name}» permanentemente?`"
                :description="`Este permiso permite «${permission.description}», eliminarlo implica no poder ejecutar dicha acción.`"
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
          <Tabs default-value="roles" class="w-auto">
            <TabsList class="grid w-full grid-cols-2">
              <TabsTrigger value="roles">Roles</TabsTrigger>
              <TabsTrigger value="usuarios">Usuarios</TabsTrigger>
            </TabsList>
            <TabsContent value="roles">
              <Roles :filters :permission-id="permission.id" :roles></Roles>
            </TabsContent>
            <TabsContent value="usuarios">
              <Usuarios :filters :permission-id="permission.id" :users></Usuarios>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </ContentLayout>
  </AppLayout>
</template>
