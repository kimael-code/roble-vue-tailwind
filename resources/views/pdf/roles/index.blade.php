@inject('exporter', 'App\Actions\Security\Role\ExportIndexToPdf')

<style>
    .td-header-no-border {
        font-family: dejavusans;
        font-size: 9pt;
    }
    .td-data-no-border {
        font-family: iosevkafixedss12;
        font-size: 10pt;
    }
    .td-header {
        font-family: dejavusans;
        font-size: 9pt;
        border-top: 0.5px solid #000;
    }
    .td-data {
        font-family: iosevkafixedss12;
        font-size: 10pt;
        border-top: 0.5px solid #000;
    }
</style>

<table style="border-collapse: collapse;">
    <tr>
        <td class="td-header" width="150"><b>N°</b></td>
        <td class="td-data" width="734">{{ $key + 1 }}</td>
    </tr>
    <tr>
        <td class="td-header" width="150"><b>{{ $headerName }}</b></td>
        <td class="td-data">{{ $role?->name }}</td>
    </tr>
    <tr>
        <td class="td-header" width="150"><b>{{ $headerDescription }}</b></td>
        <td class="td-data">{{ $role?->description }}</td>
    </tr>
    <tr>
        <td class="td-header" width="150"><b>{{ $headerCreatedAt }}</b></td>
        <td class="td-data">{{ $role?->created_at_human }}</td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150"><b>Permiso/s Asociado/s</b></td>
        <td class="td-data"></td>
    </tr>
    <tr nobr="true">
        <td class="td-header-no-border" width="150" align="right">Lectura</td>
        <td class="td-data-no-border">
            @if (count($rolePermissionsRead))
                <ul>
                    @foreach ($rolePermissionsRead as $readPermissions)
                        <li>{{ $readPermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                -
            @endif
        </td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150" align="right">Escritura</td>
        <td class="td-data">
            @if (count($rolePermissionsCreate))
                <ul>
                    @foreach ($rolePermissionsCreate as $createPermissions)
                        <li>{{ $createPermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                -
            @endif
        </td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150" align="right">Actualización</td>
        <td class="td-data">
            @if (count($rolePermissionsUpdate))
                <ul>
                    @foreach ($rolePermissionsUpdate as $updatePermissions)
                        <li>{{ $updatePermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                -
            @endif
        </td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150" align="right">Eliminación</td>
        <td class="td-data">
            @if (count($rolePermissionsDelete))
                <ul>
                    @foreach ($rolePermissionsDelete as $deletePermissions)
                        <li>{{ $deletePermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                -
            @endif
        </td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150" align="right">Exportación</td>
        <td class="td-data">
            @if (count($rolePermissionsExport))
                <ul>
                    @foreach ($rolePermissionsExport as $exportPermissions)
                        <li>{{ $exportPermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                -
            @endif
        </td>
    </tr>
</table>