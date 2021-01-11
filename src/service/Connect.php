<?php 
namespace Element_Ready_Pro\License\service; 

class Connect extends Base {

   
    public function register(){
       $this->init(); 
    }
    protected function init() {
		
		//add_action( 'admin_init', [ $this, 'register_actions' ] );
		
    }

    protected function update_settings() {
        
		update_user_option( get_current_user_id(), $this->get_option_name(), $this->data );
	}

	/**
	 * @since 1.0.0
	 * @access protected
	 */
	protected function init_data() {

		$this->data = get_user_option( $this->get_option_name() );

		if ( ! $this->data ) {
			$this->data = [];
		}
	}
    
    protected function get_slug() {
		return 'element-ready-pro-connect';
    }
}