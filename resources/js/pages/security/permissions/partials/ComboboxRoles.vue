<script setup lang="ts">
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxList } from '@/components/ui/combobox';
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input';
import { Role } from '@/types';
import { useFilter } from 'reka-ui';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
  roles?: Array<Role>;
}>();

const emit = defineEmits(['selected']);

const modelValue = ref<string[]>([]);
const open = ref(false);
const searchTerm = ref('');

const { contains } = useFilter({ sensitivity: 'base' });

const filteredRoles = computed(() => {
  const options = props.roles?.filter((i) => !modelValue.value.includes(i.name));
  return searchTerm.value ? options?.filter((option) => contains(option.name, searchTerm.value)) : options;
});

watch(modelValue, (newModelValue) => emit('selected', newModelValue), { deep: true });
</script>

<template>
  <div class="space-y-1">
    <Combobox v-model="modelValue" v-model:open="open" :ignore-filter="true">
      <ComboboxAnchor as-child>
        <TagsInput id="roles" v-model="modelValue" class="w-full gap-2 px-2">
          <div class="flex flex-wrap items-center gap-2">
            <TagsInputItem v-for="item in modelValue" :key="item" :value="item">
              <TagsInputItemText />
              <TagsInputItemDelete />
            </TagsInputItem>
          </div>

          <ComboboxInput v-model="searchTerm" as-child>
            <TagsInputInput
              :auto-focus="true"
              placeholder="Roles..."
              class="h-auto w-full min-w-[200px] border-none p-0 focus-visible:ring-0"
              @keydown.enter.prevent
            />
          </ComboboxInput>
        </TagsInput>

        <ComboboxList class="w-[--reka-popper-anchor-width]">
          <ComboboxEmpty>No hay más registros</ComboboxEmpty>
          <ComboboxGroup>
            <ComboboxItem
              v-for="role in filteredRoles"
              :key="role.id"
              :value="role.name"
              @select.prevent="
                (ev) => {
                  if (typeof ev.detail.value === 'string') {
                    searchTerm = '';
                    modelValue.push(ev.detail.value);
                  }

                  if (filteredRoles && filteredRoles.length === 0) {
                    open = false;
                  }
                }
              "
            >
              {{ role.name }}
            </ComboboxItem>
          </ComboboxGroup>
        </ComboboxList>
      </ComboboxAnchor>
    </Combobox>
  </div>
</template>
