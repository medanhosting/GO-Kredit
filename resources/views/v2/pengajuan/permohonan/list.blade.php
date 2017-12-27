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

	<!-- TAB KREDIT -->
	<div class="tab-pane active" id="kredit" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="row">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					POKOK PINJAMAN
				</p>
			</div> 
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ $permohonan['pokok_pinjaman'] }}
				</p>
			</div> 
		</div> 
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					KEMAMPUAN ANGSUR
				</p>
			</div> 
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ $permohonan['kemampuan_angsur'] }}
				</p>
			</div> 
		</div> 
	</div>

	<!-- TAB NASABAH -->
	<div class="tab-pane" id="nasabah" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="row">
			@if(!is_null($permohonan['dokumen_pelengkap']['ktp']))
			<div class="col-4 text-center">
				<img src="{{$permohonan['dokumen_pelengkap']['ktp']}}" class="img-fluid d-block mx-auto" alt="Responsive image" style="max-height:300px;padding:15px;">
			</div>
			@endif
			<div class="col-8">
				@foreach($permohonan['nasabah'] as $k => $v )
					@if($k!='keluarga' && $k!='alamat' && $k!='is_ektp' && $k!='is_lama')
						<div class="row">
							<div class="col-sm-4 text-right">
								<p class="text-secondary mb-1">
									{{ strtoupper(str_replace('_', ' ', $k)) }}
								</p>
							</div> 
							<div class="col-sm-8 text-left">
								<p class="mb-1">
									{{ ucwords(str_replace('_', ' ', $v)) }}
								</p>
							</div> 
						</div>
					@endif 
				@endforeach
				<div class="row">
					<div class="col-sm-4 text-right">
						<p class="text-secondary mb-1">
							ALAMAT
						</p>
					</div> 
					<div class="col-sm-8 text-left">
						<p class="mb-1">
							{{ implode(' ', $permohonan['nasabah']['alamat']) }}
						</p>
					</div> 
				</div>
			</div>
		</div>
	</div>

	<!-- TAB KELUARGA -->
	<div class="tab-pane" id="keluarga" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($permohonan['nasabah']['keluarga'] as $kk => $kv )
			@foreach($kv as $k => $v)
				<div class="row">
					<div class="col-sm-4 text-right">
						<p class="text-secondary mb-1">
							{{ strtoupper(str_replace('_', ' ', $k)) }}
						</p>
					</div> 
					<div class="col-sm-8 text-left">
						<p class="mb-1">
							{{ ucwords(str_replace('_', ' ', $v)) }}
						</p>
					</div> 
				</div>
			@endforeach
			<div class="clearfix">&nbsp;</div>
		@endforeach
	</div>
	
	<!-- TAB JAMINAN -->
	<div class="tab-pane" id="jaminan" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($permohonan['jaminan'] as $k => $v)
			<div class="row mt-3">
				<div class="col-sm-12">
					<p class="text-secondary mb-1"><strong><u>DATA JAMINAN {{strtoupper(str_replace('_', ' ', $v['jenis']))}} {{($k+1)}}</u></strong></p>
				</div> 
			</div> 
			@foreach($v['dokumen_jaminan'][$v['jenis']] as $k2 => $v2)
				@if ($k2!='alamat')
					<div class="row">
						<div class="col-sm-4 text-right">
							<p class="text-secondary mb-1">
								{{ strtoupper(str_replace('_', ' ', $k2)) }}
							</p>
						</div> 
						<div class="col-sm-8 text-left" >
							<p class="mb-1">
								@if (in_array($k2, ['luas_tanah', 'luas_bangunan']))
									{{ ucwords(str_replace('_', ' ', $v2)) }} M<sup>2</sup>
								@else
									{{ ucwords(str_replace('_', ' ', $v2)) }}
								@endif
							</p>
						</div> 
					</div>
				@else
					<div class="row">
						<div class="col-sm-4 text-right">
							<p class="text-secondary mb-1">
								{{strtoupper(str_replace('_', ' ', $k2))}}
							</p>
						</div> 
						<div class="col-sm-8 text-left">
							<p class="mb-1">
								@foreach($v2 as $k3 => $v3) 
									{{str_replace('_', ' ', $k3)}} {{str_replace('_', ' ', $v3)}}
								@endforeach
							</p>
						</div> 
					</div>
				@endif
			@endforeach
		@endforeach
	</div>
</div>