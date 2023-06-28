@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'pvms')
<img src="https://i.imgur.com/QtWfRxt.png" class="logo" alt="Pivotel Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
