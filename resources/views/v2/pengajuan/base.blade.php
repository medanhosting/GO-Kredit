<a href="{{route('pengajuan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['pengajuan']) ? 'active' : '' }}">
	<i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan
</a>
<a href="{{route('putusan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['putusan']) ? 'active' : '' }}">
	<i class="fa fa-hourglass-end"></i>&nbsp;&nbsp;Putusan 
</a>
<a href="{{route('simulasi.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['simulasi']) ? 'active' : '' }}">
	<i class="fa fa-calculator"></i>&nbsp;&nbsp;Simulasi
</a>
<a href="{{route('pengajuan.create', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['permohonan']) ? 'active' : '' }}">
	<i class="fa fa-plus"></i>&nbsp;&nbsp;Permohonan Baru
</a>
