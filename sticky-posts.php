<?php
/*
Plugin Name: Ultimate Sticky Posts Widget
Description: A Sticky Post Widget built around what you need to do, with lots of functions and more to come.
Author: Pieter Ferreira
Version: 3.0.0
License: GPLv2
*/

add_action( 'wp_enqueue_scripts', 'sticky_posts_widget_styles' );
function sticky_posts_widget_styles() {
  wp_register_style( 'sticky-posts', plugins_url( 'sticky-posts/sticky.css' ) );
  wp_enqueue_style( 'sticky-posts' );
}

class bsp_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of the widget
'bsp_widget', 

// Widget name that appears in UI
__('Ulitmate Sticky Posts Widget', 'bsp_widget_domain'), 

// Widget description
array( 'description' => __( 'Display your posts the way you want to!, sticky or not.', 'bsp_widget_domain' ), ) 
);
}

// Creating widget front-end
public function widget( $args, $instance ) {

if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
  $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
  $cssid = $instance['cssid'];
  $cssclass = $instance['cssclass'];
    $sticky = $instance['sticky'];
    $from_cat = empty($instance['from_cat']) ? '' : explode(',', $instance['from_cat']);
    $order = $instance['order'];
    $orderby = $instance['orderby'];

      // Ultimate Sticky posts Query
      
      if ($sticky == 'only') {
        $sticky_query = $args = array( 
        'posts_per_page' => $instance['num'], 
        'post__in' => get_option( 'sticky_posts' ),
        'category__in' => $from_cat,
        'orderby' => $instance['orderby'],
    'order' => $instance['order'],
        'ignore_sticky_posts' => 1  
         ); 
      } elseif ($sticky == 'hide') {
      $sticky_query = $args = array( 
        'posts_per_page' => $instance['num'], 
        'post__not_in' => get_option( 'sticky_posts' ),
        'category__in' => $from_cat,
        'orderby' => $instance['orderby'],
    'order' => $instance['order']
         );
      } 
      else { 
        $sticky_query = $args = array( 
        'posts_per_page' => $instance['num'],
        'category__in' => $from_cat,
        'order' => $order,
        'orderby' => $orderby,
        'ignore_sticky_posts' => 1
        
      );
      } 
      
  if(!function_exists('excerpt')) {
      function excerpt($num) {
  $limit = $num+0;
  $show_excerpt = explode(' ', get_the_excerpt(), $limit);
  array_pop($show_excerpt);
  $show_excerpt =
    implode(" ",$show_excerpt)." ... ".
    "";
  echo "<p>".$show_excerpt."</p>";
  }
  }
// This is where you run the code and display the output
        
      $query = new WP_Query( $args );
?>
      <div id="<?php echo $instance["cssid"] ?>" class="<?php echo $instance["cssclass"]  ?>">
      
      <?php
      if ( $title ) {
            echo "<h2>" . $title . "</h2>";
          }
          $featured = new WP_Query($args); 
        if ($featured->have_posts ()): while($featured->have_posts()): $featured->the_post(); 
            if ( is_sticky()) {
              $sticky_post = "stickyone";
            } else {
              $sticky_post = "";
            }
          ?>

        <div class="bsp_container <?php echo $sticky_post; ?>">
        <?php if (current_theme_supports('post-thumbnails') && $instance['show_thumbnail'] && has_post_thumbnail()) : ?>
          <div class="bsp_image">
            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail($instance['thumb_size']); ?>
            <div class="bsp_overlay"></div>
            </a>
          </div>
        <?php endif; ?>
        <?php if ( isset( $instance['show_title'] ) ) : ?>
          <div class="bsp_title">
          <h3>
            <?php if ( isset( $instance['link_title'] ) ) : ?>
              <a href="<?php the_permalink(); ?>">
            <?php endif; ?>
            <?php the_title(); ?>
            <?php if ( isset( $instance['link_title'] ) ) : ?>
            </a>
            <?php endif; ?>
          </h3>
          </div>
        <?php endif; ?>

        <?php if ( $instance['show_excerpt'] ) : ?>
        <div class="bsp_excerpt"><?php excerpt($instance["excerpt_length"]); ?></div>
        <?php endif; ?>
        
        <?php if ( isset( $instance['show_category'] ) ) : ?>
          <div class="bsp_category"><?php the_category(','); ?></div>
        <?php endif; ?>
        
        <?php if ( isset( $instance['show_readmore'] ) ) : ?>
          <div class="bsp_rm"><a href="<?php the_permalink(); ?>"><?php echo $instance[ 'readmore_text' ]; ?></a></div>
        <?php endif; ?>
        
        </div>
        <?php
        endwhile; else:
      endif;
      wp_reset_query();
      ?>
      </div>
      <?php
}
    
