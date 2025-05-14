<script setup lang="ts">
import AlertDialog from '@/components/AlertDialog.vue';
import { AspectRatio } from '@/components/ui/aspect-ratio';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, OrganizationalUnit } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Plus, Workflow } from 'lucide-vue-next';
import { ref } from 'vue';
import OrganizationalUnits from './partials/OrganizationalUnits.vue';

interface Props {
  can: Can;
  filters: object;
  organizationalUnit: OrganizationalUnit;
}

const props = defineProps<Props>();

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

const deleteForm = useForm({});
const isProcessing = ref(false);

function deleteData() {
  deleteForm.delete(route('organizational-units.destroy', props.organizationalUnit.id), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}
function editData() {
  router.visit(route('organizational-units.edit', props.organizationalUnit.id), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}
function newData() {
  router.visit(route('organizational-units.create'), {
    onFinish: () => (isProcessing.value = false),
    onStart: () => (isProcessing.value = true),
  });
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Unidades Administrativas: Ver" />
    <ContentLayout :title="organizationalUnit.name" :description="`${organizationalUnit.acronym}`">
      <template #icon>
        <Workflow />
      </template>
      <section class="grid gap-4 md:grid-cols-4">
        <div class="col-1 col-span-3 md:col-span-1">
          <Card class="container">
            <CardHeader>
              <CardTitle>Detalles</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm font-medium">Piso</p>
              <p class="text-muted-foreground text-sm">{{ organizationalUnit.floor }}</p>
              <br />
              <p v-if="organizationalUnit.code" class="text-sm font-medium">Código</p>
              <p v-if="organizationalUnit.code" class="text-muted-foreground text-sm">{{ organizationalUnit.code }}</p>
              <br v-if="organizationalUnit.code" />
              <p class="text- text-sm font-medium">Estatus</p>
              <p class="text-sm" :class="{ 'text-green-500': !organizationalUnit.disabled_at, 'text-red-500': organizationalUnit.disabled_at }">
                {{ organizationalUnit.status }}
              </p>
              <br />
              <p class="text- text-sm font-medium">Creado</p>
              <p class="text-muted-foreground text-sm">{{ organizationalUnit.created_at_human }}</p>
              <br />
              <p class="text-sm font-medium">Modificado</p>
              <p class="text-muted-foreground text-sm">{{ organizationalUnit.updated_at_human }}</p>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
              <Button @click="editData" :disabled="isProcessing">
                <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
                Editar
              </Button>
              <AlertDialog
                :title="`¿Eliminar la unidad administrativa «${organizationalUnit.name}» permanentemente?`"
                :description="`Los datos de la unidad administrativa se perderán permanentemente.`"
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
