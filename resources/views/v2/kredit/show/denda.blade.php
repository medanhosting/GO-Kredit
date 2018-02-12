@inject('carbon', 'Carbon\Carbon')

<div class="row">
	<div class="col-6">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center text-style'>
					{{ $idr->formatMoneyTo($stat['total_denda']) }}
				</h4>
				<hr/> 
			@endslot
			@slot('body') 
				<p class='text-center'>TOTAL DENDA YANG HARUS DIBAYAR</p> 
			@endslot
		@endcomponent
	</div>
	<div class="col-6">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center text-style'>
					@if($restitusi)
						<i class="fa fa-exclamation-circle text-warning"></i> {{$restitusi['jumlah']}}
					@else
						Rp 0
					@endif
				</h4>
				<hr/> 
			@endslot
			@slot('body') 
				<p class='text-center'>PERMINTAAN RESTITUSI ( {{count($restitusi)}} )</p> 
			@endslot
		@endcomponent
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-8">
		@component('bootstrap.card')
			<div class="card-header bg-light p-1">
				<h5 class='text-left mb-0 p-2'>
					<strong>BUKTI TRANSAKSI DENDA</strong>
				</h5>
			</div>
			<div class="card-body p-0">
				<table class="table table-bordered table-hover mb-0">
					<thead>
						<tr>
							<th class="text-center align-middle text-secondary">Tanggal</th>
							<th class="text-center align-middle text-secondary">Restitusi</th>
							<th class="text-center align-middle text-secondary">Status</th>
						</tr>
					</thead>
					<tbody>
						@forelse($denda as $k => $v)
							<tr>
								<td class="text-center">{{$carbon::parse($v['tanggal'])->format('d/m/Y')}}</td>
								<td class="text-right">
									<span class="d-block text-style">
										{{$v['jumlah']}}
									</span>
									<small><i>{{str_replace('_', ' ', $v['jenis'])}}</i></small>
								</td>
								<td class="text-center">
									@if(is_null($v['is_approved']))
										<span class="badge badge-warning p-1">Menunggu Konfirmasi</span>
									@elseif($v['is_approved'])
										<span class="badge badge-success">Disetujui</span>
									@else
										<span class="badge badge-danger">Ditolak</span>
									@endif
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="3" class="text-center">
									Tidak ada data
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		@endcomponent
	</div>
	<div class="col-4">
		@component('bootstrap.card')
			<div class="card-header bg-light p-0">
				<ul class="nav nav-tabs underline" role="tablist">
					<li class="nav-item">
						<a class="nav-link px-4 py-2 {{ isset($is_denda_tab) ? $is_denda_tab : 'active'}}" data-toggle="tab" href="#bayar_d" role="tab">
							<h5 class="my-1">BAYAR</h5>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link px-4 py-2 {{ $is_restitusi_tab }}" href="#restitusi" role="tab" data-toggle="tab">
							<h5 class="my-1">RESTITUSI</h5>
						</a>
					</li>
				</ul>
			</div>
			<div class='card-body'>
				<!-- Tab panes -->
				<div class="tab-content">
					<!-- tab bayar_d -->
					<div class="tab-pane p-2 {{ isset($is_denda_tab) ? $is_denda_tab : 'active' }}" id="bayar_d" role="tabpanel">
						@include('v2.kredit.show.bayar_denda')
					</div>
					<!-- tab restitusi -->
					<div class="tab-pane p-2 {{$is_restitusi_tab}}" id="restitusi" role="tabpanel">
						@if($restitusi)
							 @include('v2.kredit.show.konfirmasi_restitusi')  
						@else
							 @include('v2.kredit.show.permohonan_restitusi')  
						@endif
					</div>
				</div>
			</div>
		@endcomponent
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
