# Yii2-free-kassa
Yii2 free kassa extension.
This packages provide to you forms, action filters, and component.

[![Latest Stable Version](https://poser.pugx.org/churakovmike/yii2-free-kassa/v/stable)](https://packagist.org/packages/churakovmike/yii2-free-kassa)
[![License](https://poser.pugx.org/churakovmike/yii2-free-kassa/license)](https://packagist.org/packages/churakovmike/yii2-free-kassa)

# Getting started
## install
The package is available on packagist.
```php
composer require churakovmike/yii2-free-kassa
```
or add to require section
```php
"churakovmike/yii2-free-kassa": "~1.3"
```

## Add components to config
You need to add component config in main-local.php
```php
'freeKassaComponent' => [
    'class' => \ChurakovMike\Freekassa\FreeKassaComponent::class,
    'merchantId' => 'merchant-id',
    'firstSecret' => 'your-first-secret',
    'secondSecret' => 'your-second-secret',
],
```
## Add action filter(optional)
If you want to check callback sender, you need to add CheckIpFilter to behaviours
```php
public function behaviors()
{
    return [
        ChurakovMike\Freekassa\filters\CheckIpFilter::class,
     ]
}
```
## Forms usage
This package provide to you form for fast load and validate free-kassa callback.
```php
$form = new ChurakovMike\Freekassa\forms\SuccessPayForm();
$form->setAttributes(\Yii::$app->request->post());
$form->validate();
```
## Components usage
```php
/** @var FreeKassaComponent $component */
$component = \Yii::$app->freeKassaComponent;
```
### Check signature with component
```php
/** @var FreeKassaComponent $component */
$component = \Yii::$app->freeKassaComponent;
$component->validateSignature($amount, $orderId, $signature);
```
You can take signature from SuccessPayForm in property $form->sign
### Generate pyment link
```php
/** @var FreeKassaComponent $component */
$component = \Yii::$app->freeKassaComponent;
$component->generatePaymentLink($orderId, $sum, $description);
```
### Export orders to xml
```php
$orders = $component->exportOrders($limit, $offset, $status, $dateFrom, $dateTo);
```
### Check balance
```php
$balance = $component->getBalance();
```
### Check order
```php
$balance = $component->getOrder($orderId, $intid);
```
### Withdraw money
```php
$withdraw = $component->withdraw($amount, $currency);
```
### Invoicing
```php
$invoice = $component->invoice($email, $amount, $description);
```
### Get wallet balance
```php
$balance = $component->getWalletBalance();
```
### Withdraw money from wallet
```php
$withdraw = $component->walletWithdraw($purse, $amount, $currency, $desc, $disableExchange);
```
### Get wallet operation status
```php
$status = $component->getOperationStatus($paymentId);
```
### Transfer money to another wallet
```php
$transfer = $component->transferMoney($purse, $amount);
```
### Payment for online services
```php
$payment = $component->onlinePayment($serviceId, $account, $amount);
```
### Get list of services for online payment
```php
$list = $component->getOnlineServices();
```
### Check status online payment
```php
$status = $component->getOnlinePaymentStatus($paymentId);
```
### Create crypto walllet address
```php
$btc = $component->createBTCAddress();
$ltc = $component->createLTCAddress();
$eth = $component->createETHAddress();
```
### Get crypto wallet address
```php
$btc = $component->getBTCAddress();
$ltc = $component->getLTCAddress();
$eth = $component->getETHAddress();
```
### Get information about transaction
```php
$btcTransaction = $component->getBTCTransaction();
$ltcTransaction = $component->getLTCTransaction();
$ethTransaction = $component->getETHTransaction();
```
