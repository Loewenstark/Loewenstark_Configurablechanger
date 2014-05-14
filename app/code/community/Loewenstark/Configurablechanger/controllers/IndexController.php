<?php

class Loewenstark_Configurablechanger_IndexController
extends Mage_Core_Controller_Front_Action
{
    /**
     * 
     */
    public function indexAction()
    {
        $this->loadLayout($this->getFullActionName());
        $productid = $this->getRequest()->getParam('productid', false);
        Mage::register('product_id', $productid);
        $this->renderLayout();
        return $this;
    }
}