// Widget Backend 
public function form( $instance ) {

$instance = wp_parse_args( (array) $instance, array(
        'title' => __('Ultimate Sticky Posts', 'bsp'),
        'excerpt_length' => '15',
        'readmore_text' => 'Continue Reading...',
        'cssid' => 'your-ID-class',
        'cssclass' => 'your-CLASS',
        'num' => '5',
        'order' => 'DESC',
        'orderby' => 'date',
        'show_title' => true
        
      ) );
$title = $instance[ 'title' ];
$show_title = $instance[ 'title' ];
$link_title = $instance[ 'link_title' ];
$show_excerpt = $instance[ 'show_excerpt' ];
$excerpt_length = $instance[ 'excerpt_length' ];
$show_category = $instance[ 'show_category' ];
$show_readmore = $instance[ 'show_readmore' ];
$readmore_text = $instance[ 'readmore_text' ];
$num = $instance[ 'num' ];
$cssid = $instance[ 'cssid' ];
$cssclass = $instance[ 'cssclass' ];
$sticky = $instance['sticky'];
$from_cat = $instance['from_cat'];
$order = $instance['order'];
$orderby = $instance['orderby'];
$thumb_size = $instance['thumb_size'];
$show_thumbnail = $instance['show_thumbnail'];
// Let's turn $types, $cats, and $tags into an array if they are set
      if (!empty($from_cat)) $from_cat = explode(',', $from_cat);

      // Count number of categories for select box sizing
      $cat_list = get_categories( 'hide_empty=1' );
      if ($cat_list) {
        foreach ($cat_list as $cat) {
          $cat_ar[] = $cat;
        }
        $c = count($cat_ar);
        if($c > 6) { $c = 6; }
      } else {
        $c = 3;
      }

     



// Widget admin form
?>
<!**************************************************** Post Display Options ****************************************************************************>
<div class="bsp-seg"><h1 class="bsp_seg">Post Display Options</h1></div>
<!**************************************************** End Post Display Options *************************************************************************>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<p>
<input type="checkbox" class="show_title" id="<?php echo $this->get_field_id("show_title"); ?>" name="<?php echo $this->get_field_name("show_title"); ?>"<?php checked( (bool) $instance["show_title"], true ); ?> />
<label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e( 'Show Titles:' ); ?></label> 
</p>

<p>
<input type="checkbox" class="link_title" id="<?php echo $this->get_field_id("link_title"); ?>" name="<?php echo $this->get_field_name("link_title"); ?>"<?php checked( (bool) $instance["link_title"], true ); ?> />
<label for="<?php echo $this->get_field_id( 'link_title' ); ?>"><?php _e( 'Link Titles:' ); ?></label> 
</p>

<p>
<input type="checkbox" class="show_excerpt" id="<?php echo $this->get_field_id("show_excerpt"); ?>" name="<?php echo $this->get_field_name("show_excerpt"); ?>"<?php checked( (bool) $instance["show_excerpt"], true ); ?> />
<label for="<?php echo $this->get_field_id("show_excerpt"); ?>"><?php _e( 'Show post excerpt' ); ?></label>
</p>

<p>
<label for="<?php echo $this->get_field_id("excerpt_length"); ?>"><?php _e( 'Excerpt length' ); ?></label>
<input style="text-align: center;" type="text" id="<?php echo $this->get_field_id("excerpt_length"); ?>" name="<?php echo $this->get_field_name("excerpt_length"); ?>" value="<?php echo $instance["excerpt_length"]; ?>" size="3" />
</p>

<p>
<input type="checkbox" class="show_category" id="<?php echo $this->get_field_id("show_category"); ?>" name="<?php echo $this->get_field_name("show_category"); ?>"<?php checked( (bool) $instance["show_category"], true ); ?> />
<label for="<?php echo $this->get_field_id( 'show_category' ); ?>"><?php _e( 'Show Category:' ); ?></label> 
</p>

<p>
<input type="checkbox" class="show_readmore" id="<?php echo $this->get_field_id("show_readmore"); ?>" name="<?php echo $this->get_field_name("show_readmore"); ?>"<?php checked( (bool) $instance["show_readmore"], true ); ?> />
<label for="<?php echo $this->get_field_id( 'show_readmore' ); ?>"><?php _e( 'Show Readmore:' ); ?></label> 
</p>

<p>
<label for="<?php echo $this->get_field_id( 'readmore_text' ); ?>"><?php _e( 'Custom Readmore Text' ); ?></label> 
<input class="readmore_text" id="<?php echo $this->get_field_id( 'readmore_text' ); ?>" name="<?php echo $this->get_field_name( 'readmore_text' ); ?>" type="text" value="<?php echo esc_attr( $readmore_text ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'num' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label> 
<input class="num" id="<?php echo $this->get_field_id( 'num' ); ?>" name="<?php echo $this->get_field_name( 'num' ); ?>" type="text" value="<?php echo esc_attr( $num ); ?>" />
</p>


<?php if ( function_exists('the_post_thumbnail') && current_theme_supports( 'post-thumbnails' ) ) : ?>

          <?php $sizes = get_intermediate_image_sizes(); ?>

          <p>
            <input class="checkbox" id="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'show_thumbnail' ); ?>" type="checkbox" <?php checked( (bool) $show_thumbnail, true ); ?> />

            <label for="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>"><?php _e( 'Show thumbnail', 'bsp' ); ?></label>
          </p>

          <p<?php if (!$show_thumbnail) echo ' style="display:none;"'; ?>>
            <select id="<?php echo $this->get_field_id('thumb_size'); ?>" name="<?php echo $this->get_field_name('thumb_size'); ?>" class="widefat">
              <?php foreach ($sizes as $size) : ?>
                <option value="<?php echo $size; ?>"<?php if ($thumb_size == $size) echo ' selected'; ?>><?php echo $size; ?></option>
              <?php endforeach; ?>
              <option value="full"<?php if ($thumb_size == $size) echo ' selected'; ?>><?php _e('full'); ?></option>
            </select>
          </p>

        <?php endif; ?>
<!**************************************************** Post Ordering Options ****************************************************************************>
<div class="bsp-seg"><h1 class="bsp_seg">Post Ordering Options</h1></div>
<!**************************************************** End Post Ordering Options *************************************************************************>
<p>
<label for="<?php echo $this->get_field_id('sticky'); ?>"><?php _e( 'Posts To Display', 'bsp' ); ?>:</label>
<select name="<?php echo $this->get_field_name('sticky'); ?>" id="<?php echo $this->get_field_id('sticky'); ?>" class="widefat">
<option value="show"<?php if( $sticky === 'show') echo ' selected'; ?>><?php _e('Show All Posts', 'bsp'); ?></option>
<option value="hide"<?php if( $sticky == 'hide') echo ' selected'; ?>><?php _e('Hide Sticky Posts', 'bsp'); ?></option>
<option value="only"<?php if( $sticky == 'only') echo ' selected'; ?>><?php _e('Show Only Sticky Posts', 'bsp'); ?></option>
</select>
</p>

<p>
<label for="<?php echo $this->get_field_id('from_cat'); ?>"><?php _e( 'Show From Categories', 'bsp' ); ?>:</label>
<select name="<?php echo $this->get_field_name('from_cat'); ?>[]" id="<?php echo $this->get_field_id('from_cat'); ?>" class="widefat" style="height: auto;" size="<?php echo $c ?>" multiple>
<option value="" <?php if (empty($from_cat)) echo 'selected="selected"'; ?>><?php _e('&ndash; Show All &ndash;') ?></option>
<?php
$categories = get_categories( 'hide_empty=0' );
foreach ($categories as $category ) { ?>
<option value="<?php echo $category->term_id; ?>" <?php if(is_array($from_cat) && in_array($category->term_id, $from_cat)) echo 'selected="selected"'; ?>><?php echo $category->cat_name;?></option>
<?php } ?>
</select>
</p>
        
        <p>
          <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order by', 'bsp'); ?>:</label>
          <select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
            <option value="date"<?php if( $orderby == 'date') echo ' selected'; ?>><?php _e('Published Date', 'bsp'); ?></option>
            <option value="title"<?php if( $orderby == 'title') echo ' selected'; ?>><?php _e('Title', 'bsp'); ?></option>
            <option value="comment_count"<?php if( $orderby == 'comment_count') echo ' selected'; ?>><?php _e('Comment Count', 'bsp'); ?></option>
            <option value="rand"<?php if( $orderby == 'rand') echo ' selected'; ?>><?php _e('Random'); ?></option>
          </select>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'bsp'); ?>:</label>
          <select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" class="widefat">
            <option value="DESC"<?php if( $order == 'DESC') echo ' selected'; ?>><?php _e('Descending', 'bsp'); ?></option>
            <option value="ASC"<?php if( $order == 'ASC') echo ' selected'; ?>><?php _e('Ascending', 'bsp'); ?></option>
          </select>
        </p>
