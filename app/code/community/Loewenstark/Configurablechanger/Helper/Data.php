<?php

class Loewenstark_Configurablechanger_Helper_Data
extends Mage_Core_Helper_Abstract
{
    /**
     * 
     * @return boolean
     */
    public function canUseWebp()
    {
        if (Mage::helper('core')->isModuleEnabled('MageProfis_ImageQueue')
                && Mage::getConfig()->getModuleConfig('MageProfis_ImageQueue')->is('active', 'true'))
        {
            return Mage::helper('imagequeue')->canUseWebp();
        }
        return false;
    }
}
