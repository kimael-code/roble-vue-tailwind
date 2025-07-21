<table cellpadding="1" cellspacing="0" style="border-collapse: collapse;">
    @foreach ($activityLogs as $activityLog)
        <tr nobr="true">
            <td style="border-top: 0.5px solid #000;" width="106.3">{{ $activityLog->created_at->isoFormat('L LTS') }}</td>
            <td style="border-top: 0.5px solid #000;" width="124">{{ $activityLog->causer->name }}</td>
            <td style="border-top: 0.5px solid #000;" width="88.7">{{ $activityLog->event }}</td>
            <td style="border-top: 0.5px solid #000;" width="106.3">{{ $activityLog->log_name }}</td>
            <td style="border-top: 0.5px solid #000;" width="301">{{ $activityLog->description }}</td>
            <td style="border-top: 0.5px solid #000;" width="64">{{ $activityLog->subject_id ?? '' }}</td>
            <td style="border-top: 0.5px solid #000;" width="93.4">{{ $activityLog->properties['request']['ip_address'] }}</td>
        </tr>
    @endforeach
</table>