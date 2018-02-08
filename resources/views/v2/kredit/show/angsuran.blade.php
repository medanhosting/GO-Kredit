@inject('carbon', 'Carbon\Carbon')

@component('bootstrap.card')
	@slot('title') 
		<h5 class='text-left'>
			<strong>BUKTI TRANSAKSI</strong>
		</h5>
	@endslot
	@slot('body')
		<div class="table-responsive" style="max-height: 540px !important;">
			<table class="table table-hover">
				<thead>
					<tr>
						<th class="text-let">No. Faktur</th>
						{{--  <th class="text-left">TANGGAL</th>  --}}
						<th class="text-left">KETERANGAN</th>
						<th class="text-right">JUMLAH</th>
						<th class="text-center">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@foreach($notabayar as $k => $v)
						@php
							$temp_date = null;
							$date = $carbon::parse($v['tanggal_bayar']);
							$length = $date->diffInDays($temp_date);
						@endphp

						@if ($length != 0)
							<tr>
								<td class="bg-light" colspan="4">{{ $date->format('d/m/y') }}</td>
							</tr>
						@else
							@php
								$temp_date = $carbon::parse($v['tanggal_bayar']);
							@endphp
						@endif
						
						@if ($loop->first)
							<tr>
								<td class="bg-light text-center font-weight-bold" colspan="4">{{ $date->format('d/m/Y') }}</td>
							</tr>
						@endif

						<tr>
							<td class="text-left" style="width: 15%; word-wrap: break-word;">{{ $v['nomor_faktur'] }}</td>
							{{--  <td class="text-left">{{$carbon::parse($v['tanggal_bayar'])->format('d/m/Y')}}</td>  --}}
							<td class="text-left">{{$v['deskripsi']}}</td>
							<td class="text-right">{{$v['jumlah']}}</td>
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
					@endforeach
				</tbody>
			</table>
		</div>
	@endslot
@endcomponent


