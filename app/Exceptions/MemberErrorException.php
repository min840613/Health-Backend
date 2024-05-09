<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Log;

class MemberErrorException extends \Exception
{
    public static function renderMemberException($exception)
    {
        // 記錄例外信息
        if ($exception->getMessage() != '驗證失敗') {
            Log::alert($exception->getMessage() . "\n" . $exception->getTraceAsString());
        }

        if ($exception->getCode() == 400) {
            $response = [
                'status' => '10001',
                'message' => '請輸入正確之TVBS_PROFILE',
            ];
            return new Response($response, $exception->getCode());
        } elseif ($exception->getCode() == 500) {
            $response = [
                'status' => '10002',
                'message' => '系統錯誤，請聯絡系統管理員',
            ];
            return new Response($response, $exception->getCode());
        } else {
            $response = [
                'status' => '10003',
                'message' => '系統錯誤',
            ];;
            return new Response($response, $exception->getCode());
        }
    }
}
