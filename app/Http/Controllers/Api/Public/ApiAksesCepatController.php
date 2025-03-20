<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Website\LandingPage\KategoriAkses\KategoriAksesModel;


class ApiAksesCepatController extends BaseApiController
{
    public function getDataAksesCepat()
    {
        return $this->execute(
            function() {
                $aksesCepat = KategoriAksesModel::getDataAksesCepat();
                return $aksesCepat;
            },
            'Akses Cepat'
        );
    }
}