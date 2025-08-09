@inject('exporter', 'App\Actions\Security\User\ExportIndexToPdf')

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
        <td class="td-header" width="150"><b>{{ $headerUser }}</b></td>
        <td class="td-data">{{ $user?->name }}{{ $user?->person ? "[{$user?->person->names} {$user?->person->surnames}]" : '' }}</td>
    </tr>
    <tr>
        <td class="td-header" width="150"><b>{{ $headerEmail }}</b></td>
        <td class="td-data">{{ $user?->email }}</td>
    </tr>
    <tr>
        <td class="td-header" width="150"><b>{{ $headerCreatedAt }}</b></td>
        <td class="td-data">{{ $user?->created_at_human }}</td>
    </tr>
    <tr>
        <td class="td-header" width="150"><b>{{ $headerDisabledAt }}</b></td>
        <td class="td-data">{{ $user->disabled_at_human ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="td-header" width="150"><b>{{ $headerDeletedAt }}</b></td>
        <td class="td-data">{{ $user->deleted_at_human ?? 'N/A' }}</td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150"><b>Rol/es Asignado/s</b></td>
        <td class="td-data">
            @if (count($userRoleNames))
                <ul>
                    @foreach ($userRoleNames as $role)
                        <li>{{ $role }}</li>
                    @endforeach
                </ul>
            @else
                Sin asignar
            @endif
        </td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150"><b>Permiso/s Concedidos</b></td>
        <td class="td-data"></td>
    </tr>
    <tr nobr="true">
        <td class="td-header-no-border" width="150" align="right">Lectura</td>
        <td class="td-data-no-border">
            @if (count($userPermissionsRead))
                <ul>
                    @foreach ($userPermissionsRead as $readPermissions)
                        <li>{{ $readPermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                Sin asignar
            @endif
        </td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150" align="right">Escritura</td>
        <td class="td-data">
            @if (count($userPermissionsCreate))
                <ul>
                    @foreach ($userPermissionsCreate as $createPermissions)
                        <li>{{ $createPermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                Sin asignar
            @endif
        </td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150" align="right">Actualización</td>
        <td class="td-data">
            @if (count($userPermissionsUpdate))
                <ul>
                    @foreach ($userPermissionsUpdate as $updatePermissions)
                        <li>{{ $updatePermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                Sin asignar
            @endif
        </td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150" align="right">Eliminación</td>
        <td class="td-data">
            @if (count($userPermissionsDelete))
                <ul>
                    @foreach ($userPermissionsDelete as $deletePermissions)
                        <li>{{ $deletePermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                Sin asignar
            @endif
        </td>
    </tr>
    <tr nobr="true">
        <td class="td-header" width="150" align="right">Exportación</td>
        <td class="td-data">
            @if (count($userPermissionsExport))
                <ul>
                    @foreach ($userPermissionsExport as $exportPermissions)
                        <li>{{ $exportPermissions->description }}</li>
                    @endforeach
                </ul>
            @else
                Sin asignar
            @endif
        </td>
    </tr>
</table>