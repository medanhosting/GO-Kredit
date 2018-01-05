<div class="card {{ $class }} border-0" style="box-shadow: 0px 0px 3px rgba(48,95,129,0.2);">
	@if ($image_url)
		<img class="card-img-top" src="{{ $image_url }}">
	@endif

	@if ($pre)
		{!! $pre !!}
	@endif

	@if ($header)
		<div class="card-header p-1 bg-light">
			{!! $header !!}
		</div>
	@endif

	@if ($title || $body || $footer)
		<div class="card-body">
			@if ($title)
				<span class="card-title">{{ $title }}</span>
			@endif
			@if ($body)
				<p class="card-text">
					{!! $body !!}
				</p>
			@endif
			@if ($footer)
				{!! $footer !!}
			@endif
		</div>
	@endif

	{!! $slot !!}
</div>