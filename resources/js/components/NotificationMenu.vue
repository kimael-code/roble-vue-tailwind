<script setup lang="ts">
import { getInitials } from '@/composables/useInitials';
import { SharedData } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { useEchoModel } from '@laravel/echo-vue';
import { BellRingIcon, HourglassIcon } from 'lucide-vue-next';
import { DateTime } from 'luxon';
import { computed, ref } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from './ui/avatar';
import { Button } from './ui/button';
import { Popover, PopoverContent, PopoverTrigger } from './ui/popover';
import { ScrollArea } from './ui/scroll-area';

const page = ref(usePage<SharedData>());
const unreadNotifications = computed(() => page.value.props.unreadNotifications);
const unreadCount = computed(() => page.value.props.unreadNotifications.length);
const { channel } = useEchoModel('App.Models.User', page.value.props.auth.user.id);

function markAsRead(notificationID: string) {
  router.visit(route('notifications.mark-as-read', notificationID), {
    method: 'put',
  });
}

channel().notification(() =>
  router.reload({
    only: ['unreadNotifications'],
  }),
);
</script>

<template>
  <div class="mr-8 ml-auto">
    <Popover>
      <PopoverTrigger as-child>
        <Button v-show="unreadCount" size="icon"> <BellRingIcon /> </Button>
      </PopoverTrigger>
      <PopoverContent class="w-96">
        <div class="grid gap-4">
          <div class="space-y-2">
            <h4 class="leading-none font-medium">Notificaciones</h4>
            <ScrollArea>
              <div
                v-for="(notification, i) in unreadNotifications"
                class="mb-2 flex cursor-pointer justify-normal space-x-4 rounded-md transition-colors duration-200 hover:bg-gray-100"
                :key="i"
                @click="markAsRead(notification.id)"
              >
                <Avatar>
                  <AvatarImage v-if="notification.data.photoUrl" :src="notification.data.photoUrl" />
                  <AvatarFallback>{{ getInitials(notification.data.causer) }}</AvatarFallback>
                </Avatar>
                <div class="space-y-1">
                  <h4 class="text-sm font-semibold">{{ notification.data?.causer }}</h4>
                  <p class="text-sm">{{ notification.data?.message }}</p>
                  <div class="flex items-center pt-2">
                    <HourglassIcon class="mr-2 h-4 w-4 opacity-70" />
                    <span class="text-muted-foreground text-xs"> {{ DateTime.fromISO(notification.data?.timestamp).toRelative() }} </span>
                  </div>
                </div>
              </div>
            </ScrollArea>
          </div>
        </div>
      </PopoverContent>
    </Popover>
  </div>
</template>
