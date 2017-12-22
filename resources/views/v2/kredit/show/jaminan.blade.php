<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-hover">
			<thead>
				<tr class="text-center">
					<th>&nbsp;</th>
					<th>Catatan</th>
					<th>Dokumen</th>
				</tr>
			</thead>
			<tbody>
				@php $lua = null @endphp
				@forelse($jaminan as $k => $v)
					@php $pa = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $v['tanggal'])->format('d/m/Y') @endphp
					@if($lua != $pa)
						<tr>
							<td colspan="3" class="bg-light">
								{{$pa}}
							</td>
						</tr>
						@php $lua = $pa @endphp
					@endif
					<tr class="text-center">
						<td>
							@if(str_is($v['tag'], 'in'))
								<i class="fa fa-arrow-down text-success"></i>
							@else
								<i class="fa fa-arrow-up text-danger"></i>
							@endif
						</td>
						<td>
							{{$v['description']}}
						</td>
						<td class="text-right">
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
					</tr>
				@empty
					<tr>
						<td colspan="3">
							<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
