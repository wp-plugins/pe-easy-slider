<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Plugin Name: PE Easy Slider
 * Plugin URI: http://pixelemu.com
 * Description: Simple Horizontal Slider for Posts
 * Version: 1.0.0
 * Author: pixelemu.com
 * Author URI: http://www.pixelemu.com
 * Text Domain: pe-easy-slider
 * License: GPLv2 or later
 */


if(!class_exists('PE_Recent_Posts_Horizontal')){
    class PE_Recent_Posts_Horizontal extends WP_Widget {

        function PE_Recent_Posts_Horizontal(){
            $options_widget = array( 'classname' => 'PE_Recent_Posts_Horizontal', 'description' => __('Easy Posts Slider.', 'pe-easy-slider'));
            $this->WP_Widget( 'PE_Recent_Posts_Horizontal', __('PE Easy Slider', 'pe-easy-slider'), $options_widget );
        }

        function widget($args,  $setup)
        {
            extract($args);
			$count_posts = wp_count_posts('post');
			$number_of_posts = $setup['number_of_posts'];
			if ($number_of_posts > $count_posts->publish){
				$number_of_posts = $count_posts->publish;
			}
			$unique_id = $this->id;
			$order_posts = $setup['order_posts'];
			$order_direction = $setup['order_direction'];
			$array_loop_values= array(
					                'relation'=> 'AND',
						                array(
						                    'key' => '_thumbnail_id',
						                    'compare' => 'EXISTS'
						                )
									);
			
			$loop_check = new WP_Query(array('post_type' => 'post',
									   'posts_per_page' => ''.$number_of_posts.'', 
									   'orderby' => ''.$order_posts.'', 
									   'order' => ''.$order_direction.'',
						               'meta_query' => $array_loop_values
										)
									); 
			$counter_check = 0;
			while ( $loop_check->have_posts() ) : $loop_check->the_post();
				$counter_check++;				
			endwhile;

			if ($number_of_posts > $counter_check){
				$number_of_posts = $counter_check;
			}
		
			$navigation_way = $setup['navigation_way'];
			
            $title_widget = apply_filters('widget_title', $setup['title']);

            if ( empty($title_widget) ){
            	$title_widget = false;
            }
            echo $before_widget;
            echo '<h2 class="widgettitle">';
            echo $title_widget;
            echo '</h2>';
			$image_height = $setup['image_height'];
			$posts_in_row = $setup['posts_in_row'];
			$slide_width = 100 / $posts_in_row;
			if ($navigation_way == 1){
				$bullets_on = 'bullets-on';
			}
			$image_size = $setup['image_size'];
			if ($image_height == 0){
				$height_slide = 'wp-size';
			} else {
				$height_slide = 'custom-size';
			}
			$category_id = $setup['category_id'];
			
			$loop = new WP_Query(array('post_type' => 'post',
									   'posts_per_page' => ''.$number_of_posts.'', 
									   'orderby' => ''.$order_posts.'', 
									   'order' => ''.$order_direction.'',
						               'meta_query' => $array_loop_values,
						               'cat' => $category_id
										)
									); 
			?>
			<?php if ($loop->have_posts()){ ?>
			<div id="myCarouselSlider<?php echo $unique_id; ?>" class="slider-carousel-outer carousel slide <?php echo $image_height_class.' '.$bullets_on; ?>">
				<div class="carousel-inner <?php echo $height_slide; ?>">
						<?php 
								$counter = 0;
								if ($image_height == 0){
									$image_height = 'auto';
								} else {
									$image_height = $image_height.'px';
								}
								while ( $loop->have_posts() ) : $loop->the_post(); ?>
								<?php
								$post_url = get_permalink();
								$post_title = get_the_title();
								$counter++;
								if ($posts_in_row == 1){ 
									if ($counter == 1){ ?>
										<div class="item active">
									<?php } else { ?>
										<div class="item">
									<?php }?>
									
								<?php } else{
									if (($counter % $posts_in_row == 1)){
											if ($counter == 1){ ?>
												<div class="item active">
											<?php } else { ?>
												<div class="item">
											<?php } ?>
									<?php }
								}
										$final_link = $post_url;
									 ?>
									<ul class="thumbnails" style="width: <?php echo $slide_width; ?>%;">
										<li>
											<div class="thumbnail">
												<?php 
													echo '<a class="post-link" title="'.$post_title.'" href="'.$final_link.'">';
													echo the_post_thumbnail($image_size, array(
														'alt'   => $post_title,
														'style' => 'height: '.$image_height
													));
													echo '<span class="play">&nbsp;</span>';
													echo '<span class="post-title fadeInUp animated"><span class="post-title-in">'.$post_title.'</span></span></a>';
												 ?>
											</div>	
										</li>
									</ul>
									<?php if (($counter % $posts_in_row) == 0){ ?>
										</div>
									<?php } ?> 	
						<?php endwhile; ?>
						<?php if ((($counter % $posts_in_row) != 0) && ($counter >= $posts_in_row)){ ?>
							</div>
						<?php } ?> 
						<?php wp_reset_query(); ?>
			</div>
			<?php 
			if($counter < $posts_in_row){ ?>
			</div>	
			<?php } ?>
	        <?php $counter2 = 0; ?>
	        <?php if ($navigation_way == 1){ ?>
		        <ol class="carousel-indicators">
		        	<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
		        		<?php $counter2++; ?>
		        	<?php if (($counter2 % $posts_in_row == 1) || $posts_in_row == 1){
		        		if ($counter2 == 1){ ?>
		        			<li data-target="#myCarouselSlider<?php echo $unique_id; ?>" data-slide-to="0" class="active"></li>
						<?php } else { ?>
							<li data-target="#myCarouselSlider<?php echo $unique_id; ?>" data-slide-to="<?php echo ($counter2 -1)/$posts_in_row; ?>"></li>
						<?php } ?>	
					<?php } ?>
		            <?php endwhile; ?>
		        </ol>    	
	        <?php } else if ($navigation_way == 2) { ?>
				<a class="carousel-control left" href="#myCarouselSlider<?php echo $unique_id; ?>" data-slide="prev">&nbsp;</a>
				<a class="carousel-control right" href="#myCarouselSlider<?php echo $unique_id; ?>" data-slide="next">&nbsp;</a>
			    <button class="playButton btn btn-default btn-xs" type="button"></button>
			    <button class="pauseButton btn btn-default btn-xs" type="button"></button>
	        <?php } ?>
		</div>
		<?php } ?>
		<?php
            echo $after_widget;
        }

        //Admin Form

        function form($setup)
        {
            $setup = wp_parse_args( (array) $setup, array('title' => __('PE Easy Slider', 'pe-easy-slider'),
                'number_of_posts' => '12',
                'order_posts' => 'Date',
                'order_direction' => 'DESC',
                'navigation_way' => '1',
                'posts_in_row' => '4',
                'image_size' => '4',
                'image_height' => '0',
                'category_id' => '' ) );
				
			$title_widget= esc_attr($setup['title']);
			$number_of_posts = $setup['number_of_posts'];
            $order_posts = $setup['order_posts'];
			$image_height = $setup['image_height'];
			$posts_in_row = $setup['posts_in_row'];
			$image_size = $setup['image_size'];
			$category_id = $setup['category_id'];
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'pe-easy-slider'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title_widget; ?>" />
            </p>
            <p>
            	<label for="<?php echo $this->get_field_id('category_id'); ?>"><?php _e('Category (empty categories are not displayed)', 'pe-easy-slider'); ?></label>
				<select name="<?php echo $this->get_field_name('category_id'); ?>" id="<?php echo $this->get_field_id('category_id'); ?>">
				 <option value=""><?php _e('All Categories', 'pe-easy-slider'); ?></option> 
				 <?php 
				    $values = array(
				      'orderby' => 'name',
				      'order' => 'ASC',
				      'taxonomy' => 'category'
				     );
				  $categories = get_categories($values); 
				  foreach ($categories as $category) { ?>
				    <option value="<?php echo $category->cat_ID; ?>"<?php selected( $setup['category_id'], $category->cat_ID ); ?>><?php echo $category->cat_name; ?></option>	
				  	<?php } ?>
				</select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('posts_in_row'); ?>"><?php _e('Number of slides in row', 'pe-easy-slider'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('posts_in_row'); ?>" name="<?php echo $this->get_field_name('posts_in_row'); ?>" type="text" value="<?php echo $posts_in_row; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('number_of_posts'); ?>"><?php _e('Number of posts', 'pe-easy-slider'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('number_of_posts'); ?>" name="<?php echo $this->get_field_name('number_of_posts'); ?>" type="text" value="<?php echo $number_of_posts; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('order_direction'); ?>"><?php _e('Order Direction', 'pe-easy-slider'); ?></label>
                <select name="<?php echo $this->get_field_name('order_direction'); ?>" id="<?php echo $this->get_field_id('order_direction'); ?>">
                    <option value="ASC"<?php selected( $setup['order_direction'], 'ASC' ); ?>><?php _e('ASC', 'pe-easy-slider'); ?></option>
                    <option value="DESC"<?php selected( $setup['order_direction'], 'DESC' ); ?>><?php _e('DESC', 'pe-easy-slider'); ?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('order_posts'); ?>"><?php _e('Ordering', 'pe-easy-slider'); ?></label>
                <select name="<?php echo $this->get_field_name('order_posts'); ?>" id="<?php echo $this->get_field_id('order_posts'); ?>">
                    <option value="date"<?php selected( $setup['order_posts'], 'date' ); ?>><?php _e('Date', 'pe-easy-slider'); ?></option>
                    <option value="title"<?php selected( $setup['order_posts'], 'title' ); ?>><?php _e('Title', 'pe-easy-slider'); ?></option>
                    <option value="comment_count"<?php selected( $setup['order_posts'], 'comment_count' ); ?>><?php _e('Most commented', 'pe-easy-slider'); ?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('navigation_way'); ?>"><?php _e('Navigation', 'pe-easy-slider'); ?></label>
                <select name="<?php echo $this->get_field_name('navigation_way'); ?>" id="<?php echo $this->get_field_id('navigation_way'); ?>">
                    <option value="1"<?php selected( $setup['navigation_way'], '1' ); ?>><?php _e('Bullets', 'pe-easy-slider'); ?></option>
                    <option value="2"<?php selected( $setup['navigation_way'], '2' ); ?>><?php _e('Arrows', 'pe-easy-slider'); ?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image Size', 'pe-easy-slider'); ?></label>
                <select name="<?php echo $this->get_field_name('image_size'); ?>" id="<?php echo $this->get_field_id('image_size'); ?>">
                    <option value="thumbnail"<?php selected( $setup['image_size'], 'thumbnail' ); ?>><?php _e('thumbnail', 'pe-easy-slider'); ?></option>
                    <option value="medium"<?php selected( $setup['image_size'], 'medium' ); ?>><?php _e('medium', 'pe-easy-slider'); ?></option>
                    <option value="large"<?php selected( $setup['image_size'], 'large' ); ?>><?php _e('large', 'pe-easy-slider'); ?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image_height'); ?>"><?php _e('Image height in px ( 0 - disabled )', 'pe-easy-slider'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('image_height'); ?>" name="<?php echo $this->get_field_name('image_height'); ?>" type="text" value="<?php echo $image_height; ?>" />
            </p>
        <?php
        }

        //Update widget

        function update($new_setup, $old_setup)
        {
            $setup=$old_setup;
            $setup['title'] = strip_tags($new_setup['title']);
			$setup['posts_in_row']  = $new_setup['posts_in_row'];
			$setup['number_of_posts']  = $new_setup['number_of_posts'];
			$setup['order_posts']  = $new_setup['order_posts'];
			$setup['order_direction']  = $new_setup['order_direction'];
			$setup['navigation_way']  = $new_setup['navigation_way'];
			$setup['image_height']  = strip_tags($new_setup['image_height']);
			$setup['image_size']  = $new_setup['image_size'];
			$setup['category_id']  = $new_setup['category_id'];
            return $setup;
        }
    }
}

//add CSS
function pe_recent_posts_horizontal_css() {
	wp_enqueue_style( 'bootstrap.min', plugins_url().'/pe-easy-slider/css/bootstrap.min.css' ); 
	wp_enqueue_style( 'animate', plugins_url().'/pe-easy-slider/css/animate.css' ); 
	wp_enqueue_style( 'pe-easy-slider', plugins_url().'/pe-easy-slider/css/pe-easy-slider.css' ); 
}
add_action( 'wp_enqueue_scripts', 'pe_recent_posts_horizontal_css' );

//add JS
function pe_recent_posts_horizontal_js()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'bootstrap.min', plugins_url() . '/pe-easy-slider/js/bootstrap.min.js', array('jquery'), '3.2.0', false );
	wp_enqueue_script( 'pe-easy-slider', plugins_url() . '/pe-easy-slider/js/pe-easy-slider.js', array('jquery'), '1.0.0', false );
}
add_action( 'wp_enqueue_scripts', 'pe_recent_posts_horizontal_js' );

//load widget
add_action('widgets_init',
     create_function('', 'return register_widget("PE_Recent_Posts_Horizontal");')
);
?>
