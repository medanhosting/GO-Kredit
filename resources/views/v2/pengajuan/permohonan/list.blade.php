<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#kredit" role="tab">
			Kredit
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#nasabah" role="tab">
			Nasabah 
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#keluarga" role="tab">
			Data Keluarga
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#jaminan" role="tab">
			Jaminan 
		</a>
	</li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<!-- tab kredit -->
	<div class="tab-pane active" id="kredit" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="row">
			<div class="col-sm-4 text-right">
				POKOK PINJAMAN
			</div> 
			<div class="col-sm-8 text-left">
				{{ $permohonan['pokok_pinjaman'] }}
			</div> 
		</div> 
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				KEMAMPUAN ANGSUR
			</div> 
			<div class="col-sm-8 text-left">
				{{ $permohonan['kemampuan_angsur'] }}
			</div> 
		</div> 
	</div>
	<!-- tab nasabah -->
	<div class="tab-pane" id="nasabah" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="row">
			@if(!is_null($permohonan['dokumen_pelengkap']['ktp']))
			<div class="col-3 text-center">
				<img src="{{$permohonan['dokumen_pelengkap']['ktp']}}" class="img-fluid d-block mx-auto" alt="Responsive image" style="max-height:300px;padding:15px;">
			</div>
			@endif
			<div class="col-9">
				@foreach($permohonan['nasabah'] as $k => $v )
					@if($k!='keluarga' && $k!='alamat' && $k!='is_ektp' && $k!='is_lama')
						<div class="row">
							<div class="col-sm-4 text-right">
								{{ strtoupper(str_replace('_', ' ', $k)) }}
							</div> 
							<div class="col-sm-8 text-left">
								{{ ucwords(str_replace('_', ' ', $v)) }}
							</div> 
						</div>
					@endif 
				@endforeach
				<div class="row">
					<div class="col-sm-4 text-right">
						ALAMAT
					</div> 
					<div class="col-sm-8 text-left">
						{{ implode(' ', $permohonan['nasabah']['alamat']) }}
					</div> 
				</div>
			</div>
		</div>
	</div>
	<!-- tab keluarga -->
	<div class="tab-pane" id="keluarga" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($permohonan['nasabah']['keluarga'] as $kk => $kv )
			@foreach($kv as $k => $v)
				<div class="row mt-3">
					<div class="col-sm-4 text-right">
						{{ strtoupper(str_replace('_', ' ', $k)) }}
					</div> 
					<div class="col-sm-8 text-left">
						{{ ucwords(str_replace('_', ' ', $v)) }}
					</div> 
				</div>
			@endforeach
		@endforeach
	</div>
	<!-- tab jaminan -->
	<div class="tab-pane" id="jaminan" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($permohonan['jaminan'] as $k => $v)
			<div class="row mt-3">
				<div class="col-sm-12">
					<p class="text-secondary mb-1"><strong>DATA JAMINAN {{strtoupper(str_replace('_', ' ', $v['jenis']))}} {{($k+1)}}</strong></p>
				</div> 
			</div> 
			@foreach($v['dokumen_jaminan'][$v['jenis']] as $k2 => $v2)
				@if ($k2!='alamat')
					<div class="row">
						<div class="col-sm-4 text-right">
							{{ strtoupper(str_replace('_', ' ', $k2)) }}
						</div> 
						<div class="col-sm-8 text-left" >
							{{ ucwords(str_replace('_', ' ', $v2)) }}
						</div> 
					</div>
				@else
					<div class="row">
						<div class="col-sm-4 text-right">
							{{strtoupper(str_replace('_', ' ', $k2))}}
						</div> 
						<div class="col-sm-8 text-left">
							@foreach($v2 as $k3 => $v3) 
								{{str_replace('_', ' ', $k3)}} {{str_replace('_', ' ', $v3)}}
							@endforeach
						</div> 
					</div>
				@endif
			@endforeach
		@endforeach
	</div>
</div>