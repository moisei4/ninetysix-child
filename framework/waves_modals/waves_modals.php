<?php
/* Waves Modal */
function waves_modal_before($class){ ?>
    <div class="waves-modal-item waves-va-middle <?php echo esc_attr(str_replace('_','-',$class)); ?>"><?php
}
function waves_modal_after(){ ?></div><?php }
function waves_modal(){
    $modals=array('waves_modal_menu');
    if(waves_option('search_on_header')==='on'){
        array_push($modals,'waves_modal_search');
    }
    if(waves_woocommerce()){
        if(waves_option('cart_on_header')==='on'){
            array_unshift($modals , 'waves_modal_basket');
        }
        if(defined( 'YITH_FUNCTIONS' )&&waves_option('wishlist_on_header')==='on'){
            array_push($modals,'waves_modal_wishlist');
        }
    }
    foreach($modals as $modal){
        $modalFile=WAVES_FW_PATH.'waves_modals/'.$modal.'.php';
        if(file_exists($modalFile)){
            waves_modal_before($modal);
            require_once($modalFile);
            waves_modal_after();
        }
    } ?>
    <i class="waves-modal-close-btn ion-android-close"></i>
    <div class="waves-modal-overlay"></div><?php
}
function waves_get_mdl_btn($btnName){
    $class='';
    switch($btnName){
        case'menu'      : $class='ion-navicon';break;
        case'search'    :
            if(waves_option('search_on_header')==='on'){
                $class='ion-ios-search-strong';
            }else{
                return'';
            }
        break;
        case'wishlist'  :
            if(waves_option('wishlist_on_header')==='on'&&waves_woocommerce()&&defined( 'YITH_FUNCTIONS' )){
                $class='ion-android-favorite-outline';
            }else{
                return '';
            }
        break;
        case'basket'    :
            if(waves_option('cart_on_header')==='on'&&waves_woocommerce()){
                $class='ion-bag';
            }else{
                return '';
            }
        break;
    }
    return '<a href="#" class="waves-mbtn" data-mbtn="'.esc_attr($btnName).'"><i class="'.esc_attr($class).'"></i></a>';
}