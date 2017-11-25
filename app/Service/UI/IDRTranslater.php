<?php

namespace App\Service\UI;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\TerbilangTrait;

class IDRTranslater
{
	use IDRTrait;
	use TerbilangTrait;

	 public static function instance() {
        return new IDRTranslater();
    }
}