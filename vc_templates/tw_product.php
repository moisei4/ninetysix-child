<?php
/* ================================================================================== */
/*      Service Shortcode
  /* ================================================================================== */
wp_enqueue_script('waves-isotope');
$atts = shortcode_atts(array(
    'css' => '',
    'custom_class' => '',
    'element_class' => 'tw-element tw-product tw-isotope-container woocommerce',
    'element_dark' => '',
    'animation' => 'none',
    'animation_delay' => '',
    // ----------------
    'cats' => '',
    'count' => '',
    'column' => '3',
    'layout' => '',
    'filter' => 'none',
    'filter_add' => 'none',
    'pagination' => 'simple',
    'not_in' => '',
    'price_step' => 50,
    'price_max' => 250,
), vc_map_get_attributes($this->getShortcode(), $atts));
$query = array(
    'post_type' => 'product',
    'posts_per_page' => $atts['count'],
);
if ($atts['pagination'] == "simple" || $atts['pagination'] == "infinite") {
    global $paged;
    if (get_query_var('paged')) {
        $paged = get_query_var('paged');
    } elseif (get_query_var('page')) {
        $paged = get_query_var('page');
    } else {
        $paged = 1;
    }
    $query['paged'] = $paged;
}
if (!empty($atts['not_in'])) {
    $query['post__not_in'] = array($atts['not_in']);
}
$cats = ($atts['filter'] === 'ajax' && !empty($_REQUEST['waves_isotope_filter'])) ? array($_REQUEST['waves_isotope_filter']) : (empty($atts['cats']) ? false : explode(",", $atts['cats']));
if ($cats) {
    $query['tax_query'] = Array(Array(
            'taxonomy' => 'product_cat',
            'terms' => $cats,
            'field' => 'slug'
        )
    );
}
if(isset($_GET['orderby'])){
    switch($_GET['orderby']){
        case 'date'  :      $orderby = 'date';            $order = 'desc'; $meta_key = '';            break;
        case 'rating':      $orderby = 'meta_value_num';  $order = 'desc'; $meta_key = '_wc_average_rating';break;
        case 'price' :      $orderby = 'meta_value_num';  $order = 'asc';  $meta_key = '_price';      break;
        case 'price-desc' : $orderby = 'meta_value_num';  $order = 'desc';  $meta_key = '_price';      break;
        case 'popularity' : $orderby = 'meta_value_num';  $order = 'desc'; $meta_key = 'total_sales'; break;
        case 'title' :      $orderby = 'title';           $order = 'asc';  $meta_key = '';            break;
        case 'default':
        default :           $orderby = 'menu_order title';$order = 'asc';  $meta_key = '';            break;
    }
    if(isset($orderby))  {$query['orderby'] = $orderby;}
    if(isset($order))    {$query['order'] = $order;}
    if(!empty($meta_key)){$query['meta_key']=$meta_key;}
}
if(isset($_GET['min_price'])){
    $value=$_GET['min_price'];
    $compare='>=';
    if(isset($_GET['max_price'])){
        $value=array($_GET['min_price'], $_GET['max_price']);
        $compare='BETWEEN';//IN
    }
    $query['meta_query'][] = array(
        'key' => '_price',
        'value' => $value,
        'compare' => $compare,
        'type' => 'NUMERIC'
    );
}
if(isset($_GET['color_filter'])){
    $value=$_GET['color_filter'];
    $query['tax_query']['relation'] = 'AND';
    $query['tax_query'][] = array(
        'taxonomy' => 'pa_color',
        'field'    => 'slug',
        'terms'    => $value,
        'operator' => 'AND',
    );
}
if(isset($_GET['tag_filter'])){
    $value=$_GET['tag_filter'];
    $query['tax_query']['relation'] = 'AND';
    $query['tax_query'][] = array(
        'taxonomy' => 'product_tag',
        'field'    => 'slug',
        'terms'    => $value,
        'operator' => 'AND',
    );
}
if (!is_tax()) {
    query_posts($query);
}

$class = $atts['layout'].apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $this->settings['base'], $atts);
$output = waves_item($atts, $class);



if ($atts['filter'] !== 'none') {
    $output .= '<div class="tw-filters">';
    $output .= '<ul class="filters clearfix ' . esc_attr($atts['filter']) . '" data-option-key="filter">';
    $output .= '<li class="tw-hoverline"><a href="#filter" data-option-value="*" class="show-all selected">' . esc_html__('Show All', 'ninetysix') . '</a></li>';
    if ($cats) {
        $filters = $cats;
    } else {
        $filters = get_terms('product_cat');
    }
    foreach ($filters as $category) {
        if ($cats) {
            $category = get_term_by('slug', $category, 'product_cat');
        }
        $output .= '<li' . ($atts['filter'] === 'ajax' ? '' : ' class="tw-hoverline hidden"') . '><a href="#filter" data-option-value=".category-' . esc_attr($category->slug) . '" title="' . esc_attr($category->name) . '">' . esc_html($category->name) . '</a></li>';
    }
    $output .= '</ul>';
    if($atts['filter_add']){$output .= waves_filter_add($atts);}
    $output .= '</div>';
}

