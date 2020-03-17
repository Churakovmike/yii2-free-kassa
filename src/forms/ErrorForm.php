<?php

namespace ChurakovMike\Freekassa\forms;

/**
 * Class ErrorForm
 * @package ChurakovMike\Freekassa\forms
 *
 * @property string $merchant_order_id
 */
class ErrorForm extends BaseForm
{
    /**
     * Your order number.
     *
     * @var string $merchant_order_id
     */
    public $merchant_order_id;
}
