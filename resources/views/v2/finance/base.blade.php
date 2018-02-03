<a href="{{route('kasir.lkh', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kasir']) ? 'active' : '' }}">
	<i class="fa fa-sticky-note-o"></i>&nbsp;&nbsp;LKH
</a>
<!-- <a href="{{route('kasir.jurnalpagi', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['jurnalpagi']) ? 'active' : '' }}">
	<i class="fa fa-sticky-note-o"></i>&nbsp;&nbsp;Jurnal Pagi
</a> -->
<a href="{{route('jurnal.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['jurnal']) ? 'active' : '' }}">
	<i class="fa fa-book"></i>&nbsp;&nbsp;Jurnal
</a>
<!-- <a href="{{route('kas.penerimaan', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kas_masuk']) ? 'active' : '' }}">
	<i class="fa fa-hand-rock-o"></i>&nbsp;&nbsp;Penerimaan Kas
</a>
<a href="{{route('kas.pengeluaran', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kas_keluar']) ? 'active' : '' }}">
	<i class="fa fa-hand-paper-o"></i>&nbsp;&nbsp;Pengeluaran Kas
</a>
<a href="{{route('bank.penerimaan', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['bank_masuk']) ? 'active' : '' }}">
	<i class="fa fa-hand-rock-o"></i>&nbsp;&nbsp;Penerimaan Bank
</a>
<a href="{{route('bank.pengeluaran', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['bank_keluar']) ? 'active' : '' }}">
	<i class="fa fa-hand-paper-o"></i>&nbsp;&nbsp;Pengeluaran Bank
</a> -->
<a href="{{route('akun.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['akun']) ? 'active' : '' }}">
	<i class="fa fa-gear"></i>&nbsp;&nbsp;Akun
</a>