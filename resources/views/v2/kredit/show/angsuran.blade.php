@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('carbon', 'Carbon\Carbon')

<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-12">
		{!! Form::open(['url' => route('kredit.store', ['id' => $kredit_id]), 'method' => 'POST']) !!}

			@foreach(request()->all() as $k => $v)
				<input type="hidden" name="{{$k}}" value="{{$v}}">
			@endforeach

			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th width="5">No</th>
						<th>Jatuh Tempo</th>
						<th class="text-right">Pokok</th>
						<th class="text-right">Bunga</th>
						<th class="text-right">Denda</th>
						<th class="text-right">Potongan</th>
						<th class="text-right">Jumlah</th>
						<th class="text-center">
							<input type="checkbox" class="check-all">
						</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<tbody>
					@php
						{{--  dd($aktif['nomor_kredit']);  --}}
					@endphp
					@foreach($angsuran as $k => $v)
					<tr @if($v['is_tunggakan']) class="text-danger" @endif>
						<td class="text-center">{{ $loop->iteration }}</td>
						<td class="text-left">{{Carbon\Carbon::parse($v['tanggal_bayar'])->format('d/m/Y H:i')}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['pokok'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['bunga'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['denda'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['potongan'])}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($v['subtotal'] - $v['potongan'])}}</td>
						<td class="text-center">
							@if (is_null($v['nota_bayar_id']))
								<input type="checkbox" name="nth[]" value="{{$v['nth']}}">
							@else
								<i class="fa fa-check text-primary"></i>
								
							@endif
						</td>
						<td class="text-center">
							@if (is_null($v['nota_bayar_id']))
								<a href="#" class="text-primary btn-bayar" 
									data-value="{{ $v['nth'] }}" 
									data-toggle="modal" 
									data-target="#summary-angsuran" 
									data-url="{{ route('angsuran.show', ['id' => $v['nota_bayar_id']])  }}" 
									data-pokok="{{ $idr->formatMoneyTo($v['pokok']) }}"
									data-bunga="{{ $idr->formatMoneyTo($v['bunga']) }}"
									data-denda="{{ $idr->formatMoneyTo($v['denda']) }}"
									data-potongan="{{ $idr->formatMoneyTo($v['potongan']) }}" 
									data-subtotal="{{ $idr->formatMoneyTo($v['subtotal'] - $v['potongan']) }}">Bayar</a>
							@else
								<a href="{{route('angsuran.show', array_merge(['id' => $aktif['nomor_kredit'], 'nota_bayar_id' => $v['nota_bayar_id']], request()->all()))}}">
									Nota Angsuran
								</a>
							@endif
						</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<th class="text-right align-middle" colspan="6">
							<h5 class="mb-0"><strong>Total</strong></h5>
						</th>
						<th class="text-right align-middle">
							<h5 class="mb-0"><strong>{{ $idr->formatMoneyTo($total) }}</strong></h5>
						</th>
						<th class="text-center align-middle" colspan="2">
							<a href="#" class="btn btn-primary btn-bayar-semua invisible" data-toggle="modal" data-target="#summary-angsuran">&emsp;Bayar yang tercentang&emsp;</a>
						</th>
					</tr>
				</tfoot>
			</table>
		{!!Form::close()!!}

		{!! Form::open(['url' => route('kredit.store', ['id' => $kredit_id]), 'method' => 'POST', 'class' => 'kredit-single']) !!}
			@foreach(request()->all() as $k => $v)
				<input type="hidden" name="{{$k}}" value="{{$v}}">
			@endforeach

			@component('bootstrap.modal', ['id' => 'summary-angsuran', 'size' => 'modal-lg'])

				@slot ('title')
					Detail Angsuran
				@endslot

				@slot ('body')
					<div class="row justify-content-center">
						<div class="col">
							<div class="row">
								<div class="col-6 text-left">
									<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
									<ul class="list-unstyled fa-ul">
										<li><i class="fa fa-building-o fa-li"></i>{{ implode(' ', $kantor_aktif['alamat']) }}</li>
										<li><i class="fa fa-phone fa-li"></i>{{ $kantor_aktif['telepon'] }}</li>
									</ul>
								</div>
								<div class="col-6 text-right">
									<div class="row justify-content-end">
										<div class="col-3">Nomor</div>
										<div class="col-6">{{$kantor_aktif['id']}} / {{$angsuran['nomor_kredit']}}</div>
									</div>
									<div class="row justify-content-end">
										<div class="col-3">Tanggal</div>
										<div class="col-6">
											{{ $angsuran['tanggal'] }}
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col text-center">
									<h4 class="mb-2">BUKTI ANGSURAN KREDIT</h4>
								</div>
							</div>
							<div class="clearfix">&nbsp;</div>
							<table class="table">
								<tr class="align-top">
									<td style="width: 13%">AC / SPK</td>
									<td style="width: 1%">:</td>
									<td class="pl-2 pr-2" style="width: 20%;">
										<p class="mb-2" style="">gak tau variabelnya</p>
									</td>
									<td style="width: 13%">AO</td>
									<td style="width: 1%">:</td>
									<td class="pl-2 pr-2" style="width: 20%;">
										<p class="mb-2" style="">&nbsp;</p>
									</td>
								</tr>
								<tr class="align-top">
									<td style="width: 12.5%">Nama</td>
									<td style="width: 1%">:</td>
									<td class="w-25 pl-2 pr-2" style="">
										<p class="mb-2">
											{{ $angsuran['kredit']['nasabah']['nama'] }}
										</p>
									</td>
									<td style="width: 12.5%">Angsuran Ke-</td>
									<td style="width: 1%">:</td>
									<td class="w-25 pl-2 pr-2" style="">
										<p class="mb-2">
											@foreach($angsuran['details'] as $k => $v)
												@if ($loop->last)
													{{ $v['nth'] }}
												@else
													{{ $v['nth'] }}, 
												@endif
											@endforeach
										</p>
									</td>
								</tr>
								<tr class="align-top">
									<td style="width: 12.5%">Alamat</td>
									<td style="width: 1%">:</td>
									<td class="w-25 pl-2 pr-2 text-capitalize" style="">
										<p class="mb-2">
											{{ strtolower(implode(' ', $angsuran['kredit']['nasabah']['alamat'])) }}
										</p>
									</td>
									<td style="width: 12.5%">Sisa Angsuran</td>
									<td style="width: 1%">:</td>
									<td class="w-25 pl-2 pr-2" style="">
										<p class="mb-2">{{ $s_hutang }}</p>
									</td>
								</tr>
								<tr class="align-top">
									<td style="width: 12.5%">Telp.</td>
									<td style="width: 1%">:</td>
									<td class="w-25 pl-2 pr-2" style="">
										<p class="mb-2">{{ $angsuran['kredit']['nasabah']['telepon'] }}</p>
									</td>
									<td style="width: 12.5%">Periode Bulan</td>
									<td style="width: 1%">:</td>
									<td class="w-25 pl-2 pr-2 text-capitalize" style="">
										<p class="mb-2">
											&nbsp;
										</p>
									</td>
								</tr>
								<tr>
									<td colspan="6">
										<div class="clearfix">&nbsp;</div>
										<div class="clearfix">&nbsp;</div>
										<table class="table w-100 table-bordered">
											<thead>
												<tr>
													<th>#</th>
													<th>Angsuran</th>
													<th class="text-right">Pokok</th>
													<th class="text-right">Bunga</th>
													<th class="text-right">Denda</th>
													<th class="text-right">Potongan</th>
													<th class="text-right">Sub Total</th>
												</tr>
											</thead>
											<tbody id="template-angsuran-list">
												<tr style="display:none;">
													<td class="a-iteration"></td>
													<td class="a-nth"></td>
													<td class="text-right a-pokok"></td>
													<td class="text-right a-bunga"></td>
													<td class="text-right a-denda"></td>
													<td class="text-right a-potongan"></td>
													<td class="text-right a-subtotal"></td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<td class="text-right a-total" colspan="6"><h5><strong>Total</strong></h5></td>
													<td class="text-right"><h5><strong>{{ $idr->formatMoneyTo($total) }}</strong></h5></td>
												</tr>
											</tfoot>
										</table>
										<div class="clearfix">&nbsp;</div>
									</td>
								</tr>
							</table>			
							<div class="row">
								<div class="col-6">
									<table class="table table-bordered w-100 mt-4">
										<thead class="thead-light">
											<tr>
												<th class="text-center p-2 w-25">Dibuat</th>
												<th class="text-center p-2 w-25">Diperiksa</th>
												<th class="text-center p-2 w-25">Disetujui</th>
												<th class="text-center p-2 w-25">Dibayar</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="padding: 35px;">&nbsp;</td>
												<td style="padding: 35px;">&nbsp;</td>
												<td style="padding: 35px;">&nbsp;</td>
												<td style="padding: 35px;">&nbsp;</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-6">
									<table class="table w-50 text-center ml-auto mr-5" style="height: 220px;">
										<tbody>
											<tr>
												<td class="border-0">{{ $kantor_aktif['alamat']['kota'] }}, {{ $carbon->now()->format('d/m/Y') }}</td>
											</tr>
											<tr>
												<td class="border-0">
													<p class="border border-left-0 border-right-0 border-bottom-0">Nama terang dan tanda tangan</p>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col-6">
									<a href="{{ route('angsuran.print', ['id' => $angsuran['nomor_kredit'], 'nota_bayar_id' => $v['nota_bayar_id'], 'kantor_aktif_id' => $kantor_aktif['id']]) }}" target="__blank" class="text-success">
										<i class="fa fa-file-o fa-fw"></i>&nbsp; CETAK NOTA BUKTI ANGSURAN
									</a>
								</div>
							</div>
						</div>
					</div>
				@endslot

				@slot ('footer')
					<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
					{!! Form::submit('Hapus', ['class' => 'btn btn-outline-danger']) !!}
				@endslot

			@endcomponent
			{!! Form::hidden('nth[]', null, ['class' => 'kredit-single-checkbox']) !!}
		{!! Form::close() !!}
	</div>
</div>

@push('js')
	<script type="text/javascript">
		//MODAL PARSE DATA ATTRIBUTE//
		$("a.modal_angsuran").on("click", parsingAttributeModalAngsuran);

		function parsingAttributeModalAngsuran(){
			$('#bayar-angsuran').find('form').attr('action', $(this).attr("data-action"));
		}
		
		var flag = 0;
		$('input.check-all').on('change', function(){
			if ($(this).is(':checked')) {
				$('input[name="nth[]"]').prop('checked', true);
			} else {
				$('input[name="nth[]"]').prop('checked', false);
			}

			checked_on_checked();
		});

		$('input[name="nth[]"]').on('change', function(){
			checked_on_checked();
		});

		function checked_on_checked(){
			if ($('input[name="nth[]"]:checked').length >= 1) {
				flag = 1;
			} else {
				flag = 0;
			}

			if (flag == 1) {
				$('.btn-bayar-semua').removeClass('invisible');
			} else {
				$('.btn-bayar-semua').addClass('invisible');
			}
		}

		$('.btn-bayar').on('click', function(e) {
			e.preventDefault();

			var value = $(this).attr('data-value');

			$('.kredit-single-checkbox').val(value);
		});

		$('.btn-bayar-semua').on('click', function(e){
			e.preventDefault();

			$('input[name="nth[]"]').each(function(){
				
			})
		})

		$('#summary-angsuran').on('shown.bs.modal', function(e) {
			var linkUrl = $(e.relatedTarget).attr('data-url');
			var angsNth = $(e.relatedTarget).attr('data-value');
			var angsPokok = $(e.relatedTarget).attr('data-pokok');
			var angsDenda = $(e.relatedTarget).attr('data-denda');
			var angsBunga = $(e.relatedTarget).attr('data-bunga');
			var angsPotongan = $(e.relatedTarget).attr('data-potongan');
			var angsSubtotal = $(e.relatedTarget).attr('data-subtotal');

			var template = $('#template-angsuran-list').find('tr');
			var tmpClone = template.show();

			tmpClone.find('td.a-iteration').html('1');
			tmpClone.find('td.a-nth').html('Angsuran ke- ' + angsNth);
			tmpClone.find('td.a-pokok').html(angsPokok);
			tmpClone.find('td.a-denda').html(angsDenda);
			tmpClone.find('td.a-bunga').html(angsBunga);
			tmpClone.find('td.a-potongan').html(angsPotongan);
			tmpClone.find('td.a-subtotal').html(angsSubtotal);
		});
	</script>
@endpush