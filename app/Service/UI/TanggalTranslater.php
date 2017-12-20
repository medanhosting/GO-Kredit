<?php

namespace App\Service\UI;

use App\Service\Traits\TanggalDescTrait;
use App\Service\Traits\BulanDescTrait;
use App\Service\Traits\WaktuTrait;

class TanggalTranslater
{
	use TanggalDescTrait;
	use BulanDescTrait;
	use WaktuTrait;
}