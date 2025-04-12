<?php

namespace App\Services;
use App\Models\Channel;
use Illuminate\Support\Facades\File;
use Http;
use Illuminate\Http\UploadedFile;

class BackBlazeService
{
    private $channel;
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }
    private function getAuthorizationBody()
    {
        $blazeKey = config('backblaze.blaze_key');
        $blazeKeyId = config('backblaze.blaze_key_id');
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($blazeKeyId . ':' . $blazeKey)
        ])->get('https://api.backblazeb2.com/b2api/v3/b2_authorize_account');
        return $response->json();
    }

    private function getUploadUrl(string $authorizationToken, string $apiUrl)
    {
        $bucketId = config('backblaze.blaze_bucket_id');
        $response = Http::withHeaders([
            'Authorization' => $authorizationToken
        ])->get($apiUrl . '/b2api/v3/b2_get_upload_url?bucketId=' . $bucketId);

        return $response->json();
    }

    public function uploadFile(UploadedFile $filePath, $channelId)
    {
        $authorizationBody = $this->getAuthorizationBody();
        $uploadUrlBody = $this->getUploadUrl($authorizationBody['authorizationToken'], $authorizationBody['apiInfo']['storageApi']['apiUrl']);
        $response = Http::withHeaders([ 
            //'Content-Type' in withHeaders is completely ignored on post
            'Authorization' => $uploadUrlBody['authorizationToken'],
            'X-Bz-File-Name' => urlencode(mb_convert_encoding('channel' . $channelId . '/' .$filePath->getClientOriginalName(), 'UTF-8', 'ISO-8859-1')),
            'Content-Length' => File::size($filePath),
            'X-Bz-Content-Sha1' => sha1_file($filePath)
        ])->withBody(File::get($filePath))->contentType('b2/x-auto')->retry(3)->post($uploadUrlBody['uploadUrl']);
        error_log(json_encode($response->json()));
        return $response->json('fileId');
    }

    public function getDownloadAuth($channel_id)
    {
        $authorizationBody = $this->getAuthorizationBody();
        $apiUrl = $authorizationBody['apiInfo']['storageApi']['apiUrl'];
        $bucketId = config('backblaze.blaze_bucket_id');

        $downloadAuthBody = Http::withHeaders([
            'Authorization' => $authorizationBody['authorizationToken']
        ])->withQueryParameters([
            'bucketId' => $bucketId,
            'fileNamePrefix' => 'channel' . $channel_id . '/',
            'validDurationInSeconds' => config('backblaze.blaze_token_duration'),
            'b2ContentDisposition' => 'attachment'
        ])->get($apiUrl . '/b2api/v3/b2_get_download_authorization');

        return (object)['authorizationToken' => $downloadAuthBody->json('authorizationToken'), 'apiUrl' => $apiUrl];
    }
}
