<?php 
namespace Element_Ready_Pro\License\admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin {

    const PAGE_ID = 'element-ready-pro-license';

    public static function get_license_key() {
		return trim( get_option( 'element_ready_pro_license_key' ) );
	}

	public static function set_license_key( $license_key ) {
		return update_option( 'element_rady_pro_license_key', $license_key );
    }
    
    public static function deactivate() {

		API::deactivate_license();
		delete_option( 'element_ready_pro_license_key' );
		delete_transient( 'element_ready_pro_license_data' );
    }

    public static function get_url() {
		return admin_url( 'admin.php?page=' . self::PAGE_ID );
    }
    
    public static function get_errors_details() {
		$license_page_link = self::get_url();

		return [
			API::STATUS_EXPIRED => [
				'title'       => esc_html__( 'Your License Has Expired', 'element-ready-pro' ),
				'description' => sprintf( esc_html__( '<a href="%s" target="_blank">Renew your license today</a>, to keep getting feature updates, premium support and unlimited access to the template library.', 'element-ready-pro' ), API::RENEW_URL ),
				'button_text' => esc_html__( 'Renew License', 'element-ready-pro' ),
				'button_url'  => API::RENEW_URL,
			],
			API::STATUS_DISABLED => [
				'title'       => esc_html__( 'Your License Is Inactive', 'element-ready-pro' ),
				'description' => esc_html__( '<strong>Your license key has been cancelled</strong> (most likely due to a refund request). Please consider acquiring a new license.', 'element-ready-pro' ),
				'button_text' => esc_html__( 'Activate License', 'element-ready-pro' ),
				'button_url'  => $license_page_link,
			],
			API::STATUS_INVALID => [
				'title'       => esc_html__( 'License Invalid', 'element-ready-pro' ),
				'description' => esc_html__( '<strong>Your license key doesn\'t match your current domain</strong>. This is most likely due to a change in the domain URL of your site (including HTTPS/SSL migration). Please deactivate the license and then reactivate it again.', 'element-ready-pro' ),
				'button_text' => esc_html__( 'Reactivate License', 'element-ready-pro' ),
				'button_url'  => $license_page_link,
			],
			API::STATUS_SITE_INACTIVE => [
				'title'       => esc_html__( 'License Mismatch', 'element-ready-pro' ),
				'description' => esc_html__( '<strong>Your license key doesn\'t match your current domain</strong>. This is most likely due to a change in the domain URL. Please deactivate the license and then reactivate it again.', 'element-ready-pro' ),
				'button_text' => esc_html__( 'Reactivate License', 'element-ready-pro' ),
				'button_url'  => $license_page_link,
			],
		];
    }

    public function action_activate_license() {

         
		if ( empty( $_POST['elementor_pro_license_key'] ) ) {

		 	throw new Exception( esc_html__( 'Please enter your license key.', 'element-ready-pro' ), esc_html__( 'Element Ready Pro', 'element-ready-pro' ), [
				'back_link' => true,
			] );
		}

		$license_key = trim( $_POST['element_ready_pro_license_key'] );

		$data = API::activate_license( $license_key );

		if ( is_wp_error( $data ) ) {

			wp_die( sprintf( '%s (%s) ', $data->get_error_message(), $data->get_error_code() ), esc_html__( 'Elementor Ready Pro', 'element-ready-pro' ), [
				'back_link' => true,
            ] );
            
		}

		if ( API::STATUS_VALID !== $data['license'] ) {
			$error_msg = API::get_error_message( $data['error'] );
			wp_die( $error_msg, esc_html__( 'Elementor Ready Pro', 'element-ready-pro' ), [
				'back_link' => true,
			] );
		}

		self::set_license_key( $license_key );
		API::set_license_data( $data );

		wp_safe_redirect( $_POST['_wp_http_referer'] );
		wp_die();
    }

    public function action_deactivate_license() {

		check_admin_referer( 'element-ready-pro-license' );

		$this->deactivate();

		wp_safe_redirect( $_POST['_wp_http_referer'] );
		wp_die();
	}
}