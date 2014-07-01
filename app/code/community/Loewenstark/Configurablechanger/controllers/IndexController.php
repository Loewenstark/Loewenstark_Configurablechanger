<?php

class Loewenstark_Configurablechanger_IndexController
extends Mage_Core_Controller_Front_Action
{
    /**
     * 
     */
    public function indexAction()
    {
        $productid = $this->getRequest()->getParam('productid', false);
        Mage::register('product_id', $productid);
        $this->loadLayout($this->getFullActionName());
        $this->renderLayout();
        return $this;
    }
}
