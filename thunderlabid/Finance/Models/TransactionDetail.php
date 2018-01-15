<?php

namespace Thunderlabid\Finance\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Validator;

////////////
// EVENTS //
////////////
use Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailCreated;
use Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailCreating;
use Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailUpdated;
use Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailUpdating;
use Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailDeleted;
use Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailDeleting;
use Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailRestored;
use Thunderlabid\Finance\Events\TransactionDetail\TransactionDetailRestoring;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;

use Config;

class TransactionDetail extends Model
{
	use SoftDeletes, IDRTrait, WaktuTrait;

	protected $table 	= 'f_transaction_detail';
	protected $fillable = ['tanggal', 'amount', 'morph_reference_tag', 'morph_reference_id', 'description'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $dispatchesEvents = [
        'created' 	=> TransactionDetailCreated::class,
        'creating' 	=> TransactionDetailCreating::class,
        'updated' 	=> TransactionDetailUpdated::class,
        'updating' 	=> TransactionDetailUpdating::class,
        'deleted' 	=> TransactionDetailDeleted::class,
        'deleting' 	=> TransactionDetailDeleting::class,
        'restoring' => TransactionDetailRestoring::class,
        'restored' 	=> TransactionDetailRestored::class,
    ];

	// ------------------------------------------------------------------------------------------------------------
	// CONSTRUCT
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// BOOT
	// ------------------------------------------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------------------------------------------
	// RELATION
	// ------------------------------------------------------------------------------------------------------------
	public function coa(){
		return $this->belongsTo(COA::class, 'id', 'transaction_detail_id');
	}

	public function account(){
		return $this->belongsToMany(Account::class, 'f_coa', 'transaction_detail_id', 'akun_id');
	}

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setAmountAttribute($variable){
		$this->attributes['amount']  	= $this->formatMoneyFrom($variable);
	}

	public function setTanggalAttribute($variable){
		$this->attributes['tanggal']  	= $this->formatDateTimeFrom($variable);
	}

	// ------------------------------------------------------------------------------------------------------------
	// ACCESSOR
	// ------------------------------------------------------------------------------------------------------------
	public function getIsDeletableAttribute()
	{
		return true;
	}

	public function getIsSavableAttribute()
	{
		//////////////////
		// Create Rules //
		//////////////////
		$rules['cif_number'] 		= ['required'];
		$rules['amount'] 			= ['required', 'numeric'];
		$rules['description'] 		= ['string'];

		//////////////
		// Validate //
		//////////////
		$validator = Validator::make($this->attributes, $rules);
		if ($validator->fails())
		{
			$this->errors = $validator->messages();
			return false;
		}
		return true;
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}

	public function getAmountAttribute($variable){
		return $this->formatMoneyTo($this->attributes['amount']);
	}

	public function getTanggalAttribute($variable){
		return $this->formatDateTimeTo($this->attributes['tanggal']);
	}
}