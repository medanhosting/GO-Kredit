<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;

use Exception, DB, Auth, Carbon\Carbon;

class AngsuranController extends Controller
{
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();
	}

	public function index() 
	{
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$angsuran 	= NotaBayar::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->wherehas('details', function($q){$q;})->Displaying()->paginate();

		view()->share('active_submenu', 'angsuran');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.angsuran.index', compact('angsuran'));
		return $this->layout;
	}
}