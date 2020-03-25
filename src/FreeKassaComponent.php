<?php

namespace ChurakovMike\Freekassa;

use ChurakovMike\Freekassa\exceptions\WrongCurrenciesException;
use ChurakovMike\Freekassa\exceptions\WrongSignatureException;
use yii\base\Component;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use \Exception;

/**
 * Class FreeKassaComponent.
 * @package ChurakovMike\Freekassa
 *
 * @property string $baseUrl
 * @property string $baseFormUrl
 * @property string $baseExportOrdersUrl
 * @property string $walletApiUrl
 * @property string $merchantId
 * @property integer $defaultCurrency
 * @property string $firstSecret
 * @property string $secondSecret
 * @property string $walletId
 * @property string $walletApiKey
 * @property Client $httpClient
 */
class FreeKassaComponent extends Component
{
    /**
     * Codes of available currencies.
     */
    const
        CURRENCY_TEST = 178,
        CURRENCY_VISA_MASTERCARD_KZT = 186,
        CURRENCY_FK_WALLET_RUB = 133,
        CURRENCY_SBERBANK_RUR = 80,
        CURRENCY_MASTERCARD_VISA_RUB = 179,
        CURRENCY_QIWI_WMZ = 155,
        CURRENCY_QIWI_WMR = 63,
        CURRENCY_QIWI_EURO = 161,
        CURRENCY_QIWI_USD = 123,
        CURRENCY_YANDEX_MONEY_FIRST = 45,
        CURRENCY_YANDEX_MONEY_SECOND = 175,
        CURRENCY_QIWI_KZT = 162,
        CURRENCY_VISA_MASTERCARD_RUB_PLUS = 153,
        CURRENCY_CARD_P2P = 159,
        CURRENCY_CASA_MASTERCARD_RUB = 94,
        CURRENCY_VISA_MASTERCARD_USD = 100,
        CURRENCY_VISA_MASTERCARD_EUR = 124,
        CURRENCY_VISA_MASTERCARD = 160,
        CURRENCY_TETHER_USDT = 181,
        CURRENCY_VISA_MASTERCARD_UAH = 67,
        CURRENCY_ADVCASH_KZT = 184,
        CURRENCY_ADVCASH_USD = 136,
        CURRENCY_ADVCASH_RUB = 150,
        CURRENCY_ADVCASH_EUR = 183,
        CURRENCY_EXMO_RUB = 180,
        CURRENCY_EXMO_USD = 174,
        CURRENCY_LITECOIN = 147,
        CURRENCY_BITCOINCASH_ABC = 166,
        CURRENCY_MONERO = 172,
        CURRENCY_RIPPLE = 173,
        CURRENCY_ETHEREUM = 163,
        CURRENCY_BLACKCOIN_BLK = 167,
        CURRENCY_DOGECOIN_DOGE = 168,
        CURRENCY_EMERCOIN_EMC = 169,
        CURRENCY_PRIMECOIN_XMP = 170,
        CURRENCY_REDCOIN_RDD = 171,
        CURRENCY_ZCASH = 165,
        CURRENCY_DASH = 164,
        CURRENCY_BITCOIN = 116,
        CURRENCY_SKIN_PAY = 154,
        CURRENCY_WMZ_BILL = 131,
        CURRENCY_WEBMONEY_WMZ = 2,
        CURRENCY_WEBMONEY_WME = 3,
        CURRENCY_PAYEER_RUB = 114,
        CURRENCY_PAYEER_USD = 115,
        CURRENCY_PERFECT_MONEY_USD = 64,
        CURRENCY_PERFECT_MONEY_EUR = 69,
        CURRENCY_ALFA_BANK_RUR = 79,
        CURRENCY_PROMSVYAZBANK = 110,
        CURRENCY_RUSSIAN_STANDART = 113,
        CURRENCY_MOBILE_MEGAFON = 82,
        CURRENCY_MOBILE_MTS = 84,
        CURRENCY_MOBILE_TELE2 = 132,
        CURRENCY_MOBILE_BEELINE = 83,
        CURRENCY_TERMINAL = 99,
        CURRENCY_VISA_MC_INT = 158,
        CURRENCY_VISA_UAH_CASHOUT = 157,
        CURRENCY_SALON = 118,
        CURRENCY_TRANSFERS = 117,
        CURRENCY_PAYPAL = 70,
        CURRENCY_MOBILE_MEGAFON_NORTH_WEST_FILIAL = 137,
        CURRENCY_MOBILE_MEGAFON_SIBERIAN_FILIAL = 138,
        CURRENCY_MOBILE_MEGAFON_KAUKAZ_FILIAL = 139,
        CURRENCY_MOBILE_MEGAFON_POVOLZSKIY_FILIAL = 140,
        CURRENCY_MOBILE_MEGAFON_URAL_FILIAL = 141,
        CURRENCY_MOBILE_MEGAFON_DALNIYVOSTOK_FILIAL = 142,
        CURRENCY_MOBILE_MEGAFON_CENTRAL_FILIAL = 143;

