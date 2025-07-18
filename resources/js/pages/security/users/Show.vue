<script setup lang="ts">
import ActivityLogs from '@/components/activity-logs/ActivityLogs.vue';
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
  DropdownMenuPortal,
  DropdownMenuSeparator,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useConfirmAction, useRequestActions } from '@/composables';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { ActivityLog, BreadcrumbItem, Can, PaginatedCollection, Permission, Role, SearchFilter, User } from '@/types';
import { Head } from '@inertiajs/vue3';
import { EllipsisIcon, LoaderCircle, PencilIcon, Plus, RotateCcwIcon, ToggleLeftIcon, ToggleRightIcon, Trash2Icon, UserIcon, XIcon } from 'lucide-vue-next';
import { computed, watch } from 'vue';
import Permisos from './partials/Permisos.vue';
import Roles from './partials/Roles.vue';

const props = defineProps<{
  can: Can;
  filters: SearchFilter;
  user: User;
  permissions: PaginatedCollection<Permission>;
  permissionsCount: number;
  roles: PaginatedCollection<Role>;
  logs: PaginatedCollection<ActivityLog>;
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

const { action, requestCreate, requestAction, requestEdit, requestingCreate, resourceID } = useRequestActions('users');
const { alertOpen, alertAction, alertActionCss, alertTitle, alertDescription } = useConfirmAction();

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
    case 'destroy':
      alertAction.value = 'Eliminar';
      alertActionCss.value = 'bg-destructive text-destructive-foreground hover:bg-destructive/90';
      alertTitle.value = `¿Eliminar usuario(a) «${props.user.name}»?`;
      alertDescription.value = `«${props.user.name}» perderá el acceso al sistema. Sus datos se conservarán.`;
      alertOpen.value = true;
      break;
    case 'restore':
      alertAction.value = 'Restaurar';
      alertActionCss.value = '';
      alertTitle.value = `¿Restaurar usuario(a) «${props.user.name}»?`;
      alertDescription.value = `«${props.user.name}» recuperará el acceso al sistema. Sus datos se restaurarán.`;
      alertOpen.value = true;
      break;
    case 'force_destroy':
      alertAction.value = 'Eliminar permanentemente';
      alertActionCss.value = 'bg-destructive text-destructive-foreground hover:bg-destructive/90';
      alertTitle.value = `¿Eliminar usuario(a) «${props.user.name}» permanentemente?`;
      alertDescription.value = `Esta acción no podrá revertirse. «${props.user.name}» perderá el acceso al sistema. Sus datos se eliminarán.`;
      alertOpen.value = true;
      break;
    case 'enable':
      alertAction.value = 'Activar';
      alertActionCss.value = '';
      alertTitle.value = `Activar usuario(a) «${props.user.name}»?`;
      alertDescription.value = `«${props.user.name}» recuperará el acceso al sistema. Sus datos se restaurarán.`;
      alertOpen.value = true;
      break;
    case 'disable':
      alertAction.value = 'Desactivar';
      alertActionCss.value = 'bg-amber-500 text-foreground hover:bg-amber-500/90';
      alertTitle.value = `Desactivar usuario(a) «${props.user.name}»?`;
      alertDescription.value = `«${props.user.name}» perderá el acceso al sistema. Sus datos se conservarán.`;
      alertOpen.value = true;
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
        <div class="col-span-3 md:col-span-1">
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
              <p v-if="user.disabled_at" class="text-sm text-amber-500">DESACTIVADO</p>
              <p v-if="user.disabled_at" class="text-sm text-amber-500">{{ user.disabled_at_human }}</p>
              <p v-if="user.deleted_at" class="text-sm text-red-500">ELIMINADO</p>
              <p v-if="user.deleted_at" class="text-sm text-red-500 ">{{ user.deleted_at_human }}</p>
              <p v-if="!user.disabled_at && !user.deleted_at" class="text-sm text-green-500">ACTIVO</p>
            </CardContent>
          </Card>
        </div>
        <div class="col-span-3">
          <div class="flex items-center justify-end pb-3">
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" :disabled="resourceID !== null">
                  <EllipsisIcon v-if="resourceID === null" />
                  <LoaderCircle v-else class="animate-spin" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuLabel>Acciones</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuGroup>
                  <DropdownMenuItem v-if="can.update" @click="requestEdit(user.id, { preserveState: false })">
                    <PencilIcon />
                    <span>Editar</span>
                  </DropdownMenuItem>

                  <DropdownMenuSub v-if="can.activate || can.deactivate">
                    <DropdownMenuSubTrigger>
                      <span>Activación</span>
                    </DropdownMenuSubTrigger>
                    <DropdownMenuPortal>
                      <DropdownMenuSubContent>
                        <DropdownMenuItem v-if="can.activate" :disabled="user.disabled_at === null" @click="action = 'enable'">
                          <ToggleRightIcon />
                          <span>Activar</span>
                        </DropdownMenuItem>
                        <DropdownMenuItem v-if="can.deactivate" :disabled="user.disabled_at !== null" @click="action = 'disable'">
                          <ToggleLeftIcon />
                          <span>Desactivar</span>
                        </DropdownMenuItem>
                      </DropdownMenuSubContent>
                    </DropdownMenuPortal>
                  </DropdownMenuSub>

                  <DropdownMenuSub v-if="can.restore || can.delete || can.f_delete">
                    <DropdownMenuSubTrigger> Eliminación </DropdownMenuSubTrigger>
                    <DropdownMenuPortal>
                      <DropdownMenuSubContent>
                        <DropdownMenuItem v-if="can.restore" :disabled="!user.deleted_at" @click="action = 'restore'">
                          <RotateCcwIcon />
                          <span>Restaurar</span>
                        </DropdownMenuItem>
                        <DropdownMenuItem v-if="can.delete" :disabled="user.deleted_at ? true : false" @click="action = 'destroy'">
                          <Trash2Icon />
                          <span>Eliminar</span>
                        </DropdownMenuItem>
                        <DropdownMenuItem v-if="can.f_delete" :disabled="!user.deleted_at" @click="action = 'force_destroy'">
                          <XIcon />
                          <span>Eliminar permanentemente</span>
                        </DropdownMenuItem>
                      </DropdownMenuSubContent>
                    </DropdownMenuPortal>
                  </DropdownMenuSub>
                </DropdownMenuGroup>
              </DropdownMenuContent>
            </DropdownMenu>
            <Button v-if="can.create" class="ml-3" @click="requestCreate" :disabled="requestingCreate">
              <LoaderCircle v-if="requestingCreate" class="h-4 w-4 animate-spin" />
              <Plus v-else class="mr-2 h-4 w-4" />
              Nuevo
            </Button>
          </div>
          <Tabs default-value="roles" class="w-auto">
            <TabsList class="grid w-full grid-cols-3">
              <TabsTrigger value="roles">Roles</TabsTrigger>
              <TabsTrigger value="permissions">Permisos</TabsTrigger>
              <TabsTrigger value="logs">Actividad</TabsTrigger>
            </TabsList>
            <TabsContent value="roles">
              <Roles :filters :user-id="user.id" :roles></Roles>
            </TabsContent>
            <TabsContent value="permissions">
              <Permisos :filters :user-id="user.id" :permissions :permissions-count></Permisos>
            </TabsContent>
            <TabsContent value="logs">
              <ActivityLogs :filters :logs page-route-name="users.show" :resource-id="user.id" />
            </TabsContent>
          </Tabs>
        </div>
      </section>

      <AlertDialog v-model:open="alertOpen">
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>{{ alertTitle }}</AlertDialogTitle>
            <AlertDialogDescription>{{ alertDescription }}</AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel @click="action = null">Cancelar</AlertDialogCancel>
            <AlertDialogAction :class="alertActionCss" @click="requestAction(user.id, { preserveState: false })">
              {{ alertAction }}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </ContentLayout>
  </AppLayout>
</template>
