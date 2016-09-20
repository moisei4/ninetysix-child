<?php
function waves_woocommerce() {
    if(class_exists('woocommerce')){return true;}
    return false;
}
if(waves_woocommerce()) {
    
    add_filter( 'woocommerce_enqueue_styles', '__return_false' );

    /*
     *	Set image dimensions on theme activation
     */
    if ( ! function_exists( 'tw_woocommerce_set_image_dimensions' ) ) {
        function tw_woocommerce_set_image_dimensions() { 
                if ( ! get_option( 'tw_shop_image_sizes_set' ) ) {
                        $single = array(
                                'width' 	=> '540',	// px
                                'height'	=> '615',	// px
                                'crop'      => 1 		// crop
                        );
                        $thumbnail = array(
                                'width' 	=> '70',	// px
                                'height'	=> '75',	// px
                                'crop'      => 1 		// crop
                        );

                        // Image sizes
                        update_option( 'shop_single_image_size', $single ); 		// Single product image
                        update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs

                        // Set "images size set" option
                        add_option( 'tw_shop_image_sizes_set', '1' );
                }
        }
    }
    // Theme activation hook
    add_action( 'after_switch_theme', 'tw_woocommerce_set_image_dimensions', 1 );
    // Additional hook for when WooCommerce is installed/activated after the theme
    add_action( 'admin_init', 'tw_woocommerce_set_image_dimensions', 1000 );
    
    
    /* Customizing Checkout Button */
    function waves_custom_checkout_button_text() {
        $checkout_url = WC()->cart->get_checkout_url();

        echo '<a href="'.$checkout_url.'" class="tw-btn checkout-button button alt wc-forward" rel="nofollow">';
        esc_html_e( 'Proceed to Checkout', 'ninetysix' );
        echo '</a>'; 
    }
    remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 ); 
    add_action('woocommerce_proceed_to_checkout', 'waves_custom_checkout_button_text');
    
    
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
    add_action( 'waves_price', 'woocommerce_template_loop_price', 10 );
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    

    /* ====== Remove Actions ====== */

    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    
    add_action('waves_single_product_meta', 'woocommerce_template_single_meta', 40);
    
    add_action('woocommerce_before_single_product_summary', 'waves_before_product_summary', 5);
    add_action('woocommerce_before_single_product_summary', 'waves_before_product_summary2', 30);
    add_action('woocommerce_after_single_product_summary', 'waves_after_product_summary',  5);
    function waves_before_product_summary(){
        $single = waves_metabox('woocommerce_single');
        $bcrumb = waves_metabox('woo_breadcrumb');
        $layout = $single ? $single : waves_option('woocommerce_single', 'layout-1');
        $breadcrumb = $bcrumb ? $bcrumb : waves_option('woo_breadcrumb', 'on');
        echo '<div class="tw-product-feature with-meta '.$layout.'">';
        
        if($breadcrumb=='on'){
            $shop_page = get_option( 'woocommerce_shop_page_id' );
            ob_start();
            previous_post_link('%link', '<i class="ion-ios-arrow-thin-left"></i>');
            $prev = ob_get_clean();
            ob_start();
            next_post_link('%link', '<i class="ion-ios-arrow-thin-right"></i>');
            $next = ob_get_clean();
            echo '<div class="tw-breadcrumb-container"><div class="container"><div class="tw-breadcrumb">';
                do_action('waves_woocommerce_breadcrumb');
                echo '<div class="single-product-navigation">';
                    echo balanceTags($prev.'<a class="shoppage-link" href="'.esc_url(get_permalink($shop_page)).'"><i class="ion-grid"></i></a>'.$next);
                echo '</div>';
            echo '</div></div></div>';
        }
        
        echo '<div class="tw-product-border">';
        echo '<div class="container">';
        echo '<div class="row">';
        echo '<div class="col-md-7">';
    }
    function waves_before_product_summary2(){
        echo '</div><div class="col-md-4">';
    }
    function waves_after_product_summary(){
        echo '</div></div></div></div>';
        echo '<div class="tw-product-meta"><div class="container">';
            do_action('waves_single_product_meta');
            do_action('waves_woocommerce_share');
        echo '</div></div>';        
        echo '</div><div class="tw-product-feature-clone"></div>';
    }
    
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 15);
    
    add_action('waves_woocommerce_share', 'waves_product_share');
    function waves_product_share(){
        echo '<div class="entry-share">';
            echo '<a class="facebook" href="' . esc_url(get_permalink()) . '" title="Share this"><i class="ion-social-facebook"></i></a>';
            echo '<a class="twitter" href="' . esc_url(get_permalink()) . '" title="Tweet" data-title="' . esc_attr(get_the_title()) . '" data-id="'.esc_attr(get_the_id()).'" data-ajaxurl="'.esc_url(home_url('/')).'"><i class="ion-social-twitter"></i></a>';
            echo '<a class="pinterest" href="' . esc_url(get_permalink()) . '" title="Pin It"><i class="ion-social-pinterest"></i></a>';
        echo '</div>';
    }

    add_filter('woocommerce_output_related_products_args', 'waves_related_products_args');
    function waves_related_products_args($args){
        $args['posts_per_page'] = 3;
        $args['columns'] = 3;
        return $args;
    }


    /* ====== Remove Page Title ====== */

    add_filter('woocommerce_show_page_title', 'waves_title_none');
    function waves_title_none(){
        return false;
    }



    /* ====== Close Div Tag ====== */
    function waves_close_div()
    {
        echo "</div>";
    }




    /* ====== Change Wrapper Begin ====== */

    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    add_action('woocommerce_before_main_content', 'waves_woocommerce_output_content_wrapper', 10);

    function waves_woocommerce_output_content_wrapper() {
        if(waves_option('woocommerce_layout') === '4-columns'){
            echo "<div class='content-area waves-shop columns-4'>";
        } else {
            echo "<div class='content-area waves-shop'>";
        }
    }




    /* ====== Change Wrapper End ====== */

    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    add_action('woocommerce_after_main_content', 'waves_close_div', 10);



    /* ====== Added div loop product before ====== */

    add_action( 'woocommerce_before_shop_loop_item', 'waves_loop_product_before', 5);
    function waves_loop_product_before()
    {
        echo "<div class='tw_product_container'>";
    }
    add_action( 'woocommerce_after_shop_loop_item',  'waves_close_div', 1000);





    add_action( 'woocommerce_before_shop_loop_item_title', 'waves_loop_product_title_container', 20);
    function waves_loop_product_title_container(){  
            echo "<div class='tw_product_header'>";
    }
    
    add_action( 'woocommerce_before_shop_loop_item_title', 'waves_loop_product_title_container_end', 30);
    function waves_loop_product_title_container_end(){
        global $product;
        echo "<a href='".esc_url(get_permalink($product->id))."'>";
    }

    add_action( 'woocommerce_after_shop_loop_item', 'waves_shop_loop_item_end', 5);
    function waves_shop_loop_item_end(){
        global $product;
        echo "<div class='tw-hoverline'><a href='".esc_url(get_permalink($product->id))."'>".esc_html__('Shop Now', 'ninetysix')."</a></div>";
        do_action('waves_price');
        echo '</div>';
    }







    /* ====== Add Categories title after ====== */
    
