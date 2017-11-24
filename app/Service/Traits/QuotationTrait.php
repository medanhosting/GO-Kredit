<?php

namespace App\Service\Traits;

use Thunderlabid\Underwriting\Models\Application;
use Thunderlabid\Pricelist\Models\Object;
use Thunderlabid\Pricelist\Models\Price;

use Thunderlabid\Underwriting\Libraries\Glossary;

use Thunderlabid\Member\Models\Member;

use Exception, Carbon\Carbon, Auth;

/**
 * Trait Link list
 *
 * Digunakan untuk initizialize link list mode
 *
 * @package    TTagihan
 * @author     C Mooy <chelsymooy1108@gmail.com>
 */
trait QuotationTrait {
 	 	
	/**
	 * Add Event_list to queue
	 * @param [IEvent_list] $event_list 
	 */
	public function find_valuation_vehicle($mode, $merk, $year, $model, $type)
	{
		switch ($mode) {
			case 'mobil':
			$otype 	= [Glossary::OBJECT_CAR,Glossary::OBJECT_BUS,Glossary::OBJECT_TRUCK,Glossary::OBJECT_PICKUP];
				break;
			case 'motor':
			$otype 	= [Glossary::OBJECT_MOTORCYCLE];
				break;
		}
		
		//GET VALUATION
		$v 		= Object::whereIn('object_type', $otype)->where('merk', $merk)
				->where('model', $model)
				->where('type', $type)
				->where('year', '<=', $year)
				->first();
		
		if(!$v){
			throw new Exception("Kendaraan tidak ada dalam coverage list kami", 1);
		}
		$v 		= $v->toArray();

		$price 	= Price::where('object_id', $v['id'])->whereraw(\DB::raw('YEAR(changed_at) >= '.$year))
				->orderby('changed_at', 'desc')->first();

		if(!$price){
			throw new Exception("Kendaraan tidak ada dalam coverage list kami", 1);
		}

		$v['price']	= $price->price;

		return $v;
	}

	public function parse_object_vehicle(Application $application, $object, $usage, $ownership, $license_plate, $color = null, $machine_id = null, $body_id = null, $accessories_valuation, $accessories, $photos = null){

		$app_obj['class']	= $object['object_type'];

		switch (strtolower($usage)) {
			case 'private':
				$app_obj['usage'] 	= Glossary::OBJECT_USAGE_PRIVATE;
				break;
			case 'public':
				$app_obj['usage'] 	= Glossary::OBJECT_USAGE_PUBLIC;
				break;
			case 'commercial':
				$app_obj['usage'] 	= Glossary::OBJECT_USAGE_COMMERCIAL;
				break;
		}

		switch (strtolower($ownership)) {
			case 'perorangan':
				$app_obj['ownership'] 	= Glossary::OBJECT_OWNERSHIP_PRIVATE;
				break;
			case 'perusahaan':
				$app_obj['ownership'] 	= Glossary::OBJECT_OWNERSHIP_COMPANY;
				break;
		}

		$app_obj['brand']			= $object['merk'];
		$app_obj['type']			= $object['type'];
		$app_obj['model']			= $object['model'];
		$app_obj['license_plate']	= $license_plate;
		$app_obj['color']			= $color;
		$app_obj['machine_id']		= $machine_id;
		$app_obj['body_id']			= $body_id;
		$app_obj['year']			= $object['year'];
		$app_obj['valuation']		= $this->formatMoneyFrom($object['price']);
		$app_obj['accessories_valuation']	= $this->formatMoneyFrom($accessories_valuation);
		$app_obj['accessories']				= explode(',', $accessories);
		$app_obj['photos']			= $photos;

		$app_obj 	= array_filter($app_obj);
		if(!is_null($application->object)){
			$app_obj = array_merge($application->object, $app_obj);
		}

		$application->object 	= $app_obj;

		return $application;
	}

