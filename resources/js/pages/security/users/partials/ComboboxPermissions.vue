<script setup lang="ts">
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxList } from '@/components/ui/combobox';
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input';
import { Permission } from '@/types';
import { router } from '@inertiajs/vue3';
import { useFilter } from 'reka-ui';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
  permissions?: Array<Permission>;
}>();

const emit = defineEmits(['selected']);

const modelValue = ref<string[]>([]);
const open = ref(false);
const searchTerm = ref('');

const { contains } = useFilter({ sensitivity: 'base' });

const filteredPermissions = computed(() => {
  const options = props.permissions?.filter((o) => !modelValue.value.includes(o.description));
  return searchTerm.value ? options?.filter((option) => contains(option.description, searchTerm.value)) : options;
});

function loadPermissions(search: string) {
  router.reload({
    only: ['permissions'],
    data: { search },
    onSuccess: () => {
      open.value = true;
    },
  });
}

watch(modelValue, (newModelValue) => emit('selected', newModelValue), { deep: true });

watch(searchTerm, (newSearchTerm) => {
  if (newSearchTerm.length > 0) {
    loadPermissions(newSearchTerm);
  }
});
</script>

<template>
  <div class="space-y-1">
    <Combobox v-model="modelValue" v-model:open="open" :ignore-filter="true">
      <ComboboxAnchor as-child>
        <TagsInput id="permissions" v-model="modelValue" class="w-full gap-2 px-2">
          <div class="flex flex-wrap items-center gap-2">
            <TagsInputItem v-for="item in modelValue" :key="item" :value="item">
              <TagsInputItemText />
              <TagsInputItemDelete />
            </TagsInputItem>
          </div>

          <ComboboxInput v-model="searchTerm" as-child>
            <TagsInputInput
              :auto-focus="true"
              placeholder="Permisos..."
              class="h-auto w-full min-w-[200px] border-none p-0 focus-visible:ring-0"
              @keydown.enter.prevent
            />
          </ComboboxInput>
        </TagsInput>

        <ComboboxList class="w-[--reka-popper-anchor-width]">
          <ComboboxEmpty>No hay más registros</ComboboxEmpty>
          <ComboboxGroup>
            <ComboboxItem
              v-for="permission in filteredPermissions"
              :key="permission.id"
              :value="permission.name"
              @select.prevent="
                (ev) => {
                  if (typeof ev.detail.value === 'string') {
                    searchTerm = '';
                    modelValue.push(ev.detail.value);
                  }

                  if (filteredPermissions && filteredPermissions.length === 0) {
                    open = false;
                  }
                }
              "
            >
              {{ permission.description }}
            </ComboboxItem>
          </ComboboxGroup>
        </ComboboxList>
      </ComboboxAnchor>
    </Combobox>
  </div>
</template>
