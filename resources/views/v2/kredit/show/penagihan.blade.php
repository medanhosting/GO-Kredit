@inject('idr', 'App\Service\UI\IDRTranslater')
<div class="row">
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center text-style'>
					{{$idr->formatMoneyTo($stat['total_tunggakan'])}}
				</h4><hr> 
			@endslot
			@slot('body') <p class='text-center'>ANGSURAN JATUH TEMPO ( {{$stat['jumlah_tunggakan']}} )</p> @endslot
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
					{{ isset($stat['last_sp']) ? strtoupper(str_replace('_',' ',$stat['last_sp']['tag'])) : '-' }}
				</h4><hr> 
			@endslot
			@slot('body') <p class='text-center'>SP TERAKHIR DIKELUARKAN</p> @endslot
		@endcomponent
	</div>
</div>

<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-8">
		@include('v2.kredit.show.surat_peringatan')
	</div>
	<div class="col-4">
		@include('v2.kredit.show.kolektor')
	</div>
</div>