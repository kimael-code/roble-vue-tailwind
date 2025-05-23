<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ActivityLog } from '@/types';
import { computed } from 'vue';

interface GenericModel {
  [index: string]: string | number | boolean | null;
}

interface ComparisonResult {
  key: string;
  value1: string | number | boolean | null;
  value2: string | number | boolean | null;
  areEqual: boolean;
}

const props = defineProps<{
  log: ActivityLog;
}>();

const cardTitle = computed(() => {
  switch (props.log.event) {
    case 'auth':
      return 'Autenticación';
    case 'created':
      return 'CREATED';
    case 'updated':
      return 'UPDATED';
    case 'deleted':
      return 'DELETED';

    default:
      return 'Evento';
  }
});

const cardDescription = computed(() => {
  switch (props.log.event) {
    case 'auth':
      return 'Este evento no posee más detalles de los ya mostrados en la tarjeta "Detalles de la Petición".';
    case 'created':
      return props.log.subject_type;
    case 'updated':
      return props.log.subject_type;
    case 'deleted':
      return props.log.subject_type;

    default:
      return 'Detalle del evento.';
  }
});

function compareObjects(obj1: GenericModel, obj2: GenericModel) {
  const comparisonResults: Array<ComparisonResult> = [];
  const allKeys = new Set([...Object.keys(obj1), ...Object.keys(obj2)]);

  allKeys.forEach((key) => {
    const value1 = obj1[key];
    const value2 = obj2[key];

    let areEqual = true;
    let displayValue1 = value1;
    let displayValue2 = value2;

    // Manejo de tipos de datos complejos (objetos y arrays)
    if (typeof value1 === 'object' && value1 !== null && typeof value2 === 'object' && value2 !== null) {
      if (Array.isArray(value1) && Array.isArray(value2)) {
        // Comparar arrays (orden no importa si solo comparamos elementos)
        if (value1.length !== value2.length || !value1.every((item) => value2.includes(item)) || !value2.every((item) => value1.includes(item))) {
          areEqual = false;
        }
        displayValue1 = JSON.stringify(value1);
        displayValue2 = JSON.stringify(value2);
      } else {
        // Comparar objetos anidados (superficialmente, puedes extender esto para una comparación profunda)
        if (JSON.stringify(value1) !== JSON.stringify(value2)) {
          areEqual = false;
        }
        displayValue1 = JSON.stringify(value1);
        displayValue2 = JSON.stringify(value2);
      }
    } else if (value1 !== value2) {
      areEqual = false;
    }

    comparisonResults.push({
      key: key,
      value1: displayValue1,
      value2: displayValue2,
      areEqual: areEqual,
    });
  });

  return comparisonResults;
}
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>{{ cardTitle }}</CardTitle>
      <CardDescription>{{ cardDescription }}</CardDescription>
    </CardHeader>
    <CardContent v-if="log.event === 'created'">
      <div class="grid w-full items-center gap-4">
        <div class="space-y-1">
          <p class="text-sm leading-none font-medium">Propiedades del Registro:</p>
          <pre class="text-muted-foreground text-pretty">{{ log.properties.attributes }}</pre>
        </div>
      </div>
    </CardContent>
    <CardContent v-if="log.event === 'deleted'">
      <div class="grid w-full items-center gap-4">
        <div class="space-y-1">
          <p class="text-sm leading-none font-medium">Propiedades del Registro:</p>
          <pre class="text-muted-foreground text-pretty">{{ log.properties.old }}</pre>
        </div>
      </div>
    </CardContent>
    <CardContent v-if="log.event === 'updated'">
      <div class="grid grid-cols-3 gap-x-4 gap-y-2 border-b border-gray-200 p-1 text-sm font-semibold">
        <div>Propiedad</div>
        <div>Valor Anterior</div>
        <div>Valor Actual</div>
      </div>
      <template v-if="log.properties.attributes && log.properties.old">
        <div
          v-for="(item, i) in compareObjects(log.properties.old, log.properties.attributes)"
          class="grid grid-cols-3 gap-x-4 gap-y-2 border-b border-gray-100 p-1"
          :key="i"
        >
          <div class="text-muted-foreground p-1 font-mono text-xs">{{ item.key }}</div>
          <div class="text-muted-foreground p-1 font-mono text-xs" :class="{ 'bg-red-100 font-medium text-red-700': !item.areEqual }">
            {{ item.value1 !== undefined ? item.value1 : 'N/A' }}
          </div>
          <div
            class="p-1 font-mono text-xs"
            :class="{ 'bg-green-100 font-medium text-green-700': !item.areEqual, 'text-muted-foreground': item.areEqual }"
          >
            {{ item.value2 !== undefined ? item.value2 : 'N/A' }}
          </div>
        </div>
      </template>
    </CardContent>
  </Card>
</template>
