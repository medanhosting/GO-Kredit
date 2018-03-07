@if(array_intersect($acl_menu['keuangan.lkh'], $scopes->scopes))
<a href="{{route('kasir.lkh', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kasir']) ? 'active' : '' }}">
	<i class="fa fa-sticky-note-o"></i>&nbsp;&nbsp;LKH
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-sticky-note-o"></i>&nbsp;&nbsp;LKH
</a>
@endif

@if(array_intersect($acl_menu['keuangan.jurnal'], $scopes->scopes))
<a href="{{route('jurnal.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['jurnal']) ? 'active' : '' }}">
	<i class="fa fa-book"></i>&nbsp;&nbsp;Jurnal
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-book"></i>&nbsp;&nbsp;Jurnal
</a>
@endif

@if(array_intersect($acl_menu['keuangan.lkh'], $scopes->scopes))
<a href="{{route('kasir.penerimaan', ['tipe' => 'kas', 'kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['penerimaan']) ? 'active' : '' }}">
	<i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Penerimaan
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Penerimaan
</a>
@endif

@if(array_intersect($acl_menu['keuangan.lkh'], $scopes->scopes))
<a href="{{route('kasir.pengeluaran', ['tipe' => 'kas', 'kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['pengeluaran']) ? 'active' : '' }}">
	<i class="fa fa-minus-square-o"></i>&nbsp;&nbsp;Pengeluaran
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-minus-square-o"></i>&nbsp;&nbsp;Pengeluaran
</a>
@endif


@if(array_intersect($acl_menu['keuangan.lkh'], $scopes->scopes))
<a href="{{route('kasir.penerimaan.tk', ['tipe' => 'kas', 'kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['penerimaan_tk']) ? 'active' : '' }}">
	<i class="fa fa-check-square-o"></i>&nbsp;&nbsp;T.Kas Penerimaan
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-check-square-o"></i>&nbsp;&nbsp;T.Kas Penerimaan
</a>
@endif

@if(array_intersect($acl_menu['keuangan.lkh'], $scopes->scopes))
<a href="{{route('kasir.pengeluaran.tk', ['tipe' => 'kas', 'kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['pengeluaran_tk']) ? 'active' : '' }}">
	<i class="fa fa-minus-square-o"></i>&nbsp;&nbsp;T.Kas Pengeluaran
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-minus-square-o"></i>&nbsp;&nbsp;T.Kas Pengeluaran
</a>
@endif

@if(array_intersect($acl_menu['keuangan.kas'], $scopes->scopes))
<a href="{{route('kas.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kas']) ? 'active' : '' }}">
	<i class="fa fa-barcode"></i>&nbsp;&nbsp;BKK/BKM
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-barcode"></i>&nbsp;&nbsp;BKK/BKM
</a>
@endif
@if(array_intersect($acl_menu['keuangan.akun'], $scopes->scopes))
<a href="{{route('akun.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['akun']) ? 'active' : '' }}">
	<i class="fa fa-gear"></i>&nbsp;&nbsp;Akun
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-gear"></i>&nbsp;&nbsp;Akun
</a>
@endif