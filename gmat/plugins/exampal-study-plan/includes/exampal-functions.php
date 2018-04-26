<?php

/* Remove subarrays with empty cells from multidimencional array */
function array_remove_empty($haystack){
    foreach ($haystack as $key => $value) {
        if (is_array($value)) {
            $haystack[$key] = array_remove_empty($haystack[$key]);
        }

        if (empty($haystack[$key])) {
            unset($haystack[$key]);
        }
    }

    return $haystack;
}

/* Remove empty subarrays and arrays only on the ends of arrays */
function exampal_prepare_array($haystack){
    foreach ($haystack as $key => $value) {
        if (is_array($value)) {
            if (array_has_non_empty_cells($value)) {
                $haystack[$key] = exampal_prepare_array($haystack[$key]);
            } else {
                unset($haystack[$key]);
            }
        }

        if (empty($haystack[$key])) {
            if ($key > 6) {
                unset($haystack[$key]);
            }
        }
    }

    return $haystack;
}

/* Checks if cell contains "bold" */
function exampla_contains_bold($str){
    return strpos($str, 'bold');
}

/* Checks if arrays containt non empty cells */
function array_has_non_empty_cells($haystack){
    foreach ($haystack as $key => $value) {
        if (is_array($value)) {
            $haystack[$key] = array_has_non_empty_cells($haystack[$key]);
        }

        if (!empty($haystack[$key])) {
            return true;
        }
    }
    return false;
}

/* This will replace the first half of a string with "*" characters. */
function exampal_obfuscate_string( $string ) {

    $length = strlen( $string );
    $obfuscated_length = ceil( $length / 2 );

    $string = str_repeat( '*', $obfuscated_length ) . substr( $string, $obfuscated_length );
    return $string;
}

// Check if page exists
function exampal_is_page_exists($slug) {
    global $wpdb;
    $page_found = 0;
    $page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_type='page' AND post_name = %s LIMIT 1;", $slug ) );
    return ( !empty($page_found) && ($page_found != 0));
}

// Create page for plan calendar on activation function
function exampal_create_plan_page_on_activation() {
    $p_slug = 'gmat-study-planner-calendar';
    if (!exampal_is_page_exists($p_slug)){
        $p_name = 'GMAT Study Planner';
        $p_content = '[exampal_plan_calendar]';

        $page_data = array(
            'post_status'       => 'publish',
            'post_type'         => 'page',
            'post_author'       => 1,
            'post_name'         => $p_slug,
            'post_title'        => $p_name,
            'post_content'      => $p_content,
            'post_parent'       => 0,
            'comment_status'    => 'closed'
        );
        $page_for_plan_id = wp_insert_post( $page_data );

        if ($page_for_plan_id) update_option('exampal_page_for_plan', $page_for_plan_id);
    }
}

// Add body class to page for calendar
function exampal_body_classes( $classes ) {

    if ( get_option('exampal_page_for_plan') == get_the_ID() ) {
        $classes[] = 'exampal-calendar-plan-page';
    }

    return $classes;
}
add_filter( 'body_class', 'exampal_body_classes' );





