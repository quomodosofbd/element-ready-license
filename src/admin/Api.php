<?php 
namespace Element_Ready_Pro\License\admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class API {

    const PRODUCT_NAME = 'Element Ready Pro';

	const STORE_URL = 'https://elementsready.com/wp-json/rest/v3/license';
	const RENEW_URL = 'https://elementsready.com';

	// License Statuses
	const STATUS_VALID         = 'valid';
	const STATUS_INVALID       = 'invalid';
	const STATUS_EXPIRED       = 'expired';
	const STATUS_DEACTIVATED   = 'deactivated';
	const STATUS_SITE_INACTIVE = 'site_inactive';
	const STATUS_DISABLED      = 'disabled';
    
    /**
	 * @param array $body_args
	 *
	 * @return \stdClass|\WP_Error
	 */
	private static function remote_post( $body_args = [] ) {

		$body_args = wp_parse_args(
			$body_args,
			[
				'api_version' => ELEMENT_READY_PRO_VERSION,
				'item_name'   => self::PRODUCT_NAME,
				'site_lang'   => get_bloginfo( 'language' ),
				'url'         => home_url(),
				'domain'      => parse_url(home_url(), PHP_URL_HOST),
			]
		);

		$response = wp_remote_post( self::STORE_URL, [
			'timeout' => 40,
			'body' => $body_args,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

        $response_code = wp_remote_retrieve_response_code( $response );
        
		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, __( 'HTTP Error', 'element-ready-pro' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', __( 'An error occurred, please try again', 'element-ready-pro' ) );
		}

		return $data;
    }
    
    public static function activate_license( $license_key ) {

		$body_args = [
			'_action' => 'activate_license',
			'license' => $license_key,
		];

		$license_data = self::remote_post( $body_args );

		return $license_data;
	}

	public static function deactivate_license() {

		$body_args = [
			'_action' => 'deactivate_license',
			'license' => Admin::get_license_key(),
		];

		$license_data = self::remote_post( $body_args );

		return $license_data;
    }
    
    
	public static function set_license_data( $license_data, $expiration = null ) {

		if ( null === $expiration ) {
			$expiration = 12 * HOUR_IN_SECONDS;
		}
		//remove below line in production
		$expiration = 1 * MINUTE_IN_SECONDS;
		set_transient( 'element_ready_pro_license_data', $license_data, $expiration );
    }

	public static function set_connect_data( $data ) {

        $service  = [];
		if(isset($data['code'])){
		  $service['code'] = $data['code'];	
		}else{
			$service['code'] = 201;
		}

		if(isset($data['msg'])){
			$service['msg'] = $data['msg'];	
		  }else{
			  $service['msg'] = 'Invalid';
		  }
		  
		  if(isset($data['license'])){
			$service['license'] = $data['license'];	
		  }else{
			  $service['license'] = 'Invalid';
		  }

		  $service['domain'] = $_SERVER['HTTP_HOST'];

     	update_option( 'element_ready_pro_connect_data', $service );
    }
    
    public static function get_license_data( $force_request = false ) {

		$license_data = get_transient( 'element_ready_pro_license_data' );
	
		if ( false === $license_data || $force_request ) {
			$body_args = [
				'_action' => 'check_license',
				'license' => Admin::get_license_key(),
			];

			$license_data = self::remote_post( $body_args );

			if ( is_wp_error( $license_data ) ) {
				$license_data = [
					'license'          => 'http_error',
					'payment_id'       => '0',
					'license_limit'    => '0',
					'site_count'       => '0',
					'activations_left' => '0',
				];

				self::set_license_data( $license_data, 30 * MINUTE_IN_SECONDS );
			} else {
				self::set_license_data( $license_data );
			}
		}

		return $license_data;
    }
    
    public static function get_errors() {

		return [
			'no_activations_left' => sprintf( __( '<strong>You have no more activations left.</strong> <a href="%s" target="_blank">Please upgrade to a more advanced license</a> (you\'ll only need to cover the difference).', 'element-ready-pro' ), 'https://plugins.quomodosoft.com/element-ready/upgrade' ),
			'expired'             => sprintf( __( '<strong>Your License Has Expired.</strong> <a href="%s" target="_blank">Renew your license today</a> to keep getting feature updates, premium support and unlimited access to the template library.', 'element-ready-pro' ), 'https://plugins.quomodosoft.com/element-ready/renew' ),
			'missing'             => __( 'Your license is missing. Please check your key again.', 'element-ready-pro' ),
			'revoked'             => __( '<strong>Your license key has been cancelled</strong> (most likely due to a refund request). Please consider acquiring a new license.', 'element-ready-pro' ),
			'key_mismatch'        => __( 'Your license is invalid for this domain. Please check your key again.', 'element-ready-pro' ),
		];

	}

	public static function get_error_message( $error ) {
		$errors = self::get_errors();

		if ( isset( $errors[ $error ] ) ) {
			$error_msg = $errors[ $error ];
		} else {
			$error_msg = __( 'An error occurred. Please check your internet connection and try again. If the problem persists, contact our support.', 'element-ready-pro' ) . ' (' . $error . ')';
		}

		return $error_msg;
	}
}