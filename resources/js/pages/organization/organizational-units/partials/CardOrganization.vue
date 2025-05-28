<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Organization, OrganizationalUnit } from '@/types';

defineProps<{
  organization: Organization;
  ou?: OrganizationalUnit;
}>();
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Origen</CardTitle>
      <CardDescription>Ente y Unidad de Adscripción de la Unidad Administrativa.</CardDescription>
    </CardHeader>
    <CardContent>
      <div class="space-y-1">
        <p class="text-sm leading-none font-medium">
          Ente: <span class="text-muted-foreground text-sm">{{ organization.name }}</span>
        </p>
      </div>
      <div class="space-y-1">
        <p class="text-sm leading-none font-medium">
          Unidad de Adscripción: <span class="text-muted-foreground text-sm">{{ ou?.name ?? 'N/A' }}</span>
        </p>
      </div>
    </CardContent>
  </Card>
</template>
