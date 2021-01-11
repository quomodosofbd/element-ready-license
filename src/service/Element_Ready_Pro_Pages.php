<?php 
namespace Element_Ready_Pro\License\service;

if ( ! defined( 'ABSPATH' ) ) exit;

Class Element_Ready_Pro_Pages {

    public function add_page(){
       
        add_action( 'admin_enqueue_scripts', [$this,'add_admin_scripts'] );
        add_submenu_page( 'element_ready_elements_dashboard_page', 'er_ls_settings', 'License', 'manage_options', 'element-ready-pro-license', [$this,'settings'], 20 );
        add_action( 'element-ready-pro/dash/connection', [$this,'render_content'] );
    }

    public function store(){
       $this->components_options();
    }
    public function add_admin_scripts($handle){
           
        
        if($handle == 'element-ready_page_element-element-ready-pro-license'){
          
           wp_enqueue_style( 'element-ready-grid', ELEMENT_READY_ROOT_CSS .'grid.css' );
           wp_enqueue_style( 'element-ready-admin', ELEMENT_READY_ROOT_CSS .'admin.css' );
        }
      
   }
    public function settings(){
         
        do_action('element-ready-pro/admin/connect/init');
        wp_enqueue_style( 'element-ready-grid', ELEMENT_READY_ROOT_CSS .'grid.css' );
        wp_enqueue_style( 'element-ready-admin', ELEMENT_READY_ROOT_CSS .'admin.css' );
        
        require_once( __DIR__ .'/..' .'/Templates/settings.php' );
    }

    public function components_options(){
     
        if ( !isset($_POST['_element_ready_pro_ls_components']) || !wp_verify_nonce($_POST['_element_ready_pro_ls_components'], 'element-ready-pro-ls-components')) {
            wp_redirect($_SERVER["HTTP_REFERER"]);
        }
  
        if( !isset($_POST['element_ready_pro_license_key']) ){
            wp_redirect($_SERVER["HTTP_REFERER"]); 
        }
       
        // Save
        update_option('element_ready_pro_license_key',$_POST['element_ready_pro_license_key']);
        
        if ( wp_doing_ajax() )
        {
          wp_die();
        }else{
            wp_redirect($_SERVER["HTTP_REFERER"]);
        }  
    }

    public function render_content(){
        
        require_once( __DIR__ .'/..' .'/Templates/connect.php' );
    }

    public function element_ready_pro_status(){

        return 'inactive';
    }
}
