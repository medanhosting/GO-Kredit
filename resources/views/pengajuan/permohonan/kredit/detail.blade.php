<div class="row mt-4">
	<div class="col">
		<div class="tab-content">
			<div class="tab-pane fade show active" id="overview" role="tabpanel">
				@include ('pengajuan.permohonan.show.overview')
			</div>
			<div class="tab-pane fade" id="keluarga" role="tabpanel">
				@include ('pengajuan.permohonan.show.keluarga')
			</div>
			<div class="tab-pane fade" id="survei" role="tabpanel">
				@include ('pengajuan.permohonan.show.survei')
			</div>
			<div class="tab-pane fade" id="analisa" role="tabpanel">
				@include ('pengajuan.permohonan.show.analisa')
			</div>
			<div class="tab-pane fade" id="keputusan" role="tabpanel">
				@include ('pengajuan.permohonan.show.keputusan')
			</div>
		</div>
	</div>
</div>