<?php
function waves_option($index, $default = false) {
    return apply_filters("waves_option",ot_get_option($index,$default),$index);
}
function waves_favicon() {
    if(!function_exists('has_site_icon')||!has_site_icon()){
        $favicon = waves_option('fav_icon');
        if(!empty($favicon)){
            echo '<link rel="shortcut icon" href="' . esc_url($favicon).'"/>';
        }else{
            echo '<link rel="shortcut icon" href="' . esc_url(THEME_DIR . '/assets/img/favicon.ico') . '"/>';
        }
    }
}
// Page, Post custom metaboxes
//=======================================================
function waves_metabox($name){
    global $post;
    if ($post) {
        return apply_filters("waves_option",get_post_meta($post->ID, $name, true),$name);
    }
    return false;
}

function waves_get_options(){
    global $waves_options;
    return $waves_options;
}
function waves_set_options($new_atts){
    global $waves_options;
    $waves_options = array_merge($waves_options, $new_atts);
}

// Waves Parent Atts
//=======================================================
function waves_get_pa(){
    global $waves_parentAtts;
    return $waves_parentAtts;
}
function waves_set_pa($new_atts){
    global $waves_parentAtts;
    $waves_parentAtts=$new_atts;
}
// Print menu
//=======================================================
function waves_menu() {
    wp_nav_menu(array(
        'walker' => new Waves_CustomMenu(),
        'container' => false,
        'menu_id' => '',
        'menu_class' => 'sf-menu clearfix',
        'fallback_cb' => 'waves_nomenu',
        'theme_location' => 'main'
    ));
}

function waves_nomenu() {
    echo "<ul class='sf-menu clearfix'>";
    wp_list_pages(array('title_li' => ''));
    echo "</ul>";
}

function waves_menu2($loc='left') {
        wp_nav_menu(array(
            'walker' => new Waves_CustomMenu(),
            'container' => false,
            'menu_id' => '',
            'menu_class' => 'sf-menu',
            'fallback_cb' => 'waves_nomenu2',
            'theme_location' => $loc
        ));
}
function waves_nomenu2() {
    echo "<ul class='sf-menu'><li><a href='".esc_url(admin_url('nav-menus.php?action=locations'))."'>";
    echo esc_html__('Choose Left, Right Menu on Appearance -> Menu', 'ninetysix');
    echo "</li></a></ul>";
}
function waves_mobilemenu($loc = 'main') {
    $nav_menu = waves_metabox('onepage_menu');
    if ($nav_menu) {
        wp_nav_menu(array(
            'container' => false,
            'menu' => $nav_menu,
            'menu_id' => '',
            'menu_class' => 'sf-mobile-menu clearfix waves-modal-inside',
            'fallback_cb' => 'waves_nomobile')
        );
    } else {
        wp_nav_menu(array(
            'container' => false,
            'menu_id' => '',
            'menu_class' => 'sf-mobile-menu clearfix waves-modal-inside',
            'fallback_cb' => 'waves_nomobile',
            'theme_location' => $loc)
        );
    }
}

function waves_nomobile() {
    echo "<ul class='waves-no-mobile-menu clearfix waves-modal-inside'>";
    wp_list_pages(array('title_li' => ''));
    echo "</ul>";
}


// Print logo
//=======================================================
function waves_logo() {    
    global $waves_options;
    $logo = waves_option('logo'.($waves_options['header_color']=='header-dark' ? '_light' : ''));
    echo '<div class="tw-logo">';
        if ( !empty($logo) ) {
            echo '<a class="logo" href="' . esc_url(home_url('/')) . '">';
                echo '<img class="logo-img" src="' . esc_url($logo) . '" alt="' . esc_attr(get_bloginfo('name')) . '"/>';
            echo '</a>';
        } else {
            echo '<h1 class="site-name"><a class="logo" href="' . esc_url(home_url('/')) . '">';
                    bloginfo('name');
            echo '</a></h1>';
        }
    echo '</div>';
}


// Hex To RGB
function waves_hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgb = array($r, $g, $b);
    return implode(",", $rgb); // returns the rgb values separated by commas
}

