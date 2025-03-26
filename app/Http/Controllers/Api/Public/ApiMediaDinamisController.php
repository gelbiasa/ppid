<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Website\LandingPage\MediaDinamis\MediaDinamisModel;

class ApiMediaDinamisController extends BaseApiController
{
    public function getDataHeroSection()
    {
        return $this->execute(
            function() {
                $heroSection = MediaDinamisModel::getDataHeroSection();
                return $heroSection;
            },
            'Hero Section'
        );
    }
    public function getDataDokumentasi()
    {
        return $this->execute(
            function() {
                $dokumentasi = MediaDinamisModel::getDataDokumentasi();
                return $dokumentasi;
            },
            'Dokumentasi PPID'
        );
    }
    public function getDataMediaInformasiPublik()
    {
        return $this->execute(
            function() {
                $mediainformasi = MediaDinamisModel::getDataMediaInformasiPublik();
                return $mediainformasi;
            },
            'Dokumentasi PPID'
        );
    }
}