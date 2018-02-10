@inject('idr', 'App\Service\UI\IDRTranslater')
<div class="row">
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center'>
					{{$idr->formatMoneyTo($stat['total_tunggakan'])}}
				</h4><hr> 
			@endslot
			@slot('body') <p class='text-center'>ANGSURAN JATUH TEMPO ({{$stat['jumlah_tunggakan']}})</p> @endslot
		@endcomponent
	</div>
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center'>
					{{$stat['last_pay']['tanggal']}}
				</h4><hr> 
			@endslot
			@slot('body') <p class='text-center'>TANGGAL PEMBAYARAN TERAKHIR</p> @endslot
		@endcomponent
	</div>
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center'>
					{{strtoupper(str_replace('_',' ',$stat['last_sp']['tag']))}}&nbsp;
				</h4><hr> 
			@endslot
			@slot('body') <p class='text-center'>SP TERAKHIR DIKELUARKAN</p> @endslot
		@endcomponent
	</div>
</div>

<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
@component('bootstrap.card')
	@slot('title') 
		<h5 class='text-left'>
			<strong>SURAT PERINGATAN</strong>
		</h5>
	@endslot
	@slot('body')
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Tanggal</th>
					<th>Surat Peringatan</th>
					<th>Tanda Terima</th>
				</tr>
			</thead>
			<tbody>
				@forelse($suratperingatan as $k => $v)
				<tr>
					<td>{{$v['tanggal']}}</td>
					<td class="text-left">
						{{strtoupper(str_replace('_',' ',$v['tag']))}}
						<br/>
						<a href="{{ route('tunggakan.print', ['id' => $v['nomor_kredit'], 'kantor_aktif_id' => $kantor_aktif_id, 'sp_id' => $v['id']]) }}" target="_blank">Cetak {{ucwords(str_replace('_', ' ', $v['tag']))}}</a>
					</td>
					<td>
						@if(!$v['penagihan'])
							{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'nip_karyawan' => Auth::user()['nip'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'tagihan', 'sp_id' => $v['id']]), 'method' => 'PATCH']) !!}
							<div class="row mb-1">
								<div class="col-4">
									<label class="text-uppercase">Nama Penerima</label>
								</div>
								<div class="col-8">
									{!! Form::text('penerima[nama]', $aktif['nasabah']['nama'], ['class' => 'form-control inline-edit border-input text-info pb-1', 'placeholder' => 'Nama penerima']) !!}
								</div>
							</div>

							<div class="row mb-1">
								<div class="col-4">
									<label class="text-uppercase">Tanggal</label>
								</div>
								<div class="col-8">
									{!! Form::text('tanggal', Carbon\Carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control inline-edit border-input text-info pb-1 mask-date', 'placeholder' => 'dd/mm/yyyy']) !!}
								</div>
							</div>

							<div class="row mb-1">
								<div class="col-4">
									<label class="text-uppercase">Nominal</label>
								</div>
								<div class="col-8">
									{!! Form::text('nominal', $tunggakan['tunggakan'], ['class' => 'form-control mask-money inline-edit border-input text-info pb-1', 'placeholder' => 'Nominal'], true) !!}
								</div>
							</div>

							<!-- <div class="row mb-1">
								<div class="col-4">
									<label class="text-uppercase">Disetor Ke</label>
								</div>
								<div class="col-8">
									{!! Form::select('nomor_perkiraan', $a_tt, null, ['class' => 'form-control text-info inline-edit text-right']) !!}
								</div>
							</div> -->

							<div class="row mb-1">
								<div class="col text-right">
									<button class="btn btn-success">Tandai Penagihan</button>
								</div>
							</div>
							
						{!! Form::close() !!}
						@else
							<div class="row mb-1">
								<div class="col-4">
									<label class="text-uppercase">Nama Penerima</label>
								</div>
								<div class="col-8">
									{!! Form::label($v['penagihan']['penerima']['nama']) !!}
								</div>
							</div>

							<div class="row mb-1">
								<div class="col-4">
									<label class="text-uppercase">Tanggal</label>
								</div>
								<div class="col-8">
									{!! Form::label($v['penagihan']['tanggal']) !!}
								</div>
							</div>

							@if($v['penagihan']['nomor_faktur'])
							<div class="row mb-1">
								<div class="col-4">
									<label class="text-uppercase">Nominal</label>
								</div>
								<div class="col-8">
									{!! $v['penagihan']['jumlah'] !!}
								</div>
							</div>
							@endif

							<div class="row mb-1">
								<div class="col-4">
									<label class="text-uppercase">Kolektor</label>
								</div>
								<div class="col-8">
									{!! Form::label($v['penagihan']['karyawan']['nama']) !!}
								</div>
							</div>
							@if($v['penagihan']['nomor_faktur'])
							<div class="row mb-1">
								<div class="col-12">
									<a href="{{ route('angsuran.print', ['id' => $v['nomor_kredit'], 'nomor_faktur' => $v['penagihan']['nomor_faktur'], 'kantor_aktif_id' => $kantor_aktif['id'], 'case' => 'sementara']) }}" target="__blank" class="text-success float-right btn btn-link">
									Cetak Bukti Angsuran Sementara</a>
								</div>
							</div>
							@endif
						@endif
					</td>
				</tr>
				@empty
				<tr>
					<td colspan="3">Tidak ada data</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	@endslot
@endcomponent
