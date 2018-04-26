/**
 * Google tag manager events
 */

jQuery(function($) {

    // dataLayer.push({'event': 'exampal-events', 'eventInfo': {'category':'someCategory', 'action': 'someAction', 'label': 'someLabel', 'value': 0}});

    // Sign up to blog
    // Submit and download
    if (typeof mc4wp !== "undefined") {
        mc4wp.forms.on('success', function (form) {

            if ($(form.element).find('input.submit').hasClass('submit-download')) {
                console.log('Submit & download');
                // Submit and download
                dataLayer.push({
                    'event': 'exampal-events',
                    'eventInfo': {'category': 'Blog', 'action': 'Submit & download', 'label': 'Download.pdf', 'value': 0}
                });
            } else {
                console.log('Sign up');
                // Sign up to blog
                dataLayer.push({
                    'event': 'exampal-events',
                    'eventInfo': {'category': 'Blog', 'action': 'Sign up', 'label': 'Sign up to blog', 'value': 0}
                });
            }
        });
    }

    // Click to read a blog post
    $('body.home').on('click', '.home_featured_post .read_more, .infinite-item  a', function(e){
        var urlDest = $(this).attr('href');

        dataLayer.push({
            'event': 'exampal-events',
            'eventInfo': {'category': 'Blog', 'action': 'Read', 'label': 'read post '+urlDest, 'value': 0}
        });

        return true;
    });

    // Click on 'start your GMAT course' button
    $('.sign_up_menu.isgin').on('click', function(e){
        dataLayer.push({
            'event': 'exampal-events',
            'eventInfo': {'category': 'Blog', 'action': 'Buttons', 'label': 'Click on start your GMAT course', 'value': 0}
        });

        return true;
    });

    // Click to read a related blog post
    $('.related li a').on('click', function(e){
        dataLayer.push({
            'event': 'exampal-events',
            'eventInfo': {'category': 'Blog', 'action': 'Read', 'label': 'Click to read a related blog post', 'value': 0}
        });

        return true;
    });

    // Click on social buttons
    $('.right_icons_follow a, .foot_icons a').on('click', function(e){
        var socialNetwork = '';

        if ($(this).find('i.fa').hasClass('fa-twitter'))
            socialNetwork = 'Twitter';
        else if ($(this).find('i.fa').hasClass('fa-facebook'))
            socialNetwork = 'Facebook';
        else if ($(this).find('i.fa').hasClass('fa-google-plus'))
            socialNetwork = 'Google+';
        else if ($(this).find('i.fa').hasClass('fa-linkedin'))
            socialNetwork = 'LinkedIn';
        else if ($(this).find('i.fa').hasClass('fa-youtube'))
            socialNetwork = 'Youtube';

        dataLayer.push({
            'event': 'exampal-events',
            'eventInfo': {'category': 'Blog', 'action': 'Click on social buttons', 'label': socialNetwork, 'value': 0}
        });

        return true;
    });


    // Click to share a post
    $('body').on('click', '.shareaholic-share-buttons .shareaholic-share-button', function(e){
        var socialNetwork = $(this).data('service');

        if (socialNetwork == 'twitter') socialNetwork = 'Twitter';
        if (socialNetwork == 'facebook') socialNetwork = 'Facebook';
        if (socialNetwork == 'google_plus') socialNetwork = 'Google+';
        if (socialNetwork == 'linkedin') socialNetwork = 'LinkedIn';
        if (socialNetwork == 'email_this') socialNetwork = 'Email';

        dataLayer.push({
            'event': 'exampal-events',
            'eventInfo': {'category': 'Blog', 'action': 'Click to share a post', 'label': socialNetwork, 'value': 0}
        });

        return true;
    });
    $('body').on('click', '.addtoany_list a', function(e){
        var socialNetwork = $(this).attr('title');

        dataLayer.push({
            'event': 'exampal-events',
            'eventInfo': {'category': 'Blog', 'action': 'Click to share a post', 'label': socialNetwork, 'value': 0}
        });

        return true;
    });

    // Post a comment
    $('#commentform').on('submit', function(){
        dataLayer.push({
            'event': 'exampal-events',
            'eventInfo': {'category': 'Blog', 'action': 'Comment', 'label': 'Leave a comment', 'value': 0}
        });

        return true;
    });

    // Open chat window
    if (typeof Intercom !== "undefined") {
        Intercom('onShow', function () {
            dataLayer.push({
                'event': 'exampal-events',
                'eventInfo': {'category': 'Blog', 'action': 'Open chat', 'label': '-', 'value': 0}
            });
        });
    }

    // Send contact us form
    if (typeof Tawk_API !== "undefined") {
        Tawk_API = Tawk_API || {};
        Tawk_API.onOfflineSubmit = function(){
            dataLayer.push({
                'event': 'exampal-events',
                'eventInfo': {'category': 'Blog', 'action': 'Send contact us form', 'label': window.location.href, 'value': 0}
            });
        };
    }


    // Refresh the post pagination
    if (typeof Waypoint !== "undefined") {
        if (typeof Waypoint.Infinite !== "undefined") {
            var sendGTMEvent = function () {
                dataLayer.push({
                    'event': 'exampal-events',
                    'eventInfo': {'category': 'Blog', 'action': 'Read', 'label': 'Refresh the post pagination', 'value': 0}
                });
            };
            Waypoint.Infinite.defaults.onBeforePageLoad = sendGTMEvent;
        }
    }

    // Click on all posts button
    $('.related a.button.show_more').on('click', function(e){
        dataLayer.push({
            'event': 'exampal-events',
            'eventInfo': {'category': 'Blog', 'action': 'Read', 'label': 'Click on all posts button', 'value': 0}
        });

        return true;
    });

    // Click on banners to go to Exampal website
    $('#desktopbanner a, a.banner_img').on('click', function(e){
        var URL = $(this).attr('href');

        // Check if it lead on the main exampal.com site
        if ( (URL.indexOf('exampal.com') != -1) && (URL.indexOf('gmat/blog') == -1) ) {
            dataLayer.push({
                'event': 'exampal-events',
                'eventInfo': {'category': 'Blog', 'action': 'Buttons', 'label': 'Click on banners to go to Exampal website', 'value': 0}
            });
        }

        return true;
    });

    // Sorting posts
    $('body').on('click', '.tab_open', function(e){
        dataLayer.push({
            'event': 'exampal-events',
            'eventInfo': {'category': 'Blog', 'action': 'Sorting', 'label': 'Sorting posts', 'value': 0}
        });

        return true;
    });


});