import { Ref, ref } from 'vue';

export function useConfirmAction() {
  const alertOpen = ref(false);
  const alertAction = ref('Continuar');
  const alertActionCss = ref('');
  const alertTitle = ref('¿Está seguro?');
  const alertDescription = ref('Si realmente está seguro haga clic en el botón "Continuar"');
  const alertData: Ref<Record<string, any>> = ref({});

  return { alertOpen, alertAction, alertActionCss, alertTitle, alertDescription, alertData };
}
