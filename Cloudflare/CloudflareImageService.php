<?php
namespace App\Services\Cloudflare;

use GuzzleHttp\Client as GuzzleHttpC;
use GuzzleHttp\Exception\BadResponseException;

class CloudflareImageService
{
    protected $accountId;
    protected $token;
    protected $key;
    protected $deliveryUrl;
    protected $varient;

    public function __construct($accountId = null, $token = null, $key = null, $deliveryUrl = null, $varient = null)
    {
        $this->accountId = $accountId ?: config('cloudflare-images.account_id');
        $this->token = $token ?: config('cloudflare-images.token');
        $this->key = $key ?: config('cloudflare-images.key');
        $this->deliveryUrl = $deliveryUrl ?: config('cloudflare-images.delivery_url');
        $this->varient = $varient ?: config('cloudflare-images.variant');
    }

    public function http() 
    {
        $http = new GuzzleHttpC([
            'base_uri' => 'https://api.cloudflare.com/client/v4/accounts/'.$this->accountId.'/',
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
                'Content-Type' => 'multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW'
                ]
            ]);
        return $http;
    }

    public function upload($file, string $filename = '')
    {
        if ($file instanceof UploadedFile) {
            $path = $file->getRealPath();
        } else {
            $path = $file;
        }

        $input = [
            'file' => [

                'Content-type' => 'multipart/form-data',
                'name'         => 'file',
                'contents'     => fopen($path, 'rb'),
                'filename'     => $filename ?: basename($path),

            ],
            [
            'name'     => 'requireSignedURLs',
            'contents' =>  'false'
            ],
        ];

        try {
            $response = $this->http()->post("images/v1", ['multipart' => $input]);
            $response = json_decode($response->getBody(), true);
            return $response;
            
        } catch (BadResponseException $e) {
            $response = $e->getResponse();      
        
            return $response;
        }
    }

    public function get(string $imageId)
    {
        $response = $this->http()->get("images/v1/".$imageId);
        $response = json_decode($response->getBody(), true);
        return $response;
    }

    public function getImageUrl(string $imageId)
    {
        return $this->deliveryUrl.'/'.$imageId.'/'.$this->varient;
    }

    public function delete(string $imageId)
    {
        try {
            $response = $this->http()->delete("images/v1/".$imageId);
            $response = json_decode($response->getBody(), true);
            return $response;
            
        } catch (BadResponseException $e) {
            $response = $e->getResponse();      
            return $response;
        }
    }


}