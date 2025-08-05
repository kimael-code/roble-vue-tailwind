<script setup lang="ts">
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxList } from '@/components/ui/combobox';
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input';
import { useFilter } from 'reka-ui';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
  statuses?: Array<{ [key: string]: string }>;
}>();

const emit = defineEmits(['selected']);

const modelValue = ref<string[]>([]);
const open = ref(false);
const searchTerm = ref('');

const { contains } = useFilter({ sensitivity: 'base' });

const filteredStatus = computed(() => {
  const options = props.statuses?.filter((i) => !modelValue.value.includes(i.value));
  return searchTerm.value ? options?.filter((option) => contains(option.value, searchTerm.value)) : options;
});

watch(modelValue, (newModelValue) => emit('selected', newModelValue), { deep: true });
</script>

<template>
  <div class="space-y-1">
    <Combobox v-model="modelValue" v-model:open="open" :ignore-filter="true">
      <ComboboxAnchor as-child>
        <TagsInput id="users" v-model="modelValue" class="w-full gap-2 px-2">
          <div class="flex flex-wrap items-center gap-2">
            <TagsInputItem v-for="item in modelValue" :key="item" :value="item">
              <TagsInputItemText />
              <TagsInputItemDelete />
            </TagsInputItem>
          </div>

          <ComboboxInput v-model="searchTerm" as-child>
            <TagsInputInput
              :auto-focus="true"
              placeholder="Estatus..."
              class="h-auto w-full min-w-[200px] border-none p-0 focus-visible:ring-0"
              @keydown.enter.prevent
            />
          </ComboboxInput>
        </TagsInput>

        <ComboboxList class="w-[--reka-popper-anchor-width]">
          <ComboboxEmpty>No hay más registros</ComboboxEmpty>
          <ComboboxGroup>
            <ComboboxItem
              v-for="status in filteredStatus"
              :key="status.value"
              :value="status.value"
              @select.prevent="
                (ev) => {
                  if (typeof ev.detail.value === 'string') {
                    searchTerm = '';
                    modelValue.push(ev.detail.value);
                  }

                  if (filteredStatus && filteredStatus.length === 0) {
                    open = false;
                  }
                }
              "
            >
              {{ status.name }}
            </ComboboxItem>
          </ComboboxGroup>
        </ComboboxList>
      </ComboboxAnchor>
    </Combobox>
  </div>
</template>
