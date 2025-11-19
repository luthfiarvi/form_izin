@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            {{-- Gunakan logo IHBS dari domain publik agar email (Gmail, dll) bisa memuat gambarnya --}}
            <img src="https://ihbs.sch.id/wp-content/uploads/2024/06/LOGO-ihbs.png"
                 class="logo"
                 alt="IHBS">
        </a>
    </td>
</tr>

