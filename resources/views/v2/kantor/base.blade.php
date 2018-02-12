@if(array_intersect($acl_menu['kantor.kantor'], $scopes->scopes))
<a href="{{route('kantor.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['kantor']) ? 'active' : '' }}">
	<i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor
</a>
@endif

@if(array_intersect($acl_menu['kantor.karyawan'], $scopes->scopes))
<a href="{{route('karyawan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['karyawan']) ? 'active' : '' }}">
	<i class="fa fa-users"></i>&nbsp;&nbsp;Karyawan
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-users"></i>&nbsp;&nbsp;Karyawan
</a>
@endif
