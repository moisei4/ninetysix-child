<!DOCTYPE html>
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <!-- Mobile Specific Metas
        ================================================== -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
        <?php waves_favicon(); ?>
        <!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>><?php 
        $waves_options = waves_get_options();
        $loaderData='';
        if(!isset($_POST['customized'])&&waves_option('preloader', 'none')!=='none'){
            echo '<div class="animsition-loading"><div class="tw-folding-cube"><div class="tw-cube1 tw-cube"></div><div class="tw-cube2 tw-cube"></div><div class="tw-cube4 tw-cube"></div><div class="tw-cube3 tw-cube"></div></div></div>';
            $loader = waves_option('preloader');
            $loaderData.=' data-animsition-in="'.str_replace("#","in",$loader).'"';
            $loaderData.=' data-animsition-in-duration="1000"';
            $loaderData.=' data-animsition-out="'.str_replace("#","out",$loader).'"';
            $loaderData.=' data-animsition-out-duration="800"';
        } ?>
        <div id="theme-layout"<?php echo ($loaderData); ?>>
            <!-- Start Header -->
            <div class="waves-header"><?php
                // Header left buttons
                $waves_h_left_buttons  =waves_get_mdl_btn('menu');
                $waves_h_left_buttons .=waves_get_mdl_btn('search');
                // Header right buttons
                $waves_h_right_buttons =waves_get_mdl_btn('wishlist');
                $waves_h_right_buttons.=waves_get_mdl_btn('basket');
                if($waves_options['header'] == 'header-logo-center' &&(!waves_woocommerce()||waves_option('search_on_header')==='on'&&waves_option('wishlist_on_header')==='off'&&waves_option('cart_on_header')==='off')){
                    $waves_h_left_buttons =waves_get_mdl_btn('menu');
                    $waves_h_right_buttons=waves_get_mdl_btn('search');
                } ?>
                <header class="waves-header-inner">
                    <div class="<?php echo esc_attr($waves_options['hf_cont_class']); ?>">
                        <div class="row">
                            <div class="col-md-2 waves-header-left"><?php
                                if($waves_options['header'] == 'header-logo-center'){
                                    echo balanceTags($waves_h_left_buttons);
                                }else{
                                    waves_logo();
                                } ?>
                            </div>
                            <div class="col-md-8 waves-header-middle"><?php
                                if($waves_options['header'] == 'header-normal'){ ?>
                                    <nav class="menu-container"><div class="tw-menu"><?php waves_menu(); ?></div></nav><?php
                                }elseif($waves_options['header'] == 'header-logo-center'){
                                    waves_logo();
                                }
								
								waves_modal();
								
								?>
                            </div>
                            <div class="col-md-2 waves-header-right"><?php
                                if($waves_options['header'] != 'header-logo-center'){
                                    echo balanceTags($waves_h_left_buttons);
                                }
								
                                echo balanceTags($waves_h_right_buttons);
                                ?>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="header-clone"></div>
            </div>
            <!-- End Header -->
            <?php get_template_part('feature', 'area'); ?>
            <!-- Waves Container -->
            <div class="waves-container container">