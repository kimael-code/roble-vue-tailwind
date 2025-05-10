<script setup lang="ts">
import AlertDialog from '@/components/AlertDialog.vue';
import { AspectRatio } from '@/components/ui/aspect-ratio';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, Organization, OrganizationalUnit, PaginatedCollection } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Building, LoaderCircle, Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import OrganizationalUnits from './partials/OrganizationalUnits.vue';

interface Props {
  can: Can;
  filters: object;
  organization: Organization;
  ous: PaginatedCollection<OrganizationalUnit>;
}

const props = defineProps<Props>();

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

const deleteForm = useForm({});
const isProcessing = ref(false);

function deleteData() {
  deleteForm.delete(route('organizations.destroy', props.organization.id), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}
function editData() {
  router.get(
    route('organizations.edit', props.organization.id),
    {},
    {
      onFinish: () => (isProcessing.value = false),
      onStart: () => (isProcessing.value = true),
    },
  );
}
function newData() {
  router.get(
    route('organizations.create'),
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
    <Head title="Entes: Ver" />
    <ContentLayout :title="organization.name" :description="organization.rif">
      <template #icon>
        <Building />
      </template>
      <section class="grid gap-4 md:grid-cols-4">
        <div class="col-1 col-span-3 md:col-span-1">
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
              <p class="text- text-sm font-medium">Estatus</p>
              <p class="text-sm" :class="{ 'text-green-500': !organization.disabled_at, 'text-red-500': organization.disabled_at }">
                {{ organization.status }}
              </p>
              <br />
              <p class="text- text-sm font-medium">Creado</p>
              <p class="text-muted-foreground text-sm">{{ organization.created_at_human }}</p>
              <br />
              <p class="text-sm font-medium">Modificado</p>
              <p class="text-muted-foreground text-sm">{{ organization.updated_at_human }}</p>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
              <Button @click="editData" :disabled="isProcessing">
                <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
                Editar
              </Button>
              <AlertDialog
                :title="`¿Eliminar al ente «${organization.rif} ${organization.name}» permanentemente?`"
                :description="`Los datos del ente se perderán permanentemente.`"
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
    </ContentLayout>
  </AppLayout>
</template>
