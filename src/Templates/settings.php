<div class="element-ready-admin-dashboard-container wrap">

    <div id="element-ready-adpage-tabs" class="element-ready-adpage-tabs">

        <div class="element-ready-nav-wrapper">
            <div class="element-ready-logo">
                <a href="http://quomodosoft.com/"><img src="<?php echo esc_url(ELEMENT_READY_ROOT_IMG.'logo.jpg'); ?>" alt="<?php echo esc_attr__('logo','element-ready'); ?>"></a>
            </div>
            <ul>
                <li class="element-ready-dashboard element-ready-header-footer">
                    <a href="#element-ready-adpage-tabs-1">
                        <i class="dashicons dashicons-admin-home"></i>
                        <h3 class="element-ready-title"><?php echo esc_html__('License','element-ready'); ?> </h3>
                      
                    </a>
                </li>
               
            </ul>
        </div>
        <div id="element-ready-adpage-tabs-1" class="element-ready-adpage-tab-content element-ready-dashboard dashboard">

            <form class="element-ready-components-action quomodo-component-data" action="<?php echo admin_url('admin-post.php') ?>" method="post">
                    <div class="quomodo-container-wrapper">
                        <div class="quomodo-row-wrapper">
                            <div class="element-ready-component-form-wrapper components">
                                <div class="element-ready-components-topbar">
                                    <div class="element-ready-title">
                                        <h3 class="title"><i class="dashicons dashicons-editor-alignleft"></i> <?php echo esc_html__('License Settings','element-ready'); ?> </h3>
                                    </div>
                                    <div class="element-ready-savechanges">

                                        <button type="submit" class="element-ready-component-submit button element-ready-submit-btn"><i class="dashicons dashicons-yes"></i> <?php echo esc_html__('Save Change','element-ready'); ?></button>

                                    </div>
                                </div>
                                <div class="quomodo-row">
                                    <div class="element-ready-col quomodo-col-md-6">
                                        <div class="element-ready-data">
                                            <strong><?php echo esc_html__('License Key','element-ready-pro'); ?></strong>
                                            <input value="" name="element_ready_pro_license_key" class="quomodo_text " id="element_ready_pro_license_key" type="text">
                                            <label for="element_ready_pro_license_key"></label>
                                        </div>
                                    </div>
                                    <div class="element-ready-col quomodo-col-md-6">
                                        <div class="element-ready-data">
                                            <strong><?php echo esc_html__('Welcome To Element Ready Pro','element-ready-pro'); ?></strong>
                                           
                                        </div>    
                                    </div>    
                                </div>
                            </div>
                            <input type="hidden" name="action" value="element_ready_pro_license_options">
                            <?php echo wp_nonce_field('element-ready-pro-ls-components', '_element_ready_pro_ls_components'); ?>
                        </div>
                    </div> <!-- container end -->
                </form>
            </div>
       
    </div>

</div>
