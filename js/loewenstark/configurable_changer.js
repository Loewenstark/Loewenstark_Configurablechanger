function confChangeOnSuccess(){}
document.observe("dom:loaded", function() {
    var configurableCache = [];
    
    if(typeof spConfig == 'undefined') return true;
    
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
    spConfig.loadConfData = function() {
        var id = spConfig.getIdOfSelectedProduct();
            if(typeof(id)=='undefined'){
                id = spConfig.config.productId;
            }
            if (typeof (configurableCache[id]) != 'undefined') {
                setProductData(configurableCache[id]);
                confChangeOnSuccess();
                return false;
            }
            if (id) {
                var path = '/configurablechanger/index/index/productid/';
                var url = '';
                if (typeof(BASE_URL) == 'undefined') {
                    var port = (document.location.port == '') ? '': ':'+document.location.port;
                    url = document.location.protocol+'//'+document.location.hostname+port+path;
                } else {
                    url = BASE_URL+path;
                }
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
            } else {
                return false;
            }
    }
    $$('select.super-attribute-select').each(function(item, index) {
        item.addEventListener('change', function() {
            return spConfig.loadConfData();
        }, false);
    });
    
    // If defaults are overwritten by url (e.g. product.html#123=45&45=66) load current data
    var separatorIndex = window.location.href.indexOf('#');
    if (separatorIndex != -1) {
        spConfig.loadConfData();
    }
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
            var element = $$(e.class)[0];
            if(typeof element != 'undefined') {
                element.innerHTML = e.content;
            }else{
                console.log('[Loewenstark_Configurablechanger] Can\'t finde element with class: '+e.class);
            }
        }
    });
}
