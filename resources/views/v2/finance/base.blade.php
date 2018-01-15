<a href="{{route('kasir.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kasir']) ? 'active' : '' }}">
	<i class="fa fa-sticky-note-o"></i>&nbsp;&nbsp;LKH
</a>
<a href="{{route('jurnal.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['jurnal']) ? 'active' : '' }}">
	<i class="fa fa-book"></i>&nbsp;&nbsp;Jurnal
</a>
<a href="{{route('akun.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['akun']) ? 'active' : '' }}">
	<i class="fa fa-gear"></i>&nbsp;&nbsp;Akun
</a>