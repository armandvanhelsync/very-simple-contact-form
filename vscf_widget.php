<?php

class vscf_widget extends WP_Widget {

	// Constructor 
	function vscf_widget() {
		$widget_ops = array( 'classname' => 'vscf_sidebar', 'description' => __('Very Simple Contact Form sidebar widget', 'verysimple') );
		parent::__construct( 'vscf-widget', __('Very Simple Contact Form', 'verysimple'), $widget_ops );
	}

	// Display widget with contact form in frontend 
	function widget( $args, $instance ) {

		echo $args['before_widget']; 

		if ( !empty( $instance['title'] ) ) { 
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title']; 
		} 

		echo do_shortcode( '[contact-widget]' );

		echo $args['after_widget']; 
	}

	// Update widget 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags from title to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	// Set widget and title in dashboard
	function form( $instance ) {

		$defaults = array( 'title' => __('Very Simple Contact Form', 'verysimple') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Widget Title:', 'verysimple'); ?></label>
		<input class="widefat" type='text' maxlength='50' id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

	<?php
	}
}

?>