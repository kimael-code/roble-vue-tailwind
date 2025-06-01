<script setup lang="ts">
defineProps<{
  title: string;
  description?: string;
}>();
</script>

<template>
  <div class="duration-750 starting:opacity-0 px-4 py-6 opacity-100 transition-opacity">
    <div class="mb-8 space-y-0.5">
      <div class="flex items-start space-x-2">
        <slot v-if="$slots.icon" name="icon"></slot>
        <h2 class="text-xl font-semibold tracking-tight">{{ title }}</h2>
      </div>
      <p v-if="description" class="text-sm text-muted-foreground">
        {{ description }}
      </p>
    </div>

    <slot />
  </div>
</template>
