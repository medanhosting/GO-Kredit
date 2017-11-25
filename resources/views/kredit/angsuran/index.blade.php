@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-secondary'>
					<span class="text-uppercase">Daftar Angsuran</span> 
					<small><small>@if($angsuran->currentPage() > 1) Halaman {{$angsuran->currentPage()}} @endif</small></small>
				</h4>
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
						@foreach($angsuran as $k => $v)
							<tr>
								<td>
									{{$v['nomor_kredit']}}
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
									<a href="{{route('kredit.angsuran.show', ['id' => $v['id'], 'kantor_aktif_id' => $v['kode_kantor']])}}">Bayar</a>
								</td>
							</tr>
						@endforeach
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