    /**
     * List of available currencies for wallet.
     */
    const WALLET_CURRENCIES = [
        self::CURRENCY_FK_WALLET_RUB,
        self::CURRENCY_QIWI_WMR,
        self::CURRENCY_QIWI_EURO,
        self::CURRENCY_QIWI_USD,
        self::CURRENCY_YANDEX_MONEY_FIRST,
        self::CURRENCY_QIWI_KZT,
        self::CURRENCY_CASA_MASTERCARD_RUB,
        self::CURRENCY_VISA_MASTERCARD_USD,
        self::CURRENCY_VISA_MASTERCARD_EUR,
        self::CURRENCY_VISA_MASTERCARD_UAH,
        self::CURRENCY_ADVCASH_USD,
        self::CURRENCY_ADVCASH_RUB,
        self::CURRENCY_EXMO_USD,
        self::CURRENCY_WEBMONEY_WMZ,
        self::CURRENCY_PAYEER_RUB,
        self::CURRENCY_PAYEER_USD,
        self::CURRENCY_PERFECT_MONEY_USD,
        self::CURRENCY_PERFECT_MONEY_EUR,
        self::CURRENCY_MOBILE_MEGAFON,
        self::CURRENCY_MOBILE_MTS,
        self::CURRENCY_MOBILE_TELE2,
        self::CURRENCY_MOBILE_BEELINE,
        self::CURRENCY_PAYPAL,
    ];

    /**
     * List of available actions.
     */
    const
        ACTION_GET_BALANCE = 'get_balance',
        ACTION_GET_ORDER = 'check_order_status',
        ACTION_EXPORT_ORDERS = 'get_orders',
        ACTION_PAYMENT = 'payment',
        ACTION_CREATE_BILL = 'create_bill',
        ACTION_WALLET_WITHDRAW = 'cashout',
        ACTION_OPERATION_STATUS = 'get_payment_status',
        ACTION_TRANSFER_MONEY = 'transfer',
        ACTION_ONLINE_PAYMENT = 'online_payment',
        ACTION_PROVIDERS = 'providers',
        ACTION_CHECK_ONLINE_PAYMENT = 'check_online_payment',
        ACTION_CREATE_BTC_ADDRESS = 'create_btc_address',
        ACTION_CREATE_LTC_ADDRESS = 'create_ltc_address',
        ACTION_CREATE_ETH_ADDRESS = 'create_eth_address',
        ACTION_GET_BTC_ADDRESS = 'get_btc_address',
        ACTION_GET_LTC_ADDRESS = 'get_ltc_address',
        ACTION_GET_ETH_ADDRESS = 'get_eth_address',
        ACTION_GET_BTC_TRANSACTION = 'get_btc_transaction',
        ACTION_GET_LTC_TRANSACTION = 'get_ltc_transaction',
        ACTION_GET_ETH_TRANSACTION = 'get_eth_transaction';

    const
        ORDER_STATUS_NEW = 'new',
        ORDER_STATUS_PAID = 'paid',
        ORDER_STATUS_COMPLETED = 'completed',
        ORDER_STATUS_ALL = 'all';

