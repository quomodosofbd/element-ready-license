<div class="element-ready-pro-connect">

   <div class="quomodo-row">
         <div class="element-ready-col quomodo-col-md-6"> 
                <p>
                    <?php echo esc_html__( 'Status: ', 'element-ready-pro' ) ?>
                    <?php echo $this->element_ready_pro_status(); ?>
                </p>
          </div>
         <div class="element-ready-col quomodo-col-md-6 right">
            <p>
                <a href="<?php echo esc_url( ELEMENT_READY_DEMO_URL .'account'); ?>" class="button primary">
                    <?php echo esc_html__( 'My Account', 'element-ready-pro' ) ?>
                </a>
            </p>
         </div>
   </div>
   <div class="quomodo-row">
         <div class="element-ready-col quomodo-col-md-6"> 
            <p>
                <?php echo esc_html__( 'Activate license: ', 'element-ready-pro' ) ?> 
            </p>
          </div>
         <div class="element-ready-col quomodo-col-md-6 right">
            <p>
                <a href="<?php echo esc_url( ELEMENT_READY_DEMO_URL .'account'); ?>" class="button secondary">
                    <?php echo esc_html__( 'Activate', 'element-ready-pro' ) ?>
                </a>
            </p>
         </div>
   </div>
   <div class="quomodo-row">
         <div class="element-ready-col quomodo-col-md-6"> 
            <p>
                <?php echo esc_html__( 'Deactivate license: ', 'element-ready-pro' ) ?> 
            </p>
          </div>
         <div class="element-ready-col quomodo-col-md-6 right">
            <p>
                <a href="<?php echo esc_url( ELEMENT_READY_DEMO_URL .'account'); ?>" class="button danger">
                    <?php echo esc_html__( 'Deactivate', 'element-ready-pro' ) ?>
                </a>
            </p>
         </div>
   </div>
  
</div>