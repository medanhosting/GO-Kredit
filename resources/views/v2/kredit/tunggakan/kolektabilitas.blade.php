@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0">&nbsp;&nbsp;LAPORAN KOLEKTABILITAS</h5>
				@endslot

				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							{{-- SEARCH --}}
							{!! Form::open(['method' => "GET"]) !!}
								@foreach(request()->all() as $k => $v)
									@if(!str_is($k, 'q'))
										<input type="hidden" name="{{$k}}" value="{{$v}}">
									@endif
								@endforeach
								<div class="form-row align-items-end">
									<div class='col-sm-2 order-1'>
										{!! Form::bsText('Cari Tanggal', 'q', null, ['placeholder' => 'sebelum tanggal', 'class' => 'mask-date form-control']) !!}
									</div>
									<div class='col-auto order-3'>
										<div class="form-group">
											<label for="">&nbsp;</label>
											{!! Form::bsSubmit('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary']) !!}
										</div>
									</div>
								</div>
							{!! Form::close() !!}

							<div class="clearfix">&nbsp;</div>
							<table class="table table-bordered" style="font-size:10px;">
								<thead>
									<tr class="text-center">
										<th>Kredit/Nasabah</th>
										<th>Saldo Pinjaman</th>
										<th>Coll 1</th>
										<th>Coll 2</th>
										<th>Coll 3</th>
										<th>Coll 4</th>
										<th>Coll 5</th>
										<th>&nbsp;</th>
									</tr>
									<tr class="text-center">
										<th></th>
										<th></th>
										<th>Lancar</th>
										<th>2-3 Bulan Menunggak</th>
										<th>4-6 Bulan Menunggak</th>
										<th>6-8 Bulan Menunggak</th>
										<th> 8 Bulan Menunggak</th>
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									@forelse($tunggakan as $k => $v)
										<tr class="text-center">
											<td class="text-left">
												<p class="mb-0">{{ $v['nomor_kredit'] }}</p>
												<p class="mb-0">{{ $v['nasabah']['nama'] }}</p>
												<p class="mb-0">({{ $v['nasabah']['telepon'] }})</p>
											</td>
											<td class="text-right">
												{{ $idr->formatMoneyTo($v['kol_1'] + $v['kol_2'] + $v['kol_3'] + $v['kol_4'] + $v['kol_5']) }}
											</td>
											<td class="text-right">
												{{ $idr->formatMoneyTo($v['kol_1']) }}
											</td>
											<td class="text-right">
												{{ $idr->formatMoneyTo($v['kol_2']) }}
											</td>
											<td class="text-right">
												{{ $idr->formatMoneyTo($v['kol_3']) }}
											</td>
											<td class="text-right">
												{{ $idr->formatMoneyTo($v['kol_4']) }}
											</td>
											<td class="text-right">
												{{ $idr->formatMoneyTo($v['kol_5']) }}
											</td>
											<td>
												<a href="{{route('kredit.show', ['id' => $v['kredit']['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'penagihan'])}}">Lihat Kredit</a>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="6" class="text-center">
												<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
											</td>
										</tr>
									@endforelse
									<tr class="text-center">
										<td class="text-left">
											<strong>TOTAL</strong>
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyTo($stat['all']) }}
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyTo($stat['kol_1']) }}
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyTo($stat['kol_2']) }}
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyTo($stat['kol_3']) }}
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyTo($stat['kol_4']) }}
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyTo($stat['kol_5']) }}
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr class="text-center">
										<td class="text-left" colspan="2">
											<strong>Cadangan Resiko Penghapusan</strong>
										</td>
										<td class="text-right">
											{{ $perc['kol_1'] }} %
										</td>
										<td class="text-right">
											{{ $perc['kol_2'] }} %
										</td>
										<td class="text-right">
											{{ $perc['kol_3'] }} %
										</td>
										<td class="text-right">
											{{ $perc['kol_4'] }} %
										</td>
										<td class="text-right">
											{{ $perc['kol_5'] }} %
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr class="text-center">
										<td class="text-left" colspan="2">
											<strong>Jumlah Resiko Penghapusan</strong>
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyto(($perc['kol_1'] * $stat['kol_1'])/100) }}
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyto(($perc['kol_2'] * $stat['kol_2'])/100) }}
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyto(($perc['kol_3'] * $stat['kol_3'])/100) }}
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyto(($perc['kol_4'] * $stat['kol_4'])/100) }}
										</td>
										<td class="text-right">
											{{ $idr->formatMoneyto(($perc['kol_5'] * $stat['kol_5'])/100) }}
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr class="text-center">
										<td class="text-left" colspan="2">
											<strong>Jumlah Kolektibilitas I - V</strong>
										</td>
										<td class="text-right" colspan="5">
											{{ $idr->formatMoneyto($side) }}
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr class="text-center">
										<td class="text-left" colspan="2">
											<strong>Rasio Cadangan Penghapusan (%)</strong>
										</td>
										<td class="text-right" colspan="5">
											{{ ceil($side/$stat['all'] * 100) }}
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				@endcomponent
			</div>
		</div>
		@include('v2.kredit.modal.konfirmasi_tunggakan')
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush