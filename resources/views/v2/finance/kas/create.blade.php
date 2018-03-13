@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-line-chart mr-2"></i> KEUANGAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.finance.base')
		</div>
		<div class="col">
			@if($notabayar)
				{!! Form::open(['url' => route('kas.update', ['id' => $notabayar['id'], 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'PATCH']) !!}
			@else
				{!! Form::open(['url' => route('kas.store', ['kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'POST']) !!}
			@endif
			
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0 float-left">&nbsp;&nbsp;BKK/BKM BARU</h5>
				@endslot
				
				<div class="card-body">
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-6 text-left">
							<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
							<ul class="list-unstyled fa-ul">
								<li>
									<i class="fa fa-building-o fa-li" style="margin-top: .2rem;"></i>
									{{ implode(' ', $kantor_aktif['alamat']) }}
								</li>
								<li>
									<i class="fa fa-phone fa-li" style="margin-top: .2rem;"></i>
									{{ $kantor_aktif['telepon'] }}
								</li>
							</ul>
						</div>
						<div class="col-6 text-right">
							<div class="row justify-content-end">
								<div class="col-3">Nomor</div>
								<div class="col-5 mb-3">{{$notabayar['nomor_faktur']}}&nbsp;</div>
							</div>
							<div class="row justify-content-end">
								<div class="col-3">Tanggal</div>
								<div class="col-5">
									{!! Form::bsText(null, 'tanggal', $notabayar['hari'], ['class' => 'form-control inline-edit text-info pb-0 border-input date-mask', 'placeholder' => 'dd/mm/yyyy', 'id' => 'tanggal'], true) !!}
								</div>
							</div>
							<div class="row justify-content-end">
								<div class="col-3">Dibayar Kepada</div>
								<div class="col-5">
									{!! Form::bsText(null, 'kepada', $notabayar['karyawan']['penerima']['nama'], ['class' => 'form-control inline-edit text-info pb-0 border-input', 'placeholder' => 'Rudy', 'id' => 'penerima'], true) !!}
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col text-center">
							<h4 class="mb-1">
								<strong>
									{!! Form::bsSelect(null, 'jenis', ['bkk' => 'Bukti Kas Keluar', 'bkm' => 'Bukti Kas Masuk'], null, ['class' => 'form-control text-info inline-edit text-center'], true) !!}
								</strong>
							</h4>
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>
					
					<div class="row">
						<div class="col-8">
							<div id="table" class="table-editable">
								<span class="table-add fa fa-plus"></span>
								<table class="table">
									<tr>
									<th>Keterangan</th>
									<th>Jumlah</th>
									<th></th>
									<th></th>
									</tr>

									@forelse($notabayar['details'] as $k => $v)
										<tr>
											<td contenteditable="true">{{$v['deskripsi']}}</td>
											<td contenteditable="true">{{$v['jumlah']}}</td>
											<td>
												<span class="table-remove fa fa-remove"></span>
											</td>
										</tr>
									@empty
										<tr>
											<td contenteditable="true">Biaya Operasional</td>
											<td contenteditable="true">Rp 30.000</td>
											<td>
												<span class="table-remove fa fa-remove"></span>
											</td>
										</tr>
									@endforelse

									<!-- This is our clonable table line -->
									<tr class="hide">
									<td contenteditable="true">Beli Bensin 1 Liter</td>
									<td contenteditable="true">Rp 9.000</td>
									<td>
										<span class="table-remove fa fa-remove"></span>
									</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="col-4">
							<table class="table table-bordered w-100">
								<thead class="thead-light">
									<tr>
										<th class="text-center p-2 w-33">Kasir</th>
										<th class="text-center p-2 w-33">Disetujui</th>
										<th class="text-center p-2 w-33">Diterima</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="padding: 35px;">&nbsp;</td>
										<td style="padding: 35px;">&nbsp;</td>
										<td style="padding: 35px;">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-12 text-right">
							<button id="export-btn" class="btn btn-primary">Export Data</button>
							{{Form::hidden('details', null, ['id' => 'export'])}}
						</div>
					</div>
				</div>
			@endcomponent

			{!! Form::close() !!}	
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
	<style type="text/css">
		.table-editable {
		  position: relative;
		  
		  .glyphicon {
		    font-size: 20px;
		  }
		}

		.table-remove {
		  color: #700;
		  cursor: pointer;
		  
		  &:hover {
		    color: #f00;
		  }
		}

		.table-up, .table-down {
		  color: #007;
		  cursor: pointer;
		  
		  &:hover {
		    color: #00f;
		  }
		}

		.table-add {
		  color: #070;
		  cursor: pointer;
		  position: absolute;
		  top: 8px;
		  right: 0;
		  
		  &:hover {
		    color: #0b0;
		  }
		}

		.hide{
			display: none;
		}
	</style>
@endpush

@push('js')
	<script type="text/javascript">
		var $TABLE = $('#table');
		var $BTN = $('#export-btn');
		var $EXPORT = $('#export');

		$('.table-add').click(function () {
		  var $clone = $TABLE.find('tr.hide').clone(true).removeClass('hide table-line');
		  $TABLE.find('table').append($clone);
		});

		$('.table-remove').click(function () {
		  $(this).parents('tr').detach();
		});

		$('.table-up').click(function () {
		  var $row = $(this).parents('tr');
		  if ($row.index() === 1) return; // Don't go above the header
		  $row.prev().before($row.get(0));
		});

		$('.table-down').click(function () {
		  var $row = $(this).parents('tr');
		  $row.next().after($row.get(0));
		});

		// A few jQuery helpers for exporting only
		jQuery.fn.pop = [].pop;
		jQuery.fn.shift = [].shift;

		$BTN.click(parsingData);

		function parsingData(){
		  var $rows = $TABLE.find('tr:not(:hidden)');
		  var headers = [];
		  var body = [];
		  
		  // Get the headers (add special header logic here)
		  $($rows.shift()).find('th:not(:empty)').each(function () {
		    headers.push($(this).text().toLowerCase());
		  });
		  
		  // Turn all existing rows into a loopable array
		  $rows.each(function () {
		    var $td = $(this).find('td');
		    var h = {};
		    
		    // Use the headers from earlier to name our hash keys
		    headers.forEach(function (header, i) {
		      h[header] = $td.eq(i).text();   
		    });
		    
		    body.push(h);
		  });

		  // Output the result
		  $EXPORT.val(JSON.stringify(body));
		};
	</script>
@endpush