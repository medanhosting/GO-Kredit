@inject('terbilang', 'App\Http\Service\UI\Terbilang')
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#rknasabah" role="tab">
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
	<div class="tab-pane" id="rknasabah" role="tabpanel">
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

	<div class="tab-pane" id="fanalisa" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		<div class="row text-justify">
			<div class="col-sm-12 text-left">
				<p class="pt-2 pb-1 mb-0">Dari hasil wawancara dan survey lapangan, analisa yang dapat direkomendasikan tentang nasabah tersebut mempunyai :</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Character
			</div>
			<div class="col-sm-8 text-left">
				{{ucwords(str_replace('_',' ', $permohonan['analisa']['character']))}}
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Capacity
			</div>
			<div class="col-sm-8 text-left">
				{{ucwords(str_replace('_',' ', $permohonan['analisa']['capacity']))}}
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Collateral
			</div>
			<div class="col-sm-8 text-left">
				{{ucwords(str_replace('_',' ', $permohonan['analisa']['collateral']))}}
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Capital
			</div>
			<div class="col-sm-8 text-left">
				{{ucwords(str_replace('_',' ', $permohonan['analisa']['capital']))}}
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Condition
			</div>
			<div class="col-sm-8 text-left">
				{{ucwords(str_replace('_',' ', $permohonan['analisa']['condition']))}}
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-12 text-left">
				<p class="pt-2 pb-1 mb-0">Plafon Kredit yang direkomendasikan :</p>
			</div>
		</div>

		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Jenis Pinjaman
			</div>
			<div class="col-sm-8 text-left">
				{{strtoupper($permohonan['analisa']['jenis_pinjaman'])}}
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Suku Bunga
			</div>
			<div class="col-sm-8 text-left">
				{{$permohonan['analisa']['suku_bunga']}} %
			</div>
		</div>

		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Jangka Waktu
			</div>
			<div class="col-sm-8 text-left">
				{{$permohonan['analisa']['jangka_waktu']}} bulan
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Max. Plafon Kredit
			</div>
			<div class="col-sm-8 text-left">
				{{$permohonan['analisa']['limit_angsuran']}} x {{$permohonan['analisa']['limit_jangka_waktu']}} bulan
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
			</div>
			<div class="col-sm-8 text-left">
				{{$terbilang->formatMoneyTo($terbilang->formatmoneyfrom($permohonan['analisa']['limit_angsuran']) * $permohonan['analisa']['limit_jangka_waktu'])}} 
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Kredit yang diusulkan
			</div>
			<div class="col-sm-8 text-left">
				{{$permohonan['analisa']['kredit_diusulkan']}}
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-12 text-left">
				<p class="pt-2 pb-1 mb-0">Pengembalian Angsuran Kredit Perbulan :</p>
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Angsuran Pokok
			</div>
			<div class="col-sm-8 text-left">
				{{$permohonan['analisa']['angsuran_pokok']}}
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Angsuran Bunga
			</div>
			<div class="col-sm-8 text-left">
				{{$permohonan['analisa']['angsuran_bunga']}}
			</div>
		</div>
		<div class="row text-justify">
			<div class="col-sm-4 text-right">
				Total Angsuran
			</div>
			<div class="col-sm-8 text-left">
				{{$terbilang->formatMoneyTo($terbilang->formatmoneyfrom($permohonan['analisa']['angsuran_pokok']) + $terbilang->formatmoneyfrom($permohonan['analisa']['angsuran_bunga']))}} 
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
		<div class="clearfix">&nbsp;</div>
		<div class="row">
			<div class="col text-right">
				{{Carbon\Carbon::now()->format('d/m/Y')}}
			</div>
		</div>
		<div class="row">
			<div class="col text-right">
				Analis Kredit
				<br/>
				<br/>
				<br/>
				{{$permohonan['analisa']['analis']['nama']}}
			</div>
		</div>

	</div>
</div>