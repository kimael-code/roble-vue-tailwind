<script setup lang="ts">
defineProps<{
  title: string;
  description?: string;
}>();
</script>

<template>
  <div class="px-4 py-6">
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
