<?php

namespace App\Actions\Debugging\ActivityLog;

use App\Models\Debugging\ActivityLog;
use App\Models\Organization\Organization;
use App\Support\DataExport\BasePdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;

class ExportIndexToPdf extends BasePdf
{
    public function __construct(
        protected string $orientation = 'L',
        protected string $format = 'LETTER',
        private array $filters = [],
    ) {
        parent::__construct(
            orientation: $orientation,
            format: $format,
        );
    }

    public function Header(): void
    {
        parent::Header();

        $filters = $this->getFilters();

        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->setFillColor(0, 53, 41);
        $this->setTextColor(255, 255, 255);
        $this->Cell(w: 0, txt: '1. FILTROS APLICADOS', border: 0, ln: 1, fill: true);
        $this->setTextColor(0, 0, 0);
        $this->MultiCell(w: 40, h: 0, align: 'L', ln: 0, txt: 'Buscar');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w:  0, h: 0, align: 'L', ln: 1, txt: $filters['search'] ?: 'Todo');
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, align: 'L', border: 'T', ln: 0, txt: 'Usuario(s)');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w:  0, h: 0, border: 'T', ln: 1, txt: $filters['users'] ?: 'Todos');
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, align: 'L', border: 'T', ln: 0, txt: 'Fecha/Hora');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w:  0, h: 0, border: 'T', ln: 1, txt: $filters['date_time'] ?: 'Todo');
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, align: 'L', border: 'T', ln: 0, txt: 'Tipo de Actividad');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w:  0, h: 0, border: 'T', ln: 1, txt: $filters['events'] ?: 'Todas');
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, align: 'L', border: 'T', ln: 0, txt: 'Módulo/Funcionalidad');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w:  0, h: 0, border: 'T', ln: 1, txt: $filters['modules'] ?: 'Todo');
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, align: 'L', border: 'T', ln: 0, txt: 'IP Origen');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w:  0, h: 0, border: 'T', ln: 1, txt: $filters['ip_dirs'] ?: 'Todas');

        $this->setLineStyle([
            'width' => 0.75 / $this->k,
            'cap' => 'butt',
            'join' => 'mitter',
            'dash' => 0,
            'color' => [0, 0, 0],
        ]);

        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->setFillColor(0, 53, 41);
        $this->setTextColor(255, 255, 255);
        $this->Cell(w: 0, txt: '2. DETALLE DE LAS TRAZAS DE ACTIVIDADES', border: 0, ln: 1, fill: true);
        $this->setTextColor(0, 0, 0);

        $this->setFont(family: 'dejavusans', style: 'B', size: 9);
        $this->MultiCell(w: 30, h: 5, maxh: 5, align: 'L', valign: 'M', ln: 0, txt: $this->getString('Fecha/Hora', 'created_at'));
        $this->MultiCell(w: 35, h: 5, maxh: 5, align: 'L', valign: 'M', ln: 0, txt: $this->getString('Usuario', 'causer'));
        $this->MultiCell(w: 25, h: 5, maxh: 5, align: 'L', valign: 'M', ln: 0, txt: $this->getString('Actividad', 'event'));
        $this->MultiCell(w: 30, h: 5, maxh: 5, align: 'L', valign: 'M', ln: 0, txt: $this->getString('Módulo/Func.', 'log_name'));
        $this->MultiCell(w: 85, h: 5, maxh: 5, align: 'L', valign: 'M', ln: 0, txt: $this->getString('Descripción', 'description'));
        $this->MultiCell(w: 18, h: 5, maxh: 5, align: 'L', valign: 'M', ln: 0, txt: $this->getString('ID', 'subject_id'));
        $this->MultiCell(w:  0, h: 5, maxh: 5, align: 'L', valign: 'M', ln: 1, txt: $this->getString('IP Origen', 'ip_address'));

        // establece el margen superior a la altura ocupada por el header
        $this->tMargin = $this->GetY();
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    }

    public function make(): string
    {
        // metadatos del archivo
        $this->setTitle('REPORTE: TRAZAS DE ACTIVIDADES DE USUARIOS');
        $this->setSubject('Reporte de Trazas');
        $this->setKeywords('reporte, PDF, traza, trazas, actividades');

        $organizationLogo = Organization::active()->first()->logo_path ?? '';

        $this->setHeaderData(
            ln: storage_path("app/public/{$organizationLogo}"),
            lw: 60,
            ht: 'REPORTE: TRAZAS DE ACTIVIDADES DE USUARIOS',
            hs: now()->toDateTimeLocalString(),
            tc: [0, 30, 15],
            lc: [0, 128, 100],
        );
        $this->setFooterData(
            tc: [0, 30, 15],
            lc: [0, 128, 100],
        );

        $this->setFooterFont(['helvetica', '', 8]);

        $this->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $this->setMargins(PDF_MARGIN_LEFT, 77.5, PDF_MARGIN_RIGHT);
        $this->setHeaderMargin();
        $this->setFooterMargin(15);

        $this->setAutoPageBreak(TRUE, 20);

        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------

        $this->setFont(family: 'iosevkafixedss12', size: 8, );

        $this->AddPage();

        $html = View::make('pdf.activity-logs.index', [
            'activityLogs' => ActivityLog::filter($this->filters)->get(),
        ]);

        $this->writeHTML($html);

        return $this->Output('REPORTE: TRAZAS DE ACTIVIDADES DE USUARIOS');
    }

    public function getFilters(): array
    {
        $filters = [
            'date_time' => '',
            'users' => '',
            'events' => '',
            'modules' => '',
            'ip_dirs' => '',
            'search' => '',
        ];

        if (isset($this->filters['date']))
        {
            $date = Carbon::createFromDate(
                $this->filters['date']['year'],
                $this->filters['date']['month'],
                $this->filters['date']['day'],
            );
            $filters['date_time'] .= $date->isoFormat('L');
        }

        if (isset($this->filters['date_range']))
        {
            $dateStart = Carbon::createFromDate(
                $this->filters['date_range']['start']['year'],
                $this->filters['date_range']['start']['month'],
                $this->filters['date_range']['start']['day'],
            );
            $dateEnd = Carbon::createFromDate(
                $this->filters['date_range']['end']['year'],
                $this->filters['date_range']['end']['month'],
                $this->filters['date_range']['end']['day'],
            );
            $dateRange = "{$dateStart->isoFormat('L')} - {$dateEnd->isoFormat('L')}";

            if ($filters['date_time'])
            {
                $filters['date_time'] .= ", {$dateRange}";
            }
            else
            {
                $filters['date_time'] = $dateRange;
            }
        }

        if (isset($this->filters['time']))
        {
            if ($filters['date_time'])
            {
                $filters['date_time'] .= ", {$this->filters['time']}";
            }
            else
            {
                $filters['date_time'] = $this->filters['time'];
            }
        }

        if (isset($this->filters['time_from']) && isset($this->filters['time_until']))
        {
            if ($filters['date_time'])
            {
                $filters['date_time'] .= ", {$this->filters['time_from']} - {$this->filters['time_until']}";
            }
            else
            {
                $filters['date_time'] = "{$this->filters['time_from']} - {$this->filters['time_until']}";
            }
        }

        if (isset($this->filters['time_from']) && !isset($this->filters['time_until']))
        {
            if ($filters['date_time'])
            {
                $filters['date_time'] .= ", desde {$this->filters['time_from']}";
            }
            else
            {
                $filters['date_time'] = "desde {$this->filters['time_from']}";
            }
        }

        if (!isset($this->filters['time_from']) && isset($this->filters['time_until']))
        {
            if ($filters['date_time'])
            {
                $filters['date_time'] .= ", hasta {$this->filters['time_until']}";
            }
            else
            {
                $filters['date_time'] = "hasta {$this->filters['time_until']}";
            }
        }

        if (isset($this->filters['selected_events']))
        {
            $filters['events'] .= Arr::join($this->filters['selected_events'], ', ');
        }

        if (isset($this->filters['selected_modules']))
        {
            $filters['modules'] .= Arr::join($this->filters['selected_modules'], ', ');
        }

        if (isset($this->filters['selected_users']))
        {
            $filters['users'] .= Arr::join($this->filters['selected_users'], ', ');
        }

        if (isset($this->filters['ip_addrs']))
        {
            $filters['ip_dirs'] .= Arr::join($this->filters['ip_addrs'], ', ');
        }

        if (isset($this->filters['search']))
        {
            $filters['search'] .= $this->filters['search'];
        }

        return $filters;
    }

    private function getString(string $txt, string $col): string
    {
        if ($col === 'created_at')
        {
            if (!isset($this->filters['sort_by']))
            {
                return "↓ {$txt}";
            }
            elseif (isset($this->filters['sort_by']['created_at']))
            {
                return $this->filters['sort_by']['created_at'] === 'asc' ? "↑ {$txt}" : "↓ {$txt}";
            }
            elseif (isset($this->filters['sort_by']['created_at_human']))
            {
                return $this->filters['sort_by']['created_at_human'] === 'asc' ? "↑ {$txt}" : "↓ {$txt}";
            }
            else
            {
                return $txt;
            }
        }
        elseif ($col === 'permission')
        {
            if (isset($this->filters['sort_by']['name']))
            {
                return $this->filters['sort_by']['name'] === 'asc' ? "↑ {$txt}" : "↓ {$txt}";
            }
            elseif (isset($this->filters['sort_by']['description']))
            {
                return $this->filters['sort_by']['description'] === 'asc' ? "↑ {$txt}" : "↓ {$txt}";
            }

            return $txt;
        }
        elseif (isset($this->filters['sort_by'][$col]))
        {
            return $this->filters['sort_by'][$col] === 'asc' ? "↑ {$txt}" : "↓ {$txt}";
        }
        else
        {
            return $txt;
        }
    }
}
