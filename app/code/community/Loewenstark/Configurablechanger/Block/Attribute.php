<?php

class Loewenstark_Configurablechanger_Block_Attribute
extends Mage_Core_Block_Abstract
{
    
    protected $_attr_code = null;


    /**
     * get Product Model
     * 
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = Mage::registry('product');
        if($product instanceof Mage_Catalog_Model_Product)
        {
            return $product;
        }
        $product = Mage::getModel('catalog/product')->load(Mage::registry('product_id'));
        Mage::register('product', $product);
        return $product;
    }
    
    /**
     * 
     * @param string $code
     * @return Loewenstark_Configurablechanger_Block_Attribute
     */
    public function setProductAttribute($code)
    {
        $this->_attr_code = $code;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getProductAttribute()
    {
        $_product = $this->getProduct();
        return Mage::helper('catalog/output')->productAttribute($_product, $_product->getResource()->getAttribute($this->_attr_code)->getFrontend()->getValue($_product), $this->_attr_code);
    }
    
    /**
     * 
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getProductAttribute();
    }
}