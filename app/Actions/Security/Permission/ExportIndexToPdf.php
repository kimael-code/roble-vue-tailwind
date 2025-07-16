<?php

namespace App\Actions\Security\Permission;

use App\Models\Organization\Organization;
use App\Models\Security\Permission;
use App\Models\Security\Role;
use App\Support\DataExport\BasePdf;
use Illuminate\Support\Arr;
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
        $this->MultiCell(w: 40, h: 0, align: 'L', ln: 0, txt: 'Buscar');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w: 0, h: 0, align: 'L', ln: 1, txt: $filters['search'] ?: 'Todo');
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, border: 'T', align: 'L', ln: 0, txt: 'Usuario(s)');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w: 0, h: 0, align: 'L', border: 'T', ln: 1, txt: $filters['users'] ?: 'Todos');
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, border: 'T', align: 'L', ln: 0, txt: 'Rol(es)');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w: 0, h: 0, align: 'L', border: 'T', ln: 1, txt: $filters['roles'] ?: 'Todos');
        $this->setFont(family: 'helvetica', style: 'B', size: 10);
        $this->MultiCell(w: 40, h: 0, border: 'T', align: 'L', ln: 0, txt: 'Operación');
        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->MultiCell(w: 0, h: 0, align: 'L', border: 'T', ln: 1, txt: $filters['operations'] ?: 'Todas');

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
        $this->MultiCell(w: 30, h: 5, align: 'L', ln: 0, border: 'B', txt: 'Fecha Creado', );
        $this->MultiCell(w: 10, h: 5, align: 'L', ln: 0, border: 'B', txt: 'ID', );
        $this->MultiCell(w: 64, h: 5, align: 'L', ln: 0, border: 'B', txt: 'Permiso', );
        $this->MultiCell(w: 40, h: 5, align: 'L', ln: 0, border: 'B', txt: 'Rol/es', );
        $this->MultiCell(w: 40, h: 5, align: 'L', ln: 0, border: 'B', txt: 'Usuario/s', );
        $this->MultiCell(w: 65, h: 5, align: 'L', ln: 1, border: 'B', txt: 'Operación', );

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

        $this->setAutoPageBreak(true, 10);

        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------

        $this->setFont(family: 'iosevkafixedss12', size: 8, );

        $this->AddPage();

        $permissions = Permission::filter($this->filters)->get();

        foreach ($permissions as $permission)
        {
            $ts = $permission->created_at->isoFormat('L LTS');
            $heightTs = ceil($this->getStringHeight(30, $ts, border: 'B'));
            $heightId = ceil($this->getStringHeight(10, $permission->id ?? '', border: 'B'));
            $heightPermission = ceil($this->getStringHeight(64, "[{$permission->name}] {$permission->description}", border: 'B'));
            $heightRoles = ceil($this->getStringHeight(40, $permission->roles()->pluck('name')->implode(', '), border: 'B'));
            $heightUsers = ceil($this->getStringHeight(40, $permission->users()->pluck('email')->implode(', '), border: 'B'));
            $heightOperations = ceil($this->getStringHeight(65, $this->getOperation($permission), border: 'B'));

            $maxHeight = max($heightId, $heightOperations, $heightPermission, $heightRoles, $heightTs, $heightUsers, 0);

            $txtRoles = $permission->roles()->pluck('name')->implode(', ');
            $txtUsers = $this->getUsers($permission);

            $this->startTransaction();
            $startPage = $this->getPage();
            $this->MultiCell(w: 30, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: $ts);
            $this->MultiCell(w: 10, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: $permission->id ?? '');
            $this->MultiCell(w: 64, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: "[{$permission->name}] {$permission->description}");
            $this->MultiCell(w: 40, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: $txtRoles);
            $this->MultiCell(w: 40, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: $txtUsers);
            $this->MultiCell(w: 65, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 1, txt: $this->getOperation($permission));

            if ($this->getPage() > $startPage)
            {
                $this->rollbackTransaction(true);
                $this->AddPage();
                $this->MultiCell(w: 30, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: $ts);
                $this->MultiCell(w: 10, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: $permission->id ?? '');
                $this->MultiCell(w: 64, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: "[{$permission->name}] {$permission->description}");
                $this->MultiCell(w: 40, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: $txtRoles);
                $this->MultiCell(w: 40, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 0, txt: $txtUsers);
                $this->MultiCell(w: 65, h: $maxHeight, maxh: $maxHeight, border: 'B', align: 'L', valign: 'T', ln: 1, txt: $this->getOperation($permission));
            }
            else
            {
                $this->commitTransaction();
            }
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

    private function getOperation(Permission $permission): string
    {
        return match (true)
        {
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

    private function getUsers(Permission $permission): string
    {
        $usersByDirectPermission = $permission->users()->pluck('email')->implode(', ');
        $usersByRoles = $permission->roles->map(
            fn(Role $role) => $role->users->pluck('email')->implode(', ')
        )->implode(', ');

        $users = trim("{$usersByDirectPermission}, {$usersByRoles}", ', ');
        // $users = explode(', ', $users);
        // $users = array_unique($users);
        // $users = implode(', ', $users);

        return $users;
    }
}
