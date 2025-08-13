<!-- resources/js/Pages/Admin/Maintenance.vue -->

<script setup lang="ts">
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { Info } from 'lucide-vue-next';

const props = defineProps<{
  status: boolean;
}>();
console.log(props);


const form = useForm({
  secret: '',
  refresh: 15,
  retry: 60,
});

const toggleMaintenance = () => {
  form.post(route('admin.maintenance.toggle'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <div class="container mx-auto py-6">
    <Card>
      <CardHeader>
        <CardTitle>Configuración de Mantenimiento</CardTitle>
      </CardHeader>
      <CardContent>
        <Alert v-if="$page.props.flash.success" class="mb-4">
          <Info class="h-4 w-4" />
          <AlertDescription>
            {{ $page.props.flash.success }}
          </AlertDescription>
        </Alert>

        <div class="space-y-4">
          <div>
            <p class="text-sm text-muted-foreground">
              Estado actual:
              <span class="font-medium" :class="status ? 'text-destructive' : 'text-success'">
                {{ status ? 'EN MANTENIMIENTO' : 'ACTIVO' }}
              </span>
            </p>
          </div>

          <form @submit.prevent="toggleMaintenance" class="space-y-4">
            <div v-if="!status" class="space-y-2">
              <Label for="secret">Clave secreta (opcional)</Label>
              <Input id="secret" v-model="form.secret" placeholder="Dejar vacío para acceso público restringido" />
            </div>

            <Button :type="'submit'" :variant="status ? 'default' : 'destructive'" :disabled="form.processing">
              {{ status ? 'Desactivar Modo Mantenimiento' : 'Activar Modo Mantenimiento' }}
            </Button>
          </form>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
