<?php

return [
	'region'		=>	env('AWS_DEFAULT_REGION', 'us-east-1'),
    'access_key'	=>	env('AWS_ACCESS_KEY_ID'),
    'secret_key'	=>	env('AWS_SECRET_ACCESS_KEY'),
	'ec_api_url'	=>	env('MEMBER_EC_API_URL',''), // 預設測試環境，正式請去除『-test』
	'crm_url'		=>	env('MEMBER_CRM_URL','') // 預設測試環境，正式請去除『-st』
];
