@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-secondary'>
					<span class="text-uppercase">Mutasi Jaminan</span> 
					<small><small>@if($jaminan->currentPage() > 1) Halaman {{$jaminan->currentPage()}} @endif</small></small>
				</h4>
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
								<td colspan="6">
									<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push ('js')
@endpush