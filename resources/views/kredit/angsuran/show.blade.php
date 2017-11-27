@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<ul class="nav text-right">
			@if(is_null($angsuran['paid_at']))
			@if(!isset($lunas))
			<li class="nav-item">
				<a class="nav-link modal_angsuran" data-toggle="modal" data-target="#bayar-angsuran" href="#" data-action="{{route('kredit.angsuran.update', ['id' => $angsuran['id'], 'kantor_aktif_id' => $angsuran['kode_kantor']])}}">TANDAI LUNAS</a>
			</li>
			@else
			<li class="nav-item">
				<a class="nav-link modal_angsuran" data-toggle="modal" data-target="#bayar-angsuran" href="#" data-action="{{route('kredit.angsuran.update', ['id' => $angsuran['id'], 'kantor_aktif_id' => $angsuran['kode_kantor'], 'pelunasan' => 1])}}">TANDAI LUNAS</a>
			</li>
			@endif
			@else
			<li class="nav-item">
				<a class="nav-link disabled" href="#">LUNAS</a>
			</li>
			@endif	
			@if(is_null($angsuran['paid_at']))
			<li class="nav-item">
				<a class="nav-link disabled" href="#">CETAK BUKTI BAYAR</a>
			</li>
			@else
			<li class="nav-item">
				<a class="nav-link" href="{{route('kredit.angsuran.print', ['id' => $angsuran['id'], 'kantor_aktif_id' => $angsuran['kode_kantor']])}}">CETAK BUKTI BAYAR</a>
			</li>
			@endif	
			@if(is_null($angsuran['paid_at']) && $angsuran['issued_at'] >= $today->subDays(Config::get('kredit.batas_pembayaran_angsuran_hari'))->format('d/m/Y H:i'))
			<li class="nav-item">
				@if(isset($lunas))
				<a class="nav-link" href="{{route('kredit.angsuran.show', ['id' => $angsuran['id'], 'kantor_aktif_id' => $kantor_aktif_id])}}">HITUNG TANPA PELUNASAN</a>
				@else
				<a class="nav-link" href="{{route('kredit.angsuran.show', ['id' => $angsuran['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'pelunasan' => true])}}">HITUNG PELUNASAN</a>
				@endif
			</li>
			@else
			<li class="nav-item">
				<a class="nav-link disabled" href="#">HITUNG PELUNASAN</a>
			</li>
			@endif
		</ul>
	</div>
	<div class="clearifx">&nbsp;</div>
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col-sm-4">
			<h4>{{$kantor_aktif['nama']}}</h4>
			{{$kantor_aktif['alamat']['alamat']}}
			<br>
			Telepon {{$kantor_aktif['telepon']}}
			</div>
			<div class="col-sm-4">
				<h1>KWITANSI</h1>
			</div>
			<div class="col-sm-2">
			TANGGAL 
			<br>
			FAKTUR NO 
			<br>
			NO NASABAH 
			</div>
			<div class="col-sm-2">
			: {{is_null($angsuran['issued_at']) ? Carbon\Carbon::now()->format('d/m/Y H:i') : $angsuran['issued_at'] }}
			<br>
			: {{$angsuran['nomor_kredit']}} / {{$angsuran['id']}}
			<br>
			: {{$angsuran['kredit']['nasabah']['id']}}
			</div>
		</div>
		<div class="clearifx">&nbsp;</div>
		<div class="clearifx">&nbsp;</div>
		<div class="row">
			<div class="col-sm-2">
			DITERIMA DARI
			<br>SEJUMLAH UANG
			</div>
			<div class="col-sm-2">
			 : {{$angsuran['kredit']['nasabah']['nama']}}
			 <br>
			 @if(isset($lunas))
			 : {{$idr->formatMoneyTo($angsuran['amount'] + $lunas)}}
			 @else
			 : {{$idr->formatMoneyTo($angsuran['amount'])}}
			 @endif
			</div>
			<div class="col-sm-8 text-left">
			 @if(isset($lunas))
			{{ucwords($idr::terbilang($angsuran['amount'] + $lunas))}} Rupiah
			 @else
			{{ucwords($idr::terbilang($angsuran['amount']))}} Rupiah
			 @endif
			</div>
		</div>
		<div class="clearifx">&nbsp;</div>
		<div class="clearifx">&nbsp;</div>
		<div class="row">
			<div class="col-sm-12">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th width="5">No</th>
							<th>Keterangan</th>
							<th class="text-right">Jumlah</th>
						</tr>
					</thead>
					<tbody>
						@foreach($angsuran->details as $k => $v)
						<tr>
							<td>{{$k+1}}</td>
							<td>{{$v['description']}}</td>
							<td class="text-right">{{$v['amount']}}</td>
						</tr>
						@endforeach
						@if(isset($lunas))
							<tr>
								<td>{{count($angsuran->details) + 1}}</td>
								<td>Pelunasan Angsuran</td>
								<td class="text-right">{{$idr->formatMoneyTo($lunas)}}</td>
							</tr>
						@endif
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2">Total</th>
							@if(isset($lunas))
							<th class="text-right">{{$idr->formatMoneyTo($angsuran['amount'] + $lunas)}}</th>
							@else
							<th class="text-right">{{$idr->formatMoneyTo($angsuran['amount'])}}</th>
							@endif
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2">
				TOTAL HUTANG<br>
				TOTAL ANGSURAN<br>
				SISA HUTANG<br><br>
				STATUS<br>
				JATUH TEMPO
			</div>
			<div class="col-sm-2">
				{{$t_hutang}}<br>
				{{$t_lunas}}<br>
				{{$s_hutang}}<br><br>
				{{(is_null($angsuran->paid_at) ? 'BELUM LUNAS' : 'LUNAS')}}<br>
				{{$angsuran->jatuh_tempo}}
			</div>
			<div class="col-sm-4">
			CATATAN
			<div style="border:1px solid #000">
				&nbsp;<br/>
				&nbsp;<br/>
				&nbsp;<br/>
				&nbsp;<br/>
			</div>
			</div>
			<div class="col-sm-4 text-right">
			{{is_null($angsuran['paid_at']) ? Carbon\Carbon::now()->format('d/m/Y H:i') : $angsuran['paid_at'] }}
			<br/>
			<br/>
			<br/>
			<br/>
			{{$kantor_aktif['nama']}}
			</div>
		</div>
	</div>

	@component ('bootstrap.modal', ['id' => 'bayar-angsuran', 'form' => true, 'method' => 'patch', 'url' => route('kredit.angsuran.update', ['id' => $angsuran['id'], 'kantor_aktif_id' => $kantor_aktif['id']])])
		@slot ('title')
			Tandai Angsuran Lunas
		@endslot

		@slot ('body')
			<p>Tanggal pelunasan akan terhitung tepat ketika Anda mengisi password berikut</p>

			{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
		@endslot

		@slot ('footer')
			<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
			{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
		@endslot
	@endcomponent

@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('js')
	<script type="text/javascript">
		//MODAL PARSE DATA ATTRIBUTE//
		$("a.modal_angsuran").on("click", parsingAttributeModalAngsuran);

		function parsingAttributeModalAngsuran(){
			$('#bayar-angsuran').find('form').attr('action', $(this).attr("data-action"));
		}
	</script>
@endpush