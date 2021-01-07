<?php 
namespace Element_Ready_Pro\License\service;
use WP_REST_Request;
if ( ! defined( 'ABSPATH' ) ) exit;

Class License
{
    private static $obj;
    private final function __construct()
    {
      add_action('admin_menu',[$this,'register_page'],15);
      add_action( 'rest_api_init', [$this,'license_key'] );
    }
    public function register_page(){
        $page = new Element_Ready_Pro_Pages();
        $page->add_page();
    }
    public static function getInit() {

        if(!isset(self::$obj)) {
            self::$obj = new License();
        }
        return self::$obj;
    }
  
    public function license_key(){

        register_rest_route( 'element-ready-pro/v1', '/varify', array(
            'methods'  => 'GET',
            'callback' => [$this,'get_key'],
        ) );

    }

    public function get_key( WP_REST_Request $request ){

        $domain = $request->get_param( 'domain' );
        
        $l_key = element_ready_get_api_option('license_key');
        if($l_key == '' || $l_key == false ){
            return false;
        }
        
        return \rest_ensure_response( "Hello World, this is the WordPress REST API {$l_key}" );
    }
}

