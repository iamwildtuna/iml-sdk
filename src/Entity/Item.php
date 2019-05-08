<?php
namespace WildTuna\ImlSdk\Entity;

class Item
{
    /**
     * Артикул товара
     * @var string
     */
    private $articul = null;

    /**
     * Наименование товара
     * @var string
     */
    private $name = null;

    /**
     * Параметры товара (размер, цвет и т.д)
     * @var string
     */
    private $params = null;

    /**
     * Штрих-код товара
     * @var null
     */
    private $barcode = null;

    /**
     * Дополнительный штрих-код
     * @var string
     */
    private $coupon_code = null;

    /**
     * Скидка //TODO уточнить в каком формате
     * @var int
     */
    private $discount = 0;

    /**
     * Вес единицы товара в кг
     * @var float
     */
    private $weight = 0;

    /**
     * Стоимость товара используется для передачи в ОФД (тег ФФД 1043, 1079)
     * @var float
     */
    private $amount = 0;

    /**
     * Оценочная стоимость
     * @var float
     */
    private $valuated_amount = 0;

    /**
     * Количество товара (Используется для передачи в ОФД (тег ФФД 1023))
     * ВАЖНО! На данный момент каждая позиция должна быть отдельной строкой, даже одинакового товара!
     * @var int
     */
    private $quantity = 1;

    /**
     * Запрет отказа от позиции при частичной выдаче заказа,
     * если нужно чтобы получатель не мог отказаться от этой позиции при частичной выдаче
     * @var bool
     */
    private $ban_refusal_of_goods = true;

    /**
     * Тип товара (по умолчанию "Товар"), Code из справочника типов с типом (StatusType) 39 (http://list.iml.ru/Status)
     * @var int
     */
    private $type = 0;

    /**
     * Примечание к товару (комментарий который вы хотели бы донести до сотрудника IML)
     * @var string
     */
    private $note = null;

    /**
     * Используется для $this->type = 10 для разрешения или запрета дополнительных условий выдачи заказа
     * Code из справочника типов с типом (StatusType) 40 (http://list.iml.ru/Status)
     * @var int
     */
    private $allowed = null;

    /**
     * Размер ставки НДС в процентах (по умолчанию 20%)
     * Может принимать значение Code из справочника типов с типом (StatusType) 52 (http://list.iml.ru/Status)
     * @var int
     */
    private $vat_rate = 20;

    /**
     * Длина в см
     * @var float
     */
    private $lenght = 0;

    /**
     * Высота в см
     * @var float
     */
    private $height = 0;

    /**
     * Ширина в см
     * @var float
     */
    private $width = 0;

    /**
     * Код товарной номенклатуры. Используется для передачи в ОФД (тег ФФД 1162)
     *
     * Данные представлены в виде строки, в которой:
     * - первые 2 байта - код справочника;
     * - последующие 6 байт - код идентификации группы товара;
     * - последние 24 байта - код идентификации экземпляра товара
     *
     * @var string
     */
    private $class = null;

    /**
     * Единица измерения товара, работы, услуги, платежа, выплаты, иного предмета расчета
     * Используется для передачи в ОФД (тег ФФД 1197)
     *
     * @var string
     */
    private $unit = null;

    /**
     * Признак предмета товара, услуги. Используется для передачи в ОФД (тег ФФД 1212)
     * Может принимать значения Code из справочника типов с типом (StatusType) 53 (http://list.iml.ru/Status)
     *
     * @var string
     */
    private $attribute = null;

    /**
     * Наименование поставщика. Используется для передачи в ОФД (тег ФФД 1225)
     * @var string
     */
    private $vendor_name = null;

    /**
     * ИНН поставщика. Если ИНН имеет длину меньше 12 цифр, то он дополняется справа пробелами.
     * Данный реквизит принимает значение "000000000000" в случае если поставщику не присвоен ИНН на территории Российской Федерации.
     * Используется для передачи в ОФД (тег ФФД 1226)
     *
     * @var string
     */
    private $vendor_inn = null;

    /**
     * Номера контактных телефонов поставщика. Используется для передачи в ОФД (тег ФФД 1171)
     * @var string
     */
    private $vendor_phone = null;

    /**
     * Цифровой код страны происхождения товара в соответствии с Общероссийским классификатором стран мира.
     * Используется для передачи в ОФД (тег ФФД 1230)
     *
     * @var string
     */
    private $goods_country = null;

    /**
     * Номер таможенной декларации в соответствии с форматом, установленным приказом ФНС России от 24.03.2016 N ММВ-7-15/155.
     * Используется для передачи в ОФД (тег ФФД 1231)
     *
     * @var string
     */
    private $customs_declaration_number = null;

