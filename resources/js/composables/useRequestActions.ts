import { OperationType } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

export function useRequestActions(resourceName: string) {
  /**
   * Opciones disponibles para la petición.
   * @see https://inertiajs.com/manual-visits
   */
  interface RequestOptions {
    data?: { [index: string]: any };
    replace?: boolean;
    preserveState?: boolean;
    preserveScroll?: boolean;
    only?: Array<any>;
    except?: Array<any>;
    headers?: { [index: string]: any };
    errorBag?: null;
    forceFormData?: boolean;
    queryStringArrayFormat?: 'brackets';
    async?: boolean;
    showProgress?: true;
    fresh?: boolean;
    reset?: Array<any>;
    preserveUrl?: boolean;
    prefetch?: boolean;
  }

  const action = ref<OperationType>(null);
  const resourceID = ref<number | string | null>(null);
  const request = useForm({});

  const requestingCreate = ref(false);
  const requestingRead = ref(false);
  const requestingEdit = ref(false);
  const requestingDestroy = ref(false);
  const requestingForceDestroy = ref(false);
  const requestingRestore = ref(false);

  function requestAction(id: number | string, options?: RequestOptions) {
    resourceID.value = id;

    switch (action.value) {
      case 'destroy':
        requestDestroy(id, options);
        break;
      case 'force_destroy':
        requestForceDestroy(id, options);
        break;
      case 'restore':
        requestRestore(id, options);
        break;

      default:
        console.log('action: ', action.value);
        break;
    }
  }

  function requestCreate(options?: RequestOptions) {
    requestingCreate.value = false;

    request.get(route(`${resourceName}.create`), {
      ...options,
      onStart: () => (requestingCreate.value = true),
      onFinish: () => {
        requestingCreate.value = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestRead(id: number | string, options?: RequestOptions) {
    requestingRead.value = false;
    resourceID.value = id;

    request.get(route(`${resourceName}.show`, id), {
      ...options,
      onStart: () => (requestingRead.value = true),
      onFinish: () => {
        requestingRead.value = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestEdit(id: number | string, options?: RequestOptions) {
    requestingEdit.value = false;
    resourceID.value = id;

    request.get(route(`${resourceName}.edit`, id), {
      ...options,
      onStart: () => (requestingEdit.value = true),
      onFinish: () => {
        requestingEdit.value = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestDestroy(id: number | string, options?: RequestOptions) {
    requestingDestroy.value = false;
    resourceID.value = id;

    request.delete(route(`${resourceName}.destroy`, id), {
      ...options,
      onStart: () => (requestingDestroy.value = true),
      onFinish: () => {
        requestingDestroy.value = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestForceDestroy(id: number | string, options?: RequestOptions) {
    requestingForceDestroy.value = false;
    resourceID.value = id;

    request.delete(route(`${resourceName}.destroy`, id), {
      ...options,
      onStart: () => (requestingForceDestroy.value = true),
      onFinish: () => {
        requestingForceDestroy.value = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestRestore(id: number | string, options?: RequestOptions) {
    requestingRestore.value = false;
    resourceID.value = id;

    request.put(route(`${resourceName}.restore`, id), {
      ...options,
      onStart: () => (requestingRestore.value = true),
      onFinish: () => {
        requestingRestore.value = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  return {
    action,
    request,
    resourceID,
    requestAction,
    requestCreate,
    requestRead,
    requestEdit,
    requestDestroy,
    requestForceDestroy,
    requestRestore,
    requestingCreate,
    requestingRead,
    requestingEdit,
    requestingDestroy,
    requestingForceDestroy,
    requestingRestore,
  };
}
