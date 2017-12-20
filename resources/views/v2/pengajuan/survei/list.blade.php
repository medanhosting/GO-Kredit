<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link @if($lokasi['agenda']=='nasabah') active @endif" data-toggle="tab" href="#character" role="tab">
			Character
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#condition" role="tab">
			Condition
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#capacity" role="tab">
			Capacity
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#capital" role="tab">
			Capital
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link @if($lokasi['agenda']=='jaminan') active @endif " data-toggle="tab" href="#collateral" role="tab">
			Collateral
		</a>
	</li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane @if($lokasi['agenda']=='nasabah') active @endif" id="character" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['character']['dokumen_survei']['character'] as $k => $v )
			@if(is_array($v))
				@foreach($v as $k2 => $v2)
					<div class="row">
						<div class="col-sm-4 text-right">
							{{strtoupper(str_replace('_', ' ', $k))}} {{strtoupper(str_replace('_', ' ', $k2))}}
						</div> 
						<div class="col-sm-8 text-left">
							{{str_replace('_', ' ', $v2)}}
						</div> 
					</div>
				@endforeach
			@else
				<div class="row">
					<div class="col-sm-4 text-right">
						{{strtoupper(str_replace('_', ' ', $k))}}
					</div> 
					<div class="col-sm-8 text-left">
						{{str_replace('_', ' ', $v)}}
					</div> 
				</div>
			@endif
		@endforeach
	</div>

	<div class="tab-pane" id="condition" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['condition']['dokumen_survei']['condition'] as $k => $v )
			@if(is_array($v))
				@foreach($v as $k2 => $v2)
					<div class="row">
						<div class="col-sm-4 text-right">
							{{strtoupper(str_replace('_', ' ', $k))}} {{strtoupper(str_replace('_', ' ', $k2))}}
						</div> 
						<div class="col-sm-8 text-left">
							{{str_replace('_', ' ', $v2)}}
						</div> 
					</div>
				@endforeach
			@else
				<div class="row">
					<div class="col-sm-4 text-right">
						{{strtoupper(str_replace('_', ' ', $k))}}
					</div> 
					<div class="col-sm-8 text-left">
						{{str_replace('_', ' ', $v)}}
					</div> 
				</div>
			@endif
		@endforeach
	</div>

	<div class="tab-pane" id="capacity" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['capacity']['dokumen_survei']['capacity'] as $k => $v )
			<div class="row">
				<div class="col-sm-12 text-left">
					@if(is_array($v))
						<strong>{{strtoupper(str_replace('_', ' ', $k))}}</strong>
						@foreach((array)$v as $k2 => $v2)
							@if(is_array($v2))
								@foreach((array)$v2 as $k3 => $v3)
									<div class="row">
										<div class="col-sm-4 text-right">
											{{strtoupper(str_replace('_', ' ', $k3))}}
										</div> 
										<div class="col-sm-8 text-left">
											{{str_replace('_', ' ', $v3)}}
										</div> 
									</div>
								@endforeach
							@else
								<div class="row">
									<div class="col-sm-4 text-right">
										{{strtoupper(str_replace('_', ' ', $k2))}}
									</div> 
									<div class="col-sm-8 text-left">
										{{str_replace('_', ' ', $v2)}}
									</div> 
								</div>
							@endif
						@endforeach
					@else
						<div class="row">
							<div class="col-sm-4 text-right">
								{{strtoupper(str_replace('_', ' ', $k))}}
							</div> 
							<div class="col-sm-8 text-left">
								{{str_replace('_', ' ', $v)}}
							</div> 
						</div>
					@endif
				</div> 
			</div>
		@endforeach
	</div>

	<div class="tab-pane" id="capital" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['capital']['dokumen_survei']['capital'] as $k => $v )
			<div class="row">
				<div class="col-sm-12 text-left">
					<strong>{{strtoupper(str_replace('_', ' ', $k))}}</strong>
					@foreach((array)$v as $k2 => $v2)
						@if(is_array($v2))
							@foreach((array)$v2 as $k3 => $v3)
								<div class="row">
									<div class="col-sm-4 text-right">
										{{strtoupper(str_replace('_', ' ', $k3))}}
									</div> 
									<div class="col-sm-8 text-left">
										{{str_replace('_', ' ', $v3)}}
									</div> 
								</div>
							@endforeach
						@else
							<div class="row">
								<div class="col-sm-4 text-right">
									{{strtoupper(str_replace('_', ' ', $k2))}}
								</div> 
								<div class="col-sm-8 text-left">
									{{str_replace('_', ' ', $v2)}}
								</div> 
							</div>
						@endif
					@endforeach
				</div> 
			</div>
		@endforeach
	</div>

	<div class="tab-pane @if($lokasi['agenda']=='jaminan') active @endif" id="collateral" role="tabpanel">
		<div class="clearfix">&nbsp;</div>
		@foreach($survei['collateral'] as $k0 => $v0 )
			<p class="text-secondary mb-1"><strong>DATA JAMINAN {{strtoupper(str_replace('_', ' ', $v0['jenis']))}} {{($k0+1)}}</strong></p>
			<div class="row">
				<div class="col-3">
					@foreach($v0['foto']['arsip_foto'] as $k2 => $v2)
						<img src="{{$v2['url']}}"  class="img img-fluid" alt="Foto Jaminan" style="max-width: 100%;padding: 15px 0px">
					@endforeach
				</div>
				<div class="col-9">
					@foreach($v0['dokumen_survei']['collateral'] as $k => $v )
						@if(!str_is($k, 'jenis'))
						<div class="row">
							<div class="col-sm-12 text-left">
								@if(is_array($v))
									@foreach((array)$v as $k2 => $v2)
										@if(is_array($v2))
											@foreach((array)$v2 as $k3 => $v3)
												<div class="row">
													<div class="col-sm-4 text-right">
														{{strtoupper(str_replace('_', ' ', $k3))}}
													</div> 
													<div class="col-sm-8 text-left">
														{{str_replace('_', ' ', $v3)}}
													</div> 
												</div>
											@endforeach
										@else
											<div class="row">
												<div class="col-sm-4 text-right">
													{{strtoupper(str_replace('_', ' ', $k2))}}
												</div> 
												<div class="col-sm-8 text-left">
													{{str_replace('_', ' ', $v2)}}
												</div> 
											</div>
										@endif
									@endforeach
								@else
									<div class="row">
										<div class="col-sm-4 text-right">
											{{strtoupper(str_replace('_', ' ', $k))}}
										</div> 
										<div class="col-sm-8 text-left">
											{{str_replace('_', ' ', $v)}}
										</div> 
									</div>
								@endif
							</div> 
						</div> 
						@endif
					@endforeach
				</div>
			</div>
		@endforeach
	</div>
</div>