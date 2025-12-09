<?php
 class WP_Widget_Archives extends WP_Widget { public function __construct() { $widget_ops = array( 'classname' => 'widget_archive', 'description' => __( 'A monthly archive of your site&#8217;s Posts.' ), 'customize_selective_refresh' => true, 'show_instance_in_rest' => true, ); parent::__construct( 'archives', __( 'Archives' ), $widget_ops ); } public function widget( $args, $instance ) { $default_title = __( 'Archives' ); $title = ! empty( $instance['title'] ) ? $instance['title'] : $default_title; $title = apply_filters( 'widget_title', $title, $instance, $this->id_base ); $count = ! empty( $instance['count'] ) ? '1' : '0'; $dropdown = ! empty( $instance['dropdown'] ) ? '1' : '0'; echo $args['before_widget']; if ( $title ) { echo $args['before_title'] . $title . $args['after_title']; } if ( $dropdown ) { $dropdown_id = "{$this->id_base}-dropdown-{$this->number}"; ?>
		<label class="screen-reader-text" for="<?php echo esc_attr( $dropdown_id ); ?>"><?php echo $title; ?></label>
		<select id="<?php echo esc_attr( $dropdown_id ); ?>" name="archive-dropdown">
			<?php
 $dropdown_args = apply_filters( 'widget_archives_dropdown_args', array( 'type' => 'monthly', 'format' => 'option', 'show_post_count' => $count, ), $instance ); switch ( $dropdown_args['type'] ) { case 'yearly': $label = __( 'Select Year' ); break; case 'monthly': $label = __( 'Select Month' ); break; case 'daily': $label = __( 'Select Day' ); break; case 'weekly': $label = __( 'Select Week' ); break; default: $label = __( 'Select Post' ); break; } ?>

			<option value=""><?php echo esc_html( $label ); ?></option>
			<?php wp_get_archives( $dropdown_args ); ?>

		</select>

			<?php ob_start(); ?>
<script>
( ( dropdownId ) => {
	const dropdown = document.getElementById( dropdownId );
	function onSelectChange() {
		setTimeout( () => {
			if ( 'escape' === dropdown.dataset.lastkey ) {
				return;
			}
			if ( dropdown.value ) {
				document.location.href = dropdown.value;
			}
		}, 250 );
	}
	function onKeyUp( event ) {
		if ( 'Escape' === event.key ) {
			dropdown.dataset.lastkey = 'escape';
		} else {
			delete dropdown.dataset.lastkey;
		}
	}
	function onClick() {
		delete dropdown.dataset.lastkey;
	}
	dropdown.addEventListener( 'keyup', onKeyUp );
	dropdown.addEventListener( 'click', onClick );
	dropdown.addEventListener( 'change', onSelectChange );
})( <?php echo wp_json_encode( $dropdown_id, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ); ?> );
</script>
			<?php
 wp_print_inline_script_tag( wp_remove_surrounding_empty_script_tags( ob_get_clean() ) . "\n//# sourceURL=" . rawurlencode( __METHOD__ ) ); } else { $format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml'; $format = apply_filters( 'navigation_widgets_format', $format ); if ( 'html5' === $format ) { $title = trim( strip_tags( $title ) ); $aria_label = $title ? $title : $default_title; echo '<nav aria-label="' . esc_attr( $aria_label ) . '">'; } ?>

			<ul>
				<?php
 wp_get_archives( apply_filters( 'widget_archives_args', array( 'type' => 'monthly', 'show_post_count' => $count, ), $instance ) ); ?>
			</ul>

			<?php
 if ( 'html5' === $format ) { echo '</nav>'; } } echo $args['after_widget']; } public function update( $new_instance, $old_instance ) { $instance = $old_instance; $new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'count' => 0, 'dropdown' => '', ) ); $instance['title'] = sanitize_text_field( $new_instance['title'] ); $instance['count'] = $new_instance['count'] ? 1 : 0; $instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0; return $instance; } public function form( $instance ) { $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0, 'dropdown' => '', ) ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $instance['dropdown'] ); ?> id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown' ); ?></label>
			<br />
			<input class="checkbox" type="checkbox"<?php checked( $instance['count'] ); ?> id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts' ); ?></label>
		</p>
		<?php
 } } 