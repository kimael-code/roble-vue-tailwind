<?php

namespace App\Actions\Debugging\ActivityLog;

use App\Models\Debugging\ActivityLog;
use App\Models\Organization\Organization;
use App\Support\DataExport\BasePdf;

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

        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->setFillColor(0, 53, 41);
        $this->setTextColor(255, 255, 255);
        $this->Cell(w: 0, txt: '1. FILTROS APLICADOS', border: 0, ln: 1, fill: true);
        $this->setTextColor(0, 0, 0);
        $this->Cell(w: 40, txt: 'Usuario(s)', border: 'B');
        $this->setFont(family: 'helvetica', style: 'I', size: 10);
        $this->Cell(w: 0, txt: 'Todos', border: 'B', ln: 1);
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->Cell(w: 40, txt: 'Fecha/Hora', border: 'B');
        $this->setFont(family: 'helvetica', style: 'I', size: 10);
        $this->Cell(w: 0, txt: 'Todo', border: 'B', ln: 1);
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->Cell(w: 40, txt: 'Tipo de Actividad', border: 'B');
        $this->setFont(family: 'helvetica', style: 'I', size: 10);
        $this->Cell(w: 0, txt: 'Todas', border: 'B', ln: 1);
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->Cell(w: 40, txt: 'Módulo/Funcionalidad', border: 'B');
        $this->setFont(family: 'helvetica', style: 'I', size: 10);
        $this->Cell(w: 0, txt: 'Todo', border: 'B', ln: 1);
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->Cell(w: 40, txt: 'IP Origen', border: 'B');
        $this->setFont(family: 'helvetica', style: 'I', size: 10);
        $this->Cell(w: 0, txt: 'Todas', border: 'B', ln: 1);

        $this->Ln(10);

        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->setFillColor(0, 53, 41);
        $this->setTextColor(255, 255, 255);
        $this->Cell(w: 0, txt: '2. DETALLE DE LAS TRAZAS DE ACTIVIDADES', border: 0, ln: 1, fill: true);
        $this->setTextColor(0, 0, 0);
        $this->MultiCell(w: 30, h: 5, maxh: 5, txt: 'Fecha/Hora', border: 0, align: 'L', valign: 'M', ln: 0);
        $this->MultiCell(w: 35, h: 5, maxh: 5, txt: 'Usuario', border: 0, align: 'L', valign: 'M', ln: 0);
        $this->MultiCell(w: 25, h: 5, maxh: 5, txt: "Actividad", border: 0, align: 'L', valign: 'M', ln: 0);
        $this->MultiCell(w: 30, h: 5, maxh: 5, txt: "Módulo/Func.", border: 0, align: 'L', valign: 'M', ln: 0);
        $this->MultiCell(w: 85, h: 5, maxh: 5, txt: "Descripción", border: 0, align: 'L', valign: 'M', ln: 0);
        $this->MultiCell(w: 18, h: 5, maxh: 5, txt: "ID", border: 0, align: 'L', valign: 'M', ln: 0);
        $this->MultiCell(w: 0, h: 5, maxh: 5, txt: 'IP Origen', border: 0, align: 'L', valign: 'M', ln: 1);
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
            lw: 45,
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
        $this->setFooterMargin();

        $this->setAutoPageBreak(TRUE);

        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------

        $this->setFont(family: 'iosevkafixedss12', size: 8, );

        $this->AddPage();

        $activities = ActivityLog::filter($this->filters)->get();

        foreach ($activities->chunk(15) as $index => $chunk)
        {
            if ($index > 0)
            {
                $this->AddPage();
            }

            $chunk->each(function (ActivityLog $activity)
            {
                $ts = $activity->created_at->isoFormat('L LTS');

                $this->MultiCell(w: 30, h: 7, maxh: 7, txt: $ts, border: 'B', align: 'L', valign: 'M', ln: 0);
                $this->MultiCell(w: 35, h: 7, maxh: 7, txt: $activity->causer->name, fitcell: true, border: 'B', align: 'L', valign: 'M', ln: 0);
                $this->MultiCell(w: 25, h: 7, maxh: 7, txt: $activity->event, fitcell: true, border: 'B', align: 'L', valign: 'M', ln: 0);
                $this->MultiCell(w: 30, h: 7, maxh: 7, txt: $activity->log_name, border: 'B', align: 'L', valign: 'M', ln: 0);
                $this->MultiCell(w: 85, h: 7, maxh: 7, txt: $activity->description, fitcell: true, border: 'B', align: 'L', valign: 'M', ln: 0);
                $this->MultiCell(w: 18, h: 7, maxh: 7, txt: $activity->subject_id ?? '', border: 'B', align: 'L', valign: 'M', ln: 0);
                $this->MultiCell(w: 0, h: 7, maxh: 7, txt: $activity->properties['request']['ip_address'], border: 'B', align: 'L', valign: 'M', ln: 1);
            });
        }

        return $this->Output('REPORTE: TRAZAS DE ACTIVIDADES DE USUARIOS');
    }
}
