<div class="container-fluid bg-white" style="border-bottom: 2px solid #f5f5f5;">
	<div class="row">
		<div class="col pl-0 pr-0">
			<nav class="nav m-2">
				<a href="{{ route('home', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-tachometer"></i>&nbsp;&nbsp;Dashboard</a>

				@if(in_array('operasional', $scopes->scopes) || in_array('pengajuan', $scopes->scopes))
				<a href="{{ route('pengajuan.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
				@endif
				@if(in_array('operasional', $scopes->scopes) || in_array('kredit', $scopes->scopes))
				<a href="{{ route('kredit.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit</a>
				@endif
				@if(in_array('kasir', $scopes->scopes))
				<a href="{{ route('jurnal.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-line-chart"></i>&nbsp;&nbsp;Keuangan</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-line-chart"></i>&nbsp;&nbsp;Keuangan</a>
				@endif
				@if($is_holder && in_array('kantor', $scopes->scopes))
				<a href="{{ route('kantor.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor</a>
				@endif

				@if($is_holder)
				<a href="{{ route('passcode.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-unlock-alt"></i>&nbsp;&nbsp;Passcode</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-unlock-alt"></i>&nbsp;&nbsp;Passcode</a>
				@endif

				<a href="{{ route('jurnal.test', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-book"></i>&nbsp;&nbsp;Jurnal</a>
			</nav>
		</div>
	</div>
</div>