$atts['img_size'] = 'waves_portfolio_s' . $atts['column'];
$abs_start = $abs_end = '';
$atts['column']=intval($atts['column']);
if($atts['layout']=='layout-2'){
    $abs_start = '</a><div class="absolute-content"><a href="%s">';
    $abs_end = '</div>';
}elseif($atts['layout']=='layout-3'){
    $abs_start = '</a><div class="absolute-content"><a href="%s">';
    $abs_end = '</div>';
    $productSizes[1]='large';
    switch ($atts['column']){
        case 5:
            $productSizesRes=15;
            $productSizes[8]='vertical';
            $productSizes[11]='horizontal';
        break;
        case 4:
            $productSizesRes=11;
            $productSizes[6]='vertical';
            $productSizes[8]='horizontal';
        break;
        case 3:
            $productSizesRes=7;
            $productSizes[4]='vertical';
            $productSizes[5]='horizontal';
        break;
        case 2:
            $productSizesRes=5;
            $productSizes[2]='vertical';
            $productSizes[5]='horizontal';
        break;
    }
}
$i=isset($productSizesRes)&&isset($query['paged'])?(($query['paged']-1)*$query['posts_per_page']):0;

$output .= '<div class="isotope-container" data-column="' . esc_attr($atts['column']) . '">';
if (have_posts()) {
    global $post;
    while (have_posts()) {
        the_post();
        $size='default';
        if($atts['layout']=='layout-3'&&isset($productSizesRes)){
            wp_enqueue_style('waves-product-slider');
            wp_enqueue_script('waves-product-slider');
            $atts['img_size']=$size=='default'?'waves_portfolio_s4':'waves_portfolio_s2';
            $i=++$i%$productSizesRes;
            if($i===0){$i=$productSizesRes;}
            if(isset($productSizes[$i])){
                $size=$productSizes[$i];
            }
        }
        $artClass = 'not-inited';
        $image = waves_image($atts['img_size'], true);

        $catalogs = wp_get_post_terms($post->ID, 'product_cat');
        foreach ($catalogs as $catalog) {
            $artClass .= " category-" . $catalog->slug;
        }

        $output .= '<article class="product ' . esc_attr($artClass) . '" data-size="'.esc_attr($size).'">';
        $output .= '<div class="tw_product_container">';

        // if($size == 'large'){
            // $output .= '<div class="tw-product-images">';
            
            // if ( has_post_thumbnail() ) {
                // $image = get_the_post_thumbnail( $post->ID, 'full' );
                // $output .= '<div class="waves-slick-slide">'.$image.'</div>';
            // }
            // $attachment_ids = array_filter( array_filter( (array) explode( ',', waves_metabox('_product_image_gallery') ), 'wp_attachment_is_image' ));
            // if ( $attachment_ids ) {
                // foreach ( $attachment_ids as $attachment_id ) {
                    // $image = wp_get_attachment_image( $attachment_id, 'full' );
                    // $output .= '<div class="waves-slick-slide">'.$image.'</div>';
                // }
            // }
            
            // $output .= '</div>';
            // $output .= '<div class="product-slider-content">';
                // $output .= '<h2 class="product-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h2>';
                // $output .= "<div class='tw-hoverline'><a href='".esc_url(get_permalink())."'>".esc_html__('Shop Now', 'ninetysix')."</a></div>";
                // ob_start();
                    // do_action('waves_price');
                // $output .= ob_get_clean();
            // $output .= '</div>';
            
        // } else {
        
            ob_start();

            echo '<a href="' . get_permalink() . '">';
            do_action('woocommerce_before_shop_loop_item_title');
            printf($abs_start, get_permalink());
            do_action('woocommerce_shop_loop_item_title');
            do_action('woocommerce_after_shop_loop_item_title');
            do_action('woocommerce_after_shop_loop_item');
            echo ($abs_end);

            $output .= ob_get_clean();
        // }

        $output .= '</article>';
    }
}
$output .= "</div>";
ob_start();
if ($atts['pagination'] == "simple") {
    waves_pagination();
} elseif ($atts['pagination'] == "infinite") {
    waves_infinite();
}
$output .= ob_get_clean();

$output .= "</div>";

echo balanceTags($output);
wp_reset_query();