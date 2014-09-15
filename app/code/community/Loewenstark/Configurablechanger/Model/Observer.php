<?php

class Loewenstark_Configurablechanger_Model_Observer
{
    /**
     * @mageEvent controller_action_predispatch_checkout_cart_add
     * @mageEvent controller_action_predispatch_
     * @param type $event
     */
    public function addToCartPreDispatch($event)
    {
        if(!Mage::getStoreConfigFlag('configurable_changer/properties/active'))
        {
            return $this;
        }
        $controller = $event->getControllerAction();
        /* @var $controller Mage_Checkout_CartController */
        $super_attribute = $controller->getRequest()->getParam('super_attribute');
        $productId = (int)$controller->getRequest()->getParam('product');
        if($super_attribute && $productId)
        {
            $_isConf = (int) Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect(array('sku', 'type_id'))
                    ->addAttributeToFilter('type_id','configurable')
                    ->addAttributeToFilter('entity_id', $controller->getRequest()->getParam('product'))
                    ->getSize();
            if($_isConf > 0)
            {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($super_attribute, $product);
                $controller->getRequest()->setParam('product', $childProduct->getId());
            }
        }
        return $this;
    }
}