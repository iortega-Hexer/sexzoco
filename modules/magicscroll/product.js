
var magictoolboxImagesOrder = [];

window['displayImage'] = function(jQuerySetOfAnchors) {

}

window['findCombinationOriginal'] = window['findCombination'];
window['findCombination'] = function(firstTime) {
    window['findCombinationOriginal'].apply(window, arguments);
    if(typeof(firstTime) != 'undefined' && firstTime) {
        return;
    }
    var idCombination = $('#idCombination').val();
    for(var i in combinations) {
        if(combinations[i]['idCombination'] == idCombination) {
            var position = jQuery.inArray(combinations[i]['image'], magictoolboxImagesOrder);
            MagicScroll.jump('productMagicScroll', position);
            $('#bigpic').attr('src', $('#productMagicScroll img').get(position).src);
            break;
        }
    }
}

window['refreshProductImagesOriginal'] = window['refreshProductImages'];
window['refreshProductImages'] = function(id_product_attribute) {
}

