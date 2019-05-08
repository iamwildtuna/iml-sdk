<?php
namespace WildTuna\ImlSdk\Entity;

class Order
{
    const PRT = 0;
    const ANY = 1;
    const ONE_CLASS = 2;

    const SHOW_LC = 0;
    const IMPORT_LC = 1;
    const JOB_POCHTA = 'ПОЧТА';

    /**
     * Тестовый режим
     * @var bool
     */
    private $test_mod = false;

    /**
     * Услуга доставки, Code из справочника услуг
     * @var string
     */
    private $job = null;

    /**
     * Номер заказа
     * @var string
     */
    private $number = null;

    /**
     * ШК заказа
     * @var string
     */
    private $barcode = null;

    /**
     * Дата доставки
     * @var string
     */
    private $delivery_date = null;

    /**
     * Количество мест
     * @var int
     */
    private $places = 1;

    /**
     * Вес заказа в кг
     * @var float
     */
    private $weight = 0;

    /**
     * Пункта самовывоза, RequestCode из справочника пунктов самовывоза (http://list.iml.ru/sd)
     * @var string
     */
    private $pvz = null;

    /**
     * Уникальный номер пункта самовывоза, ID из справочника пунктов самовывоза, альтернатива pvz (http://list.iml.ru/sd)
     * @var string
     */
    private $pvz_id = null;

    /**
     * Телефон получателя
     * @var string
     */
    private $phone = null;

    /**
     * Email для уведомлений
     * @var string
     */
    private $email = null;

    /**
     * Контактное лицо
     * @var string
     */
    private $fio = null;

    /**
     * Почтовый индекс региона отправления, альтернатива region_code_from
     * @var int
     */
    private $zip_from = null;

    /**
     * Почтовый индекс региона получения, альтернатива region_code_to
     * @var int
     */
    private $zip_to = null;


    /**
     * Код региона отправления, Code из справочника регионов (http://list.iml.ru/region)
     * @var string
     */
    private $region_code_from = null;

    /**
     * Код региона получения, Code из справочника регионов (http://list.iml.ru/region)
     * @var string
     */
    private $region_code_to = null;

    /**
     * Адрес доставки
     * @var string
     */
    private $address = null;

    /**
     * Время доставки от XX:XX
     * @var string
     */
    private $time_from = null;

    /**
     * Время доставки до XX:XX
     * @var string
     */
    private $time_to = null;

    /**
     * Сумма
     * @var float
     */
    private $amount = 0;

    /**
     * Оценочная стоимость
     * @var float
     */
    private $valuated_amount = 0;

    /**
     * Комментарий
     * @var string
     */
    private $comment = null;

    /**
     * Город доставки для отправки Почтой России
     * @var string
     */
    private $city = null;

    /**
     * Регион для отправки Почтой России
     * @var string
     */
    private $region = null;

    /**
     * Район для отправки Почтой России
     * @var string
     */
    private $area = null;

    /**
     * Тип вложения для отправки Почтой России (см. контстанты self::PRT, self::ANY, self::ONE_CLASS)
     * @var int
     */
    private $attachment_type = self::PRT;

    /**
     * Флаг обработки заказа
     * 0 - означает что заказ грузится в список "просмотр" ЛК
     * 1 - означает что заказ грузится в список "импорт" ЛК
     * @var int
     */
    private $draft = self::SHOW_LC;

    /**
     * Список вложений
     * @var array
     */
    private $items = [];


