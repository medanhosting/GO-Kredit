@foreach (['success', 'info', 'danger', 'warning'] as $x)
	@if (session()->has('alert_' . $x))
		<div class='alert alert-{{ $x }}'>
			{{ session()->get('alert_' . $x) }}
		</div>
	@endif
@endforeach