// ThemeWaves Pagination
function waves_pagination() { 
    $next = get_next_posts_link('<span>'.esc_html__( 'Older Posts', 'ninetysix').'</span><i class="ion-ios-arrow-thin-right"></i>' );
    $prev = get_previous_posts_link('<span>'.esc_html__( 'Newer Posts', 'ninetysix').'</span><i class="ion-ios-arrow-thin-left"></i>');
    if($next || $prev){ ?>
        <div class="tw-pagination clearfix">
            <div class="older"><?php echo ($next); ?></div>
            <div class="newer"><?php echo ($prev); ?></div>
        </div>
    <?php }
}

function waves_infinite($class='') {
    global $wp_query;
    $pages = intval($wp_query->max_num_pages);
    $paged = (get_query_var('paged')) ? intval(get_query_var('paged')) : 1;
    if (empty($pages)) {
        $pages = 1;
    }
    if (1 != $pages) {
        echo '<div class="tw-pagination tw-infinite-scroll '.esc_attr($class).'" data-has-next="' . ($paged === $pages ? 'false' : 'true') . '">';
        echo '<a class="loading" href="#"><i class="fa fa-cog fa-spin"></i></a>';
        echo '<a class="next" href="' . esc_url(get_pagenum_link($paged + 1)) . '"><span>'.esc_html__('View more', 'ninetysix').'</span><i class="ion-ios-arrow-thin-right"></i></a>';
        echo '</div>';
    }
}

function waves_get_image_by_id($id,$url=false,$size='full'){
    $lrg_img=wp_get_attachment_image_src($id,$size);
    $output='';
    $attachment_title='';
    $attachment_title = get_the_title($id);
    if(isset($lrg_img[0])){
        if($url){
            $output.=$lrg_img[0];
        }else{
            $output.='<img alt="'.$attachment_title.'" src="'.esc_url($lrg_img[0]).'" />';
        }
    }
    return $output;
}
function waves_icon($atts,$styled=false,$span=false) {
    $output='';
    if(is_array($atts)&&!empty($atts['icon'])&&!empty($atts[$atts['icon']])&&$atts['icon']!=='none'){
        vc_icon_element_fonts_enqueue($atts['icon']);
        $style = '';
        $class = $atts[$atts['icon']];
        if($atts['icon']==='fi_image'){
            $output.= waves_get_image_by_id($class);
        }elseif($atts['icon']==='fi_text'){
            $output.= $atts[$atts['icon']];
        }else{
            if($styled){
                if(!empty($atts['fi_color'])){
                    $style .='color:'.esc_attr($atts['fi_color']).';';
                }
                if(!empty($atts['fi_bgcolor'])){
                    $style .='background-color:'.esc_attr($atts['fi_bgcolor']).';';
                }
                if(!empty($atts['fi_brcolor'])){
                    $style .='border-color:'.esc_attr($atts['fi_brcolor']).';';
                }
            }
            $output.='<i class="fi '.esc_attr($class).'" style="'.esc_attr($style).'">'.($span ? ('<span></span>') : '').'</i>';
        }
        
    }
    return $output;
}

