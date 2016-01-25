<?php
/**
 * @package CarRental
 */

class CarRental_Widget_Info extends WP_Widget {

	function __construct() {
		load_plugin_textdomain('carrental');
		parent::__construct('carrental_widget_info', __( 'CarRental Info' , 'carrental'), array('description' => __('Company info.', 'carrental') ));
	}

	function form($instance) {
		$title = (($instance) ? $instance['title'] : __( 'Company info' , 'carrental'));
		include(CARRENTAL__PLUGIN_DIR . 'widget-views/info-form.php');
	}
	
	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function widget($args, $instance) {
		$info = get_option('carrental_company_info');

		echo $args['before_widget'];
		include(CARRENTAL__PLUGIN_DIR . 'widget-views/info-widget.php');
		echo $args['after_widget'];
		
	}
}

function carrental_register_widgets() {
	
	register_widget('CarRental_Widget_Info');
	
}

add_action( 'widgets_init', 'carrental_register_widgets' );
