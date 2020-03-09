<?php

namespace ChurakovMike\Freekassa\filters;

use ChurakovMike\Freekassa\exceptions\WrongIpException;
use yii\base\ActionFilter;

/**
 * Filter for check ip addresses free-kassa callbacks.
 *
 * Class CheckIpFilter
 * @package ChurakovMike\Freekassa\filters
 */
class CheckIpFilter extends ActionFilter
{
    /**
     * List of allowed ip for callbacks.
     *
     * @var array $allowedUrls
     */
    public $allowedIps = [
        '136.243.38.147',
        '136.243.38.149',
        '136.243.38.150',
        '136.243.38.151',
        '136.243.38.189',
        '136.243.38.108',
    ];

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws WrongIpException
     */
    public function beforeAction($action)
    {
        if (!$this->checkIp()) {
            throw new WrongIpException();
        }

        return parent::beforeAction($action);
    }

    /**
     * Get ip.
     *
     * @return mixed
     */
    protected function getIp()
    {
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Check allowed address.
     *
     * @return bool
     */
    protected function checkIp(): bool
    {
        if (!in_array($this->getIp(), $this->allowedIps)) {
            return false;
        }

        return true;
    }
}
