<?php

class Loewenstark_Configurablechanger_Block_Text_List
extends Mage_Core_Block_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->addData(array('cache_lifetime' => 43200)); // 12 hours
        $this->addCacheTag(array(
            Mage_Catalog_Model_Product::CACHE_TAG,
            Mage_Catalog_Model_Product::CACHE_TAG . '_' . $this->getProductId(),
        ));
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            Mage_Catalog_Model_Product::CACHE_TAG . '_' . $this->getProductId(),
            $this->getNameInLayout(),
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
        );
    }

    /**
     * 
     * @return string
     */
    protected function _toHtml()
    {
        $result = array(
            'product_id' => $this->getProduct()->getId(),
            'items' => array()
        );
        foreach ($this->getSortedChildren() as $name) {
            $block = $this->getLayout()->getBlock($name);
            /* @var $block Mage_Core_Block_Abstract */
            if (!$block) {
                Mage::throwException(Mage::helper('core')->__('Invalid block: %s', $name));
            }
            $block->setProduct($this->getProduct());
            $result['items'][] = array(
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
        $product = Mage::getModel('catalog/product')->load($this->getProductId());
        Mage::register('product', $product);
        return $product;
    }
    
    /**
     * get Product Id
     * 
     * @return int
     */
    public function getProductId()
    {
        return Mage::registry('product_id');
    }
    
    /**
     * Add tag to block
     *
     * @param string|array $tag
     * @return Mage_Core_Block_Abstract
     */
    public function addCacheTag($tag)
    {
        if (method_exists('Mage_Core_Block_Abstract', 'addCacheTag'))
        {
            return parrent::addCacheTag($tag);
        }
        if (!is_array($tag))
        {
            $tag = array($tag);
        }
        $this->addData(array(
            'cache_tags'    => $tag
        ));
        return $this;
    }
}
