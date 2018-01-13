<a href="{{route('kasir.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kredit']) ? 'active' : '' }}">
	<i class="fa fa-line-chart"></i>&nbsp;&nbsp;Jurnal
</a>
<a href="{{route('akun.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['akun']) ? 'active' : '' }}">
	<i class="fa fa-book"></i>&nbsp;&nbsp;Akun
</a>