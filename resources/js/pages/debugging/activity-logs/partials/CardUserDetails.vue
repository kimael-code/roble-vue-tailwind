<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { getInitials } from '@/composables/useInitials';
import { User, UserAgent } from '@/types';
import { CalendarIcon } from 'lucide-vue-next';

defineProps<{
  causer?: User | string;
  userAgent: UserAgent;
}>();
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Usuario</CardTitle>
      <CardDescription>Detalles del usuario causante del evento.</CardDescription>
    </CardHeader>
    <CardContent>
      <div v-if="typeof causer === 'object'" class="flex justify-start space-x-4">
        <Avatar>
          <AvatarImage v-if="causer.avatar" :src="causer.avatar" />
          <AvatarFallback>{{ getInitials(causer.name) }}</AvatarFallback>
        </Avatar>
        <div class="space-y-1">
          <h4 class="font-mono text-sm font-semibold">{{ `${causer.name}` }}</h4>
          <p class="text-sm">{{ causer.email }}</p>
          <p v-if="causer.person" class="text-sm">{{ `${causer.person?.names} ${causer.person?.surnames}` }}</p>
          <div class="flex items-center pt-2">
            <CalendarIcon class="mr-2 h-4 w-4 opacity-70" />
            <span class="text-muted-foreground text-xs"> {{ `Creado ${causer.created_at_human}` }} </span>
          </div>
        </div>
      </div>
      <div v-else class="">
        <p class="text-sm">No es posible recuperar completamente los datos del usuario causante del evento. Verifique que:</p>
        <ul class="list-inside list-disc text-sm">
          <li>El usuario esté registrado en el sistema.</li>
          <li>El usuario no esté con estatus "Inactivo".</li>
          <li>El usuario no esté con estatus "Eliminado"</li>
        </ul>
        <br />
        <p class="text-sm">
          <span class="font-mono">{{ causer }}</span> fue quien intentó ejecutar la acción.
        </p>
      </div>
    </CardContent>
  </Card>
  <Card class="mt-3">
    <CardHeader>
      <CardTitle>Navegador</CardTitle>
      <CardDescription>Detalles técnicos del agente de usuario.</CardDescription>
    </CardHeader>
    <CardContent>
      <div class="space-y-1">
        <p class="text-sm leading-none font-medium">
          Plataforma / Sistema Operativo: <span class="text-muted-foreground text-sm">{{ userAgent.details.platform }}</span>
        </p>
      </div>
      <div class="space-y-1">
        <p class="text-sm leading-none font-medium">
          Navegador: <span class="text-muted-foreground text-sm">{{ userAgent.details.browser }}</span>
        </p>
      </div>
      <div class="space-y-1">
        <p class="text-sm leading-none font-medium">
          Idioma: <span class="text-muted-foreground text-sm">{{ userAgent.locale }}</span>
        </p>
      </div>
      <div class="space-y-1">
        <p class="text-sm leading-none font-medium">
          Versión: <span class="text-muted-foreground text-sm">{{ userAgent.details.version }}</span>
        </p>
      </div>
      <div class="space-y-1">
        <p class="text-sm leading-none font-medium">
          Renderizador: <span class="text-muted-foreground text-sm">{{ userAgent.details.renderer }}</span>
        </p>
      </div>
    </CardContent>
  </Card>
</template>
