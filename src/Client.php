<?php

namespace WildTuna\ImlSdk;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use WildTuna\ImlSdk\Entity\Order;
use WildTuna\ImlSdk\Exception\ImlException;

class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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

        $this->storage[$key]['username'] = $login;
        $this->storage[$key]['password'] = $passwd;
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

    /**
     * Инициализирует вызов к api.iml.ru
     *
     * @param $method
     * @param array $params
     * @return array
     * @throws ImlException
     */
    private function callApi($method, $params = [])
    {
        if ($this->logger) {
            $this->logger->info('IML API request: '.http_build_query($params));
        }

        $auth = $this->getAuthParams();
        $response = $this->httpApi->post('/'.$method, [
            'auth' => [$auth['username'], $auth['password']],
            'headers' => [
                'Accept' => 'application/json'
            ],
            'form_params' => $params
        ]);

        $json = $response->getBody()->getContents();

        if ($this->logger) {
            $this->logger->info('IML API response: '.$json);
        }

        if ($response->getStatusCode() != 200)
            throw new ImlException('Неверный код ответа от сервера IML при вызове метода '.$method.': ' . $response->getStatusCode(), $response->getStatusCode(), $json);

        $array = json_decode($json, true);

        if (empty($array))
            throw new ImlException('От сервера IML при вызове метода '.$method.' пришел пустой ответ', $response->getStatusCode(), $json);

        if (!empty($array['Result']) && $array['Result'] != "OK") {
            $errors = "\n";
            foreach ($array['Errors'] as $err) {
                $errors .= $err['Message']."\n";
            }

            throw new ImlException('От сервера IML при вызове метода '.$method.' получены ошибки: '.$errors, $response->getStatusCode(), $json);
        }

        return $array;
    }

    /**
     * Инициализирует вызов к list.iml.ru
     *
     * @param $method
     * @param $params
     * @return array
     * @throws ImlException
     */
    private function callList($method, $params = [])
    {
        if ($this->logger) {
            $this->logger->info('IML API request: '.http_build_query($params));
        }

        $auth = $this->getAuthParams();
        $response = $this->httpList->get('/'.$method, [
            'auth' => [$auth['username'], $auth['password']],
            'headers' => [
                'Accept' => 'application/json'
            ],
            'query' => $params
        ]);

        $json = $response->getBody()->getContents();

        if ($this->logger) {
            $this->logger->info('IML API response: '.$json);
        }

        if ($response->getStatusCode() != 200)
            throw new ImlException('Неверный код ответа от сервера IML при вызове метода '.$method.': ' . $response->getStatusCode(), $response->getStatusCode(), $json);

        $array = json_decode($json, true);

        if (empty($array))
            throw new ImlException('От сервера IML при вызове метода '.$method.' пришел пустой ответ', $response->getStatusCode(), $json);

        return $array;
    }

    /**
     * Создание заказа
     *
     * @param Order $order - параметры заказа
     * @return array
     * @throws ImlException
     */
    public function createOrder($order)
    {
        $params = $order->asArr();
        return $this->callApi('CreateOrder', $params);
    }

    /**
     * Получить состояние заказа (статус)
     *
     * @param string $order_id - номер заказа ИМ
     * @return array
     * @throws ImlException
     */
    public function getOrderStatus($order_id)
    {
        $params['CustomerOrder'] = $order_id;
        $result = $this->callApi('GetStatuses', $params);
        return array_shift($result);
    }

    /**
     * Список заказов
     *
     * @param string $from - период от (дата в любом формате)
     * @param string $to - период до (дата в любом формате)
     * @return array
     * @throws ImlException
     */
    public function getOrdersList($from, $to)
    {
        // TODO обработка всех параметов

        if (empty($from) || empty($to))
            throw new \InvalidArgumentException('Не передан период дат');

        $params['DeliveryDateStart'] = date('d.m.Y', strtotime($from));
        $params['DeliveryDateEnd']   = date('d.m.Y', strtotime($to));
        return $this->callApi('GetOrders', $params);
    }

    /**
     * Печать этикетки
     *
     * @param $barcode
     * @return array
     * @throws ImlException
     */
    public function printLabel($barcode)
    {
        // TODO обработка всех параметров
        $params['BarCode'] = $barcode;
        $result = $this->callApi('PrintBar', $params);
        if (empty($result['Url']))
            throw new ImlException('От сервера IML не пришла ссылка на этикетку');

        return $result['Url'];
    }

    /**
     * Справочник ограниченных ресурсов
     *
     * @return array
     * @throws ImlException
     */
    public function getResourceLimitList()
    {
        return $this->callList('ResourceLimit');
    }

    /**
     * Справочник почтовых индексов
     *
     * @return array
     * @throws ImlException
     */
    public function getZipList()
    {
        return $this->callList('PostCode');
    }

    /**
     * Детальная информация по почтовому индексу
     *
     * @param int $zip - почтовый индекс
     * @return array
     * @throws ImlException
     */
    public function getZipInfo($zip)
    {
        $params['index'] = $zip;
        return $this->callList('PostCode', $params);
    }


    /**
     * Справочник складов
     *
     * @param boolean $full - расширенная информация по складам
     * @return array
     * @throws ImlException
     */
    public function getWarehouseList($full = false)
    {
        $method = 'Location';
        if ($full)
            $method .= 'Ext';

        return $this->callList($method);
    }

    /**
     * Справочник регионов, где возможен самовывоз (есть ПВЗ)
     *
     * @return array
     * @throws ImlException
     */
    public function getSelfDeliveryRegions()
    {
        return $this->callList('SelfDeliveryRegions');
    }


    /**
     * Справочник почтовых ограничений
     *
     * @return array
     * @throws ImlException
     */
    public function getPostDeliveryLimits()
    {
        return $this->callList('PostDeliveryLimit');
    }

    /**
     * Справочник регионов IML
     *
     * @return array
     * @throws ImlException
     */
    public function getRegionsList()
    {
        return $this->callList('region');
    }

    /**
     * Справочник пунктов самовывоза (ПВЗ)
     *
     * @return array
     * @throws ImlException
     */
    public function getPvzList()
    {
        return $this->callList('sd');
    }

    /**
     * Список ПВЗ в указанном регионе
     *
     * @param string $region_code - код региона IML из справочника регионов (см. $this->getRegionsList)
     * @return array
     * @throws ImlException
     */
    public function getPvzInRegion($region_code)
    {
        $params['RegionCode'] = $region_code;
        return $this->callList('sd', $params);
    }

    /**
     * Список ПВЗ по коду КЛАДР
     *
     * @param string $kladr - код КЛАДР
     * @return array
     * @throws ImlException
     */
    public function getPvzByKladr($kladr)
    {
        $params['kladr'] = $kladr;
        return $this->callList('sd', $params);
    }

    /**
     * Все справочники одним запросом
     *
     * @return array
     * @throws ImlException
     */
    public function allReferenceBooks()
    {
        return $this->callList('all');
    }

    /**
     * Справочник услуг
     *
     * @return array
     * @throws ImlException
     */
    public function getServicesList()
    {
        return $this->callList('service');
    }

    /**
     * Справочник статусов
     *
     * @return array
     * @throws ImlException
     */
    public function getStatusesList()
    {
        return $this->callList('status');
    }

    /**
     * Справочник сегментов маршрутов
     *
     * @return array
     * @throws ImlException
     */
    public function getRouteSegments()
    {
        return $this->callList('GlobalRouteLine');
    }

    /**
     * Получение данных сегмента маршрута по коду
     *
     * @param string $segment_code - код сегмента
     * @return array
     * @throws ImlException
     */
    public function getRouteSegmentByCode($segment_code = null)
    {
        $params['tripcode'] = $segment_code;
        return $this->callList('GlobalRouteLine');
    }

    /**
     * Справочник дополнительных зон доставки
     *
     * @return array
     * @throws ImlException
     */
    public function getAdditionalDeliveryZones()
    {
        return $this->callList('AdditionalDeliveryZone');
    }

    // TODO LocationResource


    /**
     * Список заблокированных регионов IML в разрезе услуг
     *
     * @return array
     * @throws ImlException
     */
    public function getBlockedRegionsList()
    {
        return $this->callList('ExceptionServiceRegion');
    }

    /**
     * Справочник валют оценочной стоимости
     *
     * @return array
     * @throws ImlException
     */
    public function getValuationCurrencies()
    {
        return $this->callList('ValuationCurrency');
    }

    /**
     * Рабочий календарь IML
     *
     * @return array
     * @throws ImlException
     */
    public function getWorkCalendarIml()
    {
        return $this->callList('calendar');
    }

    // TODO GlobalRouteHeader

    // TODO ChangeModeWork

    /**
     * Справочник регионов и городов доставки
     *
     * @return array
     * @throws ImlException
     */
    public function getRegionsAndCities()
    {
        return $this->callList('RegionCity');
    }

    /**
     * Справочник тарифных зон Почты России
     *
     * @return array
     * @throws ImlException
     */
    public function getPostTariffZones()
    {
        return $this->callList('PostRateZone');
    }


    /**
     * Справочник единиц измерения вложений заказа
     *
     * @return array
     * @throws ImlException
     */
    public function getUnitsList()
    {
        return $this->callList('UnitOfMeasure');
    }

    /**
     * Справочник зон доставки
     *
     * @return array
     * @throws ImlException
     */
    public function getDeliveryZones()
    {
        return $this->callList('zone');
    }
}
