<script setup lang="ts">
import { useFilter } from 'reka-ui';
import { computed, ref } from 'vue';

const props = defineProps<{
  show: boolean;
}>();

const showAdvancedFilters = ref(props.show);

const frameworks = [
  { value: 'next.js', label: 'Next.js' },
  { value: 'sveltekit', label: 'SvelteKit' },
  { value: 'nuxt', label: 'Nuxt' },
  { value: 'remix', label: 'Remix' },
  { value: 'astro', label: 'Astro' },
]

const modelValue = ref<string[]>([])
const open = ref(false)
const searchTerm = ref('')

const { contains } = useFilter({ sensitivity: 'base' })
const filteredFrameworks = computed(() => {
  const options = frameworks.filter(i => !modelValue.value.includes(i.label))
  return searchTerm.value ? options.filter(option => contains(option.label, searchTerm.value)) : options
})
</script>

<template>
  <Sheet v-model:open="showAdvancedFilters">
    <SheetContent side="right">
      <SheetHeader>
        <SheetTitle>Filtros de Búsqueda Avanzados</SheetTitle>
        <SheetDescription>Haga uso de los siguientes controles para aplicarlos como filtros.</SheetDescription>
      </SheetHeader>
      <div class="grid gap-4 py-4">
        <Combobox v-model="modelValue" v-model:open="open" :ignore-filter="true">
          <ComboboxAnchor as-child>
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
      <SheetFooter>
        <SheetClose as-child>
          <Button type="submit"> Save changes </Button>
        </SheetClose>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
