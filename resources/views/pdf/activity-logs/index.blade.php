<table cellpadding="1" cellspacing="0" style="border-collapse: collapse;">
    @foreach ($activityLogs as $activityLog)
        <tr nobr="true">
            <td style="border-top: 0.5px solid #000;" width="106.3">{{ $activityLog->created_at->isoFormat('L LTS') }}</td>
            <td style="border-top: 0.5px solid #000;" width="106.3">{{ $activityLog->causer->name ?? $activityLog->properties['causer'] ?? 'N/D' }}</td>
            <td style="border-top: 0.5px solid #000;" width="70">{{ $activityLog->event }}</td>
            <td style="border-top: 0.5px solid #000;" width="106.3">{{ $activityLog->log_name }}</td>
            <td style="border-top: 0.5px solid #000;" width="379">{{ $activityLog->description }}</td>
            <td style="border-top: 0.5px solid #000;" width="35.5">{{ $activityLog->subject_id ?? 'N/D' }}</td>
            <td style="border-top: 0.5px solid #000;" width="80">{{ $activityLog->properties['request']['ip_address'] }}</td>
        </tr>
    @endforeach
</table>
