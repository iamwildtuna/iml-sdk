<a href="https://lapay.group/"><img align="left" width="200" src="https://lapay.group/lglogo.jpg"></a>
<a href="https://api.iml.ru/"><img align="right" width="200" src="https://lapay.group/imllogo.png"></a>    

<br /><br /><br />

[![Latest Stable Version](https://poser.pugx.org/wildtuna/iml-sdk/v/stable)](https://packagist.org/packages/wildtuna/iml-sdk)
[![Total Downloads](https://poser.pugx.org/wildtuna/iml-sdk/downloads)](https://packagist.org/packages/wildtuna/iml-sdk)
[![License](https://poser.pugx.org/wildtuna/iml-sdk/license)](https://packagist.org/packages/wildtuna/iml-sdk)
[![Telegram Chat](https://img.shields.io/badge/telegram-chat-blue.svg?logo=telegram)](https://t.me/phpboxberrysdk)


# SDK для [интеграции с программным комплексом IML](https://iml.ru).

Посмотреть все проекты или подарить автору кофе можно [тут](https://lapay.group/opensource).   

# Содержание      
- [Changelog](#changelog)    
- [Установка](#install)  
- [Настройка аутентификации](#settings)  
- [Отладка](#debugging)  
- [API интеграции с IML](#apiimlru)  
  - [Создание заказа](#createOrder)    
  - [Получить состояние заказа (статус)](#getOrderStatus)  
  - [Список заказов](#getOrdersList)  
  - [Печать этикетки](#printLabel)  
- [Справочный сервис IML](#listimlru) 
  - [Справочник ограниченных ресурсов](#getResourceLimitList)  
  - [Справочник почтовых индексов](#getZipList)  
  - [Детальная информация по почтовому индексу](#getZipInfo)  
  - [Справочник складов](#getWarehouseList)  
  - [Справочник регионов, где возможен самовывоз](#getSelfDeliveryRegions)  
  - [Справочник почтовых ограничений](#getPostDeliveryLimits)  
  - [Справочник регионов IML](#getRegionsList)  
  - [Справочник пунктов самовывоза (ПВЗ)](#getPvzList)  
  - [Список ПВЗ в указанном регионе](#getPvzInRegions)  
  - [Список ПВЗ по коду КЛАДР](#getPvzByKladr)  
  - [Все справочники одним запросом](#allReferenceBooks)  
  - [Справочник услуг](#getServicesList)  
  - [Справочник статусов](#getStatusesList)  
  - [Справочник сегментов маршрутов](#getRouteSegments)  
  - [Получение данных сегмента маршрута по коду](#getRouteSegmentByCode)  
  - [Справочник дополнительных зон доставки](#getAdditionalDeliveryZones)  
  - [Список заблокированных регионов IML](#getBlockedRegionsList)    
  - [Справочник валют оценочной стоимости](#getValuationCurrencies)  
  - [Рабочий календарь IML](#getWorkCalendarIml)   
  - [Справочник регионов и городов доставки](#getRegionsAndCities)  
  - [Справочник тарифных зон Почты России](#getPostTariffZones)  
  - [Справочник единиц измерения вложений заказа](#getUnitsList)  
  - [Справочник зон доставки](#getDeliveryZones)   
     

<a name="links"><h1>Changelog</h1></a>

- 0.6.2 - Совместимость с Guzzle 7;  
- 0.6.1 - Доработка логирования;
- 0.6.0 - Добавлено логирование запросов и ответов к API;  
- 0.5.0 - Реализованы функции https://api.iml.ru;  
- 0.4.0 - Реализованы функции https://list.iml.ru, добавлено [описание](README.md);  
- 0.3.0 - Реализована часть функций https://list.iml.ru;  
- 0.2.0 - Созданы сущности для работы с заказом и механизм смены учетных данных;  
- 0.1.0 - Cкелет SDK;  


<a name="install"><h1>Установка</h1></a>  
Для установки можно использовать менеджер пакетов Composer

    composer require wildtuna/iml-sdk
    

<a name="termins"><h1>Термины и обознаения</h1></a>  
- **API_IML** - API интеграции https://api.iml.ru;
- **LIST_IML** - Справочный сервис IML https://list.iml.ru; 
- **УЗ** - учетная запись;
- **ИМ** - интернет-манагиз;
- **ПВЗ** - пункт выдачи заказов;
- **БД** - база данных (СУБД);
- **ОФД** - оператор фискальных данных;
- **ФФД** - формат фискальных документов;
- **EAN-13** - формат штрих-кода.  


<a name="settings"><h1>Настройка аутентификации</h1></a>  
API клиент позволяет использовать несколько учетных записей IML и переключатьcя между ними.  
При добавлении учетных записей последняя добавленная становися выбранной.

Добавление УЗ при инициализации:
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password'); // Заносим данные IML и присваиваем им ключ main
$imlClient->setAuthParams('another', 'api_login', 'api_password');
````

Переключение УЗ:    
```php
<?php
$imlClient->switchLogin('main');
$imlClient->switchLogin('another');
````    


<a name="debugging"><h1>Отладка</h1></a>  
Для логирования запросов и ответов используется [стандартный PSR-3 логгер](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md). 
Ниже приведен пример логирования используя [Monolog](https://github.com/Seldaek/monolog).  

```php
<?php
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    
    $log = new Logger('name');
    $log->pushHandler(new StreamHandler('log.txt', Logger::INFO));
    
    $imlClient = new \WildTuna\ImlSdk\Client();
    $imlClient->setAuthParams('main', 'api_login', 'api_password');
    $imlClient->setLogger($log);
    $result = $imlClient->getPvzList();
```  

Запрос и ответ будут в файле log.txt.

<a name="apiimlru"><h1>API интеграции с IML</h1></a>  
Функции основного [API](https://api.iml.ru/) компании IML (Работа с заказами и этикетками).    


<a name="createOrder"><h3>Создание заказа</h3></a>  
Создание нового заказа в IML. Подробное описание свойств объекта *\WildTuna\ImlSdk\Entity\Order()* можно посмотреть [тут](src/Entity/Order.php).  
Описание порядка заполнения полей можно посмотреть [тут](https://api.iml.ru/Help/v4/CreateOrder).      

**Входные параметры:**  
Объект *\WildTuna\ImlSdk\Entity\Order*
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $order = new \WildTuna\ImlSdk\Entity\Order();
    $order->testMod(true);
    $order->setNumber(9999111);
    $order->setJob('24КО');
    $order->setDeliveryDate('2019-05-15');
    $order->setPlaces(1);
    $order->setWeight(1.5);
    // $order->setBarcode(123456789);
    $order->setPhone('89992223344');
    $order->setEmail('test@test.ru');
    $order->setFio('Иванов Петр');
    $order->setZipFrom(115551);
    $order->setZipTo(115551);
    $order->setAddress('Красная пл. д. 1 кв. 1');
    $order->setTimeFrom('10:00');
    $order->setTimeTo('19:00');
    $order->setAmount(1000);
    $order->setValuatedAmount(1000);
    $order->setComment('Текст комментария');
    
    $item = new \WildTuna\ImlSdk\Entity\Item();
    $item->setArticul(12345);
    $item->setName('Тестовый товар');
    $item->setParams('красная коробка');
    $item->setWeight(1.5);
    $item->setAmount(1000);
    $item->setValuatedAmount(1000);
    $item->setVatRate(20);
    $item->setUnit('шт.');
    
    $order->setItem($item);
    
    $result = $imlClient->createOrder($order);
    
    /*
     Array
     (
         [Result] => OK
         [Order] => Array
             (
                 [Job] => 24КО
                 [CustomerOrder] => 9999111
                 [DeliveryDate] => 15.05.2019 0:00:00
                 [Volume] => 1
                 [Weight] => 1.5
                 [BarCode] => 7500019030317
                 [DeliveryPoint] =>
                 [Phone] => 89992223344
                 [Email] => test@test.ru
                 [Contact] => Иванов Петр
                 [IndexTo] => 115551
                 [IndexFrom] => 115551
                 [RegionCodeTo] => МОСКВА
                 [RegionCodeFrom] => МОСКВА
                 [Address] => Красная пл. д. 1 кв. 1
                 [TimeTo] => 19:00:00
                 [TimeFrom] => 10:00:00
                 [ValuatedAmount] => 1000
                 [Amount] => 1000
                 [State] => 999
                 [OrderStatus] => 0
                 [Comment] => Текст комментария
                 [GoodItems] => Array
                     (
                         [0] => Array
                             (
                                 [productNo] => 12345
                                 [productName] => Тестовый товар
                                 [productVariant] => красная коробка
                                 [productNote] => красная коробка
                                 [productBarCode] =>
                                 [couponCode] =>
                                 [discount] => 0
                                 [weightLine] =>
                                 [amountLine] => 1000
                                 [statisticalValueLine] => 1000
                                 [deliveryService] => 1
                                 [itemQuantity] => 1
                                 [itemType] => 0
                                 [itemNote] =>
                                 [allowed] => True
                                 [VATRate] => 1
                                 [VATAmount] =>
                                 [Length] => 0
                                 [Height] => 0
                                 [Width] => 0
                                 [GoodsClass] =>
                                 [PaymentItemUnit] => шт.
                                 [VendorName] =>
                                 [VendorINN] =>
                                 [VendorPhone] =>
                                 [PaymentItemSign] => 1
                                 [ExciseAmount] =>
                                 [ItemCountryOrigin] =>
                                 [CustomsDeclaration] =>
                             )
     
                     )
     
                 [ForLocation] => МОСКВА
                 [Draft] =>
                 [DeliveryAmount] =>
                 [Commission] =>
                 [PostIdentifier] => // Номер РПО Почты России
                 [PaymentType] =>
                 [FullDeliveryAmount] =>
             )
     
     )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
``` 
    


<a name="getOrderStatus"><h3>Получить состояние заказа (статус)</h3></a>  
Возвращает детальную информацию о состояние заказа с списком вложения.  

**Входные параметры:**  
*$order_id (string)* - номер заказа ИМ (не IML) 
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getOrderStatus(8133829);
    /*
     Array
     (
         [Number] => 8133829
         [State] => 13
         [OrderStatus] => 1
         [StateDescription] => Завершено
         [OrderStatusDescription] => Доставлен
         [StatusDate] => 2019-05-10T08:03:09.79
         [ReturnPayment] =>
         [BarCode] => 6034813382910
         [ReturnStatus] => 0
         [CashReceiptAmount] => 4020
         [LocationCode] =>
         [TransferredToDate] =>
         [Details] => Array
             (
                 [0] => Array
                     (
                         [change] => 0001-01-01T00:00:00
                         [productNo] => 72614619
                         [productName] => Сумка спортивная Wenger, серая/салатовая, 48х24x30 см
                         [service] =>
                         [isAllowed] =>
                         [amount] => 3720
                         [cashRecieptAmount] => 3720
                         [cancel] =>
                         [returnCode] =>
                         [productVariant] =>
                         [statisticalValueLine] => 3720
                         [externalBarcode] => 0000000442824
                         [couponCode] =>
                         [productType] => 0
                         [productTypeId] =>
                         [status] =>
                         [side1] =>
                         [side2] =>
                         [side3] =>
                         [weight] =>
                         [vatRate] =>
                         [vatAmount] =>
                         [discount] => 0
                         [consignorPhone] =>
                         [note] =>
                         [quantity] => 1
                     )
         [DeliveryDate] => 2019-05-09T00:00:00
         [PostIdentifier] => // Номер РПО Почты России
         [PaymentType] => 0
     )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getOrdersList"><h3>Список заказов</h3></a>  
Возвращает список заказов за указанный период дат.

**Входные параметры:**  
*$from (string)* -  период от (дата в любом формате)  
*$to (string)* -  период до (дата в любом формате)  
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getOrdersList('2019-05-01', '2019-05-01');
    /*
     Array
     (
         [0] => Array
             (
                 [Job] => 24КО
                 [CustomerOrder] => 4199130
                 [DeliveryDate] => 01.05.2019 0:00:00
                 [Volume] => 1
                 [Weight] => 0.7
                 [BarCode] => 6034419913013
                 [DeliveryPoint] =>
                 [Phone] => 89995556677
                 [Email] =>
                 [Contact] => Иванов Иван
                 [IndexTo] =>
                 [IndexFrom] =>
                 [RegionCodeTo] => МОСКВА
                 [RegionCodeFrom] => МОСКВА
                 [Address] => 115142, Москва г, Коломенская наб, дом 1, квартира 1
                 [TimeTo] => 18:00:00
                 [TimeFrom] => 10:00:00
                 [ValuatedAmount] => 2838
                 [Amount] => 2838
                 [State] => 13
                 [OrderStatus] => 1
                 [Comment] =>
                 [GoodItems] => Array
                     (
                         [0] => Array
                             (
                                 [productNo] => 702-500 АКУЛА
                                 [productName] => Термос (сититерм-вакуумный) Арктика (0,5 литра),акула
                                 [productVariant] =>
                                 [productNote] =>
                                 [productBarCode] => 2000044959065
                                 [couponCode] =>
                                 [discount] => 0
                                 [weightLine] =>
                                 [amountLine] => 1269
                                 [statisticalValueLine] => 1269
                                 [deliveryService] => 0
                                 [itemQuantity] =>
                                 [itemType] => 0
                                 [itemNote] =>
                                 [allowed] =>
                                 [VATRate] =>
                                 [VATAmount] =>
                                 [Length] =>
                                 [Height] =>
                                 [Width] =>
                                 [GoodsClass] =>
                                 [PaymentItemUnit] =>
                                 [VendorName] =>
                                 [VendorINN] =>
                                 [VendorPhone] =>
                                 [PaymentItemSign] =>
                                 [ExciseAmount] =>
                                 [ItemCountryOrigin] =>
                                 [CustomsDeclaration] =>
                             )
                     )
    
            [ForLocation] =>
            [Draft] =>
            [DeliveryAmount] => 255.6
            [Commission] => 86.85
            [PostIdentifier] => // Номер РПО Почты России
            [PaymentType] => 1
            [FullDeliveryAmount] => 255.6
        )
     )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="printLabel"><h3>Печать этикетки</h3></a>  
Возвращает ссылку на PDF-файл или PNG картинку с этикетами заказа.  

**Входные параметры:**  
*$barcode (string)* -  штрих код заказа в формате EAN-13. Существует возможность принимать несколько штрих кодов через разделитель '|' без пробелов.  
 
**Выходные параметры:**  
Ссылка на PDF-файл или PNG картинку

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->printLabel('7500264678517'); // $result = https://api.iml.ru/PrintBar?Barcode=7500264678517
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```




<a name="listimlru"><h1>Справочный сервис IML</h1></a>  
Функции [API справочников](https://list.iml.ru/) компании IML (Получение ПВЗ, услуг, городов и т.п.).


<a name="getResourceLimitList"><h3>Справочник ограниченных ресурсов</h3></a>  
Возвращает список ограниченных ресурсов.

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getResourceLimitList();
    /*
     Array
     (
         [0] => Array
             (
                 [JobNo] =>
                 [RegionCode] =>
                 [LocationCode] =>
                 [BeginLimit] =>
                 [EndLimit] =>
                 [CalendarCodeLocation] =>
                 [CalendarCodeOffice] =>
                 [InquiryLimitTime] => 00:00:00
                 [InquiryLimitShiftDay] => 0
                 [DeliveryTimeMIN] => 00:00:00
                 [DeliveryTimeMAX] => 00:00:00
                 [DeliveryPeriod] => 0
                 [AmountMIN] => 0
                 [AmountMAX] => 0
                 [ValuatedAmountMIN] => 0
                 [ValuatedAmountMAX] => 0
                 [WeightMIN] => 0
                 [WeightMAX] => 0
                 [PackagesMin] => 0
                 [PackagesMAX] => 0
                 [SidesAmountMIN] => 0
                 [SidesAmountMAX] => 100
                 [Blocked] =>
                 [Side1Min] => 0
                 [Side1Max] => 0
                 [Side2Min] => 0
                 [Side2Max] => 0
                 [Side3Min] => 0
                 [Side3Max] => 0
             )
     */   
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```

<a name="getZipList"><h3>Справочник почтовых индексов</h3></a>  
Возвращает список почтовых индексов.

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getZipList();
    /*
     Array
     (
         [0] => Array
             (
                 [INDEX] => 101000
                 [CITY] => МОСКВА
                 [REGION] => МОСКВА
                 [AUTONOM] =>
                 [AREA] =>
                 [RATEZONE] => 0
                 [RegionIML] => МОСКВА
                 [AddZone] => ПАРКОВКА
                 [KLADR] => 7700000000000
                 [Name] => МОСКВА
                 [Type] => О
                 [Submission] => 127950
                 [PrevIndex] =>
                 [ActDate] => 2011-01-21T00:00:00
                 [LocationCode] =>
             )
     
         [1] => Array
             (
                 [INDEX] => 101300
                 [CITY] => МОСКВА
                 [REGION] => МОСКВА
                 [AUTONOM] =>
                 [AREA] =>
                 [RATEZONE] => 0
                 [RegionIML] => МОСКВА
                 [AddZone] => ПАРКОВКА
                 [KLADR] =>
                 [Name] => МОСКВА-300
                 [Type] => ТИ
                 [Submission] => 101000
                 [PrevIndex] =>
                 [ActDate] => 2012-02-21T00:00:00
                 [LocationCode] => МСК_ПС202
             )
     */   
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getZipInfo"><h3>Детальная информация по почтовому индексу</h3></a>  
Возвращает детальную информацию о почтовом индексе.

**Входные параметры:**  
- *$zip (int)* - Почтовый индекс
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getZipInfo(115551);
    /*
     Array
     (
         [0] => Array
             (
                 [INDEX] => 115551
                 [CITY] => МОСКВА
                 [REGION] => МОСКВА
                 [AUTONOM] =>
                 [AREA] =>
                 [RATEZONE] => 0
                 [RegionIML] => МОСКВА
                 [AddZone] => 01V 000
                 [KLADR] => 7700000000000
                 [Name] => МОСКВА 551
                 [Type] => О
                 [Submission] => 109950
                 [PrevIndex] =>
                 [ActDate] => 2011-01-21T00:00:00
                 [LocationCode] =>
             )
     
     )
     */   
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```

<a name="getWarehouseList"><h3>Справочник складов</h3></a>  
Возвращает детальную информацию о почтовом индексе.

**Входные параметры:**  
- *$full (boolean)* - флаг расширенной информации по складам  
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getWarehouseList();
    /*
     Array
     (
         [0] => Array
             (
                 [Code] => BEIJING
                 [Name] => Пекин
                 [RequestCode] =>
                 [RegionCode] => КИТАЙ
                 [Index] =>
                 [Address] =>
                 [Phone] =>
                 [EMail] =>
                 [WorkMode] =>
                 [Latitude] => 0
                 [Longitude] => 0
                 [HomePage] =>
                 [ClosingDate] =>
                 [OpeningDate] => 2020-12-31T21:00:00
                 [DaysFreeStorage] => 7
                 [SubAgent] => 0
                 [DeliveryTimeFrom] => 00:00:00
                 [DeliveryTimeTo] => 00:00:00
                 [Submission] =>
                 [ReceiptOrder] => 0
                 [TimeZone] => 0
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getSelfDeliveryRegions"><h3>Справочник регионов, где возможен самовывоз</h3></a>  
Возвращает список регионов, где возможен самовывоз (есть ПВЗ).

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getSelfDeliveryRegions();
    /*
     Array
     (
         [0] => Array
             (
                 [Code] => АБАКАН
                 [Description] => Абакан
             )
     
         [1] => Array
             (
                 [Code] => АЛУШТА
                 [Description] => Алушта
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getPostDeliveryLimits"><h3>Справочник почтовых ограничений</h3></a>  
Возвращает список ограничений заказов отправляемых через [Почту России](https://pochta.ru).

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getPostDeliveryLimits();
    /*
     Array
     (
         [0] => Array
             (
                 "INDEX": "sample string 1",
                 "PRBEGDATE": "2019-05-11T01:34:39.0230285+03:00",
                 "PRENDDATE": "2019-05-11T01:34:39.0230285+03:00",
                 "DELIVTYPE": 1,
                 "DELIVINDEX": "sample string 3",
                 "BASECOEFF": 1.0,
                 "TRANSFCNT": 1.0
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```

<a name="getRegionsList"><h3>Справочник регионов IML</h3></a>  
Возвращает список регионов IML.

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getRegionsList();
    /*
     Array
     (
         [0] => Array
             (
                 [Code] => АБАЗА
                 [Description] => Абаза
             )
     
         [1] => Array
             (
                 [Code] => АБАКАН
                 [Description] => Абакан
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```

<a name="getPvzList"><h3>Справочник пунктов самовывоза (ПВЗ)</h3></a>  
Возвращает список пунктов выдачи заказов.

**Входные параметры:**  
Отсутствуют 
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getPvzList();
    /*
     Array
     (
         [0] => Array
             (
                 [ID] => 1000002
                 [CalendarWorkCode] => 000476
                 [Code] => АБАКАН_ПС3
                 [Name] => Абакан ПВЗ 3
                 [RequestCode] => 3
                 [RegionCode] => АБАКАН
                 [Index] => 655016
                 [Address] => ул. Кати Перекрещенко, д. 10,  магазин «Фадейка»
                 [Phone] => 8-495-988-49-05
                 [EMail] => call@iml.ru
                 [WorkMode] => Пн-Пт 10:00-19:00 (без п-ва), Сб-Вс 10:00-18:00 (без п-ва)
                 [FittingRoom] => 0
                 [PaymentCard] => 1
                 [PaymentPossible] => 1
                 [ReceiptOrder] => 0
                 [Latitude] => 53.740823
                 [Longitude] => 91.434581
                 [HomePage] => https://iml.ru/p/1000002
                 [ClosingDate] =>
                 [OpeningDate] => 2016-11-30T21:00:00
                 [CouponReceipt] => 0
                 [DaysFreeStorage] => 7
                 [SubAgent] => 0
                 [DeliveryTimeFrom] => 10:00:00
                 [DeliveryTimeTo] => 19:00:00
                 [Carrier] =>
                 [ReplicationPath] =>
                 [Submission] => МОСКВА
                 [Special_Code] => 3
                 [HowToGet] => Проезд: автобус № 3, 5а, 7, 10, 12, 14, 17, троллейбус № 1, 1А, 3А. Остановка Дворец Молодежи: Пройти в сторону рынка Северный 250 метров. Остановка "Рынок "Северный": Пройти в сторону Дворца молодежи 150 метров. ПВЗ в магазине Фадейка.
                 [FormPostCode] =>
                 [FormRegion] => Хакасия Респ.
                 [FormCity] => Абакан г.
                 [FormStreet] => Кати Перекрещенко ул.
                 [FormHouse] => 10
                 [FormBuilding] =>
                 [FormOffice] =>
                 [FormKLADRCode] => 19000001000007200
                 [FormFIASCode] =>
                 [FormalizedArea] =>
                 [FormalizedLocality] =>
                 [Scale] => 0
                 [TimeZone] => 7
                 [Type] => 1
                 [ReplacementLocation] =>
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getPvzInRegion"><h3>Список ПВЗ в указанном регионе</h3></a>  
Возвращает список пунктов выдачи заказов в указанном регионе (см. [справочник регионов](#getRegionsList))

**Входные параметры:**  
- *$region_code (string)* - Код региона из [справочника регионов](#getRegionsList)
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getPvzInRegion('МОСКВА');
    /*
     Array
     (
         [0] => Array
             (
                 [ID] => 1001639
                 [CalendarWorkCode] => 001282
                 [Code] => МСК_ПС292
                 [Name] => Пушкинская ПС1
                 [RequestCode] => 1
                 [RegionCode] => МОСКВА
                 [Index] => 125009
                 [Address] => ул. Тверская, д. 12, стр. 2
                 [Phone] => 8-495-988-49-05
                 [EMail] => call@iml.ru
                 [WorkMode] => Пн-Вс 11:00-21:00 (без п-ва)
                 [FittingRoom] => 0
                 [PaymentCard] => 0
                 [PaymentPossible] => 1
                 [ReceiptOrder] => 7
                 [Latitude] => 55.763711
                 [Longitude] => 37.607511
                 [HomePage] => https://iml.ru/p/1000474
                 [ClosingDate] =>
                 [OpeningDate] => 2014-12-31T21:00:00
                 [CouponReceipt] => 0
                 [DaysFreeStorage] => 7
                 [SubAgent] => 0
                 [DeliveryTimeFrom] => 11:00:00
                 [DeliveryTimeTo] => 20:00:00
                 [Carrier] =>
                 [ReplicationPath] =>
                 [Submission] => МОСКВА
                 [Special_Code] => 1
                 [HowToGet] => м. Пушкинская. Выход № 8, двигаться прямо по Тверской. Вход с ул. Тверская, 3й этаж, пом. № 308
                 [FormPostCode] =>
                 [FormRegion] => Москва г.
                 [FormCity] =>
                 [FormStreet] => Тверская ул.
                 [FormHouse] => 12
                 [FormBuilding] => 2
                 [FormOffice] =>
                 [FormKLADRCode] => 77000000000287700
                 [FormFIASCode] =>
                 [FormalizedArea] =>
                 [FormalizedLocality] =>
                 [Scale] => 0
                 [TimeZone] => 3
                 [Type] => 1
                 [ReplacementLocation] =>
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```

<a name="getPvzByKladr"><h3>Список ПВЗ по коду КЛАДР</h3></a>  
Возвращает список пунктов выдачи заказов по коду КЛАДР.  

**Входные параметры:**  
- *$kladr (string)* - Код КЛАДР
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getPvzByKladr('77000000000287700');
    /*
     Array
     (
         [0] => Array
             (
                 [ID] => 1001639
                 [CalendarWorkCode] => 001282
                 [Code] => МСК_ПС292
                 [Name] => Пушкинская ПС1
                 [RequestCode] => 1
                 [RegionCode] => МОСКВА
                 [Index] => 125009
                 [Address] => ул. Тверская, д. 12, стр. 2
                 [Phone] => 8-495-988-49-05
                 [EMail] => call@iml.ru
                 [WorkMode] => Пн-Вс 11:00-21:00 (без п-ва)
                 [FittingRoom] => 0
                 [PaymentCard] => 0
                 [PaymentPossible] => 1
                 [ReceiptOrder] => 7
                 [Latitude] => 55.763711
                 [Longitude] => 37.607511
                 [HomePage] => https://iml.ru/p/1000474
                 [ClosingDate] =>
                 [OpeningDate] => 2014-12-31T21:00:00
                 [CouponReceipt] => 0
                 [DaysFreeStorage] => 7
                 [SubAgent] => 0
                 [DeliveryTimeFrom] => 11:00:00
                 [DeliveryTimeTo] => 20:00:00
                 [Carrier] =>
                 [ReplicationPath] =>
                 [Submission] => МОСКВА
                 [Special_Code] => 1
                 [HowToGet] => м. Пушкинская. Выход № 8, двигаться прямо по Тверской. Вход с ул. Тверская, 3й этаж, пом. № 308
                 [FormPostCode] =>
                 [FormRegion] => Москва г.
                 [FormCity] =>
                 [FormStreet] => Тверская ул.
                 [FormHouse] => 12
                 [FormBuilding] => 2
                 [FormOffice] =>
                 [FormKLADRCode] => 77000000000287700
                 [FormFIASCode] =>
                 [FormalizedArea] =>
                 [FormalizedLocality] =>
                 [Scale] => 0
                 [TimeZone] => 3
                 [Type] => 1
                 [ReplacementLocation] =>
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```

<a name="getPvzByKladr"><h3>Все справочники одним запросом</h3></a>  
Возвращает все справочники одним запросом. Порядок справочников можно посмотреть [тут](http://list.iml.ru/Help/Api/GET-All).  
**Обратите внимание, что в описании ключи справочников строковые, по факту числовые, но порядок совпадает.**    


**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->allReferenceBooks();
    /*
     Array
     (
         [0] => Array
             (
                 [0] => Array
                     (
                         [Code] => 0
                         [Name] => Не принят
                         [StatusType] => 1
                         [StatusTypeDescription] => Доставка
                         [Description] => Заказ ожидается на складе консолидации
                     )
     
                 [1] => Array
                     (
                         [Code] => 1
                         [Name] => На Складе
                         [StatusType] => 1
                         [StatusTypeDescription] => Доставка
                         [Description] => Заказ на складе консолидации
                     )
         [1] => Array
                 (
                     [0] => Array
                         (
                             [Code] => АБАЗА
                             [Description] => Абаза
                         )
         
                     [1] => Array
                         (
                             [Code] => АБАКАН
                             [Description] => Абакан
                         )
         
                     [2] => Array
                         (
                             [Code] => АБИНСК
                             [Description] => АБИНСК
                         )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getServicesList"><h3>Справочник услуг</h3></a>  
Возвращает список услуг.  

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getServicesList();
    /*
     Array
     (
         [0] => Array
             (
                 [Code] => 24
                 [Description] => Доставка предоплаченого заказа
                 [Type] => 0
                 [CommissionType] => 0
                 [ReceiptFromCustomer] =>
                 [AmountMAX] => 0
                 [AmountMIN] => 0
                 [ValuatedAmountMIN] => 0
                 [ValuatedAmountMAX] => 300000
                 [WeightMIN] => 0
                 [WeightMAX] => 25
                 [VolumeMIN] => 0
                 [VolumeMAX] => 0
                 [Scope] => 0
             )
     
         [1] => Array
             (
                 [Code] => 24КО
                 [Description] => Доставка с кассовым обслуживанием
                 [Type] => 0
                 [CommissionType] => 2
                 [ReceiptFromCustomer] =>
                 [AmountMAX] => 300000
                 [AmountMIN] => 0.01
                 [ValuatedAmountMIN] => 0
                 [ValuatedAmountMAX] => 300000
                 [WeightMIN] => 0
                 [WeightMAX] => 27
                 [VolumeMIN] => 0
                 [VolumeMAX] => 0
                 [Scope] => 0
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getStatusesList"><h3>Справочник статусов</h3></a>  
Возвращает список услуг.  

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getStatusesList();
    /*
     Array
     (
         [0] => Array
             (
                 [Code] => 0
                 [Name] => Не принят
                 [StatusType] => 1
                 [StatusTypeDescription] => Доставка
                 [Description] => Заказ ожидается на складе консолидации
             )
     
         [1] => Array
             (
                 [Code] => 0
                 [Name] => -
                 [StatusType] => 2
                 [StatusTypeDescription] => Заказ
                 [Description] => Отсутствует статус заказа
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getAdditionalDeliveryZones"><h3>Справочник дополнительных зон доставки</h3></a>  
Возвращает список дополнительных зон доставки.  

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getAdditionalDeliveryZones();
    /*
     Array
     (
         [0] => Array
             (
                 [Index] => 630011
                 [City] => НОВОСИБИРСК
                 [Region] => НОВОСИБИРСКАЯ ОБЛАСТЬ
                 [Autonom] =>
                 [Area] =>
                 [RegionIML] => НОВОСИБИРСК
                 [RateZoneMoscow] => F
                 [RateZoneSpb] => F
                 [AddZone] => 01V 000
                 [KLADR] => 5400000100000
             )
     
         [1] => Array
             (
                 [Index] => 450963
                 [City] => УФА
                 [Region] => БАШКОРТОСТАН РЕСПУБЛИКА
                 [Autonom] =>
                 [Area] =>
                 [RegionIML] => УФА
                 [RateZoneMoscow] => D
                 [RateZoneSpb] => E
                 [AddZone] => 01V 000
                 [KLADR] =>
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getValuationCurrencies"><h3>Справочник валют оценочной стоимости</h3></a>  
Возвращает список валют.  

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getValuationCurrencies();
    /*
     Array
     (
         [0] => Array
             (
                 [Type] => Код валюты
                 [Option] => 156
                 [PublicNameUnified] => Китайский юань
                 [NameUnified] => Yuan Renminbi
                 [CurrencyCode] => CNY
             )
     
         [1] => Array
             (
                 [Type] => Код валюты
                 [Option] => 643
                 [PublicNameUnified] => Российский рубль
     
                 [NameUnified] => Russian Rouble
                 [CurrencyCode] => RUB
             )
    */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getWorkCalendarIml"><h3>Рабочий календарь IML</h3></a>  
Возвращает рабочий календарь IML.  

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getWorkCalendarIml();
    /*
     Array
     (
         [0] => Array
             (
                 [Code] => 000001
                 [RecurringSystem] => 2
                 [Date] =>
                 [DayOfWeek] => 1
                 [Description] =>
                 [Nonworking] => 0
                 [TimeFrom] => 9:00 AM
                 [TimeTo] => 6:00 PM
                 [RecurringSystemDescription] =>
             )
     
         [1] => Array
             (
                 [Code] => 000001
                 [RecurringSystem] => 2
                 [Date] =>
                 [DayOfWeek] => 2
                 [Description] =>
                 [Nonworking] => 0
                 [TimeFrom] => 9:00 AM
                 [TimeTo] => 6:00 PM
                 [RecurringSystemDescription] =>
             )
    */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getRegionsAndCities"><h3>Справочник регионов и городов доставки</h3></a>  
Возвращает список регионов и городов доставки.  

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getRegionsAndCities();
    /*
     Array
     (
         [0] => Array
             (
                 [City] => 1 МАЯ
                 [Region] => НИЖЕГОРОДСКАЯ ОБЛАСТЬ
                 [Area] => БАЛАХНИНСКИЙ РАЙОН
                 [RegionIML] => НИЖНИЙ НОВГОРОД
                 [RateZoneMoscow] => B
                 [RateZoneSpb] => C
             )
     
         [1] => Array
             (
                 [City] => 11
                 [Region] => НИЖЕГОРОДСКАЯ ОБЛАСТЬ
                 [Area] =>
                 [RegionIML] => АРЗАМАС
                 [RateZoneMoscow] => B
                 [RateZoneSpb] => C
             )
    */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getPostTariffZones"><h3>Справочник тарифных зон Почты России</h3></a>  
Возвращает список тарифных зон Почты России.  

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getPostTariffZones();
    /*
     Array
     (
         [0] => Array
             (
                 [INDEX]: 1,
                 [RATEZONE]: "sample string 2"
             )
     
         [1] => Array
             (
                 [INDEX]: 1,
                 [RATEZONE]: "sample string 2"
             )
    */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getUnitsList"><h3>Справочник единиц измерения вложений заказа</h3></a>  
Возвращает список единиц измерения вложений заказа.  

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getUnitsList();
    /*
     Array
     (
         [0] => Array
             (
                 [Type] => Единица Измерения
                 [Option] => 2
                 [PublicNameUnified] => Палета
                 [NameUnified] => Paleta
             )
     
         [1] => Array
             (
                 [Type] => Единица Измерения
                 [Option] => 6
                 [PublicNameUnified] => Метр
                 [NameUnified] => Meter
             )
    */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```

<a name="getRouteSegments"><h3>Справочник сегментов маршрутов</h3></a>  
Возвращает список сегментов маршрутов.  

**Входные параметры:**  
Отсутствуют
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getRouteSegments();
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getRouteSegmentByCode"><h3>Получение данных сегмента маршрута по коду</h3></a>  
Возвращает список сегментов маршрутов.  

**Входные параметры:**  
*$segment_code (string)* - код сегмента  
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getRouteSegmentByCode('segment_code');
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="getBlockedRegionsList"><h3>Список заблокированных регионов IML</h3></a>  
Список заблокированных регионов IML в разрезе услуг.  

**Входные параметры:**  
Отсутствуют  
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getBlockedRegionsList();
    /*
     Array
     (
         [0] => Array
             (
                 [RegionCode] =>
                 [JobNo] =>
                 [Open] => 1753-01-01T00:00:00
                 [End] => 9999-12-31T23:59:59.9999999
             )
     
         [1] => Array
             (
                 [RegionCode] =>
                 [JobNo] => 24КО
                 [Open] => 1753-01-01T00:00:00
                 [End] => 9999-12-31T23:59:59.9999999
             )
     
         [2] => Array
             (
                 [RegionCode] => АНАПА
                 [JobNo] => 24НАЛ
                 [Open] => 1753-01-01T00:00:00
                 [End] => 9999-12-31T23:59:59.9999999
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```

<a name="getDeliveryZones"><h3>Справочник зон доставки</h3></a>  
Список зон доставки с указанием количества дней.  

**Входные параметры:**  
Отсутствуют  
 
**Выходные параметры:**  
Ассоциативный массив данных

**Примеры вызова:**
```php
<?php
$imlClient = new \WildTuna\ImlSdk\Client();
$imlClient->setAuthParams('main', 'api_login', 'api_password');

try {
    $result = $imlClient->getDeliveryZones();
    /*
     Array
     (
         [0] => Array
             (
                 [FromRegion] => 101000
                 [ToRegion] => 671433
                 [ZoneCode] => ПОЧТА
                 [DayLimit] => 16
                 [NeedVolumetric] => 2
             )
     
         [1] => Array
             (
                 [FromRegion] => 101000
                 [ToRegion] => 671434
                 [ZoneCode] => ПОЧТА
                 [DayLimit] => 16
                 [NeedVolumetric] => 2
             )
     */
}

catch (\WildTuna\ImlSdk\Exception\ImlException $e) {
    // Обработка ошибки вызова API IML
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса IML
    // $e->getRawResponse(); // ответ сервиса IML как есть (http response body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```
