<?php

class Loewenstark_Configurablechanger_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $request = $this->getRequest()->getParams();
        $productId = $request['productid'];
        $cache = Mage::app()->getCache();
        $json = $cache->load("changer_product_" . $productId);
        if ($json) {
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));
        } else {
            $product = Mage::getModel('catalog/product')->load($productId);
            $productArray = array(
                'name' => $product->getName(),
                'short_description' => $product->getShortDescription(),
                'image' => $product->getImageUrl()
            );
            $json = Mage::helper('core')->jsonEncode($productArray);
            $cache->save($json, "changer_product_".$productId, array("configurable_change_products"), 60 * 60);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));
        }
    }
}
