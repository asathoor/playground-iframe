<?php
 function render_block_core_image( $attributes, $content, $block ) { if ( false === stripos( $content, '<img' ) ) { return ''; } $processor = new class( $content ) extends WP_HTML_Tag_Processor { public function block_core_image_extract_empty_figcaption_element() { $this->set_bookmark( 'here' ); $opener = $this->bookmarks['here']; while ( $this->next_token() && '#comment' === $this->get_token_name() ) { continue; } if ( 'FIGCAPTION' !== $this->get_tag() || ! $this->is_tag_closer() ) { return false; } $this->set_bookmark( 'here' ); $closer = $this->bookmarks['here']; return new WP_HTML_Span( $opener->start, $closer->start + $closer->length - $opener->start ); } }; if ( ! $processor->next_tag( 'img' ) || ! $processor->get_attribute( 'src' ) ) { return ''; } $has_id_binding = isset( $attributes['metadata']['bindings']['id'] ) && isset( $attributes['id'] ); if ( $has_id_binding ) { $id = $attributes['id']; $image_classnames = $processor->get_attribute( 'class' ); $class_with_binding_value = "wp-image-$id"; if ( is_string( $image_classnames ) && ! str_contains( $image_classnames, $class_with_binding_value ) ) { $image_classnames = preg_replace( '/wp-image-(\d+)/', $class_with_binding_value, $image_classnames ); $processor->set_attribute( 'class', $image_classnames ); } } if ( isset( $attributes['data-id'] ) ) { $data_id = $has_id_binding ? $attributes['id'] : $attributes['data-id']; $processor->set_attribute( 'data-id', $data_id ); } if ( $processor->next_tag( 'FIGCAPTION' ) && empty( $attributes['caption'] ) ) { $figcaption_span = $processor->block_core_image_extract_empty_figcaption_element(); } $link_destination = isset( $attributes['linkDestination'] ) ? $attributes['linkDestination'] : 'none'; $lightbox_settings = block_core_image_get_lightbox_settings( $block->parsed_block ); if ( isset( $lightbox_settings ) && 'none' === $link_destination && isset( $lightbox_settings['enabled'] ) && true === $lightbox_settings['enabled'] ) { wp_enqueue_script_module( '@wordpress/block-library/image/view' ); add_filter( 'render_block_core/image', 'block_core_image_render_lightbox', 15, 2 ); } else { remove_filter( 'render_block_core/image', 'block_core_image_render_lightbox', 15 ); } $output = $processor->get_updated_html(); if ( ! empty( $figcaption_span ) ) { return substr( $output, 0, $figcaption_span->start ) . substr( $output, $figcaption_span->start + $figcaption_span->length ); } return $output; } function block_core_image_get_lightbox_settings( $block ) { if ( isset( $block['attrs']['lightbox'] ) ) { $lightbox_settings = $block['attrs']['lightbox']; } if ( ! isset( $lightbox_settings ) ) { $lightbox_settings = wp_get_global_settings( array( 'lightbox' ), array( 'block_name' => 'core/image' ) ); if ( isset( $lightbox_settings['lightbox'] ) ) { $lightbox_settings = wp_get_global_settings( array( 'lightbox' ) ); } } return $lightbox_settings ?? null; } function block_core_image_render_lightbox( $block_content, $block ) { $processor = new WP_HTML_Tag_Processor( $block_content ); if ( $processor->next_tag( 'figure' ) ) { $processor->set_bookmark( 'figure' ); } if ( ! $processor->next_tag( 'img' ) ) { return $block_content; } $alt = $processor->get_attribute( 'alt' ); $img_uploaded_src = $processor->get_attribute( 'src' ); $img_class_names = $processor->get_attribute( 'class' ); $img_styles = $processor->get_attribute( 'style' ); $img_width = 'none'; $img_height = 'none'; $aria_label = __( 'Enlarge' ); $dialog_aria_label = __( 'Enlarged image' ); if ( isset( $block['attrs']['id'] ) ) { $img_uploaded_src = wp_get_attachment_url( $block['attrs']['id'] ); $img_metadata = wp_get_attachment_metadata( $block['attrs']['id'] ); $img_width = $img_metadata['width'] ?? 'none'; $img_height = $img_metadata['height'] ?? 'none'; } $processor->seek( 'figure' ); $figure_class_names = $processor->get_attribute( 'class' ); $figure_styles = $processor->get_attribute( 'style' ); $unique_image_id = uniqid(); wp_interactivity_state( 'core/image', array( 'metadata' => array( $unique_image_id => array( 'uploadedSrc' => $img_uploaded_src, 'figureClassNames' => $figure_class_names, 'figureStyles' => $figure_styles, 'imgClassNames' => $img_class_names, 'imgStyles' => $img_styles, 'targetWidth' => $img_width, 'targetHeight' => $img_height, 'scaleAttr' => $block['attrs']['scale'] ?? false, 'ariaLabel' => $dialog_aria_label, 'alt' => $alt, ), ), ) ); $processor->add_class( 'wp-lightbox-container' ); $processor->set_attribute( 'data-wp-interactive', 'core/image' ); $processor->set_attribute( 'data-wp-context', wp_json_encode( array( 'imageId' => $unique_image_id, ), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ) ); $processor->set_attribute( 'data-wp-key', $unique_image_id ); $processor->next_tag( 'img' ); $processor->set_attribute( 'data-wp-init', 'callbacks.setButtonStyles' ); $processor->set_attribute( 'data-wp-on--load', 'callbacks.setButtonStyles' ); $processor->set_attribute( 'data-wp-on-window--resize', 'callbacks.setButtonStyles' ); $processor->set_attribute( 'data-wp-on--click', 'actions.showLightbox' ); $processor->set_attribute( 'data-wp-class--hide', 'state.isContentHidden' ); $processor->set_attribute( 'data-wp-class--show', 'state.isContentVisible' ); $body_content = $processor->get_updated_html(); $img = null; preg_match( '/<img[^>]+>/', $body_content, $img ); $button = $img[0] . '<button
			class="lightbox-trigger"
			type="button"
			aria-haspopup="dialog"
			aria-label="' . esc_attr( $aria_label ) . '"
			data-wp-init="callbacks.initTriggerButton"
			data-wp-on--click="actions.showLightbox"
			data-wp-style--right="state.imageButtonRight"
			data-wp-style--top="state.imageButtonTop"
		>
			<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 12 12">
				<path fill="#fff" d="M2 0a2 2 0 0 0-2 2v2h1.5V2a.5.5 0 0 1 .5-.5h2V0H2Zm2 10.5H2a.5.5 0 0 1-.5-.5V8H0v2a2 2 0 0 0 2 2h2v-1.5ZM8 12v-1.5h2a.5.5 0 0 0 .5-.5V8H12v2a2 2 0 0 1-2 2H8Zm2-12a2 2 0 0 1 2 2v2h-1.5V2a.5.5 0 0 0-.5-.5H8V0h2Z" />
			</svg>
		</button>'; $body_content = preg_replace( '/<img[^>]+>/', $button, $body_content ); add_action( 'wp_footer', 'block_core_image_print_lightbox_overlay' ); return $body_content; } function block_core_image_print_lightbox_overlay() { $close_button_label = esc_attr__( 'Close' ); $background_color = '#fff'; $close_button_color = '#000'; if ( wp_theme_has_theme_json() ) { $global_styles_color = wp_get_global_styles( array( 'color' ) ); if ( ! empty( $global_styles_color['background'] ) ) { $background_color = esc_attr( $global_styles_color['background'] ); } if ( ! empty( $global_styles_color['text'] ) ) { $close_button_color = esc_attr( $global_styles_color['text'] ); } } echo <<<HTML
		<div
			class="wp-lightbox-overlay zoom"
			data-wp-interactive="core/image"
			data-wp-router-region='{ "id": "core/image-overlay", "attachTo": "body" }'
			data-wp-key="wp-lightbox-overlay"
			data-wp-context='{}'
			data-wp-bind--role="state.roleAttribute"
			data-wp-bind--aria-label="state.currentImage.ariaLabel"
			data-wp-bind--aria-modal="state.ariaModal"
			data-wp-class--active="state.overlayEnabled"
			data-wp-class--show-closing-animation="state.overlayOpened"
			data-wp-watch="callbacks.setOverlayFocus"
			data-wp-on--keydown="actions.handleKeydown"
			data-wp-on--touchstart="actions.handleTouchStart"
			data-wp-on--touchmove="actions.handleTouchMove"
			data-wp-on--touchend="actions.handleTouchEnd"
			data-wp-on--click="actions.hideLightbox"
			data-wp-on-window--resize="callbacks.setOverlayStyles"
			data-wp-on-window--scroll="actions.handleScroll"
			data-wp-bind--style="state.overlayStyles"
			tabindex="-1"
			>
				<button type="button" aria-label="$close_button_label" style="fill: $close_button_color" class="close-button">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="m13.06 12 6.47-6.47-1.06-1.06L12 10.94 5.53 4.47 4.47 5.53 10.94 12l-6.47 6.47 1.06 1.06L12 13.06l6.47 6.47 1.06-1.06L13.06 12Z"></path></svg>
				</button>
				<div class="lightbox-image-container">
					<figure data-wp-bind--class="state.currentImage.figureClassNames" data-wp-bind--style="state.figureStyles">
						<img data-wp-bind--alt="state.currentImage.alt" data-wp-bind--class="state.currentImage.imgClassNames" data-wp-bind--style="state.imgStyles" data-wp-bind--src="state.currentImage.currentSrc">
					</figure>
				</div>
				<div class="lightbox-image-container">
					<figure data-wp-bind--class="state.currentImage.figureClassNames" data-wp-bind--style="state.figureStyles">
						<img data-wp-bind--alt="state.currentImage.alt" data-wp-bind--class="state.currentImage.imgClassNames" data-wp-bind--style="state.imgStyles" data-wp-bind--src="state.enlargedSrc">
					</figure>
				</div>
				<div class="scrim" style="background-color: $background_color" aria-hidden="true"></div>
		</div>
HTML;
} function register_block_core_image() { register_block_type_from_metadata( __DIR__ . '/image', array( 'render_callback' => 'render_block_core_image', ) ); } add_action( 'init', 'register_block_core_image' ); 