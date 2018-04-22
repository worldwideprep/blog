<?php defined( 'ABSPATH' ) or die();

class intercom_wp_api {

	var $api_key;
	var $app_id;
	var $settings;

	const API_URL = 'https://api.intercom.io/';

	function set_auth(){
		if( !$this->settings )
			$this->settings = intercom_wp::get_settings();

		if( ! $this->app_id = $this->settings[ 'app-id' ] ) return;
		if( ! $this->api_key = $this->settings[ 'api-key' ] ) return;
	}

	function do_call( $endpoint = 'users/', $data = array() ){

		// Set the Auth
		$this->set_auth();

		/**
		* DEBUG
		*

		echo "DO CALL";
		echo '<br />' . $this->app_id;
		echo '<br />' . $this->api_key;
		*/


		if( !$this->app_id or !$this->api_key ) return;

		$headers = array(
			'Authorization' => 'Basic ' . base64_encode( "$this->app_id:$this->api_key" ),
			'Accept' => 'application/json',
			'Content-Type' => 'application/json'
		);

		/**
		* DEBUG
		echo '<pre>';
		echo    . ( ( !empty( $data ) ? '?' . http_build_query( $data ) : '' ) ) ;
		echo '</pre>';

		echo '<pre>';
		print_r( $headers );
		echo '</pre>';
		*/

		$post_result = wp_remote_post(
			self::API_URL . $endpoint,
			array(
				'headers' => $headers,
				'body' => json_encode( $data )
			)
		);

		/**
		* DEBUG
		echo '<pre>';
		print_r( $post_result );
		echo '</pre>';
		*/

		$body = wp_remote_retrieve_body( $post_result );
		$response_code = wp_remote_retrieve_response_code( $post_result );

		if( !in_array( $response_code , array( 200, 202 ) ) ) {
			$return = array(
				'success' => FALSE,
				'response_code' => $response_code,
				'message' => json_decode( $body )
			);
		} else {
			$return = array(
				'success' => TRUE,
				'response_code' => $response_code,
				'message' => json_decode( $body )
			);
		}

		$this->log( $return );

		return $return;

	}

	function createUser( $email_or_user_id = NULL, $name = NULL, $created_at = NULL, $custom_attributes = NULL, $location_data = NULL){
		global $current_user;

		/* EXAMPLE
		$api = new intercom_wp_api;
		$api->createUser( 'me@me.com' OR 12345 ); */

		// Do nothing without an event name
		if( NULL == $email_or_user_id ) return;

		if( NULL !== $custom_attributes )
			$data[ "custom_attributes" ] = $custom_attributes ;

		if( NULL !== $created_at )
			$data[ "created_at" ] = $created_at;

		if( NULL !== $name )
			$data[ "name" ] = $name;

		if( NULL !== $location_data )
			$data[ "location_data" ] = array(
				"type" => "location_data",
				"city_name" => $location_data[ 'city' ],
				"country_code" => $location_data[ 'country' ]
			);

		$data[ "last_request_at" ] = current_time( 'timestamp' );

		if( is_numeric( $email_or_user_id ) ){
			$data[ "user_id" ] = (float) $email_or_user_id;
		} else {
			$data[ "email" ] = (string) $email_or_user_id;
		}

		return $this->do_call( 'users/', $data );
	}

	function createContact( $email_or_user_id = NULL, $name = NULL, $created_at = NULL ){
		global $current_user;

		/* EXAMPLE
		$api = new intercom_wp_api;
		$api->createContact( 'me@me.com' OR 12345 ); */

		// Do nothing without an event name
		if( NULL == $email_or_user_id ) return;

		if( NULL !== $created_at )
			$data[ "created_at" ] = $created_at;

		if( NULL !== $name )
			$data[ "name" ] = $name;

		$data[ "last_request_at" ] = current_time( 'timestamp' );

		if( is_numeric( $email_or_user_id ) ){
			$data[ "user_id" ] = (float) $email_or_user_id;
		} else {
			$data[ "email" ] = (string) $email_or_user_id;
		}

		return $this->do_call( 'contacts/', $data );
	}

	function createEvent( $event_name = NULL, $metadata = NULL, $email = NULL, $user_id = NULL ){
		global $current_user;

		/* EXAMPLE
		$api = new intercom_wp_api;
		$api->createEvent( 'do something else', array(
			"Test" => 1,
			"Test 2" => 2,
		)); */

		// Do nothing without an event name
		if( NULL == $event_name ) return;

		$data = array(
			"event_name" => (string) $event_name,
			"created_at" => isset( $metadata[ 'created_at' ] ) ? $metadata[ 'created_at' ] : current_time( 'timestamp' ),
			"email" => (string) ( NULL == $email ? $current_user->user_email : $email ),
		);

		if( NULL != $user_id ){
			$data[ "user_id" ] = (float) $user_id;
		}

		if( NULL != $metadata ){
			if( isset( $metadata[ 'created_at' ] ) ) unset( $metadata[ 'created_at' ] );

			$data[ "metadata" ] = apply_filters( 'intercom_event_metadata_' . sanitize_title( $event_name ) , $metadata );
		}

		return $this->do_call( 'events/', $data );
	}

	private function log( $log )  {

		if( !function_exists( 'error_log' ) ) return;

		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				$log = print_r( $log, true );
			}

			error_log( $log );
		}
	}


}