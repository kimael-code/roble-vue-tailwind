import { OperationType } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

/**
 * A Vue composable for handling common CRUD operations with Inertia.js
 *
 * @param {string} resourceName - The base name of the resource (used for route generation)
 * @returns {Object} An object containing request methods and state
 */
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

  interface ActionData {
    [index: string]: any;
  }

  interface RequestActionParams {
    operation?: OperationType;
    data?: ActionData;
    options?: RequestOptions;
  }

  const action = ref<OperationType>(null);
  const resourceID = ref<number | string | null>(null);
  const request = useForm({});
  const requestState = ref({
    create: false,
    read: false,
    readAll: false,
    edit: false,
    destroy: false,
    forceDestroy: false,
    restore: false,
    enable: false,
    disable: false,
    batchActivate: false,
    batchDeactivate: false,
    batchDestroy: false,
  });

  function requestAction({ operation, data, options }: RequestActionParams) {
    resourceID.value = data?.id;

    if (operation) action.value = operation;

    switch (action.value) {
      case 'create':
        requestCreate(options);
        break;
      case 'read':
        requestRead(data?.id, options);
        break;
      case 'read_all':
        requestReadAll(options);
        break;
      case 'edit':
        requestEdit(data?.id, options);
        break;
      case 'destroy':
        requestDestroy(data?.id, options);
        break;
      case 'force_destroy':
        requestForceDestroy(data?.id, options);
        break;
      case 'restore':
        requestRestore(data?.id, options);
        break;
      case 'enable':
        requestEnable(data?.id, options);
        break;
      case 'disable':
        requestDisable(data?.id, options);
        break;
      case 'batch_activate':
        requestBatchActivate((data as { [x: string]: boolean }) ?? {}, options);
        break;
      case 'batch_deactivate':
        requestBatchDeactivate((data as { [x: string]: boolean }) ?? {}, options);
        break;
      case 'batch_destroy':
        requestBatchDestroy((data as { [x: string]: boolean }) ?? {}, options);
        break;

      default:
        console.log('action: ', action.value);
        break;
    }
  }

  function requestCreate(options?: RequestOptions) {
    requestState.value.create = false;

    request.get(route(`${resourceName}.create`), {
      ...options,
      onStart: () => (requestState.value.create = true),
      onFinish: () => {
        requestState.value.create = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestRead(id: number | string, options?: RequestOptions) {
    requestState.value.read = false;
    resourceID.value = id;

    request.get(route(`${resourceName}.show`, id), {
      ...options,
      onStart: () => (requestState.value.read = true),
      onFinish: () => {
        requestState.value.read = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestReadAll(options?: RequestOptions) {
    requestState.value.readAll = false;
    resourceID.value = null;

    request.get(route(`${resourceName}.index`), {
      ...options,
      onStart: () => (requestState.value.readAll = true),
      onFinish: () => {
        requestState.value.readAll = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestEdit(id: number | string, options?: RequestOptions) {
    requestState.value.edit = false;
    resourceID.value = id;

    request.get(route(`${resourceName}.edit`, id), {
      ...options,
      onStart: () => (requestState.value.edit = true),
      onFinish: () => {
        requestState.value.edit = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestDestroy(id: number | string, options?: RequestOptions) {
    requestState.value.destroy = false;
    resourceID.value = id;

    request.delete(route(`${resourceName}.destroy`, id), {
      ...options,
      onStart: () => (requestState.value.destroy = true),
      onFinish: () => {
        requestState.value.destroy = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestForceDestroy(id: number | string, options?: RequestOptions) {
    requestState.value.forceDestroy = false;
    resourceID.value = id;

    request.delete(route(`${resourceName}.force-destroy`, id), {
      ...options,
      onStart: () => (requestState.value.forceDestroy = true),
      onFinish: () => {
        requestState.value.forceDestroy = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestBatchActivate(selectedRows: { [x: string]: boolean }, options?: RequestOptions) {
    requestState.value.batchActivate = false;
    resourceID.value = null;

    request
      .transform((data) => ({ ...data, ...selectedRows }))
      .post(route('batch-activation', { resource: resourceName }), {
        ...options,
        onStart: () => (requestState.value.batchActivate = true),
        onFinish: () => {
          requestState.value.batchActivate = false;
          action.value = null;
          resourceID.value = null;
        },
      });
  }

  function requestBatchDeactivate(selectedRows: { [x: string]: boolean }, options?: RequestOptions) {
    requestState.value.batchDeactivate = false;
    resourceID.value = null;

    request
      .transform((data) => ({ ...data, ...selectedRows }))
      .post(route('batch-deactivation', { resource: resourceName }), {
        ...options,
        onStart: () => (requestState.value.batchDeactivate = true),
        onFinish: () => {
          requestState.value.batchDeactivate = false;
          action.value = null;
          resourceID.value = null;
        },
      });
  }

  function requestBatchDestroy(selectedRows: { [x: string]: boolean }, options?: RequestOptions) {
    requestState.value.batchDestroy = false;
    resourceID.value = null;

    request
      .transform((data) => ({ ...data, ...selectedRows }))
      .post(route('batch-deletion', { resource: resourceName }), {
        ...options,
        onStart: () => (requestState.value.batchDestroy = true),
        onFinish: () => {
          requestState.value.batchDestroy = false;
          action.value = null;
          resourceID.value = null;
        },
      });
  }

  function requestRestore(id: number | string, options?: RequestOptions) {
    requestState.value.restore = false;
    resourceID.value = id;

    request.put(route(`${resourceName}.restore`, id), {
      ...options,
      onStart: () => (requestState.value.restore = true),
      onFinish: () => {
        requestState.value.restore = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestEnable(id: number | string, options?: RequestOptions) {
    requestState.value.enable = false;
    resourceID.value = id;

    request.put(route(`${resourceName}.enable`, id), {
      ...options,
      onStart: () => (requestState.value.enable = true),
      onFinish: () => {
        requestState.value.enable = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  function requestDisable(id: number | string, options?: RequestOptions) {
    requestState.value.disable = false;
    resourceID.value = id;

    request.put(route(`${resourceName}.disable`, id), {
      ...options,
      onStart: () => (requestState.value.disable = true),
      onFinish: () => {
        requestState.value.disable = false;
        action.value = null;
        resourceID.value = null;
      },
    });
  }

  return {
    action,
    request,
    resourceID,
    requestState,
    requestAction,
    // requestCreate,
    // requestRead,
    // requestReadAll,
    // requestEdit,
    // requestDestroy,
    // requestForceDestroy,
    // requestBatchDestroy,
    // requestRestore,
    // requestEnable,
    // requestDisable,
  };
}
