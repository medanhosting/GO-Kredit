@if(array_intersect($acl_menu['test.jurnal'], $scopes->scopes))
<a href="{{route('jp.index', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['test_jurnal']) ? 'active' : '' }}">
	Jurnal
</a>
@else
<a href="#" class="nav-link disabled">
	Jurnal
</a>
@endif

@if(array_intersect($acl_menu['test.prediksi'], $scopes->scopes))
<a href="{{route('jp.predict', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['predict_jurnal']) ? 'active' : '' }}">
	Prediksi
</a>
@else
<a href="#" class="nav-link disabled">
	Prediksi
</a>
@endif

@if(array_intersect($acl_menu['test.rollback'], $scopes->scopes))
<a href="{{route('db.rollback', ['kantor_aktif_id' => $kantor_aktif_id])}}" class="nav-link px-2 py-1 my-2
px-2 py-1 my-2 {{ in_array(strtolower($active_submenu), ['rollback']) ? 'active' : '' }}">
	Rollback
</a>
@else
<a href="#" class="nav-link disabled">
	Rollback
</a>
@endif