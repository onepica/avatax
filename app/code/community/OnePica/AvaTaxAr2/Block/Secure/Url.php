<?php
/**
 * Created by PhpStorm.
 * User: o.marynych
 * Date: 2018-08-08
 * Time: 9:37 AM
 */

trait OnePica_AvaTaxAr2_Block_Secure_Url
{
    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = array())
    {
        if (Mage::app()->getStore()->isCurrentlySecure()) {
            $params['_secure'] = true;
        }

        return parent::getUrl($route, $params);
    }
}