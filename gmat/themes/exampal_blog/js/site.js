jQuery(document).ready(function ($) {
    jQuery('ol').each(function () {
        var starto = jQuery(this).attr('start');
        var starto2 = parseInt(starto) - 1;
        if (starto != '' && typeof starto != 'undefined') jQuery(this).find('li').attr('style', 'counter-reset: my-badass-counter ' + starto2 + ';');
    });
    var headertext = [];
    var headers = document.querySelectorAll("thead");
    var tablebody = document.querySelectorAll("tbody");

    for (var i = 0; i < headers.length; i++) {
        headertext[i] = [];
        for (var j = 0, headrow; headrow = headers[i].rows[0].cells[j]; j++) {
            var current = headrow;
            headertext[i].push(current.textContent);
        }
    }

    for (var h = 0, tbody; tbody = tablebody[h]; h++) {
        for (var i = 0, row; row = tbody.rows[i]; i++) {
            for (var j = 0, col; col = row.cells[j]; j++) {
                if (typeof headertext[h] != 'undefined')
                    col.setAttribute("data-th", headertext[h][j]);
            }
        }
    }
    jQuery(window).load(function () {
        if (window.location.hash) {
            jQuery('.tab_head a[href="' + window.location.hash + '"]').click();
        }

    });

    var lar = jQuery('.login-header').first().html();
    var lar_last = jQuery('.login-header').last().html();
    jQuery('#responsive-menu .menu-main-menu-container ul').append('<li class="login_area resp_login">' + lar + '</li>');
    if (lar != lar_last) jQuery('#responsive-menu .menu-main-menu-container ul').append('<li class="login_area resp_login resp_login_last">' + lar_last + '</li>');


    cards = jQuery('.crp_related ul');
    jQuery.each(cards, function (key, value) {
        jQuery(this).find('li').slice(3).hide();
    });
    jQuery('.show_more').show();

    jQuery('.show_more').live('click', function (e) {
        jQuery(this).parent().parent().find(":hidden").show();
        jQuery(this).fadeOut('slow');
    });

    jQuery('#comments input#submit').val('Post');
    jQuery('.c_logged input#submit').attr('disabled', 'disabled');
    jQuery('.c_not_logged input#submit').attr('disabled', 'disabled');
    jQuery('#comments textarea').attr('placeholder', 'Add a comment...');
    jQuery('#comments input#author').attr('placeholder', 'Your Name');
    jQuery('#comments input#email').attr('placeholder', 'Your Email');

    jQuery('.c_logged textarea').on('keyup', function () {
        var textarea_value = jQuery(".c_logged textarea").val();
        if (textarea_value != '') {
            jQuery('.c_logged input#submit').attr('disabled', false);
        } else {
            jQuery('.c_logged input#submit').attr('disabled', true);
        }
    });
    jQuery('.c_not_logged textarea,#comments input#author,#comments input#email').on('keyup', function () {
        var t_v = jQuery(".c_not_logged textarea").val();
        var a_v = jQuery("#comments input#author").val();
        var e_v = jQuery("#comments input#email").val();
        if (t_v != '' && a_v != '' && e_v != '') {
            jQuery('.c_not_logged input#submit').attr('disabled', false);
        } else {
            jQuery('.c_not_logged input#submit').attr('disabled', true);
        }
    });


    jQuery('.c_toggle').live('click', function (e) {
        if (jQuery(this).attr('data-closed') == 'closed') {
            jQuery(this).attr('data-closed', '');
        } else {
            jQuery(this).attr('data-closed', 'closed');
        }
        jQuery(this).toggleClass('c_close');
        jQuery('.comment-list').slideToggle('fast');
        jQuery('.comment-list').toggleClass('c_open');
        Cookies.set('c_toggle', jQuery(this).attr('data-closed'));

    });


    var page = 1;
    var ajaxurl = ajax_script.ajaxurl;
    jQuery('.tab_open').live('click', function (e) {
        e.preventDefault;
        var data_type = jQuery(this).data('type');
        var cat = jQuery(this).data('cat');
        var secur = jQuery('.secur').val();
        jQuery('.tab_open').removeClass('active');
        jQuery(this).addClass('active');

        jQuery.post(ajaxurl, {action: 'cbloader', type: data_type, cat: cat, security: secur}, function (data) {
            jQuery('.tab_content ol').html(data);
            infinite.destroy();
            infinite = new Waypoint.Infinite({
                element: jQuery('.tab_content ol')[0]
            });
        });
    });


    if (jQuery(window).scrollTop() + jQuery(window).height() > jQuery(document).height() - 100) {
        jQuery('.scroll_toggle').removeClass('is_top').addClass('is_bottom');
    } else jQuery('.scroll_toggle').removeClass('is_bottom').addClass('is_top');

    jQuery('.scroll_toggle.is_top').live('click', function (e) {
        e.preventDefault;
        jQuery("html, body").animate({scrollTop: jQuery(document).height()}, 'fast');
        return false;
    });
    jQuery('.scroll_toggle.is_bottom').live('click', function (e) {
        e.preventDefault;
        jQuery("html, body").animate({scrollTop: 0}, 'fast');
        return false;
    });


    jQuery(window).scroll(function () {
        if (jQuery(window).scrollTop() + jQuery(window).height() > jQuery(document).height() - 100) {
            jQuery('.scroll_toggle').removeClass('is_top').addClass('is_bottom');
        } else jQuery('.scroll_toggle').removeClass('is_bottom').addClass('is_top');
    });


    $('#banner-nav a').click(function () {
        $('html,body').animate({scrollTop: $(this).offset().top}, 500);
    });

    $("#close").click(function () {
        $("#search-container").slideUp();
    });


});

