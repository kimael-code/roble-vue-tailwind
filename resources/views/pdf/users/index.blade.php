<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
    @foreach ($users as $user)
        <tr nobr="true">
            <td style="border-top: 0.5px solid #000;" width="35.5">{{ $loop->iteration }}</td>
            <td style="border-top: 0.5px solid #000;" width="108">{{ $user->name }}</td>
            <td style="border-top: 0.5px solid #000;" width="163">{{ $user->email }}</td>
            <td style="border-top: 0.5px solid #000;" width="85">{{ $user->created_at_human }}</td>
            <td style="border-top: 0.5px solid #000;" width="85">{{ $user->disabled_at_human ?? 'N/A' }}</td>
            <td style="border-top: 0.5px solid #000;" width="85">{{ $user->deleted_at_human ?? 'N/A' }}</td>
            <td style="border-top: 0.5px solid #000;" width="161.5">
                @if ($user->getRoleNames()->isNotEmpty())
                    <ul>
                        @foreach ($user->getRoleNames()->sort()->values()->all() as $userRoleName)
                            <li>{{ $userRoleName }}</li>
                        @endforeach
                    </ul>
                @else
                    Sin Rol Asignado
                @endif
            </td>
            <td style="border-top: 0.5px solid #000;" width="161.5">
                @if ($user->getAllPermissions()->isNotEmpty())
                    <ul>
                        @foreach ($user->getAllPermissions()->sortBy('description')->values()->all() as $userPermission)
                            <li>{{ $userPermission->description }}</li>
                        @endforeach
                    </ul>
                @else
                    Sin Permisos Asignados
                @endif
            </td>
        </tr>
    @endforeach
</table>