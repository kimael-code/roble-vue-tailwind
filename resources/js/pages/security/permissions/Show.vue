<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, PaginatedCollection, Permission, Role, User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { KeySquare } from 'lucide-vue-next';
import { computed, ref } from 'vue';
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
    href: '/dashboard',
  },
];

const deleteForm = useForm({});
const confirmDeletion = ref(false);

const currentTab = ref('Roles');
const tabs = {
  Roles,
  Usuarios,
};
const tabProps = computed(() => {
  const tProps = {
    data: {},
    'resource-id': props.permission.id,
    'route-name': 'permission.show',
    filters: props.filters,
  };

  switch (currentTab.value) {
    case 'Roles':
      tProps.data = props.roles;
      return tProps;
    case 'Usuarios':
      tProps.data = props.permission;
      return tProps;
    default:
      return tProps;
  }
});

function deleteData() {
  deleteForm.delete(route('permissions.destroy', { permission: props.permission.id }), {
    onFinish: () => (confirmDeletion.value = false),
  });
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Permisos: Ver" />
    <ContentLayout title="Permisos: Ver">
      <template #icon>
        <KeySquare />
      </template>
      <section class="grid gap-8 md:grid-cols-2">
        <div class="max-w-md">
          <Card class="container">
            <CardHeader>
              <CardTitle>{{ permission.name }}</CardTitle>
              <CardDescription>{{ permission.description }}</CardDescription>
            </CardHeader>
            <CardContent>
              <p>{{ permission.set_menu }}</p>
              <p>Creado</p>
              <p>{{ permission.created_at }}</p>
              <p>Modificado</p>
              <p>{{ permission.updated_at }}</p>
            </CardContent>
            <CardFooter>
              <Button variant="link"> Editar </Button>
              <Button variant="destructive"> Eliminar </Button>
            </CardFooter>
          </Card>
        </div>
        <div class="mb-2">
          <Tabs default-value="roles">
            <TabsList class="grid w-full grid-cols-2">
              <TabsTrigger value="roles">Roles</TabsTrigger>
              <TabsTrigger value="usuarios">Usuarios</TabsTrigger>
            </TabsList>
            <TabsContent value="roles">
              <Roles></Roles>
            </TabsContent>
            <TabsContent value="usuarios">
              <Usuarios></Usuarios>
            </TabsContent>
          </Tabs>
        </div>
      </section>
    </ContentLayout>
  </AppLayout>
</template>
