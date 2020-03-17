<?php

namespace ChurakovMike\Freekassa\forms;

/**
 * Class SuccessForm
 * @package ChurakovMike\Freekassa\forms
 *
 * @property string $merchant_order_id
 * @property string $intid
 */
class SuccessForm extends BaseForm
{
    /**
     * Your order number.
     *
     * @var string $merchant_order_id
     */
    public $merchant_order_id;

    /**
     * Number of operaion Free-Kassa.
     *
     * @var string $intid
     */
    public $intid;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['merchant_order_id', 'intid'], 'string'],
        ];
    }
}
