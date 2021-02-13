<?php 
namespace Element_Ready_Pro\License\service;
use Elementor\Core\Common\Modules\Connect\Admin;
if ( ! defined( 'ABSPATH' ) ) exit;


abstract class Base {

	const OPTION_NAME_PREFIX = 'element_ready_connect_';

	const SITE_URL = 'https://elementsready.com';

    const API_URL = 'https://elementsready.com/wp-json/element-ready-pro/v1';

    protected $data = [];

    protected $auth_mode = '';
    
    abstract protected function get_slug();
    
    public function get_title() {
		return $this->get_slug();
    }
    
    public static function get_class_name() {
		return get_called_class();
    }

     /**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function get_option_name() {
		return static::OPTION_NAME_PREFIX . $this->get_slug();
    }

    /**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function init() {}

	/**
	 * @since 1.0.0
	 * @access protected
	 */
    protected function init_data() {}
    /**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function get_site_key() {

		$site_key = get_option( 'element_ready_connect_site_key' );

		if ( ! $site_key ) {
			$site_key = md5( uniqid( wp_generate_password() ) );
			update_option( 'element_ready_connect_site_key', $site_key );
		}

		return $site_key;
    }
    public function is_connected() {
		return (bool) $this->get( 'access_token' );
	}
    /**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function request( $action, $request_body = [], $as_array = false ) {

		$request_body = [
			'app'          => $this->get_slug(),
			'access_token' => $this->get( 'access_token' ),
			'client_id'    => $this->get( 'client_id' ),
			'local_id'     => get_current_user_id(),
			'site_key'     => $this->get_site_key(),
			'home_url'     => trailingslashit( home_url() ),
		] + $request_body;

		$headers = [];

		if ( $this->is_connected() ) {
			$headers['X-Element-Ready-Pro-Signature'] = hash_hmac( 'sha256', wp_json_encode( $request_body, JSON_NUMERIC_CHECK ), $this->get( 'access_token_secret' ) );
		}

		$response = wp_remote_post( $this->get_api_url() . '/' . $action, [
			'body' => $request_body,
			'headers' => $headers,
			'timeout' => 25,
		] );

		if ( is_wp_error( $response ) ) {
			wp_die( $response, [
				'back_link' => true,
			] );
		}

        $body = wp_remote_retrieve_body( $response );
        
		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( ! $response_code ) {
			return new \WP_Error( 500, 'No Response' );
		}

		// Server sent a success message without content.
		if ( 'null' === $body ) {
			$body = true;
		}

		$body = json_decode( $body, $as_array );

		if ( false === $body ) {
			return new \WP_Error( 422, 'Wrong Server Response' );
		}

		if ( 200 !== $response_code ) {
			// In case $as_array = true.
			$body = (object) $body;

			$message = isset( $body->message ) ? $body->message : wp_remote_retrieve_response_message( $response );
			$code = isset( $body->code ) ? $body->code : $response_code;

			if ( 401 === $code ) {
				$this->delete();
				$this->action_authorize();
			}

			return new \WP_Error( $code, $message );
		}

		return $body;
    }

    protected function set_client_id() {

		if ( $this->get( 'client_id' ) ) {
			return;
		}

		$response = $this->request( 'get_client_id' );

		if ( is_wp_error( $response ) ) {
			wp_die( $response, $response->get_error_message() );
		}

		$this->set( 'client_id', $response->client_id );
		$this->set( 'auth_secret', $response->auth_secret );
    }
    
    protected function set_request_state() {
		$this->set( 'state', wp_generate_password( 12, false ) );
	}
    
    public function action_authorize() {

		if ( $this->is_connected() ) {
			//$this->add_notice( __( 'Already connected.', 'element-ready-pro' ), 'info' );
			$this->redirect_to_admin_page();
			return;
		}

		$this->set_client_id();
		$this->set_request_state();

		$this->redirect_to_remote_authorize_url();
	}

	/**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function get_api_url() {
		return static::API_URL . '/' . $this->get_slug();
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_remote_site_url() {
		return static::SITE_URL . '/' . $this->get_slug();
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_remote_authorize_url() {
		$redirect_uri = $this->get_auth_redirect_uri();

		$url = add_query_arg( [
			'action'          => 'authorize',
			'response_type'   => 'code',
			'client_id'       => $this->get( 'client_id' ),
			'auth_secret'     => $this->get( 'auth_secret' ),
			'state'           => $this->get( 'state' ),
			'redirect_uri'    => rawurlencode( $redirect_uri ),
			'may_share_data'  => current_user_can( 'manage_options' ),
			'reconnect_nonce' => wp_create_nonce( $this->get_slug() . 'reconnect' ),
		], $this->get_remote_site_url() );

		return $url;
    }
    
    /**
	 * @since 1.0.0
	 * @access public
	 */
	public function get( $key, $default = null ) {
		$this->init_data();

		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : $default;
	}

	/**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function set( $key, $value = null ) {
		$this->init_data();

		if ( is_array( $key ) ) {
			$this->data = array_replace_recursive( $this->data, $key );
		} else {
			$this->data[ $key ] = $value;
		}

		$this->update_settings();
	}
    abstract protected function update_settings();
	/**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function delete( $key = null ) {
		$this->init_data();

		if ( $key ) {
			unset( $this->data[ $key ] );
		} else {
			$this->data = [];
		}

		$this->update_settings();
	}

	/**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function add( $key, $value, $default = '' ) {
		$new_value = $this->get( $key, $default );

		if ( is_array( $new_value ) ) {
			$new_value[] = $value;
		} elseif ( is_string( $new_value ) ) {
			$new_value .= $value;
		} elseif ( is_numeric( $new_value ) ) {
			$new_value += $value;
		}

		$this->set( $key, $new_value );
    }
    
    protected function redirect_to_remote_authorize_url() {
		
        wp_redirect( $this->get_remote_authorize_url() );
        die;
		
    }
    
	protected function redirect_to_admin_page( $url = '' ) {

		if ( ! $url ) {
			$url = Admin::$url;
		}
        wp_safe_redirect( $url );
		
	}
}