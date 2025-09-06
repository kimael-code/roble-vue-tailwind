import { router, usePage } from '@inertiajs/vue3';
import type { ComponentPublicInstance } from 'vue';
import { onMounted, ref, watch } from 'vue';

export function useRememberScroll(key: string) {
  const scrollable = ref<ComponentPublicInstance | HTMLElement | null>(null);

  const pageUrl = () => usePage().url;

  // Helper to get the actual scrollable HTML element
  const getElement = (): HTMLElement | null => {
    if (!scrollable.value) return null;
    // Check if the ref is a component instance (which has .$el) or a direct element
    return (scrollable.value as ComponentPublicInstance).$el || (scrollable.value as HTMLElement);
  };

  const saveScrollPosition = () => {
    const element = getElement();
    if (element) {
      sessionStorage.setItem(key, element.scrollTop.toString());
    }
  };

  const restoreScrollPosition = () => {
    const element = getElement();
    const savedPosition = sessionStorage.getItem(key);
    if (element && savedPosition) {
      element.scrollTop = parseInt(savedPosition, 10);
    }
  };

  // 1. Restore on initial mount
  onMounted(restoreScrollPosition);

  // 2. Save position before navigating
  router.on('before', saveScrollPosition);

  // 3. Watch for URL changes and restore after DOM updates
  watch(pageUrl, restoreScrollPosition, { flush: 'post' });

  // 4. The @scroll handler from your component will now work correctly
  return {
    scrollable,
    handleScroll: saveScrollPosition,
  };
}
