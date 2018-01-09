@inject('terbilang', 'App\Http\Service\UI\Terbilang')
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#rknasabah" role="tab">
			Riwayat Kredit Nasabah 
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#fanalisa" role="tab">
			Form Analisa
		</a>
	</li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane fade show active" id="rknasabah" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="row">
			<div class="col-sm-12">
				<table class="table table-sm table-bordered" style="">
					<thead class="thead-default">
						<tr>
							<th style="border:1px #aaa solid" class="text-center">#</th>
							<th style="border:1px #aaa solid" class="text-center">Tanggal Pengajuan</th>
							<th style="border:1px #aaa solid" class="text-center">Pokok Pinjaman</th>
							<th style="border:1px #aaa solid" class="text-center">Status Terakhir</th>
						</tr>
					</thead> 
					<tbody>
						@forelse ($r_nasabah as $k => $v)
						<tr>
							<td style="border:1px #aaa solid" class="text-center">{{ $v['id'] }}</td>
							<td style="border:1px #aaa solid" class="text-center">{{ $v['status_permohonan']['tanggal'] }}</td>
							<td style="border:1px #aaa solid" class="text-center">{{ $v['pokok_pinjaman'] }}</td>
							<td style="border:1px #aaa solid" class="text-center">{{ $v['status_terakhir']['status'] }}</td>
						</tr>
						@empty
							<tr>
								<td style="border:1px #aaa solid" colspan="4" class="text-center"><i class="text-secondary">tidak ada data</i></td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="tab-pane fade" id="fanalisa" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="row text-justify">
			<div class="col-sm-12 text-left">
				<p class="pt-2 pb-1 mb-0">Dari hasil wawancara dan survey lapangan, analisa yang dapat direkomendasikan tentang nasabah tersebut mempunyai :</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Karakter
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ ucfirst(strtolower(str_replace('_',' ', $permohonan['analisa']['character']))) }}
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Kapasitas
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ ucfirst(strtolower(str_replace('_',' ', $permohonan['analisa']['capacity']))) }}
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Kolateral
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ ucfirst(strtolower(str_replace('_',' ', $permohonan['analisa']['collateral']))) }}
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Kapital
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ ucfirst(strtolower(str_replace('_',' ', $permohonan['analisa']['capital']))) }}
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Kondisi
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ ucfirst(strtolower(str_replace('_',' ', $permohonan['analisa']['condition']))) }}
				</p>
			</div>
		</div>
		<div class="row text-justify mt-2">
			<div class="col-sm-12 text-left">
				<p class="pt-2 pb-1 mb-0">Plafon Kredit yang direkomendasikan :</p>
			</div>
		</div>

		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Jenis Pinjaman
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{ strtoupper($permohonan['analisa']['jenis_pinjaman']) }}
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Suku Bunga
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{$permohonan['analisa']['suku_bunga']}} %
				</p>
			</div>
		</div>

		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Jangka Waktu
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{$permohonan['analisa']['jangka_waktu']}} Bulan
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Max. Plafon Kredit
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{$permohonan['analisa']['limit_angsuran']}} x {{$permohonan['analisa']['limit_jangka_waktu']}} bulan = {{$terbilang->formatMoneyTo($terbilang->formatmoneyfrom($permohonan['analisa']['limit_angsuran']) * $permohonan['analisa']['limit_jangka_waktu'])}} 
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Kredit yang diusulkan
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{$permohonan['analisa']['kredit_diusulkan']}}
				</p>
			</div>
		</div>
		<div class="row text-justify mt-2">
			<div class="col-sm-12 text-left">
				<p class="pt-2 pb-1 mb-0">Pengembalian Angsuran Kredit Perbulan :</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Angsuran Pokok
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{$permohonan['analisa']['angsuran_pokok']}}
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Angsuran Bunga
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{$permohonan['analisa']['angsuran_bunga']}}
				</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				<p class="text-secondary mb-1">
					Total Angsuran
				</p>
			</div>
			<div class="col-sm-8 text-left">
				<p class="mb-1">
					{{$terbilang->formatMoneyTo($terbilang->formatmoneyfrom($permohonan['analisa']['angsuran_pokok']) + $terbilang->formatmoneyfrom($permohonan['analisa']['angsuran_bunga']))}} 
				</p>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
		<div class="clearfix">&nbsp;</div>
		<div class="row">
			<div class="col text-right pr-5">
				<p class="mb-0">
					{{Carbon\Carbon::now()->format('d/m/Y')}}
				</p>
				<p class="mb-0">Analis Kredit</p>
				<div class="clearfix">&nbsp;</div>
				<div class="clearfix">&nbsp;</div>
				<div class="clearfix">&nbsp;</div>
				<p>
					{{ $permohonan['analisa']['analis']['nama'] }}
				</p>
			</div>
		</div>

	</div>
</div>