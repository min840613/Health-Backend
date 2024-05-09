<?php

namespace App\Helpers\Indexing;

use App\Exceptions\GoogleIndexingException;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * https://developers.google.com/search/apis/indexing-api/v3/using-api?hl=zh-tw
 */
class GoogleIndexingHelper implements IndexingInterface
{
    /**
     * @param string $url
     * @return array
     * @throws ContainerExceptionInterface
     * @throws GoogleIndexingException
     * @throws NotFoundExceptionInterface
     * @throws RequestException
     */
    public function get(string $url): array
    {
        $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications/metadata';

        $response = \Http::withToken($this->token())->get($endpoint, ['url' => $url]);

        if ($response->failed()) {
            throw new GoogleIndexingException($response->throw());
        }

        if ($this->isNeedLog()) {
            \Log::debug('google indexing get log', ['url' => $url]);
        }

        return $response->json();
    }

    /**
     * @param string $url
     * @return array
     * @throws ContainerExceptionInterface
     * @throws GoogleIndexingException
     * @throws NotFoundExceptionInterface
     * @throws RequestException
     */
    public function create(string $url): array
    {
        $token = $this->update($url);

        if ($this->isNeedLog()) {
            \Log::debug('google indexing create log', ['url' => $url]);
        }

        return $token;
    }

    /**
     * @param string $url
     * @return array
     * @throws ContainerExceptionInterface
     * @throws GoogleIndexingException
     * @throws NotFoundExceptionInterface
     * @throws RequestException
     */
    public function update(string $url): array
    {
        $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

        $response = \Http::withToken($this->token())->post($endpoint, [
            'url' => $url,
            'type' => 'URL_UPDATED',
        ]);

        if ($response->failed()) {
            throw new GoogleIndexingException($response->throw());
        }

        if ($this->isNeedLog()) {
            \Log::debug('google indexing update log', ['url' => $url]);
        }

        return $response->json();
    }

    /**
     * @param string $url
     * @return array
     * @throws ContainerExceptionInterface
     * @throws GoogleIndexingException
     * @throws NotFoundExceptionInterface
     * @throws RequestException
     */
    public function delete(string $url): array
    {
        $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

        $response = \Http::withToken($this->token())->post($endpoint, [
            'url' => $url,
            'type' => 'URL_DELETED',
        ]);

        if ($response->failed()) {
            throw new GoogleIndexingException($response->throw());
        }

        if ($this->isNeedLog()) {
            \Log::debug('google indexing delete log', ['url' => $url]);
        }

        return $response->json();
    }

    /**
     * 因為 service account 是 impersonate 的關係，無法取到有效的 access token，後續有解方把這邊調整掉
     * @return array
     * @throws GoogleIndexingException
     */
    public function generateToken(): array
    {
        $serviceAccountKeyFile = config('indexing.service_account_file');

        try {
            exec('/home/ec2-user/google-cloud-sdk/bin/gcloud auth activate-service-account --key-file=' . $serviceAccountKeyFile . ' && curl -X POST -H "Authorization: Bearer $(/home/ec2-user/google-cloud-sdk/bin/gcloud auth print-access-token)" -H "Content-Type: application/json; charset=utf-8"  --data \'{"scope": ["https://www.googleapis.com/auth/indexing"],"lifetime": "3600s" }\'  "https://iamcredentials.googleapis.com/v1/projects/-/serviceAccounts/page-crawler@tvbs-health-search-console.iam.gserviceaccount.com:generateAccessToken"', $output);

            $token = json_decode(implode('', $output), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            \Log::alert('產生 google index access token 失敗', [$exception]);
            throw new GoogleIndexingException($exception);
        }

        return $token;
    }

    /**
     * @param string $tokenName
     * @param string $token
     * @param Carbon $expireTime
     * @return void
     */
    public function keepToken(string $tokenName, string $token, Carbon $expireTime): void
    {
        cache()->put($tokenName, $token, $expireTime);
    }

    /**
     * @param string|null $tokenName
     * @return string
     * @throws ContainerExceptionInterface
     * @throws GoogleIndexingException
     * @throws NotFoundExceptionInterface
     */
    private function token(string $tokenName = null): string
    {
        $tokenName = $tokenName ?? config('indexing.token_name');

        $token = cache()->get($tokenName);

        if (empty($token)) {
            $tokens = $this->generateToken();

            $this->keepToken($tokenName, $tokens['accessToken'], Carbon::parse($tokens['expireTime']));

            $token = $tokens['accessToken'];
        }

        return $token;
    }

    /**
     * @return bool
     */
    private function isNeedLog(): bool
    {
        return config('indexing.need_log');
    }
}
