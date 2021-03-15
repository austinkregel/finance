<?php
/**
 * HttpService
 */
declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Collection;

/**
 * Class HttpService
 */
class HttpService
{
    /**
     * @var
     */
    protected static $instance;

    /**
     * @var mixed
     */
    public $data;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string This is the URL path after the base URL
     */
    public $path;

    /**
     * @var self
     */
    public $client;

    /**
     * @var Collection
     */
    public $response;

    public function __construct($url, $data = [])
    {
        $this->url = $url;
        $this->new($url, $data);
    }

    /**
     * PHP Singleton pattern so we don't have to constantly new up new objects.
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        if (empty(self::$instance)) {
            self::$instance = new static;
        }

        return call_user_func_array([self::$instance, $method], $arguments);
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([self::$instance, $method], $arguments);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->response->toArray();
    }

    /**
     * @param $url
     * @param $path
     * @param array $data
     * @return $this
     */
    protected function new($path, $data = [])
    {
        $this->path = $path;
        $this->client = new Client(array_merge(['base_uri' => $this->url], $data));

        return $this;
    }

    /**
     * @param $action
     * @param null $data
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    protected function request($action, $data = [])
    {
        if (! in_array(strtolower($action), ['get', 'post', 'put', 'delete', 'patch'])) {
            throw new \Exception('Your desired action is not supported');
        }

        if (empty($data)) {
            $body = [
                'body' => '{}',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accepts' => 'application/json',
                ],
            ];
        } else {
            $body = [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accepts' => 'application/json',
                ],
            ];
        }

        try {
            $response = $this->client->request($action, $this->path, $body);

            $json = $response->getBody()->getContents();
        } catch (ClientException $exception) {
            throw new \Exception($exception->getResponse()->getBody()->getContents());
        }

        return $this->response = collect(json_decode($json));
    }

    /**
     * @param null $data
     * @return Collection
     * @throws \Exception
     */
    public function get($path, $data = null)
    {
        $this->path = $path;

        return $this->request('get', $data);
    }

    /**
     * @param null $data
     * @return Collection
     * @throws \Exception
     */
    public function post($path, $data = null)
    {
        $this->path = $path;

        return $this->request('post', $data);
    }

    /**
     * @param null $data
     * @return Collection
     * @throws \Exception
     */
    public function patch($path, $data = null)
    {
        $this->path = $path;

        return $this->request('patch', $data);
    }

    /**
     * @param null $data
     * @return Collection
     * @throws \Exception
     */
    public function delete($path, $data = null)
    {
        $this->path = $path;

        return $this->request('delete', $data);
    }

    /**
     * @param null $data
     * @return Collection
     * @throws \Exception
     */
    public function put($path, $data = null)
    {
        $this->path = $path;

        return $this->request('put', $data);
    }
}
