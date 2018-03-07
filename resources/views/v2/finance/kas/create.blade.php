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
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0 float-left">&nbsp;&nbsp;BKK/BKM BARU</h5>
				@endslot
				
				<div class="card-body">
					<div class="clearfix">&nbsp;</div>
					<div id="table" class="table-editable">
						<span class="table-add fa fa-plus"></span>
						<table class="table">
							<tr>
							<th>Deskripsi</th>
							<th>Unit</th>
							<th>Harga@</th>
							<th></th>
							<th></th>
							</tr>
							<tr>
							<td contenteditable="true">Biaya Operasional</td>
							<td contenteditable="true">1</td>
							<td contenteditable="true">Rp 30.000</td>
							<td>
								<span class="table-remove fa fa-remove"></span>
							</td>
							</tr>
							<!-- This is our clonable table line -->
							<tr class="hide">
							<td contenteditable="true">Beli Bensin 1 Liter</td>
							<td contenteditable="true">1</td>
							<td contenteditable="true">Rp 9.000</td>
							<td>
								<span class="table-remove fa fa-remove"></span>
							</td>
							</tr>
						</table>
					  </div>
					  
					  <button id="export-btn" class="btn btn-primary">Export Data</button>
					  <p id="export"></p>
					</div>
				</div>
			@endcomponent
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

		$BTN.click(function () {
		  var $rows = $TABLE.find('tr:not(:hidden)');
		  var headers = [];
		  var data = [];
		  
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
		    
		    data.push(h);
		  });
		  
		  // Output the result
		  $EXPORT.text(JSON.stringify(data));
		});
	</script>
@endpush