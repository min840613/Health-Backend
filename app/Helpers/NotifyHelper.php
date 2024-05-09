<?php

namespace App\Helpers;

use Aws\Sns\SnsClient;

/**
 * Class NotifyHelper
 * @package App\Helpers
 */
class NotifyHelper
{
    /**
     * @param array $config
     * @param string $message
     * @param string $topicName
     */
    public static function sns(array $config, string $message, string $topicName): void
    {
        $snsClient = SnsClient::factory([
            'version' => $config['version'],
            'region' => $config['region'],
            'credentials' => $config['credentials'],
        ]);

        try {
            //發送推播
            $snsClient->publish([
                'TopicArn' => $config['arn'] . $topicName,
                'MessageStructure' => 'json',
                'Message' => $message,
            ]);
        } catch (\Throwable $exception) {
            \Log::alert($exception);
        }
    }
}
