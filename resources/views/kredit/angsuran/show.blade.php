@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<ul class="nav text-right">
			@if(isset($bayar))
				<li class="nav-item">
					<a class="nav-link modal_angsuran" data-toggle="modal" data-target="#bayar-angsuran" href="#" data-action="{{route('kredit.angsuran.update', ['id' => $id, 'kantor_aktif_id' => $kantor_aktif['id'], 'nth' => request()->get('nth')])}}">BAYAR</a>
				</li>
			@else
				<li class="nav-item">
					<a class="nav-link disabled" href="#">BAYAR</a>
				</li>
			@endif

			@if(isset($lunas))
				<li class="nav-item">
					<a class="nav-link" href="{{route('kredit.angsuran.print', ['id' => $angsuran['nomor_faktur'], 'kantor_aktif_id' => $kantor_aktif['id']])}}" target="_blank">CETAK BUKTI BAYAR</a>
				</li>
			@else
				<li class="nav-item">
					<a class="nav-link disabled" href="#">CETAK BUKTI BAYAR</a>
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
			: {{is_null($angsuran['tanggal']) ? Carbon\Carbon::now()->format('d/m/Y H:i') : $angsuran['tanggal'] }}
			<br>
			: {{$angsuran['nomor_faktur']}}
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
			 : {{$idr->formatMoneyTo($total)}}
			</div>
			<div class="col-sm-8 text-left">
			 <br>
			({{ucwords($idr::terbilang($total))}} Rupiah)
			</div>
		</div>
		<div class="clearifx">&nbsp;</div>
		<div class="clearifx">&nbsp;</div>
		<div class="row">
			<div class="col-sm-12">
				{!! Form::open(['url' => route('kredit.angsuran.show', ['id' => $id]), 'method' => 'GET']) !!}

				@foreach(request()->all() as $k => $v)
					<input type="hidden" name="{{$k}}" value="{{$v}}">
				@endforeach
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th width="5">No</th>
							<th>Jatuh Tempo</th>
							<th>Pokok</th>
							<th>Bunga</th>
							<th>Denda</th>
							<th>Biaya Kolektor</th>
							<th class="text-right">Jumlah</th>
							@if(!isset($bayar) && !isset($lunas))
							<th class="text-center">Bayar Sekarang</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach($angsuran['details'] as $k => $v)
						<tr>
							<td>{{$k+1}}</td>
							<td class="text-left">{{Carbon\Carbon::parse($v['tanggal_bayar'])->addDays(Config::get('kredit.batas_pembayaran_angsuran_hari'))->format('d/m/Y H:i')}}</td>
							<td class="text-right">{{$idr->formatMoneyTo($v['pokok'])}}</td>
							<td class="text-right">{{$idr->formatMoneyTo($v['bunga'])}}</td>
							<td class="text-right">{{$idr->formatMoneyTo($v['denda'])}}</td>
							<td class="text-right">{{$idr->formatMoneyTo($v['collector'])}}</td>
							<td class="text-right">{{$idr->formatMoneyTo($v['subtotal'])}}</td>
							@if(!isset($bayar) && !isset($lunas))
							<td class="text-center">
								@if(is_null($v['nota_bayar_id']))
									<input type="checkbox" name="nth[]" value="{{$v['nth']}}">
								@else
									<a href="{{route('kredit.angsuran.show', array_merge(['id' => $id, 'nota_bayar_id' => $v['nota_bayar_id']], request()->all()))}}"><i class="fa fa-check"></i></a>
								@endif
							</td>
							@endif
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th colspan="@if(!isset($bayar) && !isset($lunas)) 7 @else 6 @endif">Total</th>
							<th class="text-right">{{$idr->formatMoneyTo($total)}}</th>
						</tr>
						@if(!isset($bayar) && !isset($lunas))
						<tr>
							<th class="text-right" colspan=8"><button type="submit" class="btn btn-primary">&emsp;Bayar&emsp;</button></th>
						</tr>
						@endif
					</tfoot>
				</table>
				{!!Form::close()!!}
			</div>
		</div>

		@if(isset($lunas))
		<div class="row">
			<div class="col-sm-2">
				<br>
				TOTAL HUTANG<br>
				TOTAL ANGSURAN<br>
				SISA HUTANG
			</div>
			<div class="col-sm-2 text-right">
				<br>
				{{$t_hutang}}<br>
				{{$t_lunas}}<br>
				{{$s_hutang}}
			</div>
			<div class="col-sm-4">
			<!-- CATATAN
			<div style="border:1px solid #000">
				&nbsp;<br/>
				&nbsp;<br/>
				&nbsp;<br/>
			</div> -->
			</div>
			<div class="col-sm-4 text-right">
			{{is_null($angsuran['tanggal']) ? Carbon\Carbon::now()->format('d/m/Y H:i') : $angsuran['tanggal'] }}
			<br/>
			<br/>
			<br/>
			{{$kantor_aktif['nama']}}
			</div>
		</div>
		@endif
	</div>

	@component ('bootstrap.modal', ['id' => 'bayar-angsuran', 'form' => true, 'method' => 'patch', 'url' => route('kredit.angsuran.update', ['id' => $id, 'kantor_aktif_id' => $kantor_aktif['id'], 'nth' => request()->get('nth')]) ])
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