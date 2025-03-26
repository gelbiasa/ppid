<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseApiController;

use App\Models\Website\Publikasi\Berita\BeritaDinamisModel;




class ApiBeritaController extends BaseApiController
{
    public function getDataBerita()
    {
        return $this->execute(
            function() {
                $berita = BeritaDinamisModel::getDataBerita();
                return $berita;
            },
            'Berita LandingPage'
        );
    }
}