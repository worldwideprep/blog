/**
 * Intercom Pro for WordPress
 * Admin JS
 *
 * @package Intercom Pro
 * @since 1.0
 */

jQuery( function($){
    $(document).on( 'click', 'a#clear', function(){

        $permission = confirm( 'Are you sure you want to clear all your settings? Once you clear them you will not be able to bring them back.' );

        if( !$permission ){
            return false;
        } else {
            return true;
        }

    });
});