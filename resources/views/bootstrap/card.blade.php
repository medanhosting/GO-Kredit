<div class="card {{ $class }}">
	@if ($image_url)
		<img class="card-img-top" src="{{ $image_url }}">
	@endif

	@if ($pre)
		{!! $pre !!}
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