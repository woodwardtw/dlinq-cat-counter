<?php 
/*
Plugin Name: DLINQ Category Counter
Plugin URI:  https://github.com/
Description: For counting posts by year and category. Use the [postcount] shortcode.
Version:     1.0
Author:      DLINQ
Author URI:  https://dlinq.middcreate.net
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('wp_enqueue_scripts', 'prefix_load_scripts');

function prefix_load_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_style( 'dlinq-cat-counter-css', plugin_dir_url( __FILE__) . 'css/prefix-main.css');
}



function dlinq_cat_counter(){
   if(current_user_can('administrator')){
       $this_year = date("Y"); //get current year
      $years = range($this_year, dlinq_get_first_post_year());//create array of years from earliest post year to now
      $content = '';
        foreach ($years as $key => $year) {
           // code...
            $content .= dlinq_by_year($year);
        }
      return $content;
   } else {
      return "<p>Please login to access this information.</p>";
   }
  
}

function dlinq_by_year($year){
   $html = '';
   $cat_args = array(
      'taxonomy' => 'category', 
         'orderby' => 'name',
         'order' => 'ASC',
   );
   $cat_ids = get_terms($cat_args); 
   $html = "<h2>{$year}</h2>";
   $html .= "<ul>"; 
   foreach ($cat_ids as $key => $cat_id) {
       // code...
      $args = array(
         'post_type' => 'post',
         'cat'  => $cat_id->term_id,
         'date_query' => array(
               array(
                   'year' => $year,
               ),
            )      
      );
      $year_query = new WP_Query($args);
      $cat_name = $cat_id->name;
      $cat_slug = $cat_id->slug;
      $count = $year_query->found_posts;
      $html .= "<li data-count='{$count}' data-name='{$cat_slug}'>{$cat_name} - {$count}</li>";
   }
   $html .= "</ul>";
   return $html;
}


add_shortcode( 'postcount', 'dlinq_cat_counter' );

//get the first year you have for posts
function dlinq_get_first_post_year(){
    $post = get_posts(array(
     'post_type' => 'post',
     'order_by' => 'publish_date',
     'order' => 'ASC',
     'posts_per_page' => 1,
   ));
   return $first_year = substr($post[0]->post_date, 0, 4);
}


//LOGGER -- like frogger but more useful

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

  //print("<pre>".print_r($a,true)."</pre>");
