@foreach (['success', 'info', 'danger', 'warning'] as $x)
	@if (session()->has('alert_' . $x))
		<div class='alert alert-{{ $x }}'>
			{{ session()->get('alert_' . $x) }}
		</div>
	@endif
@endforeach

@if($errors->has())
   @foreach ($errors->all() as $error)
      <div class="alert alert-danger">{{ $error }}</div>
  @endforeach
@endif