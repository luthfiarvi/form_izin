@php
    $fallbackSvg = 'data:image/svg+xml;utf8,'.
        '%3svg xmlns%3D%22http%3A//www.w3.org/2000/svg%22 width%3D%22160%22 height%3D%2260%22 viewBox%3D%220 0 160 60%22%3E'.
        '%3Crect width%3D%22160%22 height%3D%2260%22 rx%3D%228%22 fill%3D%22%23294755%22/%3E'.
        '%3Ctext x%3D%2280%22 y%3D%2236%22 text-anchor%3D%22middle%22 font-size%3D%2216%22 fill%3D%22%23FFFFFF%22%3EIHBS%3C/text%3E'.
        '%3C/svg%3E';
@endphp

<img src="{{ asset('img/logo-ihbs.png') }}"
     alt="IHBS"
     onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';"
     {{ $attributes->merge(['class' => 'h-10 w-auto']) }}>
