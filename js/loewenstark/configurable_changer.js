
document.observe("dom:loaded", function() {
    var configurableCache = [];
    spConfig.getIdOfSelectedProduct = function() {
        var existingProducts = new Object();
        for (var i = this.settings.length - 1; i >= 0; i--) {
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
            if (selected.config) {
                for (var iproducts = 0; iproducts < selected.config.products.length; iproducts++) {
                    var usedAsKey = selected.config.products[iproducts] + "";
                    if (existingProducts[usedAsKey] == undefined) {
                        existingProducts[usedAsKey] = 1;
                    } else {
                        existingProducts[usedAsKey] = existingProducts[usedAsKey] + 1;
                    }
                }
            }
        }
        for (var keyValue in existingProducts) {
            for (var keyValueInner in existingProducts) {
                if (Number(existingProducts[keyValueInner]) < Number(existingProducts[keyValue])) {
                    delete existingProducts[keyValueInner];
                }
            }
        }
        var sizeOfExistingProducts = 0;
        var currentSimpleProductId = "";
        for (var keyValue in existingProducts) {
            currentSimpleProductId = keyValue;
            sizeOfExistingProducts = sizeOfExistingProducts + 1
        }
        if (sizeOfExistingProducts == 1) {
            return currentSimpleProductId;
        }
    }
    $$('select.super-attribute-select').each(function(item, index) {
        item.addEventListener('change', function() {
            var id = spConfig.getIdOfSelectedProduct();
            if(typeof(id)=='undefined'){
                id = spConfig.config.productId;
            }
            if (typeof (configurableCache[id]) != 'undefined') {
                setProductData(configurableCache[id]);
                return false;
            }
            if (id) {
                new Ajax.Request('configurablechanger/index/index/productid/' + id,
                        {
                            method: 'get',
                            onSuccess: function(response) {
                                var product = response.responseText.evalJSON().evalJSON();
                                configurableCache[id] = product;
                                setProductData(product);
                            }
                        });

                return false;
            } else
                return false;
        }, false);
    });
    function setProductData(product) {       
        $$('div.product-name h1')[0].innerHTML=product.name;
        $$('div.short-description div.std')[0].innerHTML = product.short_description;
        $$('p.product-image img')[0].writeAttribute('src',product.image);
    }
});