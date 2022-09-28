var seoLoad = false;
jQuery(document).ready(function () {
    if (!seoLoad) {
        esconderTextoSeo();
    }
    /**
     * Evento seo asociado normalmente a la home
     **/
    if (jQuery('.fa-chevron-down').length > 0) {
        jQuery('.fa-chevron-down').on('click', function (e) {
            e.preventDefault();
            if (jQuery('.seotitle').hasClass('visible')) {
                jQuery('.seotitle').removeClass('visible');
            } else {
                jQuery('.seotitle').addClass('visible');
            }
        });
    }

    if (jQuery('.seobottom .title').length > 0) {
        jQuery('.seobottom .title').on('click', function (e) {
            e.preventDefault();
            if (jQuery('.seobottom').hasClass('visible')) {
                jQuery('.seobottom').removeClass('visible');
            } else {
                jQuery('.seobottom').addClass('visible');
            }
        });
    }

    if (jQuery('#product .product-accessories .products').length > 0) {
        jQuery('#product .product-accessories .products').slick({
            infinite: false,
            slidesToShow: 5,
            slidesToScroll: 1,
            dots: false,
            arrows: true,
            responsive: [
                {
                    breakpoint: 1100,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 1000,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 400,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }
    /* home */
    if (jQuery('#index .featured-products .products._desktop').length > 0) {
        if (jQuery(window).width() >= 600) {
            if (jQuery('#index .featured-products .products._desktop article').length > 4) {
                jQuery('#index .featured-products .products._desktop').slick({
                    infinite: false,
                    slidesToShow: 5,
                    slidesToScroll: 1,
                    dots: true,
                    arrows: true,
                    responsive: [
                        {
                            breakpoint: 1100,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 3,
                                infinite: true,
                                dots: true
                            }
                        },
                        {
                            breakpoint: 1000,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 2
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 400,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            } else {
                if (!jQuery('#index .featured-products .products._desktop').hasClass('without-slick')) {
                    jQuery('#index .featured-products .products._desktop').addClass('without-slick');
                }
            }
        } else {
            if (!jQuery('body').hasClass('_mobile_device')) {
                jQuery('body').addClass('_mobile_device');
            }
        }

    }
    /* checkcout pedido confirmado */
    if (jQuery('#order-confirmation .featured-products .products').length > 0) {
        jQuery('#order-confirmation .featured-products .products').slick({
            infinite: false,
            slidesToShow: 5,
            slidesToScroll: 1,
            dots: false,
            arrows: true,
            responsive: [
                {
                    breakpoint: 1100,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 1000,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 400,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }


    if (jQuery('#product .tabs.accordions .accordion-inner h3').length > 0) {
        jQuery('#product .tabs.accordions .accordion-inner h3').on('click', function (e) {
            e.preventDefault();
            var identificador = jQuery(this).data('id');
            jQuery('#product .tabs.accordions .accordion-inner .box-content.visible').removeClass('visible');
            jQuery('#product .tabs.accordions .accordion-inner h3').removeClass('visible');
            jQuery('#product .tabs.accordions .accordion-inner .box-content.content-' + identificador).addClass('visible');
            jQuery(this).addClass('visible');
        });
    }

    if (jQuery('#header #search_widget .lupa').length > 0) {
        jQuery('#header #search_widget .lupa').on('click', function (e) {
            e.preventDefault();
            jQuery('#header #search_widget form').addClass('activo');
        });
    }

    /*controla que en la resolución de 768px, se esconda en la categoría el listado de subcategorías en los filtros*/
    function checkWindowSize() {
        if (jQuery('#category #wrapper .content-inner #left-column .block-categories').length > 0) {
            if (jQuery(window).width() <= 768) {
                if (!jQuery('#category #wrapper .content-inner #left-column .block-categories').hasClass('notview')) {
                    jQuery('#category #wrapper .content-inner #left-column .block-categories').addClass('notview');

                    if (jQuery('#category .title-category .filter-button #search_filter_toggler').length > 0) {

                        jQuery('#category .title-category .filter-button #search_filter_toggler').on('click', function (e) {
                            if (jQuery('#category #wrapper .content-inner #left-column .block-categories').hasClass('notview')) {
                                jQuery('#category #wrapper .content-inner #left-column .block-categories').removeClass('notview');
                                jQuery("html, body").delay(500).animate({scrollTop: jQuery('#left-column').offset().top}, 2000);
                            }
                        });

                        jQuery('#category #wrapper .content-inner #left-column #search_filters_wrapper #search_filter_controls .btn.ok').on('click', function (e) {
                            if (!jQuery('#category #wrapper .content-inner #left-column .block-categories').hasClass('notview')) {
                                jQuery('#category #wrapper .content-inner #left-column .block-categories').addClass('notview');
                            }
                        });
                    }
                }
            } else {
                if (jQuery('#category #wrapper .content-inner #left-column .block-categories').hasClass('notview')) {
                    jQuery('#category #wrapper .content-inner #left-column .block-categories').removeClass('notview');
                }
            }
        }
    }
    checkWindowSize();
    jQuery(window).resize(checkWindowSize);


    if (jQuery('.page-home #custom-text .limitar .img').length > 0) {
        jQuery('.page-home #custom-text .limitar .img').on('click', function (e) {
            e.preventDefault;
            jQuery('.popup-contenedor .video').html('<object width="600" height="400">' +
                    '<param name="movie" value="https://www.youtube.com/v/5piPYXU-1zg?fs=1&amp;hl=en_US"></param>' +
                    '<param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>' +
                    '<embed src="https://www.youtube.com/v/5piPYXU-1zg?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="600" height="400"></embed>' +
                    '</object>');
            mostrarModal();
        });

        jQuery('div#popup span.popup-cerrar').click(function () {
            if (jQuery('div#popup').hasClass('visible')) {
                jQuery('div#popup').removeClass('visible');
                jQuery('.popup-contenedor .video').html('');
            }
        });
    }

    if (jQuery('body#authentication .btn[data-action="show-password"]').length > 0) {
        jQuery('body#authentication .btn[data-action="show-password"]').on('click', function (e) {
            e.preventDefault();
            console.log(jQuery('input.js-visible-password').attr('type'));
            if (jQuery('input.js-visible-password').attr('type') == 'text') {
                if (!jQuery(this).hasClass('show')) {
                    jQuery(this).addClass('show');
                }
            } else {
                if (jQuery(this).hasClass('show')) {
                    jQuery(this).removeClass('show');
                }
            }
        })
    }



    if (jQuery('#category #wrapper .content-inner #left-column .menu.js-top-menu .top-menu li a.dropdown-item').length > 0) {

        jQuery('#category #wrapper .content-inner #left-column .menu.js-top-menu .top-menu li a.dropdown-item .navbar-toggler').on('click', function (e) {
            e.preventDefault();
            if (jQuery('.popover.sub-menu.js-sub-menu').hasClass('mostrar')) {
                jQuery('.popover.sub-menu.js-sub-menu').removeClass('mostrar');
            }
            var idSubmenu = jQuery(this).data('target');
            if (jQuery(idSubmenu).length > 0) {
                jQuery(idSubmenu).addClass('mostrar');
            }
        });
    }

    /*ficha producto thumbs*/
    if (jQuery('.js-qv-product-images').length > 0) {
        jQuery('.js-qv-product-images .js-thumb').on('click', function () {
            jQuery("#main .js-qv-mask").trigger("forward")
        });
    }

    /* Blog anchors */
    if (jQuery('#indice_entradas').length > 0) {
      jQuery("#indice_entradas a").click(function() {
          if(!jQuery("body._mobile_device").length > 0 ){
            var offsetheader = jQuery("#header").height();
          }else{
            var offsetheader = jQuery(".textoheadermovil.scroll_heading").height() + 20;
          }
          var gotolink = jQuery(this).attr("href");
          jQuery('html, body').animate({
              scrollTop: jQuery(gotolink).offset().top - offsetheader
          }, 200);
      });
      jQuery('#indice_entradas .open').on('click', function (e) {
        jQuery('#indice_entradas').toggleClass("cerrado");
      });
    }
});

function esconderTextoSeo() {
    if (jQuery('.seotitle').length > 0) {
        /*textoseohome*/
        if (jQuery('.seotitle').hasClass('visible')) {
            jQuery('.seotitle').removeClass('visible');
        }
    }
    if (jQuery('.seobottom .title').length > 0) {
        /*textoseocat*/
        if (jQuery('.seobottom').hasClass('visible')) {
            jQuery('.seobottom').removeClass('visible');
        }
    }
}


function mostrarModal() {
    if (!jQuery('div#popup').hasClass('visible')) {
        jQuery('div#popup').addClass('visible');
    }
}

/*init dofinder*/
var doofinder_script = '//cdn.doofinder.com/media/js/doofinder-compact.7.latest.min.js';
(function (d, t) {
    var f = d.createElement(t), s = d.getElementsByTagName(t)[0];
    f.async = 1;
    f.src = ('https:' == location.protocol ? 'https:' : 'http:') + doofinder_script;
    f.setAttribute('charset', 'utf-8');
    s.parentNode.insertBefore(f, s)
}(document, 'script'));

var pathnamelang = window.location.pathname;
var hashlang = "468bab8945fd9da19846f982c7be18f4";
var pathlang = "/es/";
var currentlang = "es";

if (pathnamelang.includes('/gb/')) {
    hashlang = "ff283a8383de11229a6e12912398ccca";
    pathlang = "/gb/";
    currentlang = "en";
} else if (pathnamelang.includes('/fr/')) {
    hashlang = "97d562d1006b5a6c9a117e171a53f25c";
    pathlang = "/fr/";
    currentlang = "fr";
}
/*console.log("https://" + window.location.hostname + pathlang + "search?controller=search&s=%query%");
 console.log(currentlang);
 console.log(hashlang);*/

var dfCompactLayers = [{
        "hashid": hashlang,
        "zone": "eu1",
        "display": {
            "lang": currentlang,
            "results": {
                "allResultsURL": "https://" + window.location.hostname + pathlang + "search?controller=search&s=%query%",
                "maxResults": 5
            },
            "closeOnClick": true,
            "suggestions": {
                "maxSuggestions": 5
            }
        },
        "queryInput": "#searchboxinput"
    }];
/* end dofinder*/
/*"https://" + window.location.hostname + pathlang + "search?q=%query%"*/
