<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Website\Footer\FooterModel;


class ApiFooterController extends BaseApiController
{
     public function getDataFooter()
     {
         return $this->eksekusiDenganOtentikasi(
             function() {
                 $footerData = FooterModel::getDataFooter();
                 return $footerData;
             },
             'footer_data'
         );
     }
 }