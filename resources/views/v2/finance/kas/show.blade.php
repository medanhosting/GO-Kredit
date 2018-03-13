@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-line-chart mr-2"></i> KEUANGAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.finance.base')
		</div>
		<div class="col">
			{!! Form::open(['url' => route('jurnal.store', ['kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'POST']) !!}
			
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0 float-left">&nbsp;&nbsp;BKK/BKM BARU</h5>
				@endslot
				
				<div class="card-body">
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-6 text-left">
							<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
							<ul class="list-unstyled fa-ul">
								<li>
									<i class="fa fa-building-o fa-li" style="margin-top: .2rem;"></i>
									{{ implode(' ', $kantor_aktif['alamat']) }}
								</li>
								<li>
									<i class="fa fa-phone fa-li" style="margin-top: .2rem;"></i>
									{{ $kantor_aktif['telepon'] }}
								</li>
							</ul>
						</div>
						<div class="col-6 text-right">
							<div class="row justify-content-end">
								<div class="col-3">Nomor</div>
								<div class="col-5 mb-1">{{$notabayar['nomor_faktur']}}&nbsp;</div>
							</div>
							<div class="row justify-content-end">
								<div class="col-3">Tanggal</div>
								<div class="col-5 mb-1">
									{!! $notabayar['hari'] !!}
								</div>
							</div>
							<div class="row justify-content-end">
								<div class="col-3">Dibayar Kepada</div>
								<div class="col-5 mb-1">
									{!! $notabayar['karyawan']['penerima']['nama'] !!}
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col text-center">
							<h4 class="mb-1">
								<strong>
									{!! $notabayar['deskripsi'] !!}
								</strong>
							</h4>
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>
					
					<div class="row">
						<div class="col-12">
							<table class="table table-hover table-bordered mb-0">
								<thead>
									<tr>
										<th class="text-left text-secondary" width="5">No</th>
										<th class="text-left text-secondary">Keterangan</th>
										<th class="text-right text-secondary">Jumlah</th>
										<th class="text-center text-secondary" colspan="3">Jurnal</th>
									</tr>
								</thead>
								<tbody>
									@forelse($notabayar['details'] as $k => $v)
										<tr>
											<td class="text-left">{{ $k+1 }} </td>
											<td class="text-left">{{$v['deskripsi']}}</td>
											<td class="text-right text-style">{{$v['jumlah']}}</td>
											<td width="15%">
												{!! Form::vSelect(null, 'nomor_perkiraan_deb['.$v['id'].']', $coa_deb, $v['jurnals'][0]['coa']['nomor_perkiraan'], ['class' => 'form-control text-info inline-edit text-left ml-2 mr-2'], true) !!}
											</td>
											<td>pada</td>
											<td width="15%">
												{!! Form::vSelect(null, 'nomor_perkiraan_kre['.$v['id'].']', $coa_kre, $v['jurnals'][1]['coa']['nomor_perkiraan'], ['class' => 'form-control text-info inline-edit text-left ml-2 mr-2'], true) !!}
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="3" class="text-center">tidak ada data</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
						<div class="clearfix">&nbsp;</div>
						<div class="col-12 text-right">
							{{Form::hidden('nomor_faktur', $notabayar['nomor_faktur'])}}
							<button class="btn btn-primary">Simpan</button>
						</div>
					</div>
				</div>
			@endcomponent

			{!! Form::close() !!}	
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush
