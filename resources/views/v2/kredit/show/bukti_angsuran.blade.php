@inject('carbon', 'Carbon\Carbon')

@component('bootstrap.card')
	<div class="card-header bg-light p-1">
		<h5 class="font-weight-bold mb-0 p-2">BUKTI TRANSAKSI</h5>
	</div>
	<div class="card-body p-0 mb-0">
		<div class="table-responsive" style="max-height: 540px !important;">
			<table class="table table-hover table-bordered mb-0">
				<thead>
					<tr>
						<th class="text-center text-secondary">Tanggal</th>
						<th class="text-left text-secondary">Keterangan</th>
						<th class="text-right text-secondary">Jumlah</th>
						<th class="text-center text-secondary">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@php
						$date_temp = null;
					@endphp
					@forelse($notabayar as $k => $v)
						<tr>
							<td class="text-center">{{ $v['tanggal'] }}</td>
							<td class="text-left">{{$v['deskripsi']}}</td>
							<td class="text-right text-style">{{$v['jumlah']}}</td>
							<td class="text-center">
								@if(str_is($v['jenis'], 'angsuran'))
									<a href="{{ route('angsuran.print', ['id' => $v['morph_reference_id'], 'nomor_faktur' => $v['nomor_faktur'], 'kantor_aktif_id' => $kantor_aktif['id']]) }}" target="__blank" class="text-success">
										<i class="fa fa-external-link"></i>
									</a>
								@elseif(str_is($v['jenis'], 'angsuran_sementara'))
									<a href="{{ route('angsuran.print', ['id' => $v['morph_reference_id'], 'nomor_faktur' => $v['nomor_faktur'], 'kantor_aktif_id' => $kantor_aktif['id'], 'case' => 'sementara']) }}" target="__blank" class="text-success">
										<i class="fa fa-external-link"></i>
									</a>
								@elseif(str_is($v['jenis'], 'denda'))
									<a href="{{ route('angsuran.print', ['id' => $v['morph_reference_id'], 'nomor_faktur' => $v['nomor_faktur'], 'kantor_aktif_id' => $kantor_aktif['id'], 'case' => 'denda']) }}" target="__blank" class="text-success">
										<i class="fa fa-external-link"></i>
									</a>
								@else
									<a href="#"></a>
								@endif
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="4" class="text-center">tidak ada data</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
@endcomponent