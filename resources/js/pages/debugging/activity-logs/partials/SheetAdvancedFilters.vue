<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxList } from '@/components/ui/combobox';
import { Label } from '@/components/ui/label';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input';
import { useFilter } from 'reka-ui';
import { computed, ref } from 'vue';

defineProps<{
  show: boolean;
}>();
defineEmits(['close']);

const frameworks = [
  { value: 'next.js', label: 'Next.js' },
  { value: 'sveltekit', label: 'SvelteKit' },
  { value: 'nuxt', label: 'Nuxt' },
  { value: 'remix', label: 'Remix' },
  { value: 'astro', label: 'Astro' },
];

const modelValue = ref<string[]>([]);
const open = ref(false);
const searchTerm = ref('');

const { contains } = useFilter({ sensitivity: 'base' });
const filteredFrameworks = computed(() => {
  const options = frameworks.filter((i) => !modelValue.value.includes(i.label));
  return searchTerm.value ? options.filter((option) => contains(option.label, searchTerm.value)) : options;
});
</script>

<template>
  <div class="grid grid-cols-2 gap-2">
    <Sheet :open="show">
      <SheetContent side="right">
        <SheetHeader>
          <SheetTitle>Filtros de Búsqueda Avanzados</SheetTitle>
          <SheetDescription>Haga uso de los siguientes controles para aplicarlos como filtros.</SheetDescription>
        </SheetHeader>
        <!-- <form class="ml-4 flex flex-col gap-4"> -->
          <div class="grid gap-4 py-4">
            <div class="grid items-center grid-cols-4 gap-4">
              <Label for="name" class="text-right">Usuarios</Label>
              <Combobox v-model="modelValue" v-model:open="open" :ignore-filter="true">
                <ComboboxAnchor as-child>
                  <!-- <div class="relative w-full items-center"> -->
                  <TagsInput v-model="modelValue" class="w-80 gap-2 px-2">
                    <div class="flex flex-wrap items-center gap-2">
                      <TagsInputItem v-for="item in modelValue" :key="item" :value="item">
                        <TagsInputItemText />
                        <TagsInputItemDelete />
                      </TagsInputItem>
                    </div>

                    <ComboboxInput v-model="searchTerm" as-child>
                      <TagsInputInput
                        placeholder="Framework..."
                        class="h-auto w-full min-w-[200px] border-none p-0 focus-visible:ring-0"
                        @keydown.enter.prevent
                      />
                    </ComboboxInput>
                  </TagsInput>
                  <!-- </div> -->

                  <ComboboxList class="w-[--reka-popper-anchor-width]">
                    <ComboboxEmpty />
                    <ComboboxGroup>
                      <ComboboxItem
                        v-for="framework in filteredFrameworks"
                        :key="framework.value"
                        :value="framework.label"
                        @select.prevent="
                          (ev) => {
                            if (typeof ev.detail.value === 'string') {
                              searchTerm = '';
                              modelValue.push(ev.detail.value);
                            }

                            if (filteredFrameworks.length === 0) {
                              open = false;
                            }
                          }
                        "
                      >
                        {{ framework.label }}
                      </ComboboxItem>
                    </ComboboxGroup>
                  </ComboboxList>
                </ComboboxAnchor>
              </Combobox>
            </div>
          </div>
        <!-- </form> -->
        <SheetFooter>
          <SheetClose as-child>
            <Button type="submit" @click="$emit('close')"> Save changes </Button>
          </SheetClose>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  </div>
</template>
