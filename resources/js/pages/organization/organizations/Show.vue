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
import { AspectRatio } from '@/components/ui/aspect-ratio';
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
import { BreadcrumbItem, Can, Organization, OrganizationalUnit, PaginatedCollection } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Building, EllipsisIcon, LoaderCircle, Plus } from 'lucide-vue-next';
import { watch } from 'vue';
import OrganizationalUnits from './partials/OrganizationalUnits.vue';

const props = defineProps<{
  can: Can;
  filters: object;
  organization: Organization;
  ous: PaginatedCollection<OrganizationalUnit>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Entes',
    href: '/organizations',
  },
  {
    title: 'Ver',
    href: '',
  },
];

const { action, requestCreate, requestAction, requestEdit, requestingCreate, resourceID } = useRequestActions('organizations');
const { alertOpen, alertAction, alertActionCss, alertTitle, alertDescription } = useConfirmAction();

watch(action, () => {
  switch (action.value) {
    case 'destroy':
      alertAction.value = 'Eliminar permanentemente';
      alertActionCss.value = 'bg-destructive text-destructive-foreground hover:bg-destructive/90';
      alertTitle.value = `¿Eliminar ente «${props.organization.name}» permanentemente?`;
      alertDescription.value = `Esta acción no podrá revertirse. Los datos de «${props.organization.name}» se perderán permanentemente.`;
      alertOpen.value = true;
      break;

    default:
      break;
  }
});
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Entes: Ver" />
    <ContentLayout :title="organization.name" :description="organization.rif">
      <template #icon>
        <Building />
      </template>
      <section class="grid gap-4 md:grid-cols-4">
        <div class="col-span-3 md:col-span-1">
          <Card class="container">
            <CardHeader>
              <CardTitle>Detalles</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="w-full overflow-hidden rounded-xs shadow-sm">
                <AspectRatio :ratio="31 / 8">
                  <img class="h-full w-full object-cover" :src="organization.logo_url" alt="Logo empresarial" />
                </AspectRatio>
              </div>
              <br />
              <p class="text-sm font-medium">Acrónimo</p>
              <p class="text-muted-foreground text-sm">{{ organization.acronym }}</p>
              <br />
              <p class="text-sm font-medium">Dirección</p>
              <p class="text-muted-foreground text-sm">{{ organization.address }}</p>
              <br />
              <p class="text- text-sm font-medium">Creado</p>
              <p class="text-muted-foreground text-sm">{{ organization.created_at_human }}</p>
              <br />
              <p class="text-sm font-medium">Modificado</p>
              <p class="text-muted-foreground text-sm">{{ organization.updated_at_human }}</p>
              <br />
              <p class="text- text-sm font-medium">Estatus</p>
              <p class="text-sm" :class="{ 'text-green-500': !organization.disabled_at, 'text-red-500': organization.disabled_at }">
                {{ organization.status }}
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
                  <DropdownMenuItem @click="requestEdit(props.organization.id, { preserveState: false })">
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
          <Tabs default-value="ous" class="w-auto">
            <TabsList class="grid w-full grid-cols-2">
              <TabsTrigger value="ous">Unidades Administrativas</TabsTrigger>
            </TabsList>
            <TabsContent value="ous">
              <OrganizationalUnits :filters :resource-id="organization.id" :ous></OrganizationalUnits>
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
            <AlertDialogAction :class="alertActionCss" @click="requestAction(organization.id, { preserveState: false })">
              {{ alertAction }}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </ContentLayout>
  </AppLayout>
</template>
