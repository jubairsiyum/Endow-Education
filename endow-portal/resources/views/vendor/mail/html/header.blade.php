@props(['url'])
<tr>
<td class="header" style="background: transparent; padding: 40px 0 30px 0;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<table cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td style="text-align: center;">
<span style="font-size: 32px; font-weight: 700; background: linear-gradient(135deg, #DC143C 0%, #B0102F 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; letter-spacing: 0.5px;">
@if (trim($slot) === 'Laravel')
Endow Connect
@else
{{ $slot }}
@endif
</span>
<div style="height: 3px; width: 80px; background: linear-gradient(90deg, #DC143C 0%, #B0102F 100%); margin: 12px auto 0; border-radius: 2px;"></div>
</td>
</tr>
</table>
</a>
</td>
</tr>
</table>
</td>
</tr>
