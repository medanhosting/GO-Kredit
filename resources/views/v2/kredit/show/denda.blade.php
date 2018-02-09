@inject('carbon', 'Carbon\Carbon')

<div class="row">
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center'>
					{{ $idr->formatMoneyTo($stat['total_denda']) }}
				</h4>
				<hr/> 
			@endslot
			@slot('body') 
				<p class='text-center'>TOTAL DENDA YANG HARUS DIBAYAR</p> 
			@endslot
		@endcomponent
	</div>
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center'>
					@if($restitusi)
					<i class="fa fa-exclamation-triangle text-warning"></i> {{$restitusi['jumlah']}}
					@else
					Rp 0
					@endif
				</h4>
				<hr/> 
			@endslot
			@slot('body') 
				<p class='text-center'>PERMINTAAN RESTITUSI ({{count($restitusi)}})</p> 
			@endslot
		@endcomponent
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-8">
		@component('bootstrap.card')
			@slot('title') 
				<h5 class='text-left'>
					<strong>BUKTI TRANSAKSI DENDA</strong>
				</h5>
			@endslot
			@slot('body')
						<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center align-middle">NO</th>
							<th class="text-center align-middle">TANGGAL</th>
							<th class="text-center align-middle">RESTITUSI</th>
							<th class="text-center align-middle">STATUS</th>
						</tr>
					</thead>
					<tbody>
						@forelse($denda as $k => $v)
							<tr>
								<td class="text-center">{{ $loop->iteration }}</td>
								<td class="text-center">{{$carbon::parse($v['tanggal'])->format('d/m/Y')}}</td>
								<td class="text-right">
									{{$v['jumlah']}}<br/>
									<small><i>{{str_replace('_', ' ', $v['jenis'])}}</i></small>
								</td>
								<td class="text-center">
									@if(is_null($v['is_approved']))
										<span class="badge badge-warning">Menunggu<br/>Konfirmasi</span>
									@elseif($v['is_approved'])
										<span class="badge badge-success">Disetujui</span>
									@else
										<span class="badge badge-danger">Ditolak</span>
									@endif
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="4">
									Tidak ada data
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			@endslot
		@endcomponent
	</div>
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title')
				<ul class="nav nav-tabs underline" role="tablist">
					<li class="nav-item">
						<a class="nav-link px-4 {{$is_denda_tab}}" data-toggle="tab" href="#bayar_d" role="tab">
							BAYAR
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link px-4 {{ $is_restitusi_tab }}" href="#restitusi" role="tab" data-toggle="tab">
							RESTITUSI
						</a>
					</li>
				</ul>
			@endslot
			@slot('body')
				<!-- Tab panes -->
				<div class="tab-content">
					<!-- tab bayar_d -->
					<div class="tab-pane p-2 {{$is_denda_tab}}" id="bayar_d" role="tabpanel">
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
			@endslot
		@endcomponent
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
