<?php 
namespace Element_Ready_Pro\License\service;
use WP_REST_Request;
use Element_Ready_Pro\License\admin\Admin;
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

      add_action( 'admin_menu',[$this,'register_page'],15);
      add_action( 'admin_post_element_ready_pro_license_options',[$this,'store'],15);
     
      add_action( 'rest_api_init', [$this,'license_key'] );
      add_action( 'rest_api_init', [$this,'activate'] );
      add_action( 'rest_api_init', [$this,'deactivate'] );
  
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
    
    public function activate(){

        register_rest_route( 'element-ready-pro/v1', '/activate', array(
            'methods'  => 'POST',
            'callback' => [$this,'activate_product'],
            'permission_callback' => function () {
                return true;
            }
        ) );

    }

    public function deactivate(){

        register_rest_route( 'element-ready-pro/v1', '/deactivate', array(
            'methods'  => 'POST',
            'callback' => [$this,'temp_deactivate'],
            'permission_callback' => function () {
                return true;
            }
        ) );

    }

    public function temp_deactivate( WP_REST_Request $request){

        $key  = $request->get_header('X-Element-Ready-Pro-Signature');
        if($key != 'element-ready'){
            wp_send_json_success( $data , 403 );
            wp_die();
        }

        $act =  new Admin();
        $data = $act->action_deactivate_license(); 
        wp_send_json_success( true , 200 );
    }

   
    
    public function activate_product( WP_REST_Request $request){

        $key  = $request->get_header('X-Element-Ready-Pro-Signature');
        
        if($key != 'element-ready'){
            wp_send_json_success( $data , 102 );
            wp_die();
        }
        $act =  new Admin();
        $data = $act->action_activate_license();

        if(isset($data['code'])){
            wp_send_json_success( $data , $data['code'] );
        }else{
            wp_send_json_success( $data , 102 );
        }  
       
        wp_die();
    }

    public function get_key( WP_REST_Request $request ){
        
        $key  = $request->get_header('X-Element-Ready-Pro-Signature');
        $domain = $request->get_param( 'domain' );
        
        $l_key = get_option('element_ready_pro_license_key');
        if($l_key == '' || $l_key == false ){
            return false;
        }
        
        return \rest_ensure_response( "this is the REST API {$l_key}" );
    }
}

