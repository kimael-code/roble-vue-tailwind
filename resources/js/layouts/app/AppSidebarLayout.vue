<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType, SharedData } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { watchImmediate } from '@vueuse/core';
import { toast } from 'vue-sonner';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const flashMessage = usePage<SharedData>().props.flash.message;

watchImmediate(
  () => flashMessage,
  () => {
    if (!flashMessage) return;

    switch (flashMessage.type) {
      case 'success':
        toast.success(flashMessage.title, {
          description: flashMessage.message,
          onAutoClose: () => router.reload({ only: ['flash'] }),
          onDismiss: () => router.reload({ only: ['flash'] }),
        });
        break;
      case 'info':
        toast.info(flashMessage.title, {
          description: flashMessage.message,
          onAutoClose: () => router.reload({ only: ['flash'] }),
          onDismiss: () => router.reload({ only: ['flash'] }),
        });
        break;
      case 'warning':
        toast.warning(flashMessage.title, {
          description: flashMessage.message,
          onAutoClose: () => router.reload({ only: ['flash'] }),
          onDismiss: () => router.reload({ only: ['flash'] }),
        });
        break;
      case 'danger':
        toast.error(flashMessage.title, {
          description: flashMessage.message,
          onAutoClose: () => router.reload({ only: ['flash'] }),
          onDismiss: () => router.reload({ only: ['flash'] }),
        });
        break;

      default:
        toast(flashMessage.title, {
          description: flashMessage.message,
          onAutoClose: () => router.reload({ only: ['flash'] }),
          onDismiss: () => router.reload({ only: ['flash'] }),
        });
        break;
    }
  },
);
</script>

<template>
  <AppShell variant="sidebar">
    <AppSidebar />
    <AppContent variant="sidebar">
      <AppSidebarHeader :breadcrumbs="breadcrumbs" />
      <slot />
    </AppContent>
  </AppShell>
</template>