    public function asArr($row = '')
    {
        $params = [];
        $params['productNo'] = $this->articul;
        $params['productName'] = $this->name;
        $params['productVariant'] = $this->params;
        $params['productBarCode'] = $this->barcode;
        $params['couponCode'] = $this->coupon_code;
        $params['discount'] = $this->discount;
        $params['weightLine'] = $this->weight;
        $params['amountLine'] = $this->amount;
        $params['statisticalValueLine'] = $this->valuated_amount;

        if ($this->quantity > 1)
            throw new \InvalidArgumentException('В вложении '.$row.' найдена ошибка: На данный момент каждая позиция должна быть отдельной строкой, даже одинакового товара!');

        $params['itemQuantity'] = $this->quantity;
        if ($this->ban_refusal_of_goods)
            $params['deliveryService'] = $this->ban_refusal_of_goods;

        $params['itemType'] = $this->type;
        $params['itemNote'] = $this->note;

        if ($this->type == 10)
            $params['allowed'] = $this->allowed;

        $params['VATRate'] = $this->vat_rate;
        $params['Length'] = $this->lenght;
        $params['Height'] = $this->height;
        $params['Width'] = $this->width;

        if ($this->class)
            $params['GoodsClass'] = $this->class;

        if ($this->unit)
            $params['PaymentItemUnit'] = $this->unit;

        if ($this->attribute)
            $params['PaymentItemSign'] = $this->attribute;

        if ($this->vendor_name)
            $params['VendorName'] = $this->vendor_name;

        if ($this->vendor_inn)
            $params['VendorINN'] = $this->vendor_inn;

        if ($this->vendor_phone)
            $params['VendorPhone'] = $this->vendor_phone;

        if ($this->goods_country)
            $params['ItemCountryOrigin'] = $this->goods_country;

        if ($this->customs_declaration_number)
            $params['CustomsDeclaration'] = $this->customs_declaration_number;

        return $params;
    }

    /**
     * @return string
     */
    public function getArticul()
    {
        return $this->articul;
    }

    /**
     * @param string $articul
     */
    public function setArticul($articul)
    {
        $this->articul = $articul;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return null
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param null $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        return $this->coupon_code;
    }

    /**
     * @param string $coupon_code
     */
    public function setCouponCode($coupon_code)
    {
        $this->coupon_code = $coupon_code;
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param int $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
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
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return bool
     */
    public function isBanRefusalOfGoods()
    {
        return $this->ban_refusal_of_goods;
    }

    /**
     * @param bool $ban_refusal_of_goods
     */
    public function setBanRefusalOfGoods($ban_refusal_of_goods)
    {
        $this->ban_refusal_of_goods = $ban_refusal_of_goods;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return int
     */
    public function getAllowed()
    {
        return $this->allowed;
    }

    /**
     * @param int $allowed
     */
    public function setAllowed($allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * @return int
     */
    public function getVatRate()
    {
        return $this->vat_rate;
    }

    /**
     * @param int $vat_rate
     */
    public function setVatRate($vat_rate)
    {
        $this->vat_rate = $vat_rate;
    }

    /**
     * @return float
     */
    public function getLenght()
    {
        return $this->lenght;
    }

    /**
     * @param float $lenght
     */
    public function setLenght($lenght)
    {
        $this->lenght = $lenght;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param float $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param string $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendor_name;
    }

    /**
     * @param string $vendor_name
     */
    public function setVendorName($vendor_name)
    {
        $this->vendor_name = $vendor_name;
    }

    /**
     * @return string
     */
    public function getVendorInn()
    {
        return $this->vendor_inn;
    }

    /**
     * @param string $vendor_inn
     */
    public function setVendorInn($vendor_inn)
    {
        $this->vendor_inn = $vendor_inn;
    }

    /**
     * @return string
     */
    public function getVendorPhone()
    {
        return $this->vendor_phone;
    }

    /**
     * @param string $vendor_phone
     */
    public function setVendorPhone($vendor_phone)
    {
        $this->vendor_phone = $vendor_phone;
    }

    /**
     * @return string
     */
    public function getGoodsCountry()
    {
        return $this->goods_country;
    }

    /**
     * @param string $goods_country
     */
    public function setGoodsCountry($goods_country)
    {
        $this->goods_country = $goods_country;
    }

    /**
     * @return string
     */
    public function getCustomsDeclarationNumber()
    {
        return $this->customs_declaration_number;
    }

    /**
     * @param string $customs_declaration_number
     */
    public function setCustomsDeclarationNumber($customs_declaration_number)
    {
        $this->customs_declaration_number = $customs_declaration_number;
    }
}