<?php

namespace App\Service\System;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Finance\Models\DetailTransaksi;

interface AkunKreditInterface {
	
	public function pencairan(DetailTransaksi $model, Aktif $kredit);
	public function setoran_pencairan(DetailTransaksi $model, Aktif $kredit);
	public function piutang_pokok(DetailTransaksi $model, Aktif $kredit);
	public function piutang_bunga(DetailTransaksi $model, Aktif $kredit);
	public function piutang_denda(DetailTransaksi $model, Aktif $kredit);
	public function bayar_titipan(DetailTransaksi $model, Aktif $kredit);
	public function bayar_pokok(DetailTransaksi $model, Aktif $kredit);
	public function bayar_bunga(DetailTransaksi $model, Aktif $kredit);
	public function bayar_denda(DetailTransaksi $model, Aktif $kredit);
}