//    add_action( 'woocommerce_after_shop_loop_item_title', 'waves_loop_cats', 4);
    function waves_loop_cats(){
        global $product;
        echo balanceTags($product->get_categories( ', ', '<span class="posted_in">', '</span>' ));
    }



    /* ====== Change Pagination ====== */

    remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
    add_action('woocommerce_after_shop_loop', 'waves_pagination', 10);




    /* ====== Change Breadcrumbs ====== */

    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
    add_action( 'waves_woocommerce_breadcrumb', 'woocommerce_breadcrumb' );



    /* ====== Change Products Columns ====== */

    add_filter('loop_shop_columns', 'waves_shop_columns');
    if (!function_exists('waves_shop_columns')) {
        function waves_shop_columns() {
            if(waves_option('woocommerce_layout') === '4-columns'){
                return 4; // 3 products per row
            }
            return 3; // 3 products per row
        }
    }



    /* ====== Change Thumbnail ====== */

    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    add_action('woocommerce_before_shop_loop_item_title', 'waves_woocommerce_thumb', 10);

    function waves_woocommerce_thumb()
    { 
        global $product;
        echo '</a>';
        echo "<div class='product_thumb'>";
                echo '<a href="'.esc_url(get_permalink($product->id)).'">';
                echo waves_image('waves_archive_product');
                echo '<div class="thumb-overlay"></div>';
                echo '</a>';

                if ($product->product_type == 'bundle' ){
                        $product = new WC_Product_Bundle($product->id);
                }
                if(defined( 'YITH_FUNCTIONS' )){
                    $add = '<i class="ion-android-favorite-outline"></i>';
                    $already = '<i class="ion-android-favorite"></i>';
                    $added = '<i class="ion-android-favorite"></i>';
                    echo htmlspecialchars_decode(do_shortcode('[yith_wcwl_add_to_wishlist icon="" label="'.esc_attr($add).'" product_added_text="'.esc_attr($added).'" already_in_wishslist_text="'.esc_attr($already).'" browse_wishlist_text=""]'));
                }
        echo "</div>";   
            
    }
    function waves_woocommerce_gallery_first_thumb($id, $width)
    {
        $gallery = get_post_meta( $id, '_product_image_gallery', true );
        if(!empty($gallery))
        {
                $woo_gallery = explode(',',$gallery);
                $image_id = $woo_gallery[0];
                $image = aq_resize(wp_get_attachment_url($image_id), $width, "", true);
                $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                $alt = !empty($alt) ? $alt : get_the_title();
                if(!empty($image)) return '<img src="'.esc_url($image).'" class="product-hover-thumb" alt="'.esc_attr($alt).'">';
        }
    }




    /* ====== Products per page ====== */

    add_filter( 'loop_shop_per_page', 'waves_products_count' );
    function waves_products_count()
    {
        global $waves_woocommerce;
        if(!empty($waves_woocommerce['products_per_page']))
            return $waves_woocommerce['products_per_page'];
        return waves_option('woo_per_page');
    }




    /* ====== Products sort ====== */

    add_action( 'woocommerce_before_shop_loop', 'waves_woocommerce_sorting', 20);
    function waves_woocommerce_sorting()
    {
            global $waves_woocommerce;

            if(waves_option('woo_sorting') !== 'true') return false;

            $product_order['default'] 	= esc_html__("Default Order","ninetysix");
            $product_order['title'] 	= esc_html__("Name","ninetysix");
            $product_order['price'] 	= esc_html__("Price","ninetysix");
            $product_order['date'] 		= esc_html__("Date","ninetysix");
            $product_order['popularity']    = esc_html__("Popularity","ninetysix");

            $product_sort['asc'] 		= esc_html__("Click to order products ascending",  "ninetysix");
            $product_sort['desc'] 		= esc_html__("Click to order products descending",  "ninetysix");

            $per_page_string 		= esc_html__("Items","ninetysix");

            $per_page 		 	= waves_option('woo_per_page');
            if(!$per_page) $per_page 	= get_option('posts_per_page');

            parse_str($_SERVER['QUERY_STRING'], $params);

            if(isset($_REQUEST['product_order'])){$waves_woocommerce['product_order']=$_REQUEST['product_order'];}
            if(isset($_REQUEST['product_count'])){$waves_woocommerce['product_count']=$_REQUEST['product_count'];}
            if(isset($_REQUEST['product_sort'] )){$waves_woocommerce['product_sort'] =$_REQUEST['product_sort']; }
            $po_key = !empty($waves_woocommerce['product_order']) ? $waves_woocommerce['product_order'] : 'default';
            $ps_key = !empty($waves_woocommerce['product_sort'])  ? $waves_woocommerce['product_sort'] : 'asc';
            $pc_key = !empty($waves_woocommerce['product_count']) ? $waves_woocommerce['product_count'] : $per_page;
            $ps_key = strtolower($ps_key);

            //generate markup
            $output  = "";
            $output .= "<div class='tw-product-ordering clearfix'>";
            $output .= "    <span class='tw-order-current'>".esc_html__("Sort by","ninetysix")." </span>";
            $output .= "    <ul class='product-order'>";
            $output .= "    	<li><span>".$product_order[$po_key]."<i class='fa fa-angle-down'></i></span>";
            $output .= "    	<ul>";
            $output .= "    	<li><a href='".waves_woo_build_query_string($params, 'product_order', 'default')."'>	<span class='avia-bullet'></span>".$product_order['default']."</a></li>";
            $output .= "    	<li><a href='".waves_woo_build_query_string($params, 'product_order', 'title')."'>	<span class='avia-bullet'></span>".$product_order['title']."</a></li>";
            $output .= "    	<li><a href='".waves_woo_build_query_string($params, 'product_order', 'price')."'>	<span class='avia-bullet'></span>".$product_order['price']."</a></li>";
            $output .= "    	<li><a href='".waves_woo_build_query_string($params, 'product_order', 'date')."'>	<span class='avia-bullet'></span>".$product_order['date']."</a></li>";
            $output .= "    	<li><a href='".waves_woo_build_query_string($params, 'product_order', 'popularity')."'>	<span class='avia-bullet'></span>".$product_order['popularity']."</a></li>";
            $output .= "    	</ul>";
            $output .= "    	</li>";
            $output .= "    </ul>";

            $output .= "    <span class='tw-order-current'>".esc_html__("View Items","ninetysix")." </span>";
            $output .= "    <ul class='product-count'>";
            $output .= "    	<li><span>".$pc_key." ".$per_page_string."<i class='fa fa-sort'></i></span>";
            $output .= "    	<ul>";
            $output .= "    	<li><a href='".waves_woo_build_query_string($params, 'product_count', $per_page)."'>		<span class='avia-bullet'></span>".$per_page." ".$per_page_string."</a></li>";
            $output .= "    	<li><a href='".waves_woo_build_query_string($params, 'product_count', $per_page * 2)."'>	<span class='avia-bullet'></span>".($per_page * 2)." ".$per_page_string."</a></li>";
            $output .= "    	<li><a href='".waves_woo_build_query_string($params, 'product_count', $per_page * 3)."'>	<span class='avia-bullet'></span>".($per_page * 3)." ".$per_page_string."</a></li>";
            $output .= "    	</ul>";
            $output .= "    	</li>";
            $output .= "	</ul>";

            $output .= "    <ul class='product-sort'>";
            $output .= "    	<li>";
            if($ps_key == 'desc') 	$output .= "<a title='".esc_attr($product_sort['asc'])."' class='sort-asc'  href='".waves_woo_build_query_string($params, 'product_sort', 'asc')."'><i class='fa fa-long-arrow-down'></i></a>";
            if($ps_key == 'asc') 	$output .= "<a title='".esc_attr($product_sort['desc'])."' class='sort-desc' href='".waves_woo_build_query_string($params, 'product_sort', 'desc')."'><i class='fa fa-long-arrow-up'></i></a>";
            $output .= "    	</li>";
            $output .= "    </ul>";

            $output .= "</div>";
            echo balanceTags($output);
    }


    //helper function to build the query strings for the catalog ordering menu
    if(!function_exists('waves_woo_build_query_string'))
    {
            function waves_woo_build_query_string($params = array(), $overwrite_key, $overwrite_value)
            {
                    $params[$overwrite_key] = $overwrite_value;
                    return "?". http_build_query($params);
            }
    }

    //function that actually overwrites the query parameters
    if(!function_exists('waves_woocommerce_overwrite_catalog_ordering'))
    {
            add_action( 'woocommerce_get_catalog_ordering_args', 'waves_woocommerce_overwrite_catalog_ordering', 20);

            function waves_woocommerce_overwrite_catalog_ordering($args)
            {
                    global $waves_woocommerce;

                    if(waves_option('woo_sorting') !== 'true') return $args;

                    //check the folllowing get parameters and session vars. if they are set overwrite the defaults
                    $check = array('product_order', 'product_count', 'product_sort');
                    if(empty($waves_woocommerce)) $waves_woocommerce = array();

                    foreach($check as $key)
                    {
                            if(isset($_GET[$key]) ) $_SESSION['tw_woocommerce'][$key] = esc_attr($_GET[$key]);
                            if(isset($_SESSION['tw_woocommerce'][$key]) ) $waves_woocommerce[$key] = $_SESSION['tw_woocommerce'][$key];
                    }


                    // is user wants to use new product order remove the old sorting parameter
                    if(isset($_GET['product_order']) && !isset($_GET['product_sort']) && isset($_SESSION['tw_woocommerce']['product_sort']))
                    {
                            unset($_SESSION['tw_woocommerce']['product_sort'], $waves_woocommerce['product_sort']);
                    }

                    extract($waves_woocommerce);

                    // set the product order
                    if(!empty($product_order))
                    {
                            switch ( $product_order ) {
                                    case 'date'  : $orderby = 'date'; $order = 'desc'; $meta_key = '';  break;
                                    case 'price' : $orderby = 'meta_value_num'; $order = 'asc'; $meta_key = '_price'; break;
                                    case 'popularity' : $orderby = 'meta_value_num'; $order = 'desc'; $meta_key = 'total_sales'; break;
                                    case 'title' : $orderby = 'title'; $order = 'asc'; $meta_key = ''; break;
                                    case 'default':
                                    default : $orderby = 'menu_order title'; $order = 'asc'; $meta_key = ''; break;
                            }
                    }

                    // set the product count
                    if(!empty($product_count) && is_numeric($product_count))
                    {
                            $waves_woocommerce['products_per_page'] = (int) $product_count;
                    }

                    //set the product sorting
                    if(!empty($product_sort))
                    {
                            switch ( $product_sort ) {
                                    case 'desc' : $order = 'desc'; break;
                                    case 'asc' : $order = 'asc'; break;
                                    default : $order = 'asc'; break;
                            }
                    }


                    if(isset($orderby)) $args['orderby'] = $orderby;
                    if(isset($order)) 	$args['order'] = $order;
                    if (!empty($meta_key))
                    {
                            $args['meta_key'] = $meta_key;
                    }


                    $waves_woocommerce['product_sort'] = $args['order'];

                    return $args;
            }
    }



