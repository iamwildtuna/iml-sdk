<?php

namespace WildTuna\ImlSdk;

class Client
{
    /** @var \GuzzleHttp\Client|null */
    private $httpApi = null;

    /** @var \GuzzleHttp\Client|null */
    private $httpList = null;

    /**
     * Хранилище логинов и паролей
     * @var array
     */
    private $storage = [];

    /**
     * Выбранный ключ из хранилища
     * @var string
     */
    private $selected = null;

    /**
     * Client constructor.
     * @param int $timeout - таймаут ожидания ответа от серверов BoxBerry в секундах
     */
    public function __construct($timeout = 300)
    {
        $this->httpApi = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.iml.ru/json',
            'timeout' => $timeout,
        ]);

        $this->httpList = new \GuzzleHttp\Client([
            'base_uri' => 'https://list.iml.ru',
            'timeout' => $timeout,
        ]);
    }

    /**
     * Сохранение логина и пароля в хранилище
     *
     * @param string $key
     * @param string $login
     * @param string $passwd
     *
     * @throws \InvalidArgumentException
     */
    public function setAuthParams($key, $login, $passwd)
    {
        if (empty($key))
            throw new \InvalidArgumentException('Не передан ключ хранилища!');

        if (isset($this->storage[$key]))
            throw new \InvalidArgumentException('Передананный ключ уже занят в хранилище!');

        $this->storage[$key]['login'] = $login;
        $this->storage[$key]['passwd'] = $passwd;
        $this->selected = $key;
    }

    /**
     * Получение логина и пароля из хранилища
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getAuthParams()
    {
        if (empty($this->selected))
            throw new \InvalidArgumentException('Не выбран ключ хранилища!');

        if (empty($this->storage[$this->selected]))
            throw new \InvalidArgumentException('Выбранный ключ не найден в хранилище!');

        return $this->storage[$this->selected];
    }

    /**
     * Смена выбранной учетной записи в хранилище
     *
     * @param $key
     * @throws \InvalidArgumentException
     */
    public function switchLogin($key)
    {
        if (empty($this->storage[$key]))
            throw new \InvalidArgumentException('Переданный ключ не найден в хранилище!');

        $this->selected = $key;
    }
}