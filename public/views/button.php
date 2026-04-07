<?php
/**
 * Frontend button view.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wrapper_classes = array(
	'wacb-chat-button-wrap',
	'wacb-chat-button-wrap--' . $button_data['position'],
);

$button_classes = array(
	'wacb-chat-button',
);

$button_styles = sprintf(
	'--wacb-button-background:%1$s;--wacb-button-text-color:%2$s;',
	$button_data['background_color'],
	$button_data['text_color']
);

if ( $button_data['delay'] > 0 ) {
	$button_classes[] = 'wacb-chat-button--delayed';
	$button_styles   .= '--wacb-delay:' . absint( $button_data['delay'] ) . 's;';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
	<a
		class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>"
		href="<?php echo esc_url( $button_data['chat_url'] ); ?>"
		target="_blank"
		rel="noopener noreferrer"
		aria-label="<?php echo esc_attr( $button_data['aria_label'] ); ?>"
		data-wacb-track="<?php echo esc_attr( $button_data['tracking_data_label'] ); ?>"
		data-wacb-page-url="<?php echo esc_attr( $button_data['page_url'] ); ?>"
		style="<?php echo esc_attr( $button_styles ); ?>"
	>
		<span class="wacb-chat-button__text"><?php echo esc_html( $button_data['button_text'] ); ?></span>
	</a>
</div>
