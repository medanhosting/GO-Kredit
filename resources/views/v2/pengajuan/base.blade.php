@if(array_intersect($acl_menu['pengajuan.pengajuan'], $scopes->scopes))
<a href="{{route('pengajuan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['pengajuan']) ? 'active' : '' }}">
<i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
@else
<a href="#" class="nav-link disabled"><i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
@endif

@if(array_intersect($acl_menu['pengajuan.putusan'], $scopes->scopes))
<a href="{{route('putusan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['putusan']) ? 'active' : '' }}">
	<i class="fa fa-hourglass-end"></i>&nbsp;&nbsp;Putusan 
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-hourglass-end"></i>&nbsp;&nbsp;Putusan 
</a>
@endif

@if(array_intersect($acl_menu['pengajuan.simulasi'], $scopes->scopes))
<a href="{{route('simulasi.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['simulasi']) ? 'active' : '' }}">
	<i class="fa fa-calculator"></i>&nbsp;&nbsp;Simulasi
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-calculator"></i>&nbsp;&nbsp;Simulasi
</a>
@endif

@if(array_intersect($acl_menu['pengajuan.permohonan'], $scopes->scopes))
<a href="{{route('pengajuan.create', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2  {{ in_array(strtolower($active_submenu), ['permohonan']) ? 'active' : '' }}">
	<i class="fa fa-plus"></i>&nbsp;&nbsp;Permohonan Baru
</a>
@else
<a href="#" class="nav-link disabled">
	<i class="fa fa-plus"></i>&nbsp;&nbsp;Permohonan Baru
</a>
@endif