<!**************************************************** Post Formatting Options ****************************************************************************>
<div class="bsp-seg"><h1 class="bsp_seg">Post Formatting Options</h1></div>
<!**************************************************** End Post Formatting Options *************************************************************************>
<p>
<label for="<?php echo $this->get_field_id( 'cssid' ); ?>"><?php _e( 'CSS ID:' ); ?></label> 
<input class="classid" id="<?php echo $this->get_field_id( 'cssid' ); ?>" name="<?php echo $this->get_field_name( 'cssid' ); ?>" type="text" value="<?php echo esc_attr( $cssid ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'cssclass' ); ?>"><?php _e( 'CSS Class:' ); ?></label> 
<input class="classhere" id="<?php echo $this->get_field_id( 'cssclass' ); ?>" name="<?php echo $this->get_field_name( 'cssclass' ); ?>" type="text" value="<?php echo esc_attr( $cssclass ); ?>" />
</p>
<!**************************************************** End ALL Options *************************************************************************>

      <script>

          jQuery(document).ready(function($){

            var show_thumbnail = $("#<?php echo $this->get_field_id( 'show_thumbnail' ); ?>");
            var thumb_size_wrap = $("#<?php echo $this->get_field_id( 'thumb_size' ); ?>").parents('p');


            // Toggle excerpt length on click
            show_thumbnail.click(function(){
              thumb_size_wrap.toggle('fast');
            });

          });

        </script>