    /**
     * Free-Kassa api url.
     *
     * @var string $baseUrl
     */
    public $baseUrl = 'https://www.free-kassa.ru/api.php';

    /**
     * Url for form action.
     *
     * @var string $baseFormUrl
     */
    public $baseFormUrl = 'http://www.free-kassa.ru/merchant/cash.php';

    /**
     * Url for exports orders list.
     *
     * @var string
     */
    public $baseExportOrdersUrl = 'https://www.free-kassa.ru/export.php';

    /**
     * Api url for wallet requests.
     * 
     * @var string 
     */
    public $walletApiUrl = 'https://www.fkwallet.ru/api_v1.php';

    /**
     * Merchant ID. (example: 19999).
     *
     * @var string $merchantId
     */
    public $merchantId;

    /**
     * First secret ket generated by setting.
     *
     * @var string $firstSecret
     */
    public $firstSecret;

    /**
     * Second secret ket generated by setting.
     *
     * @var string $secondSecret
     */
    public $secondSecret;

    /**
     * Personal wallet identity.
     *
     * @var string $walletId
     */
    public $walletId;

    /**
     * Default currency for form
     *
     * @var integer $defaultCurrency
     */
    public $defaultCurrency;

    /**
     * Api key can be generate on the next link.
     * @see https://www.fkwallet.ru/settings
     *
     * @var string $walletApiKey
     */
    public $walletApiKey;

    /**
     * @var Client $_httpClient
     */
    private $_httpClient;

    /**
     * FreeKassaComponent constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function getHttpClient(): Client
    {
        if (!is_object($this->_httpClient)) {
            $this->_httpClient = \Yii::createObject($this->defaultHttpClientConfig());
        }

        return $this->_httpClient;
    }

    /**
     * Default settings for client.
     *
     * @return array
     */
    protected function defaultHttpClientConfig(): array
    {
        return [
            'class' => Client::class,
            'baseUrl' => $this->baseUrl,
            'transport' => CurlTransport::class,
        ];
    }

    /**
     * Check wallet balance.
     *
     * @return array|Response
     * @throws \Exception
     */
    public function getBalance()
    {
        $data = [
            'merchant_id' => $this->merchantId,
            's' => $this->generateApiSignature(),
            'action' => self::ACTION_GET_BALANCE,
        ];

        return $this->request($data);
    }

    /**
     * @return array|Response
     * @throws Exception
     */
    public function getOrder($orderId = null, $intId = null)
    {
        $data = [
            'merchant_id' => $this->merchantId,
            's' => md5($this->merchantId . $this->secondSecret),
            'action' => self::ACTION_GET_ORDER,
        ];

        if (!is_null($orderId)) {
            $data['order_id'] = $orderId;
        }

        if (!is_null($orderId)) {
            $data['intid'] = $intId;
        }

        return $this->request($data);
    }

    /**
     * Get orders list.
     *
     * @param int $limit
     * @param int $offset
     * @param string $status
     * @param string $date_from
     * @param string $date_to
     * @return array|Response
     * @throws Exception
     */
    public function exportOrders(
        int $limit = 100,
        int $offset = 0,
        string $status = self::ORDER_STATUS_ALL,
        string $date_from = '',
        string $date_to = ''
    ) {
        $data = [
            'merchant_id' => $this->merchantId,
            's' => $this->generateApiSignature(),
            'action' => self::ACTION_EXPORT_ORDERS,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'status' => $status,
            'limit' => $limit,
            'offest' => $offset,
        ];

        return $this->request($data, $this->baseExportOrdersUrl, 'GET');
    }

    /**
     * Withdraw money.
     *
     * @param $amount
     * @return array|Response
     * @throws Exception
     */
    public function withdraw($amount, $currency = null)
    {
        $data = [
            'merchant_id' => $this->merchantId,
            'currency' => $currency ?? $this->defaultCurrency,
            'amount' => $amount,
            's' => $this->generateApiSignature(),
            'action' => self::ACTION_PAYMENT,
        ];

        return $this->request($data);
    }

