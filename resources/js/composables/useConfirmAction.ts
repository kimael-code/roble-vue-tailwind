import { Ref, ref, toValue } from 'vue';

export function useConfirmAction() {
  const openDialog = ref(false);
  const dataRow: Ref<{ [index: string]: any }> = ref({});

  function confirmAction(withData: Ref<{ [index: string]: any }> | { [index: string]: any }) {
    openDialog.value = true;
    dataRow.value = toValue(withData);
  }

  return { confirmAction, dataRow, openDialog };
}
