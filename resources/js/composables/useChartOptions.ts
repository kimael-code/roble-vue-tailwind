import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { useDark } from '@vueuse/core';
import { computed, ref, watch } from 'vue';

const isOpen = usePage<SharedData>().props?.sidebarOpen;

export function useChartOptionsUsers(labels: Array<string>, colors = ['#2e93fa', '#e91e63']) {
  const isDark = useDark();
  const textColor = computed(() => (isDark.value ? '#f5f5f5' : '#373d3f'));

  const chartOptionsUsers = ref({
    colors: colors,
    dataLabels: {
      enabled: false,
    },
    labels: labels,
    legend: {
      show: true,
      position: 'left',
      horizontalAlign: 'left',
      formatter: function (seriesName: string, opts: { [index: string]: any }) {
        return [seriesName, ' = ', opts.w.globals.series[opts.seriesIndex]];
      },
      labels: {
        colors: textColor.value,
      },
    },
    stroke: {
      width: 0,
    },
    plotOptions: {
      pie: {
        donut: {
          size: '85%',
          labels: {
            show: true,
            value: {
              show: true,
              fontSize: '16px',
              color: textColor.value,
            },
            total: {
              show: true,
              fontSize: '15px',
              color: textColor.value,
            },
          },
        },
      },
    },
    responsive: [
      {
        breakpoint: 480,
        options: {
          chart: {
            width: 200,
          },
          legend: {
            position: 'bottom',
          },
        },
      },
      {
        breakpoint: 768,
        options: {
          chart: {
            width: 300,
          },
          legend: {
            position: 'left',
          },
        },
      },
      {
        breakpoint: 1024,
        options: {
          chart: {
            width: 300,
          },
          legend: {
            position: 'left',
          },
        },
      },
    ],
  });

  watch(
    [isDark, () => isOpen],
    ([isDarkTheme]) => {
      chartOptionsUsers.value = {
        colors: colors,
        dataLabels: {
          enabled: false,
        },
        labels: labels,
        legend: {
          show: true,
          position: 'left',
          horizontalAlign: 'left',
          formatter: function (seriesName, opts) {
            return [seriesName, ' = ', opts.w.globals.series[opts.seriesIndex]];
          },
          labels: {
            colors: isDarkTheme ? '#f5f5f5' : '#373d3f',
          },
        },
        stroke: {
          width: 0,
        },
        plotOptions: {
          pie: {
            donut: {
              size: '85%',
              labels: {
                show: true,
                value: {
                  show: true,
                  fontSize: '16px',
                  color: isDarkTheme ? '#f5f5f5' : '#373d3f',
                },
                total: {
                  show: true,
                  fontSize: '15px',
                  color: isDarkTheme ? '#f5f5f5' : '#373d3f',
                },
              },
            },
          },
        },
        responsive: [
          {
            breakpoint: 480,
            options: {
              chart: {
                width: 200,
              },
              legend: {
                position: 'bottom',
              },
            },
          },
          {
            breakpoint: 768,
            options: {
              chart: {
                width: 300,
              },
              legend: {
                position: 'left',
              },
            },
          },
          {
            breakpoint: 1024,
            options: {
              chart: {
                width: 300,
              },
              legend: {
                position: 'left',
              },
            },
          },
        ],
      };
    },
    { deep: true },
  );

  return { chartOptionsUsers };
}

export function useChartOptionsRoles(labels: Array<string>,) {
  const isDark = useDark();
  const textColor = computed(() => (isDark.value ? '#f5f5f5' : '#373d3f'));

  const chartOptionsRoles = ref({
    dataLabels: {
      enabled: false,
    },
    labels: labels,
    legend: {
      show: true,
      position: 'left',
      horizontalAlign: 'left',
      formatter: function (seriesName: string, opts: { [index: string]: any }) {
        return [seriesName, ' = ', opts.w.globals.series[opts.seriesIndex]];
      },
      labels: {
        colors: textColor.value,
      },
    },
    stroke: {
      width: 0,
    },
    plotOptions: {
      pie: {
        donut: {
          size: '85%',
          labels: {
            show: true,
            value: {
              show: true,
              fontSize: '16px',
              color: textColor.value,
            },
            total: {
              show: true,
              fontSize: '15px',
              color: textColor.value,
            },
          },
        },
      },
    },
    responsive: [
      {
        breakpoint: 480,
        options: {
          chart: {
            width: 200,
          },
          legend: {
            position: 'bottom',
          },
        },
      },
      {
        breakpoint: 768,
        options: {
          chart: {
            width: 300,
          },
          legend: {
            position: 'left',
          },
        },
      },
      {
        breakpoint: 1024,
        options: {
          chart: {
            width: 300,
          },
          legend: {
            position: 'left',
          },
        },
      },
    ],
  });

  watch([isDark, () => isOpen], ([isDarkTheme]) => {
    chartOptionsRoles.value = {
      dataLabels: {
        enabled: false,
      },
      labels: labels,
      legend: {
        show: true,
        position: 'left',
        horizontalAlign: 'left',
        formatter: function (seriesName, opts) {
          return [seriesName, ' = ', opts.w.globals.series[opts.seriesIndex]];
        },
        labels: {
          colors: isDarkTheme ? '#f5f5f5' : '#373d3f',
        },
      },
      stroke: {
        width: 0,
      },
      plotOptions: {
        pie: {
          donut: {
            size: '85%',
            labels: {
              show: true,
              value: {
                show: true,
                fontSize: '16px',
                color: isDarkTheme ? '#f5f5f5' : '#373d3f',
              },
              total: {
                show: true,
                fontSize: '15px',
                color: isDarkTheme ? '#f5f5f5' : '#373d3f',
              },
            },
          },
        },
      },
      responsive: [
        {
          breakpoint: 480,
          options: {
            chart: {
              width: 200,
            },
            legend: {
              position: 'bottom',
            },
          },
        },
        {
          breakpoint: 768,
          options: {
            chart: {
              width: 300,
            },
            legend: {
              position: 'left',
            },
          },
        },
        {
          breakpoint: 1024,
          options: {
            chart: {
              width: 300,
            },
            legend: {
              position: 'left',
            },
          },
        },
      ],
    };
  });

  return { chartOptionsRoles };
}
