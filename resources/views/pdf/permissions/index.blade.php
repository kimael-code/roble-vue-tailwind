@inject('exporter', 'App\Actions\Security\Permission\ExportIndexToPdf')

<table cellpadding="1" cellspacing="0" style="border-collapse: collapse;">
    @foreach ($permissions as $permission)
        <tr nobr="true">
            <td style="border-top: 0.5px solid #000;" width="35.5">{{ $permission->id }}</td>
            <td style="border-top: 0.5px solid #000;" width="327.5">{{ "[{$permission->name}] {$permission->description}" }}</td>
            <td style="border-top: 0.5px solid #000;" width="106.5">{{ $permission->created_at->isoFormat('L LTS') }}</td>
            <td style="border-top: 0.5px solid #000;" width="90.5">{{ $exporter->getOperation($permission) }}</td>
            <td style="border-top: 0.5px solid #000;" width="161.5">{{ $permission->roles()->pluck('name')->implode(', ') }}</td>
            <td style="border-top: 0.5px solid #000;" width="161.5">{{ $exporter->getUsers($permission) }}</td>
        </tr>
    @endforeach
</table>