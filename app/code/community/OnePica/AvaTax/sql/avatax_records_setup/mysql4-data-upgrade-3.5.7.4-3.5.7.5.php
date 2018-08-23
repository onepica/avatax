<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
$installer = $this;
$this->startSetup();

/** @var \Magento_Db_Adapter_Pdo_Mysql $adapter */
$adapter = $this->getConnection();
$table = $this->getTable('avatax_records/parameter');

// no United States of America, Liberia, Myanmar
$allCountriesExceptFew = 'AD,AE,AF,AG,AI,AL,AM,AN,AO,AQ,AR,AS,AT,AU,AW,AX,AZ,BA,BB,BD,BE,BF,BG,BH,BI,BJ,BL,BM,BN,BO,BR,BS,BT,BV,BW,BY,BZ,CA,CC,CD,CF,CG,CH,CI,CK,CL,CM,CN,CO,CR,CU,CV,CX,CY,CZ,DE,DJ,DK,DM,DO,DZ,EC,EE,EG,EH,ER,ES,ET,FI,FJ,FK,FM,FO,FR,GA,GB,GD,GE,GF,GG,GH,GI,GL,GM,GN,GP,GQ,GR,GS,GT,GU,GW,GY,HK,HM,HN,HR,HT,HU,ID,IE,IL,IM,IN,IO,IQ,IR,IS,IT,JE,JM,JO,JP,KE,KG,KH,KI,KM,KN,KP,KR,KW,KY,KZ,LA,LB,LC,LI,LK,LS,LT,LU,LV,LY,MA,MC,MD,ME,MF,MG,MH,MK,ML,MN,MO,MP,MQ,MR,MS,MT,MU,MV,MW,MX,MY,MZ,NA,NC,NE,NF,NG,NI,NL,NO,NP,NR,NU,NZ,OM,PA,PE,PF,PG,PH,PK,PL,PM,PN,PR,PS,PT,PW,PY,QA,RE,RO,RS,RU,RW,SA,SB,SC,SD,SE,SG,SH,SI,SJ,SK,SL,SM,SN,SO,SR,ST,SV,SY,SZ,TC,TD,TF,TG,TH,TJ,TK,TL,TM,TN,TO,TR,TT,TV,TW,TZ,UA,UG,UM,UY,UZ,VA,VC,VE,VG,VI,VN,VU,WF,WS,YE,YT,ZA,ZM,ZW';

try {
    /** @var Mage_Adminhtml_Helper_Data $helper */
    $helper = Mage::helper('adminhtml');

    // add Kilogram
    $adapter->insert(
        $table, array(
            'avalara_uom'             => 'Kilogram',
            'avalara_parameter_type' => 'Mass',
            'description'              => $helper->__('Kilogram'),
            'country_list'             => $allCountriesExceptFew,
        )
    );

    // add Gram
    $adapter->insert(
        $table, array(
            'avalara_uom'              => 'Gram',
            'avalara_parameter_type'   => 'Mass',
            'description'              => $helper->__('Gram'),
            'country_list'             => $allCountriesExceptFew,
        )
    );
} catch (Zend_Db_Adapter_Exception $e) {
    Mage::logException($e);
}

$this->endSetup();
