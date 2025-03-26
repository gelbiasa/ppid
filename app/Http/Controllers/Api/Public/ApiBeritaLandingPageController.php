<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseApiController;

use App\Models\Website\Publikasi\Berita\BeritaDinamisModel;




class ApiBeritaLandingPageController extends BaseApiController
{
    public function getDataBeritaLandingPage()
    {
        return $this->execute(
            function() {
                $beritaLandingPage = BeritaDinamisModel::getDataBeritaLandingPage();
                return $beritaLandingPage;
            },
            'Berita LandingPage'
        );
    }
}