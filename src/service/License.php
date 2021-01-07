<?php 
namespace Element_Ready_Pro\License\service;
if ( ! defined( 'ABSPATH' ) ) exit;

Class License
{
    private static $obj;
    private final function __construct()
    {
        
      add_action( 'rest_api_init', [$this,'license_key'] );
    }
    public static function getInit() {

        if(!isset(self::$obj)) {
            self::$obj = new License();
        }
        return self::$obj;
    }
  
    protected function license_key(){

        register_rest_route( 'element-ready-pro/v1', '/varify', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [$this,'generate_service'],
        ) );

    }

    protected function generate_service(){
        return rest_ensure_response( 'Hello World, this is the WordPress REST API' );
    }
}

