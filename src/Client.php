<?php

namespace WildTuna\ImlSdk;

class Client
{
    /** @var \GuzzleHttp\Client|null */
    private $httpApi = null;

    /** @var \GuzzleHttp\Client|null */
    private $httpList = null;


    /**
     * Client constructor.
     * @param int $timeout - таймаут ожидания ответа от серверов BoxBerry в секундах
     */
    public function __construct($timeout = 300)
    {
        $this->httpApi = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.iml.ru',
            'timeout' => $timeout,
        ]);

        $this->httpList = new \GuzzleHttp\Client([
            'base_uri' => 'https://list.iml.ru',
            'timeout' => $timeout,
        ]);
    }
}