<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tvbs\Member\MemberDecryptorFacade;

class MemberApiController extends Controller
{   
    public function decrypt(Request $request)
    {
    	$tvbs_profile = $request->query('tvbs_profile');
        $MemberData = MemberDecryptorFacade::checkLogin($tvbs_profile);
        return response()->success($MemberData['data']);
    }
}
