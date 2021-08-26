<?php

namespace Onetoweb\Exact;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Onetoweb\Exact\Exception\RequestException;

/**
 * Onetoweb Exact App - Client
 */
class Client
{
    const BASE_URI = 'https://exact.nujob.nl/api/';
    
    /**
     * @var string
     */
    private $bearer;
    
    /**
     * @var string
     */
    private $language = 'en';
    
    /**
     * @param string $bearer
     */
    public function __construct(string $bearer)
    {
        $this->bearer = $bearer;
    }
    
    /**
     * @param string $language
     * 
     * @return void
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }
    
    /**
     * @param string $endpoint
     * @param array $query = []
     * 
     * @return array
     */
    public function get(string $endpoint, array $query = []): ?array
    {
        return $this->request('GET', $endpoint, [], $query);
    }
    
    /**
     * @param string $endpoint
     * @param array $data = []
     * 
     * @return array
     */
    public function post(string $endpoint, array $data = []): ?array
    {
        return $this->request('POST', $endpoint, $data);
    }
    
    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data = []
     * @param array $query = []
     * 
     * @throws RequestException if the reponse contains error messages 
     * 
     * @return array
     */
    public function request(string $method, string $endpoint, array $data = [], array $query = []): ?array
    {
        $client = new GuzzleClient([
            'base_uri' => self::BASE_URI
        ]);
        
        $options = [
            RequestOptions::HEADERS => [
                'Cache-Control' => 'no-cache',
                'Connection' => 'close',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->bearer}",
                'X-Language' => $this->language,
            ],
        ];
        
        if (count($data) > 0) {
            $options[RequestOptions::JSON] = $data;
        }
        
        if (count($query) > 0) {
            $endpoint .= '?'.http_build_query($query);
        }
        
        try {
            
            $response = $client->request($method, $endpoint, $options);
            
            return json_decode($response->getBody()->getContents(), true);
            
        } catch (GuzzleRequestException $guzzleRequestException) {
            
            if (in_array($guzzleRequestException->getCode(), [403, 404, 422, 500])) {
                
                if ($guzzleRequestException->hasResponse()) {
                    
                    $message = $guzzleRequestException->getResponse()->getBody()->getContents();
                    
                    throw new RequestException($message, $guzzleRequestException->getCode(), $guzzleRequestException);
                    
                }
            }
            
            throw $guzzleRequestException;
        }
    }
    
    /**
     * @return array
     */
    public function getProductStocks(): ?array
    {
        return $this->get('product-stock');
    }
    
    /**
     * @param int $page = 1
     * @return array
     */
    public function getProducts(int $page = 1): ?array
    {
        return $this->get('product', [
            'page' => $page
        ]);
    }
    
    /**
     * @param string $sku
     *
     * @return array
     */
    public function getProduct(string $sku): ?array
    {
        return $this->get("product/$sku");
    }
    
    /**
     * @param array $order
     * 
     * @return array
     */
    public function createOrder(array $order): ?array
    {
        return $this->post('order', $order);
    }
    
    /**
     * @param int $orderNumber
     *
     * @return array
     */
    public function getOrder(int $orderNumber): ?array
    {
        return $this->get("order/$orderNumber");
    }
    
    /**
     * @param int $page = 1
     * @return array
     */
    public function getInvoices(int $page = 1): ?array
    {
        return $this->get('invoice', [
            'page' => $page
        ]);
    }
    
    /**
     * @param int $invoiceNumber
     * 
     * @return array
     */
    public function getInvoice(int $invoiceNumber): ?array
    {
        return $this->get("invoice/$invoiceNumber");
    }
}