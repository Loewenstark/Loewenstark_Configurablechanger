function confChangeOnSuccess(){}
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
                console.log(id);
                var port = (document.location.port == '') ? '': ':'+document.location.port;
                var url = document.location.protocol+'//'+document.location.hostname+port+'/configurablechanger/index/index/productid/'
                new Ajax.Request(url + id,
                        {
                            method: 'get',
                            onSuccess: function(response) {
                                var product = response.responseText.evalJSON();
                                configurableCache[id] = product;
                                setProductData(product);
                                confChangeOnSuccess();
                            }
                        });
                return false;
            } else
                return false;
        }, false);
    });
});

function setProductData(product)
{
    var product_id = product.product_id;
    var currentAction = $('product_addtocart_form').readAttribute('action');
    var newcurrentAction = currentAction.replace(/product\/\d+\//, 'product/' + product_id + '/');
    $('product_addtocart_form').writeAttribute('action', newcurrentAction);
    product.items.forEach(function(e) {
        if(e.content != '' && e.content != '&nbsp;')
        {
            $$(e.class)[0].innerHTML = e.content;
        }
    });
}