    public function asArr()
    {
        $params = [];
        if ($this->test_mod)
            $params['Test'] = 'True';

        $params['Job'] = $this->job;
        $params['CustomerOrder'] = $this->number;
        $params['DeliveryDate'] = date('d.m.Y', strtotime($this->delivery_date));
        $params['Volume'] = $this->places;
        $params['Weight'] = $this->weight;
        $params['BarCode'] = $this->barcode;

        if ($this->pvz)
            $params['DeliveryPoint'] = $this->pvz;

        if ($this->pvz_id)
            $params['DeliveryPointID'] = $this->pvz_id;

        $params['Phone'] = $this->phone;
        $params['Email'] = $this->email;
        $params['Contact'] = $this->fio;

        if ($this->zip_from)
            $params['IndexFrom'] = $this->zip_from;

        if ($this->zip_to)
            $params['IndexTo'] = $this->zip_to;

        if ($this->region_code_from)
            $params['RegionCodeFrom'] = $this->region_code_from;

        if ($this->region_code_to)
            $params['RegionCodeTo'] = $this->region_code_to;

        if ($this->address)
            $params['address'] = $this->address;

        if ($this->time_from)
            $params['TimeFrom'] = $this->time_from;

        if ($this->time_to)
            $params['TimeTo'] = $this->time_to;

        $params['Amount'] = $this->amount;
        $params['ValuatedAmount'] = $this->valuated_amount;
        $params['Comment'] = $this->comment;
        $params['Draft'] = $this->draft;

        // Если отправка Почтой России
        if ($this->job == self::JOB_POCHTA) {
            if (empty($this->zip_to))
                throw new \InvalidArgumentException('При отправки заказа Почтой России поле zip_to обязательно к заполнению!');

            if (empty($this->city))
                throw new \InvalidArgumentException('При отправки заказа Почтой России поле city обязательно к заполнению!');

            if (empty($this->region))
                throw new \InvalidArgumentException('При отправки заказа Почтой России поле region обязательно к заполнению!');

            if (empty($this->area))
                throw new \InvalidArgumentException('При отправки заказа Почтой России поле area обязательно к заполнению!');

            $params['City'] = $this->city;
            $params['PostCode'] = $this->zip_to;
            $params['PostRegion'] = $this->region;
            $params['PostArea'] = $this->area;
            $params['PostContentType'] = $this->attachment_type;
        }

        // Формирование вложений заказа
        $params['GoodItems'] = [];

        /** @var Item $item */
        foreach ($this->items as $key => $item) {
            $imlItem = $item->asArr(($key+1));

            if (!empty($imlItem)) {
                $params['GoodItems'][] = $imlItem;
            }
        }

        return $params;
    }

    /**
     * Установка тестового режима
     * @param bool $mod
     */
    public function testMod($mod = false)
    {
        $this->test_mod = $mod;
    }

    /**
     * @return string
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param string $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return string
     */
    public function getDeliveryDate()
    {
        return $this->delivery_date;
    }

    /**
     * @param string $delivery_date
     */
    public function setDeliveryDate($delivery_date)
    {
        $this->delivery_date = $delivery_date;
    }

    /**
     * @return int
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @param int $places
     */
    public function setPlaces($places)
    {
        $this->places = $places;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function getPvz()
    {
        return $this->pvz;
    }

    /**
     * @param string $pvz
     */
    public function setPvz($pvz)
    {
        $this->pvz = $pvz;
    }

    /**
     * @return string
     */
    public function getPvzId()
    {
        return $this->pvz_id;
    }

    /**
     * @param string $pvz_id
     */
    public function setPvzId($pvz_id)
    {
        $this->pvz_id = $pvz_id;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * @param string $fio
     */
    public function setFio($fio)
    {
        $this->fio = $fio;
    }

    /**
     * @return int
     */
    public function getZipFrom()
    {
        return $this->zip_from;
    }

    /**
     * @param int $zip_from
     */
    public function setZipFrom($zip_from)
    {
        $this->zip_from = $zip_from;
    }

    /**
     * @return int
     */
    public function getZipTo()
    {
        return $this->zip_to;
    }

    /**
     * @param int $zip_to
     */
    public function setZipTo($zip_to)
    {
        $this->zip_to = $zip_to;
    }

    /**
     * @return string
     */
    public function getRegionCodeFrom()
    {
        return $this->region_code_from;
    }

    /**
     * @param string $region_code_from
     */
    public function setRegionCodeFrom($region_code_from)
    {
        $this->region_code_from = $region_code_from;
    }

    /**
     * @return string
     */
    public function getRegionCodeTo()
    {
        return $this->region_code_to;
    }

    /**
     * @param string $region_code_to
     */
    public function setRegionCodeTo($region_code_to)
    {
        $this->region_code_to = $region_code_to;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getTimeFrom()
    {
        return $this->time_from;
    }

    /**
     * @param string $time_from
     */
    public function setTimeFrom($time_from)
    {
        $this->time_from = $time_from;
    }

    /**
     * @return string
     */
    public function getTimeTo()
    {
        return $this->time_to;
    }

    /**
     * @param string $time_to
     */
    public function setTimeTo($time_to)
    {
        $this->time_to = $time_to;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getValuatedAmount()
    {
        return $this->valuated_amount;
    }

    /**
     * @param float $valuated_amount
     */
    public function setValuatedAmount($valuated_amount)
    {
        $this->valuated_amount = $valuated_amount;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param string $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * @return int
     */
    public function getAttachmentType()
    {
        return $this->attachment_type;
    }

    /**
     * @param int $attachment_type
     */
    public function setAttachmentType($attachment_type)
    {
        $this->attachment_type = $attachment_type;
    }

    /**
     * @return int
     */
    public function getDraft()
    {
        return $this->draft;
    }

    /**
     * @param int $draft
     */
    public function setDraft($draft)
    {
        $this->draft = $draft;
    }
}