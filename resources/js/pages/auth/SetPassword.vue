<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Switch from '@/components/ui/switch/Switch.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { LoaderCircle } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';

const form = useForm({
  password: '',
  password_confirmation: '',
});
const inputType = ref('password');
const inputRef = ref();
const buttonCancel = ref(false);

function showPasswords() {
  if (inputType.value === 'password')
    inputType.value = 'text';
  else
    inputType.value = 'password';
}

function submit() {
  form.post(route('set.password.update'), {
    errorBag: 'updatePassword',
    onError : () => inputRef.value.focus(),
    onFinish: () => form.reset(),
  });
}

function logout() {
  router.post(
    route('logout'),
    {},
    {
      onStart: () => (buttonCancel.value = true),
      onFinish: () => (buttonCancel.value = false),
    },
  );
}
</script>

<template>
  <AuthLayout
    title="Establecer Contraseña"
    description="Antes de proceder, es necesario que Usted establezca su propia contraseña. Debe tener, al menos, ocho (8) caracteres."
  >
    <Head title="Establecer Contraseña" />

    <form @submit.prevent="submit">
      <div class="grid gap-6">
        <div class="grip gap-2">
          <Label for="password">Nueva Contraseña</Label>
          <Input
            ref="inputRef"
            id="password"
            :type="inputType"
            name="password"
            autocomplete="new-password"
            v-model="form.password"
            class="mt-1 block w-full"
            required
            autofocus
            placeholder="Nueva Contraseña"
          />
          <InputError :message="form.errors.password" />
        </div>

        <div class="grid gap-2">
          <Label for="password_confirmation"> Confirmar Contraseña </Label>
          <Input
            id="password_confirmation"
            :type="inputType"
            name="password_confirmation"
            autocomplete="new-password"
            v-model="form.password_confirmation"
            class="mt-1 block w-full"
            required
            placeholder="Confirmar contraseña"
          />
          <InputError :message="form.errors.password_confirmation" />
        </div>

        <div class="flex items-center space-x-2">
          <Switch id="show_fields" @update:model-value="showPasswords" />
          <Label for="show_fields">Mostrar/Ocultar campos</Label>
        </div>

        <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
          Establecer contraseña
        </Button>

        <Button class="w-full" variant="secondary" :disabled="buttonCancel || form.processing" @click="logout">
          <LoaderCircle v-if="buttonCancel || form.processing" class="h-4 w-4 animate-spin" />
          Salir
        </Button>
      </div>
    </form>
  </AuthLayout>
</template>
