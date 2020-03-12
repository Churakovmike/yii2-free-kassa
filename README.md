# Yii2-free-kassa
Yii2 free kassa extension.
This packages provide to you forms, action filters, and component

# Getting started
## install
The package is available on packagist.
```php
composer require churakovmike/yii2-free-kassa
```
or add to require section
```php
"churakovmike/yii2-free-kassa": "~1.1"
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
