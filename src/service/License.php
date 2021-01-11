<?php 
namespace Element_Ready_Pro\License\service;
use WP_REST_Request;
if ( ! defined( 'ABSPATH' ) ) exit;

Class License
{
    private static $obj;
    private $page; 
    private $connect; 
    private final function __construct()
    {

      $this->page = new Element_Ready_Pro_Pages(); 
      $this->connect =  new \Element_Ready_Pro\License\service\Connect();
      $this->connect->register();
      add_action('admin_menu',[$this,'register_page'],15);
      add_action('admin_post_element_ready_pro_license_options',[$this,'store'],15);
      add_action( 'rest_api_init', [$this,'license_key'] );
  
    }

    public function store(){
        $this->page->store();
    }
    public function register_page(){
       
        $this->page->add_page();
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
            'permission_callback' => function () {
                return true;
            }
        ) );

    }

    public function get_key( WP_REST_Request $request ){
        
        $key  = $request->get_header('X-Element-Ready-Pro-Signature');
        $domain = $request->get_param( 'domain' );
        
        $l_key = element_ready_get_api_option('license_key');
        if($l_key == '' || $l_key == false ){
            return false;
        }
        
        return \rest_ensure_response( "Hello World, this is the WordPress REST API {$l_key}" );
    }
}