<style>
.bsp-seg {
    float: left;
    position: relative;
    width: 100%;
}

h1.bsp_seg {
    border-bottom: 1px dotted #4d4d4d;
    color: #7e7e7e !important;
    font-size: 1.5em !important;
    font-weight: normal !important;
    margin: 0 0 10px;
    padding: 10px 0 !important;
}
</style>
<?php 
}
  
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
$instance['show_title'] = $new_instance['show_title'];
$instance['link_title'] = $new_instance['link_title'];
$instance['show_excerpt'] = $new_instance['show_excerpt'];
$instance['excerpt_length'] = $new_instance['excerpt_length'];
$instance['show_category'] = $new_instance['show_category'];
$instance['show_readmore'] = $new_instance['show_readmore'];
$instance['readmore_text'] = $new_instance['readmore_text'];
$instance['num'] = ( ! empty( $new_instance['num'] ) ) ? strip_tags( $new_instance['num'] ) : '';
$instance['cssid'] = strip_tags( $new_instance['cssid']);
$instance['cssclass'] = strip_tags( $new_instance['cssclass']);
$instance['sticky'] = $new_instance['sticky'];
$instance['from_cat'] = (isset( $new_instance['from_cat'] )) ? implode(',', (array) $new_instance['from_cat']) : '';
$instance['order'] = $new_instance['order'];
$instance['orderby'] = $new_instance['orderby'];
$instance['show_thumbnail'] = isset( $new_instance['show_thumbnail'] );
$instance['thumb_size'] = strip_tags( $new_instance['thumb_size'] );


return $instance;
}
} // Class bsp_widget ends here

// Register and load the widget
function bsp_load_widget() {
  register_widget( 'bsp_widget' );
}
add_action( 'widgets_init', 'bsp_load_widget' );
