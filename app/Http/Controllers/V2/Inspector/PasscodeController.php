<?php

namespace App\Http\Controllers\V2\Inspector;

use App\Http\Controllers\Controller;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\SurveiPasscode;

use Exception, Auth, Carbon\Carbon;

class PasscodeController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware('scope:'.implode('|', $this->acl_menu['inspektor.passcode']));
	}

	public function index () 
	{
		$passcode	= new SurveiPasscode;

		$passcode 	= $passcode->paginate(15, ['*'], 'passcode');

		view()->share('is_passcode_tab', 'show active');

		view()->share('active_submenu', 'passcode');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.inspector.passcode.index', compact('passcode'));
		return $this->layout;
	}
	
	public function store($id = null)
	{
		try {
			$survei 	= Survei::where('pengajuan_id', request()->get('pengajuan_id'))->where('kode_kantor', request()->get('kantor_aktif_id'))->orderby('tanggal', 'desc')->first();

			$passcode 				= new SurveiPasscode;
			$passcode->survei_id 	= $survei['id'];
			$passcode->passcode 	= $this->generatePasscode();
			$passcode->expired_at 	= Carbon::now()->addHours(2)->format('d/m/Y H:i');
			$passcode->save();

			return redirect(route('passcode.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]));
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	private function generatePasscode($length = 8){
		$str = '';

		do {
			if(rand(0,1)){
				$str 	= $str.strtoupper(chr(rand(97,122)));
			}
			else{
				$str 	= $str.rand(0,9);
			}
			$length = $length - 1;
		} while ($length > 0);

		return $str;
	}
}