    /**
     * Create invoice.
     *
     * @param string $email
     * @param mixed $amount
     * @param mixed $orderId
     * @return array|Response
     * @throws Exception
     */
    public function invoice(string $email, $amount, $description)
    {
        $data = [
            'merchant_id' => $this->merchantId,
            'email' => $email,
            'amount' => $amount,
            'desc' => urlencode($description),
            's' => $this->generateApiSignature(),
            'action' => self::ACTION_CREATE_BILL,
        ];

        return $this->request($data);
    }

    /**
     * Get wallet balance.
     *
     * @return array|Response
     * @throws Exception
     */
    public function getWalletBalance()
    {
        $data = [
            'wallet_id' => $this->walletId,
            'sign' => $this->generateWalletSignature(),
            'action' => self::ACTION_GET_BALANCE,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Withdraw money from wallet.
     *
     * @param string $purse
     * @param $amount
     * @param $currency
     * @param string $desc
     * @param int $disableExchange
     * @return array|Response
     * @throws Exception
     * @throws WrongCurrenciesException
     */
    public function walletWithdraw(string $purse, $amount, $currency, string $desc = '', $disableExchange = 1)
    {
        if (!in_array($currency, self::WALLET_CURRENCIES)) {
            throw new WrongCurrenciesException();
        }

        $data = [
            'wallet_id' => $this->walletId,
            'purse' => $purse,
            'amount' => $amount,
            'desc' => $desc,
            'disable_exchange' => $disableExchange,
            'currency' => $currency,
            'sign' => md5($this->walletId . $currency . $amount . $purse . $this->walletApiKey),
            'action' => self::ACTION_WALLET_WITHDRAW,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Get operation status.
     *
     * @param $paymentId
     * @return array|Response
     * @throws Exception
     */
    public function getOperationStatus($paymentId)
    {
        $data = [
            'wallet_id' => $this->walletId,
            'payment_id' => $paymentId,
            'sign' => md5($this->walletId . $paymentId . $this->walletApiKey),
            'action' => self::ACTION_OPERATION_STATUS,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Transfer money to another wallet.
     *
     * @param $purse
     * @param $amount
     * @return array|Response
     * @throws Exception
     */
    public function transferMoney($purse, $amount)
    {
        $data = [
            'wallet_id' => $this->walletId,
            'purse' => $purse,
            'amount' => $amount,
            'sign' => md5($this->walletId . $purse . $amount . $this->walletApiKey),
            'action' => self::ACTION_TRANSFER_MONEY,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Payment online services.
     *
     * @param $serviceId
     * @param $account
     * @param $amount
     * @return array|Response
     * @throws Exception
     */
    public function onlinePayment($serviceId, $account, $amount)
    {
        $data = [
            'wallet_id' => $this->walletId,
            'service_id' => $serviceId,
            'account' => $account,
            'amount' => $amount,
            'sign' => md5($this->walletId . $amount . $account . $this->walletApiKey),
            'action' => self::ACTION_ONLINE_PAYMENT,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Get list of payment services.
     *
     * @return array|Response
     * @throws Exception
     */
    public function getOnlineServices()
    {
        $data = [
            'wallet_id' => $this->walletId,
            'sign' => $this->generateWalletSignature(),
            'action' => self::ACTION_PROVIDERS,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Check status online payment.
     *
     * @param $paymentId
     * @return array|Response
     * @throws Exception
     */
    public function getOnlinePaymentStatus($paymentId)
    {
        $data = [
            'wallet_id' => $this->walletId,
            'payment_id' => $paymentId,
            'sign' => md5($this->walletId . $paymentId . $this->walletApiKey),
            'action' => self::ACTION_CHECK_ONLINE_PAYMENT,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Create BTC address.
     *
     * @return array|Response
     */
    public function createBTCAddress()
    {
        return $this->createCryptoAddress(self::ACTION_CREATE_BTC_ADDRESS);
    }

    /**
     * Create LTC address.
     *
     * @return array|Response
     */
    public function createLTCAddress()
    {
        return $this->createCryptoAddress(self::ACTION_CREATE_LTC_ADDRESS);
    }

    /**
     * Create ETH address.
     *
     * @return array|Response
     */
    public function createETHAddress()
    {
        return $this->createCryptoAddress(self::ACTION_CREATE_ETH_ADDRESS);
    }

    /**
     * Create crypto wallet address.
     *
     * @param $action
     * @return array|Response
     * @throws Exception
     */
    public function createCryptoAddress($action)
    {
        $data = [
            'wallet_id' => $this->walletId,
            'sign' => $this->generateWalletSignature(),
            'action' => $action,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Get BTC address.
     *
     * @return array|Response
     * @throws Exception
     */
    public function getBTCAddress()
    {
        return $this->getCryptoAddress(self::ACTION_GET_BTC_ADDRESS);
    }

    /**
     * Get LTC address.
     *
     * @return array|Response
     * @throws Exception
     */
    public function getLTCAddress()
    {
        return $this->getCryptoAddress(self::ACTION_GET_LTC_ADDRESS);
    }

    /**
     * GET ETH address.
     *
     * @return array|Response
     * @throws Exception
     */
    public function getETHAddress()
    {
        return $this->getCryptoAddress(self::ACTION_GET_ETH_ADDRESS);
    }

    /**
     * Get crypto address by action.
     *
     * @param $action
     * @return array|Response
     * @throws Exception
     */
    public function getCryptoAddress($action)
    {
        $data = [
            'wallet_id' => $this->walletId,
            'sign' => $this->generateWalletSignature(),
            'action' => $action,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Get information about BTC transaction.
     *
     * @param $transactionId
     * @return array|Response
     */
    public function getBTCTransaction($transactionId)
    {
        return $this->getTransaction(self::ACTION_GET_BTC_TRANSACTION, $transactionId);
    }

    /**
     * Get information about LTC transaction.
     *
     * @param $transactionId
     * @return array|Response
     */
    public function getLTCTransaction($transactionId)
    {
        return $this->getTransaction(self::ACTION_GET_LTC_TRANSACTION, $transactionId);
    }

    /**
     * Get information about ETH transaction.
     *
     * @param $transactionId
     * @return array|Response
     */
    public function getETHTransaction($transactionId)
    {
        return $this->getTransaction(self::ACTION_GET_ETH_TRANSACTION, $transactionId);
    }

    /**
     * Get information about transaction by action.
     * 
     * @param $action
     * @param $transactionId
     * @return array|Response
     * @throws Exception
     */
    public function getTransaction($action, $transactionId)
    {
        $data = [
            'wallet_id' => $this->walletId,
            'transaction_id' => $transactionId,
            'sign' => md5($this->walletId . $transactionId . $this->walletApiKey),
            'action' => $action,
        ];

        return $this->request($data, $this->walletApiUrl, 'POST');
    }

    /**
     * Generate payment link for redirect user to Free-Kassa.com.
     *
     * @param $orderId
     * @param $sum
     * @param string $description
     */
    public function generatePaymentLink($orderId, $sum, $email = '', $description = '')
    {
        $data = [
            'o' => $orderId,
            'oa' => $sum,
            's' => $this->generateFormSignature($sum, $orderId),
            'm' => $this->merchantId,
            'i' => $this->defaultCurrency,
            'em' => $email,
            'lang' => 'ru',
        ];

        if (!empty($description)) {
            $data['us_desc'] = $description;
        }

        return $this->baseFormUrl . "?" . http_build_query($data);
    }

    /**
     * Generate signature with amount and order_id.
     *
     * @param $amount
     * @param $orderId
     * @return string
     */
    public function generateSignature($amount, $orderId): string
    {
        return md5($this->merchantId . ':' . $amount . ':' . $this->secondSecret . ':' . $orderId);
    }

    /**
     * Generate signature to wallet requests.
     *
     * @return string
     */
    public function generateWalletSignature(): string
    {
        return md5($this->walletId . $this->walletApiKey);
    }

    /**
     * Generate signature for form.
     *
     * @param $amount
     * @param $orderId
     * @return string
     */
    public function generateFormSignature($amount, $orderId): string
    {
        return md5($this->merchantId . ':' . $amount . ':' . $this->firstSecret . ':' . $orderId);
    }

    /**
     * @return string
     */
    public function generateApiSignature(): string
    {
        return md5($this->merchantId . $this->secondSecret);
    }

    /**
     * Validate signature.
     *
     * @param $amount
     * @param $orderId
     * @param $sign
     * @return bool
     * @throws WrongSignatureException
     */
    public function validateSignature($amount, $orderId, $sign): bool
    {
        if ($this->generateSignature($amount, $orderId) !== $sign) {
            throw new WrongSignatureException('Invalid signature received');
        }
        
        return true;
    }

    /**
     * Base request with parameters.
     *
     * @param array $data
     * @param array $options
     * @throws \Exception
     * @return Response|array
     */
    protected function request(array $data, $url = null, $method = 'POST', $options = [])
    {
        $fullData = array_merge($this->defaultRequestData(), $data);
        $rowData = http_build_query($fullData);

        try {
            $request = $this->httpClient->$method($url ?? $this->httpClient->baseUrl, $fullData);
            $response = $request->send();
            if (!$response->isOk) {
                throw new Exception($response->data);
            }

            return $response->data;
        } catch (\Exception $exception) {
            \Yii::error([
                'errorMessage' => $exception->getMessage(),
                'data' => $fullData,
                'dataRow' => $rowData,
            ]);

            throw $exception;
        }
    }

    /**
     * Full list currencies key => name.
     *
     * @return array
     */
    public function getCurrencies(): array
    {
        return [
            self::CURRENCY_TEST => 'Тестовая оплата',
            self::CURRENCY_VISA_MASTERCARD_KZT => 'VISA/MASTERCARD KZT',
            self::CURRENCY_FK_WALLET_RUB => 'FK WALLET RUB',
            self::CURRENCY_SBERBANK_RUR => 'Сбербанк RUR',
            self::CURRENCY_MASTERCARD_VISA_RUB => 'MASTERCARD/VISA RUB',
            self::CURRENCY_QIWI_WMZ => 'QIWI WALLET',
            self::CURRENCY_QIWI_WMR => 'QIWI кошелек',
            self::CURRENCY_QIWI_EURO => 'QIWI EURO',
            self::CURRENCY_QIWI_USD => 'QIWI USD',
            self::CURRENCY_YANDEX_MONEY_FIRST => 'Яндекс.Деньги',
            self::CURRENCY_YANDEX_MONEY_SECOND => 'Яндекс-Деньги',
            self::CURRENCY_QIWI_KZT => 'QIWI KZT',
            self::CURRENCY_VISA_MASTERCARD_RUB_PLUS => 'VISA/MASTERCARD+ RUB',
            self::CURRENCY_CARD_P2P => 'CARD P2P',
            self::CURRENCY_CASA_MASTERCARD_RUB => 'VISA/MASTERCARD RUB',
            self::CURRENCY_VISA_MASTERCARD_USD => 'VISA/MASTERCARD USD',
            self::CURRENCY_VISA_MASTERCARD_EUR => 'VISA/MASTERCARD EUR',
            self::CURRENCY_VISA_MASTERCARD => 'VISA/MASTERCARD',
            self::CURRENCY_TETHER_USDT => 'Tether USDT',
            self::CURRENCY_VISA_MASTERCARD_UAH => 'VISA/MASTERCARD UAH',
            self::CURRENCY_ADVCASH_KZT => 'ADVCASH KZT',
            self::CURRENCY_ADVCASH_USD => 'ADVCASH USD',
            self::CURRENCY_ADVCASH_RUB => 'ADVCASH RUB',
            self::CURRENCY_ADVCASH_EUR => 'ADVCASH EUR',
            self::CURRENCY_EXMO_RUB => 'Exmo RUB',
            self::CURRENCY_EXMO_USD => 'Exmo USD',
            self::CURRENCY_LITECOIN => 'Litecoin',
            self::CURRENCY_BITCOINCASH_ABC => 'BitcoinCash ABC',
            self::CURRENCY_MONERO => 'Monero',
            self::CURRENCY_RIPPLE => 'Ripple',
            self::CURRENCY_ETHEREUM => 'Ethereum',
            self::CURRENCY_BLACKCOIN_BLK => 'Blackcoin BLK',
            self::CURRENCY_DOGECOIN_DOGE => 'Dogecoin DOGE',
            self::CURRENCY_EMERCOIN_EMC => 'Emercoin EMC',
            self::CURRENCY_PRIMECOIN_XMP => 'Primecoin XMP',
            self::CURRENCY_REDCOIN_RDD => 'Reddcoin RDD',
            self::CURRENCY_ZCASH => 'ZCASH',
            self::CURRENCY_DASH => 'DASH',
            self::CURRENCY_BITCOIN => 'Bitcoin',
            self::CURRENCY_SKIN_PAY => 'Skin pay',
            self::CURRENCY_WMZ_BILL => 'WMZ-bill',
            self::CURRENCY_WEBMONEY_WMZ => 'WebMoney WMZ',
            self::CURRENCY_WEBMONEY_WME => 'WebMoney WME',
            self::CURRENCY_PAYEER_RUB => 'PAYEER RUB',
            self::CURRENCY_PAYEER_USD => 'PAYEER USD',
            self::CURRENCY_PERFECT_MONEY_USD => 'Perfect Money USD',
            self::CURRENCY_PERFECT_MONEY_EUR => 'Perfect Money EUR',
            self::CURRENCY_ALFA_BANK_RUR => 'Альфа-банк RUR',
            self::CURRENCY_PROMSVYAZBANK => 'Промсвязьбанк',
            self::CURRENCY_RUSSIAN_STANDART => 'Русский стандарт',
            self::CURRENCY_MOBILE_MEGAFON => 'Мобильный Платеж Мегафон',
            self::CURRENCY_MOBILE_MTS => 'Мобильный Платеж МТС',
            self::CURRENCY_MOBILE_TELE2 => 'Мобильный Платеж Tele2',
            self::CURRENCY_MOBILE_BEELINE => 'Мобильный Платеж Билайн',
            self::CURRENCY_TERMINAL => 'Терминалы России',
            self::CURRENCY_VISA_MC_INT => 'VISA/MC INT',
            self::CURRENCY_VISA_UAH_CASHOUT => 'VISA UAH CASHOUT',
            self::CURRENCY_SALON => 'Салоны связи',
            self::CURRENCY_TRANSFERS => 'Денежные переводы',
            self::CURRENCY_PAYPAL => 'PayPal',
            self::CURRENCY_MOBILE_MEGAFON_NORTH_WEST_FILIAL => 'Мобильный Платеж МегаФон Северо-Западный филиал',
            self::CURRENCY_MOBILE_MEGAFON_SIBERIAN_FILIAL => 'Мобильный Платеж МегаФон Сибирский филиал',
            self::CURRENCY_MOBILE_MEGAFON_KAUKAZ_FILIAL => 'Мобильный Платеж МегаФон Кавказский филиал',
            self::CURRENCY_MOBILE_MEGAFON_POVOLZSKIY_FILIAL => 'Мобильный Платеж МегаФон Поволжский филиал',
            self::CURRENCY_MOBILE_MEGAFON_URAL_FILIAL => 'Мобильный Платеж МегаФон Уральский филиал',
            self::CURRENCY_MOBILE_MEGAFON_DALNIYVOSTOK_FILIAL => 'Мобильный Платеж МегаФон Дальневосточный филиал',
            self::CURRENCY_MOBILE_MEGAFON_CENTRAL_FILIAL => 'Мобильный Платеж МегаФон Центральный филиал',
        ];
    }

    /**
     * Return currency name by code.
     *
     * @param $code
     * @return mixed
     */
    public function getCurrencyName($code): string
    {
        return $this->getCurrencies()[$code];
    }

    /**
     * Set another default currency value.
     *
     * @param $currencyId
     */
    public function setCurrency($currencyId): void
    {
        $this->defaultCurrency = $currencyId;
    }

    /**
     * Default request params.
     * @return array
     */
    private function defaultRequestData() : array
    {
        return [];
    }
}
