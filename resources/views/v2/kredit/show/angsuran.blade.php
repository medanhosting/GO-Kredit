<!-- stat area -->
<div class="row">
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center text-style'>
					{{ $idr->formatMoneyTo($stat['sisa_hutang']) }}
				</h4>
				<hr/> 
			@endslot
			@slot('body') 
				<p class='text-center'>SISA HUTANG</p> 
			@endslot
		@endcomponent
	</div>
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center text-style'>
					{{ $idr->formatMoneyTo($stat['total_tunggakan']) }}
				</h4>
				<hr/> 
			@endslot
			@slot('body') 
				<p class="text-center">ANGSURAN JATUH TEMPO ( {{ $stat['jumlah_tunggakan'] }} )</p>
			@endslot
		@endcomponent
	</div>
	<div class="col-4">
		@component('bootstrap.card')
			@slot('title') 
				<h4 class='text-center text-style'>
					{{ $idr->formatMoneyTo($stat['total_titipan']) }}
				</h4>
				<hr/> 
			@endslot
			@slot('footer') 
				<p class="text-center">TITIPAN ANGSURAN ( {{ $stat['jumlah_titipan'] }} )</p>
			@endslot
		@endcomponent
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-8">
		@include('v2.kredit.show.bukti_angsuran')
	</div>
	<div class="col-4">
		@include('v2.kredit.show.bayar_angsuran')
	</div>
</div>