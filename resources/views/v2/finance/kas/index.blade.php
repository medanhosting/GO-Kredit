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
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0 float-left">&nbsp;&nbsp;BKK/BKM</h5>
				@endslot

				<div class="card-body">
					<div class="row">
						<div class="col-12 text-right">
							<a class="btn btn-primary" href="{{route('kas.create', ['kantor_aktif_id' => $kantor_aktif_id])}}">Bukti Baru</a>
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>
					<table class="table table-hover table-bordered mb-0">
						<thead>
							<tr>
								<th class="text-left text-secondary" width="5">No</th>
								<th class="text-left text-secondary">Tanggal</th>
								<th class="text-left text-secondary">Keterangan</th>
								<th class="text-left text-secondary">Kasir</th>
								<th class="text-left text-secondary">Diterima Oleh/Dibayar Ke</th>
								<th class="text-right text-secondary">Jumlah</th>
								<th class="text-center text-secondary" width="7.5%">Edit</th>
								<th class="text-center text-secondary" width="7.5%">Jurnal</th>
							</tr>
						</thead>
						<tbody>
							@forelse($notabayar as $k => $v)
								<tr>
									<td class="text-left">{{ $notabayar->firstItem() + $k }} </td>
									<td class="text-left">{{$v['hari']}}</td>
									<td class="text-left">{{$v['deskripsi']}}</td>
									<td class="text-left">{{$v['karyawan']['nama']}}</td>
									<td class="text-left">{{$v['karyawan']['penerima']['nama']}}</td>
									<td class="text-right text-style">{{$v['jumlah']}}</td>
									<td class="text-center">
										<a href="{{ route('kas.edit', ['id' => $v['id'], 'nomor_faktur' => $v['nomor_faktur'], 'kantor_aktif_id' => $kantor_aktif['id']]) }}" class="text-success">
											<i class="fa fa-edit"></i>
										</a>
									</td>
									<td class="text-center">
										<a href="{{ route('kas.show', ['id' => $v['id'], 'nomor_faktur' => $v['nomor_faktur'], 'kantor_aktif_id' => $kantor_aktif['id']]) }}" class="text-success">
											<i class="fa fa-book"></i>
										</a>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center">tidak ada data</td>
								</tr>
							@endforelse
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