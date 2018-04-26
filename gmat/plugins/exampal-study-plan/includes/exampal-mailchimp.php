<?php
/* Get mailchimp api key */
function exampal_get_mc_api_key() {
    return (exampal_get_mc4wp_api_key()) ? exampal_get_mc4wp_api_key() : get_option('exampal_mailchimp_api_key');
}

/* Return api key from "MailChimp fo WP" plugin if exists. Else return false. */
function exampal_get_mc4wp_api_key(){
    if (function_exists('mc4wp_get_options')) {
        $ex_mc4wp_settings = mc4wp_get_options();
        if (!empty($ex_mc4wp_settings) && isset($ex_mc4wp_settings['api_key']) && !empty($ex_mc4wp_settings['api_key']))
            return $ex_mc4wp_settings['api_key'];
    }

    return false;
}

/* Get all mailchimp lists with API */
function exampal_get_mailchimp_lists() {
    $emc_api_key = exampal_get_mc_api_key();

    $args = array(
        'method' => 'GET',
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode( 'user:'. $emc_api_key )
        ),
        'body' => json_encode(array(
            'fields' => 'lists'
        ))
    );
    $response = wp_remote_get( 'https://' . substr($emc_api_key,strpos($emc_api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/', $args );

    $lists = json_decode( $response['body'] );

    return $lists;
}

/* Subscribe to chosen lists handle */
function exampal_mailchimp_subscribe(){
    if (isset($_POST) && isset($_POST['exampal_mc_subscibe']) && wp_verify_nonce($_POST['exampal_mc_subscibe'], 'exampal_mc_subscibe_nonce')) {
        $api_key = exampal_get_mc_api_key();
        $lists_ids = get_option('exampal_mailchimp_lists');

        $lists_arr = explode(',', $lists_ids);

        $email = $edate = '';

        if (isset($_POST['exampal-plan-email']) && !empty($_POST['exampal-plan-email'])) $email = sanitize_email($_POST['exampal-plan-email']);
        if (isset($_POST['exampal-plan-date']) && !empty($_POST['exampal-plan-date'])) $edate = $_POST['exampal-plan-date'];

        if (is_email($email)) {

            $url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/batches';

            $data = new stdClass();
            $data->operations = array();

            foreach ($lists_arr as $key => $list_id) {
                $batch =  new stdClass();
                $batch->method = 'POST';
                $batch->path = 'lists/' . $list_id . '/members';
                $batch->body = json_encode( array(
                    'email_address' => $email,
                    'status'        => 'subscribed',
                    'merge_fields'  => array(
                        'PDATE' => $edate
                    )
                ) );
                $data->operations[] = $batch;
            }

            $response = json_decode( exampal_mailchimp_curl_connect( $url, 'POST', $api_key, $data ) );

        }

    }
}
add_action ('wp',  'exampal_mailchimp_subscribe', 300);

function exampal_mailchimp_curl_connect( $url, $request_type, $api_key, $data = array() ) {
    if( $request_type == 'GET' )
        $url .= '?' . http_build_query($data);

    $mch = curl_init();
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Basic '.base64_encode( 'user:'. $api_key )
    );
    curl_setopt($mch, CURLOPT_URL, $url );
    curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
    //curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
    curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
    curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
    curl_setopt($mch, CURLOPT_TIMEOUT, 10);
    curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection

    if( $request_type != 'GET' ) {
        curl_setopt($mch, CURLOPT_POST, true);
        curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
    }

    return curl_exec($mch);
}
