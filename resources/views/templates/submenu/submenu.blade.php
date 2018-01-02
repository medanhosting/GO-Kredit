<div class="container-fluid bg-white" style="border-bottom: 2px solid #f5f5f5;">
	<div class="row">
		<div class="col pl-0 pr-0">
			<nav class="nav m-2">
				<a href="{{ route('home', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-tachometer"></i>&nbsp;&nbsp;Dashboard</a>
				<a href="{{ route('pengajuan.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
				<a href="{{ route('kredit.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit</a>
				<a href="{{ route('kasir.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-line-chart"></i>&nbsp;&nbsp;Keuangan</a>
				<a href="{{ route('kantor.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor</a>
			</nav>
		</div>
	</div>
</div>