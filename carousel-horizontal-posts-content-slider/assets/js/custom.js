jQuery(document).ready(function($) {

    $.each(wa_vars.id, function( index, value ) {
  alert( index + ": " + value );
});

    $('#wa_chpcs_foo' + wa_vars.id).carouFredSel({
            responsive: (wa_vars.chpcs_pre_responsive == 1) ? true : false,
            direction: wa_vars.wa_chpcs_pre_direction,
            align: wa_vars.chpcs_pre_align,
            width: (wa_vars.chpcs_pre_responsive != 1) ? '100%' : '',
            auto: {
                play: wa_vars.wa_chpcs_auto,
                timeoutDuration: wa_vars.wa_chpcs_timeout
            },
            scroll: {
                items: (wa_vars.c_items && wa_vars.c_items != 0) ? wa_vars.c_items : '',
                fx: wa_vars.wa_chpcs_query_posts_fx,
                easing: wa_vars.c_easing,
                duration: wa_vars.c_duration,
                pauseOnHover: (wa_vars.wa_chpcs_query_pause_on_hover == 1) ? true : false,
            },
            infinite: wa_vars.wa_chpcs_infinite,
            circular: wa_vars.wa_chpcs_circular,
            onCreate: function(data) {
                if (wa_vars.wa_chpcs_query_lazy_loading) {
                    loadImage();
                }
            },
            prev: {
                onAfter: function(data) {
                    if (wa_vars.wa_chpcs_query_lazy_loading) {
                        loadImage();
                    }
                },

                button: "#foo" + wa_vars.id + "_prev"
            },
            next: {
                onAfter: function(data) {
                    if (wa_vars.wa_chpcs_query_lazy_loading) {
                        loadImage();
                    }
                },
                button: "#foo" + wa_vars.id + "_next"
            },
            items: {
                width: (wa_vars.chpcs_pre_responsive == 1) ? wa_vars.wa_chpcs_query_posts_item_width : '',
                visible: (wa_vars.chpcs_pre_responsive == 0 && wa_vars.wa_chpcs_pre_direction == "up" || wa_vars.wa_chpcs_pre_direction == "down") ? wa_vars.c_min_items : '',

                visible: {
                    min: (wa_vars.chpcs_pre_responsive == 1) ? 1 : '',
                    max: (wa_vars.chpcs_pre_responsive == 1) ? wa_vars.c_min_items : '',
                }
            },
            pagination: {
                container: '#wa_chpcs_pager_' + wa_vars.id
            }
        }

        , {
            transition: (wa_vars.wa_chpcs_query_css_transitions == 1) ? true : false
        }


    );
    
    //touch swipe
    if (wa_vars.wa_chpcs_pre_direction == "up" || wa_vars.wa_chpcs_pre_direction == "down") {

        $("#wa_chpcs_foo" + wa_vars.id).swipe({
            excludedElements: "button, input, select, textarea, .noSwipe",
            swipeUp
            : function() {
                $('#wa_chpcs_foo' + wa_vars.id).trigger('next', 'auto');
            },
            swipeDown
            : function() {
                $('#wa_chpcs_foo' + wa_vars.id).trigger('prev', 'auto');
                console.log("swipeRight");
            },
            tap: function(event, target) {
                $(target).closest('.wa_chpcs_slider_title').find('a').click();
            }
        })

    } else {
        $("#wa_chpcs_foo" + wa_vars.id).swipe({
            excludedElements: "button, input, select, textarea, .noSwipe",
            swipeLeft: function() {
                $('#wa_chpcs_foo' + wa_vars.id).trigger('next', 'auto');
            },
            swipeRight: function() {
                $('#wa_chpcs_foo' + wa_vars.id).trigger('prev', 'auto');
                console.log("swipeRight");
            },
            tap: function(event, target) {
                $(target).closest('.wa_chpcs_slider_title').find('a').click();
            }
        })
    }

    //lazy loading
    if (wa_vars.wa_chpcs_query_lazy_loading) {
        function loadImage() {
            jQuery("img.wa_lazy").lazyload({
                container: jQuery("#wa_chpcs_image_carousel" + wa_vars.id)
            });
        }

    }

    //magnific popup
    if (wa_vars.wa_chpcs_query_posts_lightbox) {

        jQuery('#wa_chpcs_foo' + $id).magnificPopup({
            delegate: 'li .wa_featured_img > a', // child items selector, by clicking on it popup will open
            type: 'image'
            // other options
        });
    }

    //animation for next and prev
    if (wa_vars.wa_chpcs_query_animate_controls == 1) {
        if (wa_vars.wa_chpcs_pre_direction == "left" || wa_vars.wa_chpcs_pre_direction == "right") {

            jQuery('#wa_chpcs_image_carousel' + wa_vars.id)
                .hover(function() {
                    jQuery('#wa_chpcs_image_carousel' + wa_vars.id + ' .wa_chpcs_prev').animate({
                        'left': '1.2%',
                        'opacity': 1
                    }), 300;
                    jQuery('#wa_chpcs_image_carousel' + wa_vars.id + ' .wa_chpcs_next').animate({
                        'right': '1.2%',
                        'opacity': 1
                    }), 300;
                }, function() {
                    jQuery('#wa_chpcs_image_carousel' + wa_vars.id + ' .wa_chpcs_prev').animate({
                        'left': 0,
                        'opacity': 0
                    }), 'fast';
                    jQuery('#wa_chpcs_image_carousel' + wa_vars.id + ' .wa_chpcs_next').animate({
                        'right': 0,
                        'opacity': 0
                    }), 'fast';
                });

        }
    }

});