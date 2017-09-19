<div class="row {{ $class_row ? $class_row : '' }}">
	<div class="col-4">
		<p class="text-secondary text-capitalize">{{ $field }}</p>
	</div>
	<div class="col">
		<p class="{{ $field != 'email' ? 'text-capitalize' : ' '}}">{{ str_replace('_', ' ', $value) }}</p>
	</div>
</div>