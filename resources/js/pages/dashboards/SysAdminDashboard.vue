<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge, type BadgeVariants } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { HoverCard, HoverCardContent, HoverCardTrigger } from '@/components/ui/hover-card';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { useChartOptionsRoles, useChartOptionsUsers } from '@/composables/useChartOptions';
import { getInitials } from '@/composables/useInitials';
import { DashboardDataSysadmin } from '@/types';
import { CalendarIcon } from 'lucide-vue-next';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps<{
  data: DashboardDataSysadmin;
}>();

const { chartOptionsUsers } = useChartOptionsUsers(props.data.users.labels);
const { chartOptionsRoles } = useChartOptionsRoles(props.data.roles.labels);

const badgeVariant = (size: number): BadgeVariants['variant'] => {
  let variant: BadgeVariants['variant'] = 'secondary';

  if (size >= 17476266) variant = 'default';
  if (size >= 52428800) variant = 'destructive';

  return variant;
};
</script>

<template>
  <div class="grid auto-rows-min gap-4 md:grid-cols-2">
    <Card class="bg-accent">
      <CardHeader>
        <CardTitle> Usuarios </CardTitle>
        <CardDescription>Cantidad de usuarios registrados / eliminados.</CardDescription>
        <CardContent>
          <VueApexCharts type="donut" height="130" :options="chartOptionsUsers" :series="data.users.series"></VueApexCharts>
        </CardContent>
      </CardHeader>
    </Card>

    <Card class="bg-accent">
      <CardHeader>
        <CardTitle> Roles </CardTitle>
        <CardDescription>Cantidad de roles registrados.</CardDescription>
        <CardContent>
          <VueApexCharts type="donut" height="130" :options="chartOptionsRoles" :series="data.roles.series"></VueApexCharts>
        </CardContent>
      </CardHeader>
    </Card>
  </div>
  <div class="grid auto-rows-min gap-4 md:grid-cols-2">
    <Card class="bg-accent">
      <CardHeader>
        <CardTitle> Usuarios Activos </CardTitle>
        <CardDescription>Usuarios que actualmente han iniciado sesión.</CardDescription>
        <CardContent>
          <ScrollArea>
            <div v-for="(u, i) in data.activeUsers" :key="i">
              <HoverCard>
                <HoverCardTrigger as-child>
                  <Button variant="link"> {{ `@${u.user.name}` }} </Button>
                </HoverCardTrigger>
                <HoverCardContent class="w-80">
                  <div class="flex justify-between space-x-4">
                    <Avatar>
                      <AvatarImage v-if="u.user.avatar" :src="u.user.avatar" />
                      <AvatarFallback>
                        {{ getInitials(u.user.name) }}
                      </AvatarFallback>
                    </Avatar>
                    <div class="space-y-1">
                      <h4 class="text-sm font-semibold">{{ `@${u.user.name}` }}</h4>
                      <p class="text-sm">
                        {{ u.user.email }}
                        <br />
                        {{ u.last_active }}
                        <br />
                        {{ u.ip_address }}
                      </p>
                      <div class="flex items-center pt-2">
                        <CalendarIcon class="mr-2 h-4 w-4 opacity-70" />
                        <span class="text-xs text-muted-foreground"> {{ `Creado ${u.user.created_at_human}` }} </span>
                      </div>
                    </div>
                  </div>
                </HoverCardContent>
              </HoverCard>
              <Separator class="my-2" />
            </div>
          </ScrollArea>
        </CardContent>
      </CardHeader>
    </Card>

    <Card class="bg-accent">
      <CardHeader>
        <CardTitle> Depuración </CardTitle>
        <CardDescription>Tamaño actual de los archivos de depuración (logs) de la aplicación.</CardDescription>
        <CardContent>
          <ScrollArea>
            <div v-for="(l, i) in data.logSizes" :key="i">
              <Badge :variant="badgeVariant(l[i].sizeRaw)">{{ `${l[i].logName}: ${l[i].sizeHuman}` }}</Badge>
              <Separator class="my-2" />
            </div>
          </ScrollArea>
        </CardContent>
      </CardHeader>
    </Card>
  </div>
</template>
