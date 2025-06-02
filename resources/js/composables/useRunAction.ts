import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

export function useRunAction(resource: string) {
  const action = ref('');
  const subject = ref<{ [index: string]: any }>({});
  const request = useForm({});

  function runAction(id: number | string) {
    switch (action.value) {
      case 'delete':
        request.delete(route(`${resource}.destroy`, id), {
          preserveState: false,
          onSuccess: () => {
            action.value = '';
          },
        });
        break;
      case 'restore':
        request.put(route(`${resource}.restore`, id), {
          preserveState: false,
          onSuccess: () => {
            action.value = '';
          },
        });
        break;
      case 'f_delete':
        request.delete(route(`${resource}.destroy`, id), {
          preserveState: false,
          onSuccess: () => {
            action.value = '';
          },
        });
        break;

      default:
        break;
    }
  }

  return {
    action,
    subject,
    request,
    runAction,
  };
}
