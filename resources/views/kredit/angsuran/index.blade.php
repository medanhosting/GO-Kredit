@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-secondary'>
					<span class="text-uppercase">Daftar Angsuran</span> 
					<small><small>@if($angsuran->currentPage() > 1) Halaman {{$angsuran->currentPage()}} @endif</small></small>
				</h4>
				<div class="row">
					<div class="col-12">
						<form action="{{route('kredit.angsuran.index', request()->all())}}" method="GET">
							 <div class="input-group">
							 	@foreach(request()->all() as $k => $v)
							 		@if(!str_is($k, 'q'))
								 		<input type="hidden" name="{{$k}}" value="{{$v}}">
							 		@endif
							 	@endforeach
								<input type="text" name="q" class="form-control" placeholder="cari nama nasabah atau nomor kredit" value="{{request()->get('q')}}">
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
						<tr>
							<th>#</th>
							<th>Nasabah</th>
							<th>Total Angsuran</th>
							<th>Jatuh Tempo</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						@forelse($angsuran as $k => $v)
							<tr>
								<td>
									{{$v['nomor_kredit']}} / {{$v['id']}}
								</td>
								<td>
									{{$v['kredit']['nasabah']['nama']}}
								</td>
								<td>
									{{$idr->formatMoneyTo($v['amount'])}}
								</td>
								<td>
									{{Carbon\Carbon::createFromFormat('d/m/Y H:i', $v['issued_at'])->adddays(\Config::get('kredit.batas_pembayaran_angsuran_hari'))->format('d/m/Y H:i')}}
								</td>
								<td>
									<a href="{{route('kredit.angsuran.show', ['id' => $v['id'], 'kantor_aktif_id' => $v['kode_kantor']])}}">
										@if(is_null($v['paid_at']))
											Bayar
										@else
											Lihat
										@endif
									</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="5">
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