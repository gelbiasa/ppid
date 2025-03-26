<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseApiController;

use App\Models\Website\Publikasi\Pengumuman\PengumumanDinamisModel;



class ApiPengumumanLandigPageController extends BaseApiController
{
    public function getDataPengumumanLandingPage()
    {
        return $this->execute(
            function() {
                $pengumumanLandingPage = PengumumanDinamisModel::getDataPengumumanLandingPage();
                return $pengumumanLandingPage;
            },
            'Pengumuman LandingPage'
        );
    }
}