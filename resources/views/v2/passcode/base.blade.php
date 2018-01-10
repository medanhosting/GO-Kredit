<a href="{{route('passcode.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['passcode']) ? 'active' : '' }}">
	<i class="fa fa-unlock-alt"></i>&nbsp;&nbsp;Passcode
</a>