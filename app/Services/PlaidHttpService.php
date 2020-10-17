<?php
/**
 * PlaidHttpService
 */
declare(strict_types=1);

namespace App\Services;

/**
 * Class PlaidHttpService
 * @package App\Services
 */
class PlaidHttpService extends HttpService
{
    /**
     * @var string
     */
    protected $env = 'sandbox';

    /**
     * @var string
     */
    protected $baseUrl = 'https://%s.plaid.com/';

    /**
     * @var array
     */
    protected $authBits = [];

    /**
     * PlaidHttpService constructor.
     */
    public function __construct()
    {
        $this->url = sprintf($this->baseUrl, $this->env);
        parent::__construct($this->url, []);
    }

    /**
     * @return PlaidHttpService
     */
    public function sandbox(): self
    {
        $this->url = sprintf($this->baseUrl, 'sandbox');
        $this->new($this->url, []);

        return $this;
    }

    /**
     * @return PlaidHttpService
     */
    public function development(): self
    {
        $this->url = sprintf($this->baseUrl, 'development');
        $this->new($this->url, []);

        return $this;
    }

    /**
     * @param $data
     * @return HttpService
     */
    public function auth($data): HttpService
    {
        $this->authBits = $data;

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
        return parent::request($action, array_merge((array) $data, $this->authBits));
    }
}
