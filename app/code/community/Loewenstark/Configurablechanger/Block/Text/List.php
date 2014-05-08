<?php

class Loewenstark_Configurablechanger_Block_Text_List
extends Mage_Core_Block_Abstract
{
    protected function _construct() {
        parent::_construct();
        $this->addData(array('cache_lifetime' => 43200)); // 12 hours
        $this->addCacheTag(array(
            Mage_Catalog_Model_Product::CACHE_TAG,
            Mage_Catalog_Model_Product::CACHE_TAG . '_' . Mage::registry('productid'),
        ));
    }

    /**
     * 
     * @return string
     */
    protected function _toHtml()
    {
        $result = array();
        foreach ($this->getSortedChildren() as $name) {
            $block = $this->getLayout()->getBlock($name);
            /* @var $block Mage_Core_Block_Abstract */
            if (!$block) {
                Mage::throwException(Mage::helper('core')->__('Invalid block: %s', $name));
            }
            $result[] = array(
                'class'   => $block->getBlockAlias(),
                'content' => $block->toHtml()
            );
        }
        return Mage::helper('core')->jsonEncode($result);
    }

    /**
     * add an attribute to json
     * 
     * @param string $attribute
     * @param string $alias
     * @param string $name
     * 
     * @return Loewenstark_Configurablechanger_Block_Text_List
     */
    public function addProductAttribute($attribute, $alias, $name=null)
    {
        if(is_null($name))
        {
            $name = 'configurablechanger_'.$attribute;
        }
        $block = $this->getLayout()->createBlock('configurablechanger/attribute', $name)->setProductAttribute($attribute);
        $this->append($block, $alias);
        return $this;
    }
    
    
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
}