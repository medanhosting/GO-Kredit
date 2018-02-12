@if(str_is($putusan['pengajuan']['status_terakhir']['progress'], 'sudah') &&  str_is($putusan['pengajuan']['status_terakhir']['status'], 'setuju'))
	<div class="row">
		<div class="col">
			{!! Form::open(['url' => route('putusan.update', ['id' => $putusan['pengajuan_id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
				<!-- <div class="row">
					<div class="col">
						{!! Form::vText('Tanggal Realisasi', 'tanggal_realisasi', $realisasi['tanggal'], ['class' => 'form-control inline-edit mask-date', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
					</div>
				</div> -->

				<div class="row">
				@foreach($putusan['checklists'] as $kop => $vop)
				<div class="col-6">
					<h5 class="mb-2 pb-2">{{strtoupper($kop)}}</h5>
				@foreach($vop as $kch => $vch)
					@if(!str_is($vch, 'cadangkan'))
						<div class="row">
							<div class="col-9">
								{!! Form::bsCheckbox(strtoupper(str_replace('_',' ',$kch)), 'checklists['.$kop.']['.$kch.']', (str_is('ada', $vch) ? 'tidak_ada' : 'ada'), (str_is('ada', $vch) ? true : false)) !!}
							</div>
							@if(str_is($kop, 'pengikat'))
							<div class="col-3 text-right">
								<a href="{{route('pengajuan.print', ['id' => $putusan['pengajuan_id'], 'mode' => $kch, 'kantor_aktif_id' => $kantor_aktif['id']])}}" target="__blank" class="text-success">
									CETAK
								</a>
							</div>
							@endif
						</div>
					@endif
				@endforeach
				</div>
				@endforeach
				</div>
				<div class="clearfix">&nbsp;</div>
				{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary float-right']) !!}
			{!! Form::close() !!}
		</div>
	</div>
@else
	<div class="row">
	@foreach($putusan['checklists'] as $kop => $vop)
		<div class="col-6">
		<ul class="list-unstyled">
			@foreach($vop as $kch => $vch)
				@if(!str_is($vch, 'cadangkan'))
				<li class="mb-2">
					@if(str_is($kop, 'pengikat'))
					<a href="{{route('putusan.print', ['id' => $putusan['pengajuan_id'], 'mode' => $kch, 'kantor_aktif_id' => $kantor_aktif['id']])}}" target="__blank" class="text-success">
						<i class="fa fa-file-o fa-fw"></i>&nbsp; CETAK {{strtoupper(str_replace('_', ' ', $kch))}}
					</a>
					@else
						<i class="fa fa-file-o fa-fw"></i>&nbsp; {{strtoupper(str_replace('_', ' ', $kch))}}
					@endif
				</li>
				@endif
			@endforeach
		</ul>
		</div>
	@endforeach
	</div>
@endif
