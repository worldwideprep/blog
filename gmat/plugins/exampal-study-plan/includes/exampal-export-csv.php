<?php 

add_action( 'wp_ajax_study_plan_csv', 'study_plan_csv' );
add_action('wp_ajax_nopriv_study_plan_csv', 'study_plan_csv');

function study_plan_csv() {

    check_ajax_referer('exampal-export-csv-nonce', 'security' );

    if(empty($_POST['exampal-plan-date'])){
        $_POST['exampal-plan-date'] = date("F j, Y");        
    }
    if(empty($_POST['plan'])){
        $_POST['plan'] = 1;
    }
    $calendar = new Calendar($_POST['exampal-plan-date'], get_option('exampal_plan_spreadsheet_array'), $_POST['plan']);


   
    
    if (!file_exists(wp_upload_dir()['basedir'] . '/my-study-plan/')) {
        mkdir(wp_upload_dir()['basedir'] . '/my-study-plan/', 0777, true);
    }
    $filename = '/my-study-plan/exampal-study-plan-' . time() . '.csv';

    $file = fopen(wp_upload_dir()['basedir'] . $filename, 'w');

    foreach ($calendar->getArray() as $fields) {          
          fputcsv($file, $fields, ";");              
    }

    fclose($file);

     if(file_exists(wp_upload_dir()['basedir'] . $filename)){

        echo wp_upload_dir()['baseurl'] . $filename;

    }else{
        echo '/';
    }

    wp_die(); 
}
?>