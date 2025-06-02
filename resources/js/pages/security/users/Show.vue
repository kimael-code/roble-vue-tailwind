<script setup lang="ts">
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useRunAction } from '@/composables/useRunAction';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, PaginatedCollection, Permission, Role, SearchFilter, User } from '@/types';
import { Head } from '@inertiajs/vue3';
import { EllipsisIcon, LoaderCircle, Plus, UserIcon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Permisos from './partials/Permisos.vue';
import Roles from './partials/Roles.vue';

const props = defineProps<{
  can: Can;
  filters: SearchFilter;
  user: User;
  permissions: PaginatedCollection<Permission>;
  permissionsCount: number;
  roles: PaginatedCollection<Role>;
}>();

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

const { action, request, runAction } = useRunAction('users');
const dialogOpen = ref(false);
const dialogAction = ref('Continuar');
const dialogTitle = ref('¿Está seguro?');
const dialogDescription = ref('Si realmente está seguro haga clic en el botón "Continuar"');

const userOUs = computed(() => {
  let result = 'Usuario Externo';

  if (!props.user.is_external && props.user.active_organizational_units?.length) {
    result = props.user.active_organizational_units?.map((ou) => ou.name).join(', ');
  } else {
    result = 'SIN ASOCIAR';
  }

  return result;
});

watch(action, () => {
  switch (action.value) {
    case 'delete':
      dialogAction.value = 'Eliminar';
      dialogTitle.value = `¿Eliminar usuario(a) «${props.user.name}»?`;
      dialogDescription.value = `«${props.user.name}» perderá el acceso al sistema. Sus datos se conservarán.`;
      dialogOpen.value = true;
      break;
    case 'restore':
      dialogAction.value = 'Restaurar';
      dialogTitle.value = `¿Restaurar usuario(a) «${props.user.name}»?`;
      dialogDescription.value = `«${props.user.name}» recuperará el acceso al sistema. Sus datos se restaurarán.`;
      dialogOpen.value = true;
      break;
    case 'f_delete':
      dialogAction.value = 'Eliminar permanentemente';
      dialogTitle.value = `¿Eliminar usuario(a) «${props.user.name}» permanentemente?`;
      dialogDescription.value = `Esta acción no podrá revertirse. «${props.user.name}» perderá el acceso al sistema. Sus datos se eliminarán.`;
      dialogOpen.value = true;
      break;

    default:
      break;
  }
});
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Usuarios: Ver" />
    <ContentLayout :title="user.name" :description="user.email">
      <template #icon>
        <UserIcon />
      </template>
      <section class="grid gap-4 md:grid-cols-4">
        <div class="col-1 col-span-3 md:col-span-1">
          <Card class="container">
            <CardHeader>
              <CardTitle>Detalles</CardTitle>
            </CardHeader>
            <CardContent>
              <template v-if="user.person">
                <p class="text-muted-foreground text-sm">{{ user.person?.id_card }}</p>
                <p class="text-muted-foreground text-sm">{{ `${user.person?.names} ${user.person?.surnames}` }}</p>
                <p class="text-muted-foreground text-sm">{{ user.person?.position }}</p>
                <p class="text-muted-foreground text-sm">{{ user.person?.staff_type }}</p>
                <p class="text-muted-foreground text-sm">{{ user.person?.emails?.join(', ') }}</p>
                <p class="text-muted-foreground text-sm">{{ user.person?.phones?.join(', ') }}</p>
                <br />
              </template>
              <p class="text-sm font-medium">Unidad Administrativa</p>
              <p class="text-muted-foreground text-sm">{{ userOUs }}</p>
              <br />
              <p class="text-sm font-medium">Creado</p>
              <p class="text-muted-foreground text-sm">{{ user.created_at_human }}</p>
              <br />
              <p class="text-sm font-medium">Modificado</p>
              <p class="text-muted-foreground text-sm">{{ user.updated_at_human }}</p>
              <br />
              <p class="text-sm font-medium">Estatus</p>
              <p v-if="user.deleted_at" class="text-sm text-red-500">ELIMINADO</p>
              <p v-if="user.deleted_at" class="text-muted-foreground text-sm">{{ user.deleted_at_human }}</p>
              <p v-else class="text-sm text-green-500">REGISTRADO</p>
            </CardContent>
          </Card>
        </div>
        <div class="col-1 col-span-3">
          <div class="flex items-center justify-end pb-3">
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" :disabled="request.processing">
                  <LoaderCircle v-if="request.processing" class="animate-spin" />
                  <EllipsisIcon v-else />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuLabel>Acciones</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuGroup>
                  <DropdownMenuItem @click="request.get(route('users.edit', props.user.id))">
                    <span>Editar</span>
                  </DropdownMenuItem>
                  <DropdownMenuItem :disabled="!user.deleted_at" @click="action = 'restore'">
                    <span>Restaurar</span>
                  </DropdownMenuItem>
                  <DropdownMenuItem :disabled="user.deleted_at ? true : false" @click="action = 'delete'">
                    <span>Eliminar</span>
                  </DropdownMenuItem>
                  <DropdownMenuItem :disabled="!user.deleted_at" @click="action = 'f_delete'">
                    <span>Eliminar permanentemente</span>
                  </DropdownMenuItem>
                </DropdownMenuGroup>
              </DropdownMenuContent>
            </DropdownMenu>
            <Button v-if="can.create" class="ml-3" @click="request.get(route('users.create'))" :disabled="request.processing">
              <LoaderCircle v-if="request.processing" class="h-4 w-4 animate-spin" />
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
              <Permisos :filters :user-id="user.id" :permissions :permissions-count></Permisos>
            </TabsContent>
          </Tabs>
        </div>
      </section>

      <AlertDialog v-model:open="dialogOpen">
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>{{ dialogTitle }}</AlertDialogTitle>
            <AlertDialogDescription>{{ dialogDescription }}</AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancelar</AlertDialogCancel>
            <AlertDialogAction @click="runAction(user.id)">
              {{ dialogAction }}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </ContentLayout>
  </AppLayout>
</template>
