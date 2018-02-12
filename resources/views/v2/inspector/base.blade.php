@if(array_intersect($acl_menu['inspektor.audit'], $scopes->scopes))
<a href="{{route('audit.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['audit']) ? 'active' : '' }}">
	<i class="fa fa-user-secret"></i>&nbsp;&nbsp;Audit
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-user-secret"></i>&nbsp;&nbsp;Audit
</a>
@endif

@if(array_intersect($acl_menu['inspektor.passcode'], $scopes->scopes))
<a href="{{route('passcode.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['passcode']) ? 'active' : '' }}">
	<i class="fa fa-unlock-alt"></i>&nbsp;&nbsp;Passcode
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-unlock-alt"></i>&nbsp;&nbsp;Passcode
</a>
@endif
