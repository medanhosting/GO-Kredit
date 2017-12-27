<a href="{{route('kantor.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['kantor']) ? 'active' : '' }}">
	<i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor
</a>
<a href="{{route('karyawan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['karyawan']) ? 'active' : '' }}">
	<i class="fa fa-users"></i>&nbsp;&nbsp;Karyawan
</a>
