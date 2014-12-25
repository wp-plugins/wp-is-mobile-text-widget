<?php
/*
Plugin Name: WP Is Mobile Text Widget
Plugin URI: https://github.com/thingsym/wp-is-mobile-text-widget
Description: This WordPress plugin adds text widget that switched display text using wp_is_mobile() function whether the device is mobile or not.
Version: 1.0.1
Author: thingsym
Author URI: http://www.thingslabo.com/
License: GPLv2 or later
Text Domain: wp-is-mobile-text-widget
*/

/*
Copyright 2014 thingsym (email : thingsym@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

if ( class_exists( 'WP_Is_Mobile_Text_Widget' ) ) {
	add_action( 'widgets_init', 'wp_is_mobile_text_widget_load_widgets' );
}

function wp_is_mobile_text_widget_load_widgets() {
	register_widget( 'WP_Is_Mobile_Text_Widget' );
}

/**
 * WP Is Mobile Text Widget class
 *
 * @since 1.0.0
 */

class WP_Is_Mobile_Text_Widget extends WP_Widget {

	public function __construct() {
		load_plugin_textdomain( 'wp-is-mobile-text-widget', false, 'wp-is-mobile-text-widget/languages' );

		$widget_ops = array( 'classname' => 'widget_is_mobile_text', 'description' => __( 'Arbitrary text or HTML.', 'wp-is-mobile-text-widget' ) );
		$control_ops = array( 'width' => 400, 'height' => 350 );
		parent::__construct( 'wp_is_mobile_text', __( 'WP Is Mobile Text', 'wp-is-mobile-text-widget' ), $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		$is_mobile_text = apply_filters( 'widget_is_mobile_text', empty( $instance['is_mobile_text'] ) ? '' : $instance['is_mobile_text'], $instance );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?>
		<?php if ( function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) : ?>
			<div class="textwidget"><?php echo ! empty( $instance['filter'] ) ? wpautop( $is_mobile_text ) : $is_mobile_text; ?></div>
		<?php else : ?>
			<div class="textwidget"><?php echo ! empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php endif; ?>
		<?php
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
			$instance['is_mobile_text'] = $new_instance['is_mobile_text'];
		}
		else {
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) );
			$instance['is_mobile_text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['is_mobile_text'] ) ) );
		}
		$instance['filter'] = isset( $new_instance['filter'] );
		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'is_mobile_text' => '', 'is_mobile_text' => '' ) );
		$title = strip_tags( $instance['title'] );
		$text = esc_textarea( $instance['text'] );
		$is_mobile_text = esc_textarea( $instance['is_mobile_text'] );
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-is-mobile-text-widget' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Text:', 'wp-is-mobile-text-widget' ); ?></label>
		<textarea class="widefat" rows="10" cols="20" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo $text; ?></textarea></p>

		<p><label for="<?php echo $this->get_field_id( 'is_mobile_text' ); ?>"><?php _e( 'WP Is Mobile Text:', 'wp-is-mobile-text-widget' ); ?></label>
		<textarea class="widefat" rows="10" cols="20" id="<?php echo $this->get_field_id( 'is_mobile_text' ); ?>" name="<?php echo $this->get_field_name( 'is_mobile_text' ); ?>"><?php echo $is_mobile_text; ?></textarea></p>

		<p><input id="<?php echo $this->get_field_id( 'filter' ); ?>" name="<?php echo $this->get_field_name( 'filter' ); ?>" type="checkbox" <?php checked( isset( $instance['filter'] ) ? $instance['filter'] : 0 ); ?> />&nbsp;<label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php _e( 'Automatically add paragraphs', 'wp-is-mobile-text-widget' ); ?></label></p>
<?php
	}
}
