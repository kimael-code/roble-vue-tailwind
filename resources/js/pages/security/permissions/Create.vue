<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/ContentLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import { KeySquare } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Permisos',
    href: '/dashboard',
  },
];

const form = useForm('post', route('permissions.store'), {
  name: '',
  description: '',
  guarde_name: 'web',
  set_menu: false,
});

const buttonCancel = ref(false);

function submit() {
  form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
}

function index() {
  router.visit(route('permissions.index'), {
    onStart: () => (buttonCancel.value = true),
    onFinish: () => (buttonCancel.value = false),
  });
}
</script>

<template>
  <AppLayout :breadcrumbs>
    <Head title="Permisos: Nuevo" />
    <ContentLayout title="Permisos: Nuevo">
      <template #icon>
        <KeySquare />
      </template>
      <div class="space-y-6">
        <form class="space-y-6">
          <div class="grid gap-2">
            <Label for="name">Nombre</Label>
            <Input
              id="name"
              v-model="form.name"
              type="text"
              class="mt-1 block w-full"
              autocomplete="on"
              placeholder="Nombre"
            />
            <InputError :message="form.errors.name" />
          </div>
        </form>
      </div>
    </ContentLayout>
  </AppLayout>
</template>
