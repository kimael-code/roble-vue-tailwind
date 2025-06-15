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
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useConfirmAction, useRequestActions } from '@/composables';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { ActivityLog, BreadcrumbItem, Can, OrganizationalUnit, PaginatedCollection, SearchFilter } from '@/types';
import { Head } from '@inertiajs/vue3';
import { EllipsisIcon, LoaderCircle, Plus, Workflow } from 'lucide-vue-next';
import { watch } from 'vue';
import CardOrganization from './partials/CardOrganization.vue';
import OrganizationalUnits from './partials/OrganizationalUnits.vue';

const props = defineProps<{
  can: Can;
  filters: SearchFilter;
  organizationalUnit: OrganizationalUnit;
  organizationalUnits: PaginatedCollection<OrganizationalUnit>;
  logs: PaginatedCollection<ActivityLog>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Unidades Administrativas',
    href: '/organizational-units',
  },
  {
    title: 'Ver',
    href: '',
  },
];

const { action, requestCreate, requestAction, requestEdit, requestingCreate, resourceID } = useRequestActions('organizational-units');
const { alertOpen, alertAction, alertActionCss, alertTitle, alertDescription } = useConfirmAction();

watch(action, () => {
  switch (action.value) {
    case 'destroy':
      alertAction.value = 'Eliminar permanentemente';
      alertActionCss.value = 'bg-destructive text-destructive-foreground hover:bg-destructive/90';
      alertTitle.value = `¿Eliminar unidad administrativa «${props.organizationalUnit.name}» permanentemente?`;
      alertDescription.value = `Esta acción no podrá revertirse. Los datos de «${props.organizationalUnit.name}» se perderán permanentemente.`;
      alertOpen.value = true;
      break;

    default:
      break;
  }
});
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Unidades Administrativas: Ver" />
    <ContentLayout :title="organizationalUnit.name" :description="`${organizationalUnit.acronym}`">
      <template #icon>
        <Workflow />
      </template>
      <section class="grid gap-4 md:grid-cols-4">
        <div class="col-span-3 md:col-span-1">
          <Card class="container">
            <CardHeader>
              <CardTitle>Detalles</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm font-medium">Piso</p>
              <p class="text-muted-foreground text-sm">{{ organizationalUnit.floor ?? '-' }}</p>
              <br />
              <p v-if="organizationalUnit.code" class="text-sm font-medium">Código</p>
              <p v-if="organizationalUnit.code" class="text-muted-foreground text-sm">{{ organizationalUnit.code }}</p>
              <br v-if="organizationalUnit.code" />
              <p class="text- text-sm font-medium">Creado</p>
              <p class="text-muted-foreground text-sm">{{ organizationalUnit.created_at_human }}</p>
              <br />
              <p class="text-sm font-medium">Modificado</p>
              <p class="text-muted-foreground text-sm">{{ organizationalUnit.updated_at_human }}</p>
              <br />
              <p class="text- text-sm font-medium">Estatus</p>
              <p class="text-sm" :class="{ 'text-green-500': !organizationalUnit.disabled_at, 'text-red-500': organizationalUnit.disabled_at }">
                {{ organizationalUnit.status }}
              </p>
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
                  <DropdownMenuItem @click="requestEdit(props.organizationalUnit.id, { preserveState: false })">
                    <span>Editar</span>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="action = 'destroy'">
                    <span>Eliminar</span>
                  </DropdownMenuItem>
                </DropdownMenuGroup>
              </DropdownMenuContent>
            </DropdownMenu>
            <Button v-if="can.create" class="ml-3" @click="requestCreate" :disabled="requestingCreate">
              <LoaderCircle v-if="requestingCreate" class="h-4 w-4 animate-spin" />
              <Plus v-else class="mr-2 h-4 w-4" />
              Nuevo
            </Button>
          </div>
          <Tabs default-value="organization" class="w-auto">
            <TabsList class="grid w-full grid-cols-3">
              <TabsTrigger value="organization">Ente</TabsTrigger>
              <TabsTrigger value="ous">Unidades Administrativas</TabsTrigger>
              <TabsTrigger value="logs">Actividad</TabsTrigger>
            </TabsList>
            <TabsContent value="organization">
              <CardOrganization :organization="organizationalUnit.organization" :ou="organizationalUnit.organizational_unit" />
            </TabsContent>
            <TabsContent value="ous">
              <OrganizationalUnits :filters :resource-id="organizationalUnit.id" :ous="organizationalUnits" />
            </TabsContent>
            <TabsContent value="logs">
              <ActivityLogs :filters :logs page-route-name="organizational-units.show" :resource-id="organizationalUnit.id" />
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
            <AlertDialogAction :class="alertActionCss" @click="requestAction(organizationalUnit.id, { preserveState: false })">
              {{ alertAction }}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </ContentLayout>
  </AppLayout>
</template>
