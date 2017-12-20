<a href="{{route('kredit.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['kredit']) ? 'active' : '' }}">
	<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit
</a>
<a href="{{route('realisasi.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['realisasi']) ? 'active' : '' }}">
	<i class="fa fa-hourglass-end"></i>&nbsp;&nbsp;Realisasi
</a>
<a href="{{route('simulasi.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link {{ in_array(strtolower($active_submenu), ['simulasi']) ? 'active' : '' }}">
	<i class="fa fa-exchange"></i>&nbsp;&nbsp;Mutasi Jaminan
</a>
<a href="#" class="nav-link {{ in_array(strtolower($active_submenu), ['permohonan']) ? 'active' : '' }}">
	<i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Laporan Tunggakan
</a>
<a href="#" class="nav-link {{ in_array(strtolower($active_submenu), ['permohonan']) ? 'active' : '' }}">
	<i class="fa fa-hand-paper-o"></i>&nbsp;&nbsp;Laporan Penagihan
</a>
