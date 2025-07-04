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
import { ScrollArea } from '@/components/ui/scroll-area';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import TabsContent from '@/components/ui/tabs/TabsContent.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem, Can, SearchFilter } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { FlexRender } from '@tanstack/vue-table';
import { BugIcon, FileDownIcon, FileX2Icon } from 'lucide-vue-next';
import { ref } from 'vue';

interface LogContent {
  context: string;
  level: string;
  levelClass: string;
  levelIcon: string;
  date: string;
  text: string;
  inFile: string;
  stack: string;
}

defineProps<{
  can: Can;
  filters: SearchFilter;
  logFiles: Array<string>;
  logs: Array<LogContent>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Logs',
    href: '',
  },
];

const deleteForm = useForm({});

const stackTrace = ref('');
const selectedFile = ref('');
const openAlertDialog = ref(false);
const openTraceStack = ref(false);

function handleConfirm(fileName: string) {
  selectedFile.value = fileName;
  openAlertDialog.value = true;
}

function handleStackTrace(stack: string) {
  stackTrace.value = stack;
  openTraceStack.value = true;
}

function download(fileName: string) {
  const url = route('log-files.export', { file: fileName });
  window.location.href = url;
}

function deleteLog() {
  deleteForm.delete(route('log-files.destroy', selectedFile.value), {
    preserveScroll: true,
    preserveState: true,
    onFinish: () => (selectedFile.value = ''),
  });
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Logs" />
    <ContentLayout title="Logs" description="Registros de los archivos de depuración generados en la aplicación.">
      <template #icon>
        <BugIcon />
      </template>

      <Tabs :default-value="logFiles[0]" class="w-auto">
        <TabsList class="grid w-full grid-cols-2">
          <TabsTrigger v-for="(logFile, i) in logFiles" :value="logFile" :key="i">
            {{ logFile }}
          </TabsTrigger>
        </TabsList>
        <TabsContent v-for="(logFile, i) in logFiles" :value="logFile" :key="i">
          <div class="flex items-center justify-between px-2 py-4">
            <div class="text-muted-foreground mr-3 text-sm">{{ `${logs.length || 0} entradas` }}</div>
            <div class="flex items-center">
              <Button variant="destructive" v-if="can && can.delete" class="ml-3" @click="handleConfirm(logFile)">
                <FileX2Icon class="mr-2 h-4 w-4" />
                Eliminar
              </Button>
              <Button v-if="can && can.export" class="ml-3" @click="download(logFile)">
                <FileDownIcon class="mr-2 h-4 w-4" />
                Descargar
              </Button>
            </div>
          </div>
          <br />
          <Table>
            <TableCaption>Registros del log</TableCaption>
            <TableHeader>
              <TableRow>
                <TableHead class="w-[100px]">Nivel</TableHead>
                <TableHead class="w-[175px]">Marca de Tiempo</TableHead>
                <TableHead class="w-[250px]">Contenido</TableHead>
                <TableHead class="w-[150px]">Traza</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="(row, i) in logs" :key="i">
                <TableCell class="font-medium">
                  {{ row.level }}
                </TableCell>
                <TableCell>{{ row.date }}</TableCell>
                <TableCell class="whitespace-pre-wrap"><FlexRender :render="row.text" /></TableCell>
                <TableCell>
                  <Button type="button" @click="handleStackTrace(row.stack)">Ver Traza</Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </TabsContent>
      </Tabs>
    </ContentLayout>

    <Sheet v-model:open="openTraceStack">
      <SheetContent side="bottom">
        <div class="mx-auto w-full">
          <SheetHeader>
            <SheetTitle>Traza de la Pila</SheetTitle>
            <SheetDescription>Detalles del log</SheetDescription>
          </SheetHeader>
          <ScrollArea class="h-72 rounded-md border">
            <pre>{{ stackTrace }}</pre>
          </ScrollArea>
          <SheetFooter>
            <SheetClose>
              <Button variant="outline" @click="stackTrace = ''">Cerrar</Button>
            </SheetClose>
          </SheetFooter>
        </div>
      </SheetContent>
    </Sheet>

    <AlertDialog v-model:open="openAlertDialog">
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>{{ `¿Eliminar archivo ${selectedFile}?` }}</AlertDialogTitle>
          <AlertDialogDescription> Antes de eliminarlo asegúrese de haberlo descargado previamente. </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel @click="selectedFile = ''"> Cancelar </AlertDialogCancel>
          <AlertDialogAction class="bg-destructive text-destructive-foreground hover:bg-destructive/90" @click="deleteLog">
            Eliminar
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  </AppLayout>
</template>

<style lang="css" scoped>
@media all and (orientation: landscape) {
  pre {
    white-space: pre-wrap;
    word-wrap: break-word;
    text-align: start;
  }
}
</style>
