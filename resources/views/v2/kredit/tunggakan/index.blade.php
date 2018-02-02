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
					<h5 class="py-2 pl-2 mb-0">&nbsp;&nbsp;LAPORAN TUNGGAKAN</h5>
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
							<table class="table table-bordered">
								<thead>
									<tr class="text-center">
										<th>Nasabah</th>
										<th>Sejak</th>
										<th class="text-right">Total Tunggakan</th>
										<th class="text-right">Sisa Hutang</th>
										<th>Surat Peringatan</th>
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									@php $lua = null; @endphp
									@forelse($tunggakan as $k => $v)
										@php 
											$pa = \Carbon\Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->format('d/m/Y');
										@endphp
										@if($lua != $pa)
											<tr>
												<td colspan="6" class="bg-light text-center">
													<strong>{{ $pa }}</strong>
												</td>
											</tr>
											@php $lua = $pa; @endphp
										@endif
										<tr class="text-center">
											<td class="text-left">
												<p class="mb-0">{{ $v['kredit']['nasabah']['nama'] }}</p>
												<p class="mb-0">{{ $v['kredit']['nasabah']['telepon'] }}</p>
											</td>
											<td>
												{{Carbon\Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->diffForHumans()}}
											</td>
											<td class="text-right">
												{{ $idr->formatMoneyTo($v['tunggakan']) }}
											</td>
											<td class="text-right">
												{{ $idr->formatMoneyTo($v['sisa_hutang']) }}
											</td>
											<td class="text-left">
												@foreach($v['kredit']['suratperingatan'] as $v0)
													<a href="{{ route('tunggakan.print', ['id' => $v['nomor_kredit'], 'kantor_aktif_id' => $kantor_aktif_id, 'sp_id' => $v0]) }}" target="__blank">Cetak {{ucwords(str_replace('_', ' ', $v0['tag']))}}</a><br>
													<small>{{Carbon\Carbon::createfromformat('d/m/Y H:i', $v0['tanggal'])->diffForHumans()}}</small><br/>
												@endforeach
												@if(is_null($v['nomor_faktur']) && !$read_only)
													<a href="{{route('tunggakan.show', ['id' => $v['nomor_kredit'], 'nomor_kredit' => $v['nomor_kredit'], 'kantor_aktif_id' => $kantor_aktif_id])}}" class="alert-link">
													Keluarkan {{ucwords(str_replace('_', ' ', $v['kredit']['should_issue_surat_peringatan']['keluarkan']))}}
													</a>
												@endif
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
								</tbody>
							</table>
						</div>
					</div>
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