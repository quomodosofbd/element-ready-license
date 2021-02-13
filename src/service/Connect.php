<?php 
namespace Element_Ready_Pro\License\service; 

class Connect extends Base {

   
    public function register(){
       $this->init(); 
    }
    protected function init() {
		
		add_filter( 'element_ready_pro_extend_modules', [$this,'modules'] , 10);
		
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

	public function modules($data){
        
        error_log(json_encode($data));
        return $data;
    }
}