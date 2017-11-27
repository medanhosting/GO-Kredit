@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<ul class="nav text-left">
			@if($can_collect)
			<li class="nav-item">
				<a class="nav-link modal_tunggakan" data-toggle="modal" data-target="#buat-penagihan" href="#" data-action="{{route('kredit.penagihan.store', ['nomor_kredit' => $nomor_kredit, 'kantor_aktif_id' => $kantor_aktif['id']])}}">BUAT PENAGIHAN BARU</a>
			</li>
			@else
			<li class="nav-item">
				<a class="nav-link disabled" href="#">BUAT PENAGIHAN BARU<br/><small>*penagihan baru max {{Config::get('kredit.selisih_penagihan_hari')}} hari sejak penagihan terakhir dilakukan </small></a>
			</li>
			@endif
		</ul>
	</div>
	<div class="clearifx">&nbsp;</div>
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col-sm-12">
				<h3>TUNGGAKAN</h3>
				<table class="table table-bordered table-hover">
					<tbody>
						@php $total_tunggakan 	= 0 @endphp
						@foreach($tunggakan as $k => $v)
							<tr>
								<th colspan="3">Angsuran {{$v['nomor_kredit']}} / {{$v['id']}}</th>
							</tr>
							@foreach($v->details as $kd => $vd)
							<tr>
								<td>{{$k+$kd+1}}</td>
								<td>{{$vd['description']}}</td>
								<td class="text-right">{{$vd['amount']}}</td>
							</tr>
							@endforeach
							@php $total_tunggakan 	= $total_tunggakan + $v['amount'] @endphp
						@endforeach
						<tr>
							<th colspan="2">Total</th>
							<th class="text-right">{{$idr->formatMoneyTo($total_tunggakan)}}</th>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="clearifx">&nbsp;</div>
		<div class="row">
			<div class="col-sm-12">
				<h3>TAGIHAN</h3>
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th width="5">No</th>
							<th>Tanggal</th>
							<th>Nasabah</th>
							<th>Alamat</th>
						</tr>
					</thead>
					<tbody>
						@forelse($penagihan as $k => $v)
							<tr>
								<td>{{$k+1}}</td>
								<td>{{$v['collected_at']}}</td>
								<td>{{$v['kredit']['nasabah']['nama']}}</td>
								<td>{{implode(', ', $v['kredit']['nasabah']['alamat'])}}</td>
							</tr>
						@empty
							<tr>
								<td colspan="4">
									<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>

	@component ('bootstrap.modal', ['id' => 'buat-penagihan', 'form' => true, 'method' => 'post', 'url' => route('kredit.penagihan.store', ['nomor_kredit' => $nomor_kredit, 'kantor_aktif_id' => $kantor_aktif['id']])])
		@slot ('title')
			Buat Tagihan Atas Tunggakan
		@endslot

		@slot ('body')
			<p>Tanggal penagihan ditandai sesuai dengan pengisian tanggal berikut</p>

			{!! Form::bsText('Tanggal Penagihan', 'collected_at', $today->format('d/m/Y H:i'), ['class' => 'form-control mask-date-time']) !!}
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
		$("a.modal_tunggakan").on("click", parsingAttributeModalTunggakan);

		function parsingAttributeModalTunggakan(){
			$('#buat-penagihan').find('form').attr('action', $(this).attr("data-action"));
		}
	</script>
@endpush