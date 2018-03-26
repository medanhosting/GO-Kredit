@if(array_intersect($acl_menu['kredit.kredit'], $scopes->scopes))
<a href="{{route('kredit.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kredit']) ? 'active' : '' }}">
	<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit
</a>
@endif
@if(array_intersect($acl_menu['kredit.jaminan'], $scopes->scopes))
<a href="{{route('jaminan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['jaminan']) ? 'active' : '' }}">
	<i class="fa fa-exchange"></i>&nbsp;&nbsp;Mutasi Jaminan
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-exchange"></i>&nbsp;&nbsp;Mutasi Jaminan
</a>
@endif
@if(array_intersect($acl_menu['kredit.tunggakan'], $scopes->scopes))
<a href="{{route('tunggakan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['tunggakan']) ? 'active' : '' }}">
	<i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Laporan Tunggakan
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Laporan Tunggakan
</a>
@endif
@if(array_intersect($acl_menu['kredit.tunggakan'], $scopes->scopes))
<a href="{{route('tunggakan.kolektabilitas', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['kolektabilitas']) ? 'active' : '' }}">
	<i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Laporan Kolektabilitas
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;Laporan Kolektabilitas
</a>
@endif