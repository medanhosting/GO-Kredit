<a href="{{route('kredit.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kredit']) ? 'active' : '' }}">
	<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit
</a>
<a href="{{route('angsuran.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['angsuran']) ? 'active' : '' }}">
	<i class="fa fa-hand-rock-o"></i>&nbsp;&nbsp;Penerimaan Angsuran
</a>
<a href="{{route('tunggakan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['tunggakan']) ? 'active' : '' }}">
	<i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Laporan Tunggakan
</a>
<a href="{{route('penagihan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['penagihan']) ? 'active' : '' }}">
	<i class="fa fa-hand-paper-o"></i>&nbsp;&nbsp;Laporan Penagihan
</a>
<a href="{{route('jaminan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['jaminan']) ? 'active' : '' }}">
	<i class="fa fa-exchange"></i>&nbsp;&nbsp;Mutasi Jaminan
</a>