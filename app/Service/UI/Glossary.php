<?php

namespace App\Service\UI;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\TerbilangTrait;

class Glossary
{
	 public static function pinjaman($val) {
	 	switch (strtolower($val)) {
	 		case 'pt':
	 			return 'Musiman [PT]';
	 			break;
	 		default:
	 			return 'Angsuran [PA]';
	 			break;
	 	}

	 	return 'Angsuran [PA]';
    }
}