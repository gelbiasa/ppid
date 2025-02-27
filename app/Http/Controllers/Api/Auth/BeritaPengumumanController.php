<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Website\WebMenuModel;

class BeritaPengumumanController extends BaseApiController
{
    public function getBeritaPengumuman()
    {
        return $this->executeWithAuth(
            function() {
                $beritaPengumuman = WebMenuModel::selectBeritaPengumuman();
                return  $beritaPengumuman ;
            },
            'beritaPengumuman'
        );
    }
}