function waves_portfolio_loop($atts) {
    global $post;
    $padding=30;
    $artClass='not-inited'; 
    $size = waves_metabox('portfolio_size');
    if($size === 'horizontal' || $size === 'large'){
        $atts['width'] = !empty($atts['width'])?(($atts['width']*2) + $padding) : 0;
        if($size === 'large'){
            $atts['height'] = !empty($atts['height'])?(($atts['height']*2) + $padding) : 0;
        }
    } elseif($size === 'vertical'){
        $atts['height'] = !empty($atts['height'])?(($atts['height']*2) + $padding) : 0;
    }       
    $image = waves_image($atts['width'], $atts['height'], true,true);
    if($image){
        $cats = wp_get_post_terms($post->ID, 'portfolio_cat');
        foreach ($cats as $catalog) {
            $artClass .= " category-" . $catalog->slug;
        }
        $content  = '<a href="'.esc_url(get_permalink()).'" title="'.esc_attr(get_the_title()).'"></a>';
        $content .= '<h3 class="portfolio-title"><a href="'.esc_url(get_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>';
        $cats = get_the_term_list( $post->ID, 'portfolio_cat', '', '. ', '' );
        if($cats){$content.='<p class="meta-text"><i class="ion-bookmark"></i>'.$cats.'</p>';}
        
        $output = '<article class="portfolio '.esc_attr($size).' '.esc_attr($artClass).' layout2" data-size="'.esc_attr($size).'">';
            $output .= '<div class="portfolio-thumb">';
                $output .= '<img src="'.esc_url($image['url']).'" alt="'.esc_attr($image['alt']).'"/>';
                $output .= '<div class="image-overlay">';
                        $output .= $content;
                $output .= '</div>';
            $output .= '</div>';
        $output .= '</article>';
        return $output;
    }
}
function waves_portfolio_loop2($atts) {
    global $post;
    $artClass='not-inited';  
    $image = waves_image($atts['width'], $atts['height'], true,true);
    if($image){
        $cats = wp_get_post_terms($post->ID, 'portfolio_cat');
        foreach ($cats as $catalog) {
            $artClass .= " category-" . $catalog->slug;
        }
        $content = '<div class="portfolio-content">';
            $content .= '<h3 class="portfolio-title"><a href="'.esc_url(get_permalink()).'">'.get_the_title().'</a></h3>';
            $cats = get_the_term_list( $post->ID, 'portfolio_cat', '', '. ', '' );
            if($cats){$content.='<p class="meta-text"><i class="ion-bookmark"></i>'.$cats.'</p>';}
        $content .= '</div>';
        
        $output = '<article class="portfolio '.esc_attr($artClass).'">';
            $output .= '<div class="portfolio-thumb">';
                $output .= '<img src="'.esc_url($image['url']).'" alt="'.esc_attr($image['alt']).'"/>';
                $output .= '<div class="image-overlay">';
                        $output .= '<a href="'.esc_url(get_permalink()).'" title="'.esc_attr(get_the_title()).'"></a>';
                $output .= '</div>';
            $output .= '</div>';    
            $output .= $content;
        $output .= '</article>';
        return $output;
    }
}
function waves_related_portfolios() {
    global $post;

    $tags = wp_get_post_terms($post->ID, 'portfolio_cat', array("fields" => "ids"));
    if ($tags) {
        $tag_ids = "";
        foreach ($tags as $tag) {
            $term = get_term($tag, 'portfolio_cat');
            $tag_ids .= $term->slug . ",";
        }

        $content = do_shortcode('[tw_portfolio layout="2" cats="' . $tag_ids . '" count="3" not_in="' . $post->ID . '" column="3" height="' . waves_option('portfolio_height') . '" pagination="none"]');
        if(!empty($content)){
            echo '<div class="related_portfolios">';        
            echo '<h4 class="desc-title">' . waves_option('portfolio_related') . '</h4>';
                echo balanceTags($content);
            echo '</div>';
        }
    }
}



if (!function_exists('waves_image')) {
    function waves_image($size = 'full', $returnURL = false) {
        global $post;
        $attachment = get_post(get_post_thumbnail_id($post->ID));
        if(!empty($attachment)){
            if ($returnURL) {
                $lrg_img = wp_get_attachment_image_src($attachment->ID, $size);
                $url = $lrg_img[0];
                $alt0 = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
                $alt = empty($alt0)?$attachment->post_title:$alt0;
                $img['url'] = $url;
                $img['alt'] = $alt;
                return $img;
            } else {
                return get_the_post_thumbnail($post->ID,$size);
            }
        }
    }
}

if (!function_exists('waves_author')) {
    function waves_author(){ ?>
        <h3 class="widget-title"><?php esc_html_e('Author', 'ninetysix');?></h3>
        <div class="tw-author clearfix">
            <div class="author-image"><?php
                global $post;
                $tw_author_email = get_the_author_meta('email');
                echo get_avatar($tw_author_email, $size = '120'); 
                echo '<span>'.count_user_posts( $post->post_author , 'post' ).'</span>';
                ?>
            </div>
            <h3><?php
                if (is_author()){
                    the_author();
                }else{
                    the_author_posts_link();
                } ?>
            </h3><?php
            echo '<p>';
                $description = get_the_author_meta('description');
                if ($description != '')
                    echo esc_html($description);
                else
                    esc_html_e('The author didnt add any Information to his profile yet', 'ninetysix');
            echo '</p>';
            $socials = get_the_author_meta('user_social');
            if(!empty($socials)){
                echo '<div class="tw-social-icon">';
                $social_links=explode("\n",$socials);
                foreach($social_links as $social_link){echo waves_social_link($social_link);}
                echo '</div>';
            } ?>
        </div><?php
    }
}

if (!function_exists('waves_comment')) {
    function waves_comment($comment, $args, $depth){
        $GLOBALS['comment'] = $comment;
        print '<div class="comment-block">'; ?>
        <div <?php comment_class();?> id="comment-<?php comment_ID(); ?>">
            <div class="comment-author">
                <div class="comment-author-img">
                    <?php echo get_avatar($comment, $size = '70'); ?>
                </div>
                <div class="comment-meta">
                    <span class="entry-date"><?php echo get_comment_date('F j, Y'); ?></span>  
                    <h3 class="comment-author-link">
                        <?php echo get_comment_author_link(); ?>
                    </h3>
                </div>
            </div>
            <div class="comment-body">
                <?php comment_text() ?>
                <p class="comment-replay-link"><?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?><i class="ion-ios-arrow-thin-right"></i></p>
            </div>
        </div><?php
    }
}

if (!function_exists('waves_comment_form')) {
    function waves_comment_form($fields) {
        global $id, $post_id;
        if (null === $post_id)
            $post_id = $id;
        else
            $id = $post_id;

        $commenter = wp_get_current_commenter();
        $req = get_option('require_name_email');
        $aria_req = ( $req ? " aria-required='true'" : '' );

        $fields = array(
            'author' => '<div class="comment-form-author"><p>' .
            '<input id="author" name="author" placeholder="' . esc_html__('Your name here', 'ninetysix') . '" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' />' . '</p>',
            'email' => '<p class="comment-form-email">' .
            '<input id="email" name="email" placeholder="' . esc_html__('Your email here', 'ninetysix') . '" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' />' . '</p>',
            'url' => '</div>',
        );
        return $fields;
    }
    add_filter('comment_form_default_fields', 'waves_comment_form');
}

if (!function_exists('waves_comment_count')) {
    function waves_comment_count() {
        $comment_count = get_comments_number('0', '1', '%');
        return "<a href='" . esc_url(get_comments_link()) . "' title='" . esc_attr($comment_count) . "' class='comment-count'><i class=\"ion-chatbubbles\"></i>" . esc_html($comment_count) . "</a>";
    }
}

function waves_anim($atts){
    $data='';
    if(isset($atts['animation'])&&$atts['animation']!=='none'){
        $atts['animation_delay']=empty($atts['animation_delay'])?'0':str_replace(' ','',$atts['animation_delay']);
        $data.=' data-animation="'.esc_attr($atts['animation']).'" data-animation-delay="'.esc_attr($atts['animation_delay']).'" data-animation-offset="90%"';
    }
    return $data;
}
function waves_item($atts, $class = '', $data = '', $style = '') {
    $animData=waves_anim($atts);
    if(!empty($animData)){$class.=' tw-animate-gen';$data.=$animData;$style.='opacity:0;';wp_enqueue_script( 'waypoints' );wp_enqueue_style('waves-animate', THEME_DIR . '/assets/css/animate.css');}
    if(!empty($atts['custom_id'])){$data.=' id="'.$atts['custom_id'].'"';}
    if(!empty($atts['element_color'])&&trim(rawUrlDecode($atts['element_color']))!==''){$style .='background-color:'.rawUrlDecode($atts['element_color']).';';}
    if(!empty($atts['element_dark'])&&$atts['element_dark']==='true'){$class.= ' dark';}
    if(!empty($atts['element_class'])){$class.= ' ' . $atts['element_class'];}
    if(!empty($atts['custom_class'])){$class.= ' ' . $atts['custom_class'];}
    if(!empty($style)){$data.=' style="'.esc_attr($style).'"';}
    if(!empty($class)){$data.=' class="'.esc_attr($class).'"';}
    $output = '<div'.$data.'>';
    return $output;
}
if (!function_exists('waves_post_share')) {
    function waves_post_share(){
        echo '<div class="entry-share tw-social-color">';
            echo '<span>'.esc_html__('Share', 'ninetysix').'</span>';
            echo '<a class="facebook" href="' . esc_url(get_permalink()) . '" title="Share this">'.esc_html__('Facebook', 'ninetysix').'.</a>';
            echo '<a class="twitter" href="' . esc_url(get_permalink()) . '" title="Tweet" data-title="' . esc_attr(get_the_title()) . '" data-id="'.esc_attr(get_the_id()).'" data-ajaxurl="'.esc_url(home_url('/')).'">'.esc_html__('Twitter', 'ninetysix').'.</a>';
            echo '<a class="pinterest" href="' . esc_url(get_permalink()) . '" title="Pin It">'.esc_html__('Pinterest', 'ninetysix').'.</a>';
        echo '</div>';
    }
}


function waves_social_link($link){
    if(!empty($link)){
        $social = waves_social_name(esc_url($link));
        return '<a title="'.esc_attr($social['name']).'" href="'.esc_url($link).'" class="'.esc_attr($social['name']).'"><i class="'.esc_attr($social['class']).'"></i></a>';
    }    
}

function waves_social_name($url){
    if(strpos($url,'twitter.com')!==false) {$social['name']='twitter';$social['class']='ion-social-twitter';return $social;}
    if(strpos($url,'linkedin.com')!==false){$social['name']='linkedin';$social['class']='ion-social-linkedin';return $social;}
    if(strpos($url,'facebook.com')!==false){$social['name']='facebook';$social['class']='ion-social-facebook';return $social;}
    if(strpos($url,'delicious.com')!==false){$social['name']='delicious';$social['class']='ion-social-delicious';return $social;}
    if(strpos($url,'codepen.io')!==false){$social['name']='codepen';$social['class']='ion-social-codepen';return $social;}
    if(strpos($url,'github.com')!==false){$social['name']='github';$social['class']='ion-social-github';return $social;}
    if(strpos($url,'wordpress.org')!==false||strpos($url,'wordpress.com')!==false){$social['name']='wordpress';$social['class']='ion-social-wordpress';return $social;}
    if(strpos($url,'youtube.com')!==false){$social['name']='youtube';$social['class']='ion-social-youtube-play';return $social;}
    if(strpos($url,'behance.net')!==false){$social['name']='behance';$social['class']='ion-social-behance';return $social;}
    if(strpos($url,'pinterest.com')!==false){$social['name']='pinterest';$social['class']='ion-social-pinterest';return $social;}
    if(strpos($url,'foursquare.com')!==false){$social['name']='foursquare';$social['class']='ion-social-foursquare';return $social;}
    if(strpos($url,'soundcloud.com')!==false){$social['name']='soundcloud';$social['class']='ion-social-soundcloud';return $social;}
    if(strpos($url,'dribbble.com')!==false){$social['name']='dribbble';$social['class']='ion-social-dribbble';return $social;}
    if(strpos($url,'instagram.com')!==false){$social['name']='instagram';$social['class']='ion-social-instagram';return $social;}
    if(strpos($url,'plus.google')!==false){$social['name']='google';$social['class']='ion-social-googleplus';return $social;}
    if(strpos($url,'vimeo.com')!==false){$social['name']='vimeo';$social['class']='ion-social-vimeo';return $social;}
    if(strpos($url,'twitch.tv')!==false){$social['name']='twitch';$social['class']='ion-social-twitch';return $social;}
    if(strpos($url,'tumblr.com')!==false){$social['name']='tumblr';$social['class']='ion-social-tumblr';return $social;}
    if(strpos($url,'trello.com')!==false){$social['name']='trello';$social['class']='ion-social-trello';return $social;}
    if(strpos($url,'spotify.com')!==false){$social['name']='spotify';$social['class']='ion-social-spotify';return $social;}
    if(strpos($url,'rss')!==false){$social['name']='feed';$social['class']='ion-social-rss';return $social;}
    if(strpos($url,'mailto')!==false){$social['name']='mail';$social['class']='ion-email-unread';return $social;}
    if(strpos($url,'tel')!==false){$social['name']='phone';$social['class']='ion-ios-telephone';return $social;}
    
    $social['name']='custom';$social['class']='ion-link';return $social;
}

if (!function_exists('waves_light_or_dark')) {
    function waves_light_or_dark( $color ) {

        if ( FALSE === strpos( $color, '#' ) ){
                // Not a color
                return NULL;
        }

        $hex = str_replace( '#', '', $color );

        $c_r = hexdec( substr( $hex, 0, 2 ) );
        $c_g = hexdec( substr( $hex, 2, 2 ) );
        $c_b = hexdec( substr( $hex, 4, 2 ) );

        $brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

        return ( $brightness > 155 ) ? 'light' : 'dark' ;
    }
}


function waves_like(){
    global $post;
    $likeit = get_post_meta($post->ID, 'post_likeit', true);
    $likecount = empty($likeit) ? '0' : $likeit;
    $likedclass = 'likeit';
    if (isset($_COOKIE['likeit-' . $post->ID])) {
        $likedclass .= ' liked';
    }
    $output = '<span data-ajaxurl="'.esc_url(home_url('/')).'" data-pid="'.esc_attr($post->ID).'" class="'.esc_attr($likedclass).'">';
        $output .= '<i class="icon-heart"></i><span>'.$likecount.(is_single() ? esc_html__(' Likes', 'ninetysix') : ''). '</span>';
    $output .= '</span>';
    return $output;
}


if (isset($_REQUEST['liked_pid'])) {
    $pid = intval($_REQUEST['liked_pid']);
    $liked = get_post_meta($pid, 'post_likeit', true);
    if (!isset($_COOKIE['likeit-' . $pid])) {
        if (empty($liked)) {
            $liked = 1;
        } else {
            $liked = (intval($liked) + 1);
        }
        update_post_meta($pid, 'post_likeit', $liked);
        setcookie('likeit-' . $pid, 1);
    }
    print "<div><div id='post_liked'>$liked</div></div>";
    die;
}
function waves_seen_add(){
    global $post;
    $seen = get_post_meta($post->ID,'post_seen',true);
    $seen = intval($seen)+1;
    update_post_meta($post->ID,'post_seen',$seen);
}
function waves_seen_count(){
    global $post;
    $seen = get_post_meta($post->ID,'post_seen',true);
    return (empty($seen)?0:$seen);    
}
/* Waves Scan Dir */
function waves_scandir($path){
    if(empty($path)||!file_exists($path)){
        $path='';
    }else{
        $path=scandir($path);
        unset($path[0]);
        unset($path[1]);
    }
    return $path;
}
/* Waves Code */
function waves_encode( $value ){
  $func = 'base64' . '_encode';
  return $func( $value );
}
function waves_decode( $value ){
  $func = 'base64' . '_decode';
  return $func( $value );
  
} 

/* Waves Request URL*/
function waves_shop_req(){
    global $wp;
    $output='';
    if(count($_GET)){
        foreach ( $_GET as $key => $value ) {
            if($key!=='shop_load'&&$key!=='_'){
                $output.=(empty($output)?'?':'&').$key.'='.$value;
            }
        }
    }
    return home_url( $wp->request ).$output;
}
/*Waves Filter Add*/
function waves_filter_add($atts){
    $output='';
    $href = waves_shop_req();
    $output .= '<div class="waves-filters-add">';
        $output .= esc_html__('Filter','ninetysix').'<i class="ion-ios-arrow-thin-down"></i>';
    $output .= '</div>';
    $output .= '<div class="waves-filters-add-content"><div class="row">';
        /* Product Sort */
        $output .= '<div class="col-md-3"><ul class="waves-product-sort">';
            $output .= '<li class="title"><h3>'.esc_html__('Sort By','ninetysix').'</h3></li>';
            $orderby=isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
            $show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
            $catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
                'menu_order'	=> __( 'Default', 'ninetysix' ),
                'popularity' 	=> __( 'Popularity', 'ninetysix' ),
                'rating'     	=> __( 'Average rating', 'ninetysix' ),
                'date'       	=> __( 'Newness', 'ninetysix' ),
                'price'      	=> __( 'Price: Low to High', 'ninetysix' ),
                'price-desc'	=> __( 'Price: High to Low', 'ninetysix' )
            ) );
            if(!$show_default_orderby){unset($catalog_orderby_options['menu_order']);}
            if('no'===get_option('woocommerce_enable_review_rating')){unset($catalog_orderby_options['rating']);}

            foreach($catalog_orderby_options as $id=>$name){
                    $output .= '<li'.($orderby==$id?' class="active"':'').'><a href="'.esc_url($orderby==$id?remove_query_arg('orderby',$href):add_query_arg('orderby',$id,$href)).'">'.esc_attr($name).'</a></li>';
            }
        $output .= '</ul></div>';
        /* Price Filter */
        $output .='<div class="col-md-3"><ul class="waves-price-filter">';
            $output .= '<li class="title"><h3>'.esc_html__('Price','ninetysix').'</h3></li>';
            $step=intval($atts['price_step']);
            $min=0;
            $max =intval($atts['price_max']);
            $min_price=isset($_GET['min_price'])?intval($_GET['min_price']):false;
            $max_price=isset($_GET['max_price'])?intval($_GET['max_price']):false;
            $priceCnt=$min;
            do{
                $priceCntMax=$priceCnt+$step;
                $last=$priceCntMax>$max;
                $priceHref=add_query_arg(array('min_price'=>$priceCnt,'max_price'=>$priceCntMax),$href);
                if($priceCnt===$min_price){
                    $priceHref=remove_query_arg('min_price',$priceHref);
                    $priceHref=remove_query_arg('max_price',$priceHref);
                }elseif($last){$priceHref=remove_query_arg('max_price',$priceHref);}
                $output .= '<li'.($priceCnt===$min_price?' class="active"':'').'><a href="'.esc_url($priceHref).'">'.(wc_price($priceCnt).($last?'+':(' - ' .wc_price($priceCntMax)))).'</a></li>';
                $priceCnt=$priceCntMax;
            }while($priceCnt<=$max);
        $output.='</ul></div>';
        /* Color Filter */
        $taxonomy=wc_attribute_taxonomy_name('color');
        $colorFilter=isset($_GET['color_filter'])?$_GET['color_filter']:false;
        if(taxonomy_exists($taxonomy)){
            $output .='<div class="col-md-3"><ul class="waves-color-filter">';
                $output .= '<li class="title"><h3>'.esc_html__('Color','ninetysix').'</h3></li>';
                $terms = get_terms($taxonomy,array('hide_empty'=>'1'));
                foreach($terms as $term){
                    $clr=get_option('taxonomy_'.$term->term_id);
                    $clr=isset($clr['attr_color'])?$clr['attr_color']:'';
                    $colorHref=$term->slug===$colorFilter?remove_query_arg('color_filter',$href):add_query_arg('color_filter',$term->slug,$href);
                    $output .= '<li'.($term->slug===$colorFilter?' class="active"':'').'><a href="'.esc_url($colorHref).'"><i'.($clr==='#fff'||$clr==='#ffffff'?' class="color-white"':'').' style="background-color:'.esc_attr($clr).';"></i>'.$term->name.'</a></li>';
                }
            $output.='</ul></div>';
        }
        /* Tag Filter */
        $tagFilter=isset($_GET['tag_filter'])?$_GET['tag_filter']:false;
        $taxonomy='product_tag';
        if(taxonomy_exists($taxonomy)){
            $output .='<div class="col-md-3"><ul class="waves-tag-filter">';
                $output .= '<li class="title"><h3>'.esc_html__('Tags','ninetysix').'</h3></li>';
                $terms = get_terms($taxonomy,array('hide_empty'=>'1'));
                foreach($terms as $term){
                    $colorHref=$term->slug===$tagFilter?remove_query_arg('tag_filter',$href):add_query_arg('tag_filter',$term->slug,$href);
                    $output .= '<li'.($term->slug===$tagFilter?' class="active"':'').'><a href="'.esc_url($colorHref).'">'.$term->name.'</a></li>';
                }
            $output.='</ul></div>';
        }
    $output.='</div></div>';
    return $output;
}
function waves_rev_check($sliderSC,$echoSlider){
    if(strpos($sliderSC,'[rev_slider')!== false){
        global $wpdb;
        $echoSlider=false;
        $revID=trim(str_replace(array('[rev_slider',']'),'',$sliderSC));
        if(!empty($revID)){if($wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "revslider_sliders WHERE id=".$revID)){$echoSlider=true;}}
    }
    return $echoSlider;
}