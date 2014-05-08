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
        
        $productId = $request['productid'];
        $cache = Mage::app()->getCache();
        $key = 'changer_product_' . $productId.'_'.Mage::app()->getStore()->getId();
        $json = $cache->load($key);
        if (!$json)
        {
            $product = Mage::getModel('catalog/product')->load($productId);
            $productArray = array(
                'name' => $product->getName(),
                'short_description' => $product->getShortDescription(),
                'image' => $product->getImageUrl()
            );
            $json = Mage::helper('core')->jsonEncode($productArray);
            $cache->save($json, $key,
                    array(Mage_Catalog_Model_Product::CACHE_TAG, Mage_Catalog_Model_Product::CACHE_TAG.'_'.$productId), 60*60*24);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));
    }
    
    /**
     * 
     * @return Loewenstark_Configurablechanger_IndexController
     */
    protected function renderJsonLayout()
    {
        /* events from renderLayout */
        Mage::dispatchEvent('controller_action_layout_render_before');
        Mage::dispatchEvent('controller_action_layout_render_before_'.$this->getFullActionName());
        
        $output = array();
        foreach($this->getLayout()->getBlock('root')->getChild() as $_child)
        {
            /* @var $_child Mage_Core_Block_Abstract */
            $output[$_child->getBlockAlias()] = $_child->toHtml();
        }
        $this->getResponse()
                ->clearHeaders()
                ->setHeader('Content-type', 'application/json', true)
                ->setBody(Mage::helper('core')->jsonEncode($output));
    }
}
