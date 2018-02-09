<div class="container-fluid bg-white" style="border-bottom: 2px solid #f5f5f5;">
	<div class="row">
		<div class="col pl-0 pr-0">
			<nav class="nav m-2">
				<a href="{{ route('home', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-tachometer"></i>&nbsp;&nbsp;Dashboard</a>

				@if(array_intersect(['permohonan', 'survei', 'analisa', 'putusan', 'pencairan', 'realisasi', 'operasional'],$scopes->scopes))
				<a href="{{ route('pengajuan.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-paper-plane"></i>&nbsp;&nbsp;Pengajuan</a>
				@endif
				
				@if(array_intersect([' kredit', 'angsuran', 'restitusi', 'penagihan', 'tunggakan', 'surat_peringatan', 'mutasi_jaminan', 'operasional'],$scopes->scopes))
				<a href="{{ route('kredit.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;Kredit</a>
				@endif

				@if(array_intersect(['keuangan', 'coa'],$scopes->scopes))
				<a href="{{ route('jurnal.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-line-chart"></i>&nbsp;&nbsp;Keuangan</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-line-chart"></i>&nbsp;&nbsp;Keuangan</a>
				@endif

				@if(array_intersect(['kantor', 'karyawan', 'audit'],$scopes->scopes) && in_array('holding', $scopes->scopes))
				<a href="{{ route('kantor.index', ['kantor_aktif_id' => $kantor_aktif['id']]) }}" class="nav-link">
				<i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor</a>
				@else
				<a href="#" class="nav-link disabled"><i class="fa fa-briefcase"></i>&nbsp;&nbsp;Kantor</a>
				@endif

				@if(array_intersect(['passcode'],$scopes->scopes))
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