//    add_action('widgets_init', 'waves_woocommerce_widgets');
    function waves_woocommerce_widgets(){
        register_sidebar(array(
            'name' => 'Woocommerce shop page',
            'id' => 'woocommerce-shop',
            'before_widget' => '<aside class="widget %2$s" id="%1$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));
        register_sidebar(array(
            'name' => 'Woocommerce product single',
            'id' => 'woocommerce-single',
            'before_widget' => '<aside class="widget %2$s" id="%1$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));
    }
    
    
    function waves_shop_columns3(){
            return 3; // 3 products per row
    }
    function waves_shop_columns4(){
            return 4; // 4 products per row
    }
    function waves_woocommerce_page($atts, $content){
        $atts = shortcode_atts( array(
            'column'  => ''
        ), $atts );
        $klass = '';
        if($atts['column'] == '3'){
            add_filter('loop_shop_columns', 'waves_shop_columns3');
            $klass = 'columns-3';
        } elseif($atts['column'] == '4'){
            add_filter('loop_shop_columns', 'waves_shop_columns4');
        }
        $per_page = waves_option('woo_per_page');
        if(!$per_page) $per_page = get_option('posts_per_page');
        $qr=array('post_type'=>'product', 'orderby'=>'title', 'order'=>'asc', 'posts_per_page'=>$per_page);
        
        if(waves_option('woo_sorting') === 'true'){
            // set the product count
            if(isset($_GET['product_count'])){
                $qr['posts_per_page']=$_GET['product_count'];
            }
            
            // set the product order
            if(!empty($_GET['product_order'])){
                switch($_GET['product_order']){
                    case 'date'  : $orderby = 'date'; $order = 'desc'; $meta_key = '';  break;
                    case 'price' : $orderby = 'meta_value_num'; $order = 'asc'; $meta_key = '_price'; break;
                    case 'popularity' : $orderby = 'meta_value_num'; $order = 'desc'; $meta_key = 'total_sales'; break;
                    case 'title' : $orderby = 'title'; $order = 'asc'; $meta_key = ''; break;
                    case 'default':
                    default : $orderby = 'menu_order title'; $order = 'asc'; $meta_key = ''; break;
                }
            }

            //set the product sorting
            if(!empty($_GET['product_sort'])){
                switch($_GET['product_sort']){
                    case 'desc' : $order = 'desc'; break;
                    case 'asc' : $order = 'asc'; break;
                    default : $order = 'asc'; break;
                }
            }
            if(isset($orderby))  {$qr['orderby'] = $orderby;}
            if(isset($order))    {$qr['order'] = $order;}
            if(!empty($meta_key)){$qr['meta_key']=$meta_key;}
        }
        query_posts($qr);
        ob_start();
        echo '<div class="woocommerce '.esc_attr($klass).'">';
        woocommerce_content();
        echo '</div>';
        return ob_get_clean();
        
    }
//    add_shortcode('woocommerce_page', 'waves_woocommerce_page');
}