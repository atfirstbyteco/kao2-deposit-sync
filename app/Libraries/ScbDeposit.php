<?php
namespace App\Libraries;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Carbon\Carbon;
use Config;
class ScbDeposit {
    protected $endpoint = "http://localhost/";
    protected $apikey = "";
    protected $client;
    protected $graph;
    protected $headers = [];
    public function __construct()
    {
        $this->headers = [
            'Content-Type' => 'application/json'
        ];
        if(Config::get('services.scb.deposit.endpoint')){
            $this->endpoint = Config::get('services.scb.deposit.endpoint');
        }
        if(Config::get('services.scb.deposit.headers')){
            $this->headers = array_merge($this->headers,Config::get('services.scb.deposit.headers'));
        }
        $this->client = new Client([
            'base_uri' => $this->endpoint(),
            'headers' => $this->headers,
            'verify' => false
        ]);
        $this->graph = new Client([
            'base_uri' => 'https://graph.facebook.com/',
            'verify' => false
        ]);
    }
    public function endpoint($requestPath = '')
    {
        return $this->endpoint . $requestPath;
    }
    public function apikey()
    {
        return $this->apikey;
    }
    public function updateShare()
    {
        try{
            $request = $this->graph->post('/',[
                'form_params' => [
                    'id' => 'https://www.kaokonlakao.com/',
                    'scrape' => true,
                    'access_token' => '817308938474623|zEk-ZTgsR732cve4qjlc8aCAE-I'
                ]
            ]);
            return json_decode($request->getBody()->getContents());
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
    public function getBalance($accountNo)
    {
        $headers = array_merge($this->headers,[
            'requestUID' => str_random(32)
        ]);
        $requestbody = [
            "accountNumber" => $accountNo,
            'accountCurrency' => env('SCB_DEPOSIT_HEADER_ACCOUNT_CURRENCY',764),
            'includeBalance' => env('SCB_DEPOSIT_HEADER_INCLUDE_BALANCE',true),
            'includeExtBalance' => env('SCB_DEPOSIT_HEADER_INCLUDE_EXT_BALANCE',true),
            'includeInterest' => env('SCB_DEPOSIT_HEADER_INCLUDE_INTEREST',true)
        ];
        try{

            $request = $this->client->post('accounts/deposits/inquiry', [
                'headers' => $headers,
                'verify' => false,
                'json' => $requestbody
            ]);
            return json_decode($request->getBody()->getContents());
        }catch(\Exception $e){
            \Log::critical($e->getMessage(),[
                'HEADERS' => json_encode($headers),
                'REQUESTBODY' => json_encode($requestbody)
            ]);
            return false;
        }

    }
}
