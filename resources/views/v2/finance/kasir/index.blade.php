@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-line-chart mr-2"></i> KEUANGAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.finance.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-3 mb-0 float-left">&nbsp;&nbsp;LAPORAN KAS HARIAN</h5>
					<a href="{{ route('kasir.print', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" target="__blank" class="text-success float-right btn btn-link">
						<i class="fa fa-file-o fa-fw"></i>&nbsp; CETAK LAPORAN KAS HARIAN
					</a>
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
									<div class='col-sm-2'>
										{!! Form::bsText('Cari Tanggal', 'q', $dday->format('d/m/Y'), ['placeholder' => 'tanggal', 'class' => 'mask-date form-control']) !!}
									</div>
									<div class='col-auto'>
										<div class="form-group">
											<label for="">&nbsp;</label>
											{!! Form::bsSubmit('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary']) !!}
										</div>
									</div>
								</div>
							{!! Form::close() !!}
						</div>
					</div>
					<hr class="mb-5"/>
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12 text-center">
							<h4>LAPORAN KAS HARIAN</h4>
							<h4>PADA PENUTUPAN KAS, TANGGAL</h4>
							<h4>{{$dday->format('d/m/Y')}}</h4>
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>
					<table class="table">
						<tbody>
							<tr>
								<td>NERACA</td>
								<td>{{$dbefore->format('d/m/Y')}}</td>
								<td class="text-right">{{$idr->formatmoneyto($balance)}}</td>
							</tr>

							<tr>
								<td>Penerimaan Kas</td>
								<td></td>
								<td class="text-right">{{$idr->formatmoneyto($in)}}</td>
							</tr>

							<tr>
								<td>TOTAL</td>
								<td></td>
								<td class="text-right">{{$idr->formatmoneyto($balance + $in)}}</td>
							</tr>

							<tr>
								<td>Pengeluaran Kas</td>
								<td></td>
								<td class="text-right">{{$idr->formatmoneyto($out)}}</td>
							</tr>

							<tr>
								<td>NERACA</td>
								<td>{{$dday->format('d/m/Y')}}</td>
								<td class="text-right">{{$idr->formatmoneyto($balance + $in + $out)}}</td>
							</tr>
						</tbody>
					</table>

					<table class="table table-bordered">
						<thead>
							<tr class="text-center">
								<th>Diperiksa</th>
								<th>Disetujui</th>
								<th>Diterima</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><br/><br/><br/></td>
								<td><br/><br/><br/></td>
								<td><br/><br/><br/></td>
							</tr>
						</tbody>
					</table>

					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12 text-left">
							<h4>BERITA ACARA PEMERIKSAAN KAS</h4>
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-6 text-left">
							<h5>Pada hari ini, __________________________</h5>
						</div>
						<div class="col-6 text-right">
							<h5>Pukul ________ WIB</h5>
						</div>
					</div>
					<div class="row">
						<div class="col-12 text-left">
							Kami yang bertanda tangan dibawah ini telah melakukan pemeriksaan Fisik Kas sebagai berikut :
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-12 text-left">
							<strong>Uang Kertas</strong>
						</div>
					</div>
					<table class="table table-bordered">
						<thead>
							<tr class="text-center">
								<th>Pecahan</th>
								<th>Jumlah</th>
								<th>Nominal</th>
							</tr>
						</thead>
						<tbody>
							<tr class="text-right">
								<td>Rp 100.000</td>
								<td>______ lembar</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td>Rp 50.000</td>
								<td>______ lembar</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td>Rp 20.000</td>
								<td>______ lembar</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td>Rp 10.000</td>
								<td>______ lembar</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td>Rp 5.000</td>
								<td>______ lembar</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td>Rp 2.000</td>
								<td>______ lembar</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td>Rp 1.000</td>
								<td>______ lembar</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td colspan="2"><strong>Subtotal</strong></td>
								<td></td>
							</tr>
						</tbody>
					</table>
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-12 text-left">
							<strong>Uang Logam</strong>
						</div>
					</div>
					<table class="table table-bordered">
						<thead>
							<tr class="text-center">
								<th>Pecahan</th>
								<th>Jumlah</th>
								<th>Nominal</th>
							</tr>
						</thead>
						<tbody>
							<tr class="text-right">
								<td>Rp 1.000</td>
								<td>______ keping</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td>Rp 500</td>
								<td>______ keping</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td>Rp 200</td>
								<td>______ keping</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td>Rp 100</td>
								<td>______ keping</td>
								<td></td>
							</tr>
							<tr class="text-right">
								<td colspan="2"><strong>Subtotal</strong></td>
								<td></td>
							</tr>
						</tbody>
					</table>

					<table class="table">
						<tbody>
							<tr>
								<td colspan="2" class="text-right">TOTAL</td>
								<td class="text-right"></td>
							</tr>
							<tr>
								<td colspan="2" class="text-right">SALDO NERACA</td>
								<td class="text-right">{{$idr->formatmoneyto($balance + $in + $out)}}</td>
							</tr>
							<tr>
								<td colspan="2" class="text-right">SELISIH KAS</td>
								<td class="text-right"></td>
							</tr>
						</tbody>
					</table>

					<div class="row">
						<div class="col-12 text-left">
							Terbilang :
							<hr/>
						</div>
					</div>

					<div class="row">
						<div class="col-12 text-left">
							Catatan :
							<hr/>
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>

					<table class="table table-bordered">
						<thead>
							<tr class="text-center">
								<th>Diperiksa</th>
								<th>Disetujui</th>
								<th>Diterima</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><br/><br/><br/></td>
								<td><br/><br/><br/></td>
								<td><br/><br/><br/></td>
							</tr>
						</tbody>
					</table>
				</div>
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush