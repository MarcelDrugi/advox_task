<?php

namespace PMazur\Cats\Service;

use Exception;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class DownloadImg
 * @package PMazur\Cats\Service
 */
class ApiRequest
{
    /**
     * @var string
     */
    public const BASIC_URL = 'https://cataas.com';

    /**
     * @var string
     */
    public const RANDOM_IMAGE_WITH_TEXT = 'cat/says/';

    /**
     * @var ClientFactory
     */
    protected $client;

    /**
     * @var ResponseFactory
     */
    protected $response;

    /**
     * @param ClientFactory $client
     * @param ResponseFactory $response
     */
    public function __construct(ClientFactory $client, ResponseFactory $response)
    {
        $this->client = $client;
        $this->response = $response;
    }


    /**
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     * @return Response
     */
    public function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response {
        if ($uriEndpoint === self::RANDOM_IMAGE_WITH_TEXT) {
            $uriEndpoint .= $params['text'] . '?json=true';
        }
        $client = $this->client->create(['config' => [
            'base_uri' => self::BASIC_URL
        ]]);

        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params['body'] ?? []
            );
        } catch (RequestException $exception) {
            $response = $this->response->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage(),
                'body' => $exception->getResponse() ? $exception->getResponse()->getBody()->getContents() : ''
            ]);
        } catch (GuzzleException $exception) {
            $response = $this->response->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage(),
            ]);
        }

        return $response;
    }
}
