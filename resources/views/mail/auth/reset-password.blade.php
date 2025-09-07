<x-mail::message>
# @lang('Greetings'), {{ $firstName }}:

@lang('You are receiving this email because we received a password reset request for your account.')

@lang('Click the button below to set a new password:')

<x-mail::button :url="$url">
{{ $actionText }}
</x-mail::button>

@lang(
    "This password reset link will expire in \":count\" minutes.",
    [
        'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')
    ]
)

@lang('If you did not request a password reset, no further action is required.')

<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $url }}]({{ $url }})</span>
</x-slot:subcopy>
</x-mail::message>
