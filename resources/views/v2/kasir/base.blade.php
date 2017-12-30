<a href="{{route('kredit.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kredit']) ? 'active' : '' }}">
	<i class="fa fa-line-chart"></i>&nbsp;&nbsp;Keuangan
</a>