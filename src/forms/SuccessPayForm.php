<?php

namespace ChurakovMike\Freekassa\forms;

use yii\base\Model;

/**
 * Class SuccessPayForm
 * @package ChurakovMike\Freekassa
 *
 * @property string $sign
 */
class SuccessPayForm extends Model
{
    /**
     * Shop Id.
     *
     * @var string $merchant_id
     */
    public $merchant_id;

    /**
     * Order sum.
     *
     * @var string $amount
     */
    public $amount;

    /**
     * Number of operaion Free-Kassa.
     *
     * @var string $intid
     */
    public $intid;

    /**
     * Your order number.
     *
     * @var string $merchant_order_id
     */
    public $merchant_order_id;

    /**
     * Customer email.
     *
     * @var string $p_email
     */
    public $p_email;

    /**
     * Customer phone.
     *
     * @var string $p_phone
     */
    public $p_phone;

    /**
     * Currency ID.
     *
     * @var string $cur_id
     */
    public $cur_id;

    /**
     * Sign.
     *
     * @var string $sign
     */
    public $sign;

    /**
     * Additional parameters.
     *
     * @var mixed $us_key
     */
    public $us_key;

    /**
     * Order description.
     *
     * @var string $us_desc
     */
    public $us_desc;

    /**
     * Only for test mode.
     *
     * @var string $test_payment
     */
    public $test_payment;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['merchant_id', 'amount', 'intid', 'merchant_order_id', 'p_email', 'p_phone', 'cur_id', 'sign', 
                'us_desc', 'test_payment'], 'string'],
            [['merchant_id', 'amount', 'intid', 'merchant_order_id', 'p_email', 'cur_id', 'sign'], 'string'],
            [['us_key'], 'safe'],
        ];
    }

    /**
     * Set attribution to model from param with capital letters.
     *
     * @param array $values
     * @param bool $safeOnly
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            $values = array_change_key_case($values, CASE_LOWER);
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }

    /**
     * Check is test order callback.
     *
     * @return bool
     */
    public function isTestOrder(): bool
    {
        return (bool)$this->test_payment;
    }
}
