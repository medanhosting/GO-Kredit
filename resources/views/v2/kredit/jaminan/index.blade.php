@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('body')
				<div class="clearfix">&nbsp;</div>
				<div class="row">
					<div class="col-12">
						<form action="{{route('jaminan.index', request()->all())}}" method="GET">
							 <div class="input-group">
							 	@foreach(request()->all() as $k => $v)
							 		@if(!str_is($k, 'q'))
								 		<input type="hidden" name="{{$k}}" value="{{$v}}">
							 		@endif
							 	@endforeach
								<input type="text" name="q" class="form-control" placeholder="cari nomor sertifikat / bpkb atau nomor kredit" value="{{request()->get('q')}}">
								<span class="input-group-btn">
									<button class="btn btn-secondary" type="submit" style="background-color:#fff;color:#aaa;border-color:#ccc;border-radius: 0px">Go!</button>
								</span>
							</div>
						</form>
					</div>
				</div>
				<div class="clearfix">&nbsp;</div>
				<table class="table table-hover">
					<thead>
						<tr class="text-center">
							<th class="text-left">#</th>
							<th>Dokumen</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						@php $lua = null @endphp
						@forelse($jaminan as $k => $v)
							@if($lua != $v['updated_at']->format('d/m/Y'))
								<tr>
									<td colspan="3" class="bg-light">
										{{$v['updated_at']->format('d/m/Y')}}
									</td>
								</tr>
								@php $lua = $v['updated_at']->format('d/m/Y') @endphp
							@endif
							<tr class="text-center">
								<td class="text-left">
									{{$v['nomor_kredit']}}
								</td>
								<td class="text-left">
									@if(str_is($v['documents']['jenis'], 'shm'))
										<h6>SHM</h6>
										Nomor Sertifikat {{$v['documents']['shm']['nomor_sertifikat']}}<br/>
										{{implode(', ', $v['documents']['shm']['alamat'])}}
									@elseif(str_is($v['documents']['jenis'], 'shgb'))
										<h6>SHGB</h6>
										Nomor Sertifikat {{$v['documents']['shgb']['nomor_sertifikat']}}<br/>
										{{implode(', ', $v['documents']['shgb']['alamat'])}}
									@else
										<h6>BPKB</h6>
										Nomor BPKB {{$v['documents']['bpkb']['nomor_bpkb']}}<br/>
										Kendaraan {{str_replace('_', ' ', $v['documents']['bpkb']['jenis'])}} - {{$v['documents']['bpkb']['merk']}} , {{$v['documents']['bpkb']['tipe']}} ({{$v['documents']['bpkb']['tahun']}})
									@endif
								</td>
								<td>
									@if(is_null($v['taken_at']))
										<i class="fa fa-arrow-down text-success"></i>
									@else
										<i class="fa fa-arrow-up text-danger"></i>
									@endif
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="3">
									<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
				@endslot
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push ('js')
@endpush