<?php

namespace App\Http\Controllers;

////////////////////
// INFRASTRUCTURE //
////////////////////
use Auth;
use Exception;
use Validator;
use Carbon\Carbon;

////////////////////
// MODEL 		  //
////////////////////
use Thunderlabid\Manajemen\Models\Orang;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

class LoginController extends Controller
{
	protected $layout;

	function __construct()
	{
		view()->share('html', ['title' => 'KLEPON - Social Media Manager']);
		$this->layout = view('templates.html.layout2');
	}

	public function login() {
		$this->layout->pages = view('login');
		return $this->layout;
	}

	public function post_login() {

		$email 		= request()->input('email');
		$password 	= request()->input('password');
		$credential = ['email' => $email, 'password' => $password];
		if (Auth::attempt($credential))
		{
			//get kantor id
			$hari_ini 	= Carbon::now();
			$penempatan	= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active($hari_ini)->first();
			
			return redirect()->route('home', ['kantor_aktif_id' => $penempatan['kantor_id']]);
		}
		else
		{
			return redirect()->route('login')->withErrors();
		}
	}

	public function logout() {
		request()->session()->flush();
		return redirect()->route('login');
	}

	public function forget_password()
	{
		$this->layout->pages = view('forget_password');
		return $this->layout;
	}

	public function post_forget_password()
	{
		$orang = Orang::email(request()->input('email'))->first();

		if (!$orang)
		{
			session()->flash('alert_danger', 'Your email has not been registered as a member');
			return redirect()->route('login');
		}
		else
		{
			session()->flash('alert_info', 'This feature is still under development');
			return redirect()->route('login');
		}
	}

	public function register()
	{
		$this->layout->pages = view('register');
		return $this->layout;
	}

	public function post_register()
	{
		////////////////////////////////////
		// Validate Password Confirmation //
		////////////////////////////////////
		$validator = Validator::make(request()->input(), ['password' => 'min:6', 'confirmed']);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$orang = new Orang(request()->input());
		try {
			$orang->save();
			session()->flash('alert_success', 'Your account has been created. Please enter your email and password to start using ' . config('APP_NAME'));
			return redirect()->route('login');
		} catch (Exception $e) {
			foreach ($e->getMessage()->messages()['email'] as $error)
			{
				if (str_is('The email*taken*', $error))
				{
					session()->flash('alert_danger', 'Your account is already registered as ' . config('APP_NAME') . ' member. Please enter your email and password below.');
					return redirect()->route('login');
				}
			}
			return redirect()->back()->withInput()->withErrors($e->getMessage());
		}
	}
}