	public function parse_coverage_vehicle(Application $application, $level, $at = null, $extended_perils = null, $extended_covers = null){
		$coverage['nth'] 		= 12;
		$coverage['count'] 		= 12;

		if(!is_null($at)){
			$coverage['at'] 	= Carbon::parse($at);
		}else{
			$coverage['at']		= Carbon::now()->addHours(84)->startofday()->addHours(12);
		}

		$coverage['until'] 		= $coverage['at']->addMonth(1);

		$perils 				= explode(',', $extended_perils);
	
		$coverage['extended_perils']	= [];
		foreach ($perils as $k => $v) {
			switch (strtoupper($v)) {
				case 'EQVET':
					$coverage['extended_perils'][]	= Glossary::XPERIL_EQVET;
					break;
				case 'FTSWD':
					$coverage['extended_perils'][]	= Glossary::XPERIL_FTSWD;
					break;
				case 'SRCC':
					$coverage['extended_perils'][]	= Glossary::XPERIL_SRCC;
					break;
				case 'TS':
					$coverage['extended_perils'][]	= Glossary::XPERIL_TS;
					break;
			}
		}

		$coverage['extended_covers']	= [];
		foreach ($extended_covers as $k => $v) {
			switch (strtoupper($v['cover'])) {
				case 'TPL':
					$coverage['extended_covers'][$k]['cover']	= Glossary::XCOVER_TPL;
					$coverage['extended_covers'][$k]['amount']	= $this->formatMoneyFrom($v['amount']);
					break;
			}
		}

		switch (strtoupper($level)) {
			case 'COMPREHENSIVE':
				$coverage['level']	= Glossary::COVERAGE_COMPREHENSIVE;
				break;
			case 'TLO':
				$coverage['level']	= Glossary::COVERAGE_TLO;
				break;
		}

		$coverage 		= array_filter($coverage);
		if(!is_null($application->coverage)){
			$coverage	= array_merge($application->coverage, $coverage);
		}

		$application->coverage 	= $coverage;

		return $application;
	}

	public function parse_owner_and_creator(Application $application){
		if(Auth::check()){
			$member		= Member::where('email', Auth::user()->email)->first();
			$application->created_by 	= $member->cif_number;
		}

		//owner	
		$owner			= Member::where('email', $application->email)->first();
		if($owner){
			$application->owner_id 		= $owner->cif_number;
		}

		return $application;
	}

	public function parse_object_person(Application $application, $object, $ahli_waris, $photos){
		$app_obj['class']	= Glossary::OBJECT_PERSON;
		$app_obj['nik']		= $object['nik'];
		$app_obj['dob']		= $object['dob'];

		$app_obj['ahli_waris']['name']		= $ahli_waris['name'];
		$app_obj['ahli_waris']['nik']		= $ahli_waris['nik'];
		$app_obj['ahli_waris']['relation']	= $ahli_waris['relation'];

		$app_obj['photos']	= $photos;

		$app_obj 	= array_filter($app_obj);
		if(!is_null($application->object)){
			$app_obj= array_merge($application->object, $app_obj);
		}
		$application->object 	= $app_obj;

		return $application;
	}

	public function parse_coverage_person(Application $application, $level, $at = null){
		$coverage['nth'] 		= 12;
		$coverage['count'] 		= 12;

		if(!is_null($at)){
			$coverage['at'] 	= Carbon::parse($at);
		}else{
			$coverage['at']		= Carbon::now()->addHours(84)->startofday()->addHours(12);
		}

		$coverage['until'] 		= $coverage['at']->addMonth(1);

		switch (strtoupper($level)) {
			case 'PLAN A':
				$coverage['level']	= Glossary::COVERAGE_PA_PLAN_A;
				break;
			case 'PLAN B':
				$coverage['level']	= Glossary::COVERAGE_PA_PLAN_B;
				break;
			case 'PLAN C':
				$coverage['level']	= Glossary::COVERAGE_PA_PLAN_C;
				break;
			case 'PLAN D':
				$coverage['level']	= Glossary::COVERAGE_PA_PLAN_D;
				break;
		}
		
		$coverage 		= array_filter($coverage);
		if(!is_null($application->coverage)){
			$coverage 	= array_merge($application->coverage, $coverage);
		}
		$application->coverage 	= $coverage;

		return $application;
	}
}