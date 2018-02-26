@inject('carbon', 'Carbon\Carbon')

<div class="row">
	<div class="col-4">
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
	@if(array_intersect($acl_menu['kredit.kredit.aktif.denda.restitusi'], $scopes->scopes))
	<div class="col-4">
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
	@endif
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
							<th class="text-left align-middle">TANGGAL</th>
							<th class="text-left align-middle">KETERANGAN</th>
							<th class="text-right align-middle">JUMLAH</th>
							<th class="text-center align-middle">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						@forelse($denda as $k => $v)
							<tr>
								<td class="text-left">{{$v['hari']}}</td>
								<td class="text-left">{{$v['deskripsi']}}</td>
								<td class="text-right">
									{{$v['jumlah']}}
								</td>
								<td class="text-center">
									@if(str_is($v['jenis'], 'restitusi_denda'))
										<a href="{{ route('angsuran.print', ['id' => $v['morph_reference_id'], 'nomor_faktur' => $v['nomor_faktur'], 'kantor_aktif_id' => $kantor_aktif['id'], 'case' => 'restitusi_denda']) }}" target="__blank" class="text-success">
											<i class="fa fa-print"></i>
										</a>
									@elseif(str_is($v['jenis'], 'denda'))
										<a href="{{ route('angsuran.print', ['id' => $v['morph_reference_id'], 'nomor_faktur' => $v['nomor_faktur'], 'kantor_aktif_id' => $kantor_aktif['id'], 'case' => 'denda']) }}" target="__blank" class="text-success">
											<i class="fa fa-print"></i>
										</a>
									@else
										<a href="#"></a>
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
		@if($stat['total_denda'] > 0)
			@component('bootstrap.card')
				<div class="card-header bg-light p-0">
					<ul class="nav nav-tabs underline" role="tablist">
						@if(array_intersect($acl_menu['kredit.kredit.aktif.denda.bayar'], $scopes->scopes))
						<li class="nav-item">
							<a class="nav-link px-4 {{$is_bayar_denda_tab}}" data-toggle="tab" href="#bayar_d" role="tab">
								<h7 class="text-left p-2 mb-0">
									<strong>BAYAR</strong>
								</h7>
							</a>
						</li>
						@else
						<li class="nav-item">
							<a class="nav-link disabled">
								<h7 class="text-left p-2 mb-0">
									<strong>BAYAR</strong>
								</h7>
							</a>
						</li>
						@endif
						@if(array_intersect($acl_menu['kredit.kredit.aktif.denda.restitusi'], $scopes->scopes))
						<li class="nav-item">
							<a class="nav-link px-4 {{ $is_restitusi_tab }}" href="#restitusi" role="tab" data-toggle="tab">
								<h7 class="text-left p-2 mb-0">
									<strong>RESTITUSI</strong>
								</h7>
							</a>
						</li>
						@else
						<li class="nav-item">
							<a class="nav-link disabled">
								<h7 class="text-left p-2 mb-0">
									<strong>RESTITUSI</strong>
								</h7>
							</a>
						</li>
						@endif
					</ul>
				</div>
				<div class='card-body'>
					<!-- Tab panes -->
					<div class="tab-content">
						<!-- tab bayar_d -->
						@if(array_intersect($acl_menu['kredit.kredit.aktif.denda.bayar'], $scopes->scopes))
						<div class="tab-pane p-2 {{$is_bayar_denda_tab}}" id="bayar_d" role="tabpanel">
							@include('v2.kredit.show.bayar_denda')
						</div>
						@endif
						@if(array_intersect($acl_menu['kredit.kredit.aktif.denda.restitusi'], $scopes->scopes))
						<!-- tab restitusi -->
						<div class="tab-pane p-2 {{$is_restitusi_tab}}" id="restitusi" role="tabpanel">
							@if($restitusi)
								 @include('v2.kredit.show.konfirmasi_restitusi')  
							@else
								 @include('v2.kredit.show.permohonan_restitusi')  
							@endif
						</div>
						@endif
					</div>
				</div>
			@endcomponent
		@else
			@component('bootstrap.card')
				@slot('title') 
					<h4 class='text-center text-success pt-4 pb-4'>
						TIDAK ADA DENDA
					</h4>
				@endslot
			@endcomponent
		@endif
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
