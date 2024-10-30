<?php
/*
Plugin Name: Moortak Lof Slider News
Plugin URI: http://aliqorbani.com/wp/plugins/moortak-lof-slider-news/
Description: plugin to add a widget for showing posts from a category as a news slider(lofnewsslider).
Author: Ali Qorbani
Version: 1.0.2
Author URI: http://aliqorbani.com/
Text Domain: mlsn
*/

if(!function_exists('mlsn_scripts')){
    function mlsn_scripts(){
        // Register the script like this for a plugin:
        wp_register_script( 'jquery-easing', plugins_url( '/js/jquery.easing.js', __FILE__ ), array( ), '1.3', false  );
        wp_register_script( 'slider', plugins_url( '/js/slidernews.js', __FILE__ ), array( ), '1.0.1', false );
        wp_register_script( 'custom-slidernews', plugins_url( '/js/slidernews-custom.js', __FILE__ ), array( ), '1.0.1', false );
        // For either a plugin or a theme, you can then enqueue the script:
        wp_register_style( 'slider-style', plugins_url( '/css/style.css', __FILE__ ), array(), '1.0.1', 'all' );
        wp_register_style( 'responsive-style', plugins_url( '/css/responsive.css', __FILE__ ), array(), '1.0.1', 'all' );

        wp_enqueue_style( 'slider-style' );
        wp_enqueue_style( 'responsive-style' );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-easing' );
        wp_enqueue_script( 'slider' );
        wp_enqueue_script( 'custom-slidernews' );
    }
    add_action( 'wp_enqueue_scripts', 'mlsn_scripts' );
    add_image_size('mlsn-image-full',650 , 300, true);
    add_image_size('mlsn-image-thumbnail',52 , 52, true);
}

add_action('widgets_init', 'mlsn_posts');

function mlsn_posts()
{
    register_widget('mlsn_widget');
}
class mlsn_widget extends WP_Widget{
    function __construct() {
        parent::__construct(
        // Base ID of your widget
        'mlsn_widget',
        // Widget name will appear in UI
        __('Moortak Lof Slider News widget', 'mlsn'),

        // Widget description
        array( 'description' => __( 'Shows Post from a category in Lof Slider View', 'mlsn' ), )
        );
    }
    public function widget($args, $instance)
    {
        extract($args);
        $title = $instance['title'];
        $post_type = 'all';
        $categories = $instance['categories'];
        $posts = $instance['posts'];
        $images = true;

        //echo $before_widget;
        ?>

        <?php
        $post_types = get_post_types();
        unset($post_types['page'], $post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);

        if($post_type == 'all') {
            $post_type_array = $post_types;
        } else {
            $post_type_array = $post_type;
        }
        ?>

        <?php
        $mlsn_query = new WP_Query(array(
            'showposts' => $posts,
            'post_type' => $post_type_array,
            'cat' => $categories,
        ));
        ?>
            <div id="mlsncontent" class="mlsncontent container-fluid">
            <div class="row">
            <div class="button-previous"><?php _e('Previous','mlsn');?></div>
                <!-- MAIN CONTENT -->
                <div class="lof-main-outer col-lg-8">
                    <div class="row">
                        <ul class="lof-main-wapper">
          		            <?php $i=0; while($mlsn_query->have_posts()): $mlsn_query->the_post(); $i++; ?>
          		            <li>
                                <?php the_post_thumbnail('slider-image-size', array('alt'    =>  the_title_attribute('echo=0'),'calss' =>  'img-responsive', 'title' =>the_title_attribute('echo=0')));?>
                                <div class="lof-main-item-desc">
                                    <?php printf( _e('<h3 class="mini-title"><a href="%s" title="%s">%s</a></h3>','mlsn'),esc_url(get_permalink()), esc_attr(get_the_title()), esc_html(get_the_title())); ?>
                                    <?php the_excerpt();?>
                                </div>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
                <!-- END MAIN CONTENT -->
                <!-- NAVIGATOR -->
                <div class="lof-navigator-outer col-lg-4">
                    <div class="row">
                        <ul class="lof-navigator">
                            <?php $i=0; while($slideshow_posts->have_posts()): $slideshow_posts->the_post(); $i++; ?>
                            <li>
                                <div>
                                    <?php the_post_thumbnail('slider-image-thumbnail', array('alt'    =>  the_title_attribute('echo=0'),'calss' =>  'img-responsive', 'title' =>the_title_attribute('echo=0')));?>
                                    <?php the_title('<h3 class="mini-title">','</h3>'); ?>
                                </div>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
                <div class="button-next"><?php _e('Next','mlsn');?></div>
            </div>
            </div>
        <!-- end of slideshow   -->
        <?php
    }
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['show_excerpt'] = $new_instance['show_excerpt'];
        $instance['title'] = $new_instance['title'];
        $instance['post_type'] = 'all';
        $instance['categories'] = $new_instance['categories'];
        $instance['posts'] = $new_instance['posts'];
        $instance['show_images'] = true;

        return $instance;
    }

    function form($instance)
    {
        $defaults = array('show_excerpt' => null,   'title' =>  _e('Lof Slider News','mlsn'), 'post_type' => 'all', 'categories' => 'all', 'posts' => 7);
        $instance = wp_parse_args((array) $instance, $defaults); ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','mlsn');?>:</label><br />
            <input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />

            <label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Filter by Category','mlsn');?>:</label>
            <select id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" class="widefat categories" style="width:100%;">
                <option value='all' <?php if ('all' == $instance['categories']) echo 'selected="selected"'; ?>>all categories</option>
                <?php $categories = get_categories('hide_empty=0&depth=1&type=post'); ?>
                <?php foreach($categories as $category) { ?>
                <option value='<?php echo $category->term_id; ?>' <?php if ($category->term_id == $instance['categories']) echo 'selected="selected"'; ?>><?php echo $category->cat_name; ?></option>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('posts'); ?>"><?php _e('Number of posts','mlsn');?>:</label>
            <input class="widefat" style="width: 30px;" id="<?php echo $this->get_field_id('posts'); ?>" name="<?php echo $this->get_field_name('posts'); ?>" value="<?php echo $instance['posts']; ?>" />
        </p>

    <?php }
}
?>