// Global ajax var;
var xhrOrderFields;

jQuery(function ($) {
    $(document).ready(function () {

        // Submit and download
        if (typeof mc4wp !== "undefined") {
            mc4wp.forms.on('submitted', function (form) {
                if ($(form.element).find('input.submit').hasClass('submit-download')) {
                    var fileUrl = $(form.element).find('input.download-file').val();
                    if (fileUrl) {
                        window.open(fileUrl)
                    }
                }
            });
        }

    });
    
   $('.sign--up-form').on('submit', function(e){
       e.preventDefault();
        
        if((xhrOrderFields == null) || (xhrOrderFields.readyState == 4)) {

            messageObj = $('.message');
            messageObj.hide().removeClass('success error');

            var phone = ($(this).find('input[name="sign-phone"]').length > 0) ? $(this).find('input[name="sign-phone"]').val() : '';

            var data = {action: 'exampal_sign_up_form',
                security: ajax_script.ofs_ajax_nonce_sign_up, 
                email: $(this).find('input[name="sign-email"]').val(),
                pass:$(this).find('input[name="sign-password"]').val(),
                phone: phone,
            };

           $('.sign--up-request').show();
            xhrOrderFields = $.post(ajax_script.ajaxurl, data,  function(result) {

                try {
                    var obj = JSON.parse(result);

                    if(obj.message){

                        messageObj.html(obj.message).addClass('success');
                        $('.sing--up-form').trigger('reset');
                        setTimeout(function(){
                            messageObj.hide();
                        }, 2000);

                    } else if(obj.error) {

                        messageObj.addClass('error').html(obj.error.email);

                    }else{

                        messageObj.addClass('error').html('Something went wrong. Please try again later.');
                        setTimeout(function(){
                            messageObj.hide();
                        }, 4000);
                    }

                } catch(e) {
                    messageObj.addClass('error').html("Something went wrong. E-mail may have already used.");
                    setTimeout(function(){
                        messageObj.hide();
                    }, 4000);
                }

                $('.sign--up-request').hide();
                messageObj.show();
                           
            });
        }
   });
});
