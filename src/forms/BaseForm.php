<?php

namespace ChurakovMike\Freekassa\forms;

use yii\base\Model;

/**
 * Class BaseForm
 * @package ChurakovMike\Freekassa\forms
 */
class BaseForm extends Model
{
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
}
