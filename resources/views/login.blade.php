@push('logo')
	<img class="card-img-top" src="/images/background.jpg">
@endpush

@push('title')
	LOGIN
@endpush

@push('body')
	{!! Form::open(['url' => route('login.post'), 'method' => 'post']) !!}
	{!! Form::bsText(null, 'nip', null, ['placeholder' => 'nip']) !!}
	{!! Form::bsPassword(null, 'password', ['placeholder' => 'password']) !!}
	<a href='{{ route('forget_password') }}'>Forget my password</a>
	{!! Form::bsSubmit('LOGIN', ['class' => 'btn btn-primary float-right']) !!}

	{!! Form::close() !!}
	<p class='pt-3'>
		<hr>
	</p>
	<p class='text-center'>
		Not a member? <a href='{{ route('register') }}'>Sign up now</a>
	</p>
@endpush