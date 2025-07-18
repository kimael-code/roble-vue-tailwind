<script setup lang="ts">
import CalendarMonthYear from '@/components/CalendarMonthYear.vue';
import DateRangePickerIndependentMonths from '@/components/DateRangePickerIndependentMonths.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { DateFormatter, getLocalTimeZone } from '@internationalized/date';
import { CalendarIcon, DeleteIcon } from 'lucide-vue-next';
import { DateRange, DateValue } from 'reka-ui';
import { Ref, ref, watch } from 'vue';

const emit = defineEmits(['dateSet', 'dateRangeSet', 'timeSet', 'timeFromSet', 'timeUntilSet']);

const df = new DateFormatter('es-VE', {
  dateStyle: 'long',
});
const value = ref<DateValue>();

const dateRangeValue = ref({
  start: undefined,
  end: undefined,
}) as Ref<DateRange>;

const timeSpecific = ref('');
const timeFrom = ref('');
const timeUntil = ref('');

const btnDeleteA = ref(false);

function handleBtnDeleteA(isOpen: boolean) {
  btnDeleteA.value = value.value && !isOpen ? true : false;
}

function deleteDate() {
  value.value = undefined;
  btnDeleteA.value = false;
}

function deleteDateRange() {
  dateRangeValue.value = { start: undefined, end: undefined };
}
watch(value, (newValue) => emit('dateSet', newValue), { deep: true });
watch(dateRangeValue, (newDateRangeValue) => emit('dateRangeSet', newDateRangeValue), { deep: true });
watch(timeSpecific, (newTimeValue) => emit('timeSet', newTimeValue));
watch(timeFrom, (newTimeFromValue) => emit('timeFromSet', newTimeFromValue));
watch(timeUntil, (newTimeUntilValue) => emit('timeUntilSet', newTimeUntilValue));
</script>

<template>
  <form class="flex flex-col gap-6">
    <div class="grid grid-cols-3 gap-6">
      <div class="grid gap-2">
        <Label for="date_single">Fecha Específica</Label>
        <div class="flex items-center gap-2">
          <Popover @update:open="(val) => handleBtnDeleteA(val)">
            <PopoverTrigger as-child>
              <Button
                id="date_single"
                variant="outline"
                :class="cn('w-[280px] justify-start text-left font-normal', !value && 'text-muted-foreground')"
              >
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ value ? df.format(value.toDate(getLocalTimeZone())) : 'Escoja una fecha' }}
              </Button>
            </PopoverTrigger>
            <PopoverContent class="flex w-auto flex-col gap-y-2 p-2">
              <CalendarMonthYear v-model="value" />
            </PopoverContent>
          </Popover>
          <Button v-if="btnDeleteA" type="button" variant="ghost" size="icon" @click="deleteDate">
            <DeleteIcon class="h-4 w-4" />
          </Button>
        </div>
      </div>

      <div class="grid gap-2">
        <Label for="date_range">Rango de Fechas</Label>
        <div class="flex items-center gap-2">
          <DateRangePickerIndependentMonths v-model="dateRangeValue" />
          <Button v-if="dateRangeValue.start" type="button" variant="ghost" size="icon" @click="deleteDateRange">
            <DeleteIcon class="h-4 w-4" />
          </Button>
        </div>
      </div>

      <div class="grid gap-2">
        <Label for="time_specific">Hora Específica</Label>
        <Input
          type="time"
          id="time_specific"
          v-model="timeSpecific"
          placeholder="p. ej.: 08:25"
          class="w-[280px] appearance-none bg-background [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none"
        />
      </div>
      <div class="grid gap-2">
        <Label for="time_from">Hora Desde</Label>
        <Input
          type="time"
          id="time_from"
          v-model="timeFrom"
          placeholder="p. ej.: 14:00"
          class="w-[280px] appearance-none bg-background [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none"
        />
      </div>
      <div class="grid gap-2">
        <Label for="time_until">Hora Hasta</Label>
        <Input
          type="time"
          id="time_until"
          v-model="timeUntil"
          placeholder="p. ej.: 16:25"
          class="w-[280px] appearance-none bg-background [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none"
        />
      </div>
    </div>
  </form>
</template>
