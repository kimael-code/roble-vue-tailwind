<?php

namespace App\Actions\Security\Permission;

use App\Models\Debugging\ActivityLog;
use App\Models\Organization\Organization;
use App\Models\Security\Permission;
use App\Support\DataExport\BasePdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
        $this->MultiCell(w: 40, h: 0, txt: 'Buscar', align: 'L', ln: 0);
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w: 0, h: 0, txt: $filters['search'] ?: 'Todo', ln: 1);
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, txt: 'Usuario(s)', border: 'T', align: 'L', ln: 0);
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w: 0, h: 0, txt: $filters['users'] ?: 'Todos', border: 'T', ln: 1);
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, txt: 'Rol(es)', border: 'T', align: 'L', ln: 0);
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w: 0, h: 0, txt: $filters['roles'] ?: 'Todos', border: 'T', ln: 1);
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, txt: 'Operación', border: 'T', align: 'L', ln: 0);
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w: 0, h: 0, txt: $filters['operations'] ?: 'Todas', border: 'T', ln: 1);

        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->setFillColor(0, 53, 41);
        $this->setTextColor(255, 255, 255);
        $this->Cell(w: 0, txt: '2. DETALLE DE LAS TRAZAS DE ACTIVIDADES', border: 0, ln: 1, fill: true);
        $this->setTextColor(0, 0, 0);
        $this->MultiCell(w: 30, h: 5, align: 'L', ln: 0, txt: 'Fecha Creado',);
        $this->MultiCell(w: 10, h: 5, align: 'L', ln: 0, txt: 'ID',);
        $this->MultiCell(w: 64, h: 5, align: 'L', ln: 0, txt: 'Permiso',);
        $this->MultiCell(w: 40, h: 5, align: 'L', ln: 0, txt: 'Rol/es',);
        $this->MultiCell(w: 40, h: 5, align: 'L', ln: 0, txt: 'Usuario/s',);
        $this->MultiCell(w: 65, h: 5, align: 'L', ln: 1, txt: 'Operación',);

        // establece el margen superior a la altura ocupada por el header
        $this->tMargin = $this->GetY();
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    }

    public function make(): string
    {
        // metadatos del archivo
        $this->setTitle('REPORTE: PERMISOS');
        $this->setSubject('Reporte de Permisos registrados');
        $this->setKeywords('reporte, PDF, permiso, permisos');

        $organizationLogo = Organization::active()->first()->logo_path ?? '';

        $this->setHeaderData(
            ln: storage_path("app/public/{$organizationLogo}"),
            lw: 60,
            ht: 'REPORTE: PERMISOS',
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

        $permissions = Permission::filter($this->filters)
            ->with(['users', 'roles'])
            ->get();

        foreach ($permissions->chunk(15) as $index => $chunk)
        {
            if ($index > 0)
            {
                $this->AddPage();
            }

            $chunk->each(function (Permission $permission)
            {
                $ts = $permission->created_at->isoFormat('L LTS');

                $this->MultiCell(w: 30, h: 10, maxh: 10, fitcell: true, border: 'B', align: 'L', valign: 'M', ln: 0, txt: $ts,);
                $this->MultiCell(w: 10, h: 10, maxh: 10, fitcell: true, border: 'B', align: 'L', valign: 'M', ln: 0, txt: $permission->id ?? '',);
                $this->MultiCell(w: 64, h: 10, maxh: 10, fitcell: true, border: 'B', align: 'L', valign: 'M', ln: 0, txt: "[{$permission->name}] {$permission->description}",);
                $this->MultiCell(w: 40, h: 10, maxh: 10, fitcell: true, border: 'B', align: 'L', valign: 'M', ln: 0, txt: $permission->roles()->pluck('name')->implode(', '),);
                $this->MultiCell(w: 40, h: 10, maxh: 10, fitcell: true, border: 'B', align: 'L', valign: 'M', ln: 0, txt: $permission->users()->pluck('email')->implode(', '),);
                $this->MultiCell(w: 65, h: 10, maxh: 10, fitcell: true, border: 'B', align: 'L', valign: 'M', ln: 1, txt: $this->getOperation($permission),);
            });
        }

        return $this->Output('REPORTE: PERMISOS');
    }

    private function getFilters(): array
    {
        $filters = [
            'roles' => '',
            'users' => '',
            'operations' => '',
            'set_menu' => '',
            'search' => '',
        ];

        if (isset($this->filters['roles']))
        {
            $filters['roles'] .= Arr::join($this->filters['roles'], ', ');
        }

        if (isset($this->filters['users']))
        {
            $filters['users'] .= Arr::join($this->filters['users'], ', ');
        }

        if (isset($this->filters['operations']))
        {
            $filters['operations'] .= Arr::join($this->filters['operations'], ', ');
        }

        // if (isset($this->filters['set_menu']))
        // {
        //     $filters['set_menu'] .= Arr::join($this->filters['set_menu'], ', ');
        // }

        if (isset($this->filters['search']))
        {
            $filters['search'] .= $this->filters['search'];
        }

        return $filters;
    }

    private function getOperation(Permission $permission) : string
    {
        return match (true) {
            Str::contains($permission->name, 'create') => 'Creación',
            Str::contains($permission->name, 'read') => 'Lectura',
            Str::contains($permission->name, 'update') => 'Actualización',
            Str::contains($permission->name, 'delete') => 'Eliminación',
            Str::contains($permission->name, 'export') => 'Exportación',
            Str::contains($permission->name, 'activate') => 'Activación',
            Str::contains($permission->name, 'deactivate') => 'Desactivación',
            Str::contains($permission->name, 'restore') => 'Restauración',
            default => 'Desconocida',
        };
    }
}
