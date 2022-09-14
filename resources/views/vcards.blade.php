@foreach ($members as $member)
BEGIN:VCARD
VERSION:3.0
PRODID:-//LS Verein 7//DE
N:{{ $member['surname'] }};{{ $member['first_name'] }};;;
FN:{{ $member['first_name'] }} {{ $member['surname'] }}
EMAIL;type=INTERNET;type=HOME;type=pref:{{ $member['email'] }}
TEL;type=CELL;type=VOICE;type=pref:{{ $member['phone'] }}
BDAY:{{ $member['birthday']->format('Y-m-d') }}
ADR;TYPE=home:;;{{ $member['street'] }};{{ $member['city'] }};;{{ $member['zipcode'] }};Germany
END:VCARD
@endforeach
