<div class="clearfix">&nbsp;</div>

<div class="row">
	<div class="col-sm-12 text-center">
		<h5>PERMOHONAN KREDIT</h5>
	</div>
</div> 

<div id="accordion" role="tablist" aria-multiselectable="true">
	<div class="card" style="background-color:#fff;border:none;border-radius:0">
		<div class="card-header" role="tab" id="character" style="background-color:#aaa;border-bottom:1px solid #eee;border-radius:0">
			<div class="row">
				<div class="col-sm-8">
					KREDIT
				</div>
				<div class="col-sm-4 text-right">
					<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsekredit" aria-expanded="false" aria-controls="collapsekredit" style="color:black"><i class="fa fa-chevron-down"></i></a>
				</div>
			</div>
	    </div>
		<div id="collapsekredit" class="collapse" role="tabpanel" aria-labelledby="kredit">
			<div class="card-block" style="padding-bottom:20px;">
				<div class="row text-justify" style="border-bottom:1px solid #aaa;">
					<div class="col-sm-4 text-left">
						POKOK PINJAMAN
					</div> 
					<div class="col-sm-8 text-right">
						{{$permohonan['pokok_pinjaman']}}
					</div> 
				</div> 
				<div class="row text-justify" style="border-bottom:1px solid #aaa;">
					<div class="col-sm-4 text-left">
						KEMAMPUAN ANGSUR
					</div> 
					<div class="col-sm-8 text-right">
						{{$permohonan['kemampuan_angsur']}}
					</div> 
				</div> 
			</div>
		</div>
	</div>
	<div class="card" style="background-color:#fff;border:none;border-radius:0">
		<div class="card-header" role="tab" id="character" style="background-color:#aaa;border-bottom:1px solid #eee;border-radius:0">
			<div class="row">
				<div class="col-sm-8">
					NASABAH
				</div>
				<div class="col-sm-4 text-right">
					<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsenasabah" aria-expanded="false" aria-controls="collapsenasabah" style="color:black"><i class="fa fa-chevron-down"></i></a>
				</div>
			</div>
	    </div>
		<div id="collapsenasabah" class="collapse" role="tabpanel" aria-labelledby="nasabah">
			<div class="card-block" style="border-bottom:1px solid #bbb;padding-bottom:20px;">
				@foreach($permohonan['nasabah'] as $k => $v )
					@if($k!='keluarga' && $k!='alamat' && $k!='is_ektp' && $k!='is_lama')
						<div class="row text-justify" style="border-bottom:1px solid #aaa;">
							<div class="col-sm-6 text-left">
								{{strtoupper(str_replace('_', ' ', $k))}}
							</div> 
							<div class="col-sm-6 text-right">
								{{str_replace('_', ' ', $v)}}
							</div> 
						</div>
					@endif 
				@endforeach
				<div class="row text-justify" style="border-bottom:1px solid #aaa;">
					<div class="col-sm-6 text-left">
						ALAMAT
					</div> 
					<div class="col-sm-6 text-right">
						{{implode(' ', $permohonan['nasabah']['alamat'])}}
					</div> 
				</div>
			</div>
		</div>
	</div>
	<div class="card" style="background-color:#fff;border:none;border-radius:0">
		<div class="card-header" role="tab" id="character" style="background-color:#aaa;border-bottom:1px solid #eee;border-radius:0">
			<div class="row">
				<div class="col-sm-8">
					KELUARGA
				</div>
				<div class="col-sm-4 text-right">
					<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsekeluarga" aria-expanded="false" aria-controls="collapsekeluarga" style="color:black"><i class="fa fa-chevron-down"></i></a>
				</div>
			</div>
	    </div>
		<div id="collapsekeluarga" class="collapse" role="tabpanel" aria-labelledby="keluarga">
			<div class="card-block" style="border-bottom:1px solid #bbb;padding-bottom:20px;">
				@foreach($permohonan['nasabah']['keluarga'] as $kk => $kv )
					@foreach($kv as $k => $v)
						<div class="row text-justify" style="border-bottom:1px solid #aaa;">
							<div class="col-sm-6 text-left">
								{{strtoupper(str_replace('_', ' ', $k))}}
							</div> 
							<div class="col-sm-6 text-right">
								{{str_replace('_', ' ', $v)}}
							</div> 
						</div>
					@endforeach
				@endforeach
			</div>
		</div>
	</div>
	<div class="card" style="background-color:#fff;border:none;border-radius:0">
		<div class="card-header" role="tab" id="character" style="background-color:#aaa;border-bottom:1px solid #eee;border-radius:0">
			<div class="row">
				<div class="col-sm-8">
					JAMINAN
				</div>
				<div class="col-sm-4 text-right">
					<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsejaminan" aria-expanded="false" aria-controls="collapsejaminan" style="color:black"><i class="fa fa-chevron-down"></i></a>
				</div>
			</div>
	    </div>
		<div id="collapsejaminan" class="collapse" role="tabpanel" aria-labelledby="jaminan">
			<div class="card-block" style="border-bottom:1px solid #bbb;padding-bottom:20px;">
				@foreach($permohonan['jaminan'] as $k => $v)
					<div class="row text-justify">
						<div class="col-sm-12 text-center">
							<div class="row" style="background-color:#eee;padding:5px;">
								<div class="col-sm-12">
									DATA JAMINAN {{strtoupper(str_replace('_', ' ', $v['jenis']))}} {{($k+1)}}
								</div> 
							</div> 
							@foreach($v['dokumen_jaminan'][$v['jenis']] as $k2 => $v2)
								@if($k2!='alamat')
									<div class="row text-justify" style="margin:10px 0px @if($k2 =='tahun_perolehan') -1px @else 10px @endif -15px;border-bottom:1px solid #eee;">
										<div class="col-sm-6 text-left">
											{{strtoupper(str_replace('_', ' ', $k2))}}
										</div> 
										<div class="col-sm-6 text-right" >
											{{str_replace('_', ' ', $v2)}}
										</div> 
									</div>
								@else
									<div class="row text-justify" style="margin:10px 0px 10px -15px;border-bottom:1px solid #eee;">
										<div class="col-sm-6 text-left">
											{{strtoupper(str_replace('_', ' ', $k2))}}
										</div> 
										<div class="col-sm-6 text-right">
											@foreach($v2 as $k3 => $v3) 
												{{str_replace('_', ' ', $k3)}} {{str_replace('_', ' ', $v3)}}
											@endforeach
										</div> 
									</div>
								@endif

							@endforeach
						</div> 
					</div> 
				@endforeach

			</div>
		</div>
	</div>

</div>
