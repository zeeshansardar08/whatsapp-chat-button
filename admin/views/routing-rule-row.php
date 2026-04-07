<?php
/**
 * Routing rule row view.
 *
 * @package WhatsApp_Chat_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="wacb-routing-rule-row" data-wacb-rule-row>
	<td class="wacb-routing-rule-cell">
		<input
			type="text"
			class="regular-text"
			name="<?php echo esc_attr( $field_name_prefix ); ?>[label]"
			value="<?php echo esc_attr( (string) $rule['label'] ); ?>"
			placeholder="<?php echo esc_attr__( 'Rule label', 'whatsapp-chat-button' ); ?>"
		/>
	</td>
	<td class="wacb-routing-rule-cell">
		<select name="<?php echo esc_attr( $field_name_prefix ); ?>[rule_type]" data-wacb-rule-type>
			<?php foreach ( $rule_type_options as $rule_type => $rule_type_label ) : ?>
				<option value="<?php echo esc_attr( $rule_type ); ?>" <?php selected( $rule['rule_type'], $rule_type ); ?>>
					<?php echo esc_html( $rule_type_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</td>
	<td class="wacb-routing-rule-cell">
		<div class="wacb-routing-target-wrap<?php echo 'page' === $rule['rule_type'] ? ' is-active' : ''; ?>" data-wacb-target-wrap="page">
			<select name="<?php echo esc_attr( $field_name_prefix ); ?>[target_id_page]">
				<option value="0"><?php echo esc_html__( 'Select a page', 'whatsapp-chat-button' ); ?></option>
				<?php foreach ( $page_options as $page_option ) : ?>
					<option value="<?php echo esc_attr( (string) $page_option['id'] ); ?>" <?php selected( 'page' === $rule['rule_type'] ? (int) $rule['target_id'] : 0, $page_option['id'] ); ?>>
						<?php echo esc_html( $page_option['title'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="wacb-routing-target-wrap<?php echo 'post' === $rule['rule_type'] ? ' is-active' : ''; ?>" data-wacb-target-wrap="post">
			<select name="<?php echo esc_attr( $field_name_prefix ); ?>[target_id_post]">
				<option value="0"><?php echo esc_html__( 'Select a post', 'whatsapp-chat-button' ); ?></option>
				<?php foreach ( $post_options as $post_option ) : ?>
					<option value="<?php echo esc_attr( (string) $post_option['id'] ); ?>" <?php selected( 'post' === $rule['rule_type'] ? (int) $rule['target_id'] : 0, $post_option['id'] ); ?>>
						<?php echo esc_html( $post_option['title'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="wacb-routing-target-wrap<?php echo 'category' === $rule['rule_type'] ? ' is-active' : ''; ?>" data-wacb-target-wrap="category">
			<select name="<?php echo esc_attr( $field_name_prefix ); ?>[target_id_category]">
				<option value="0"><?php echo esc_html__( 'Select a category', 'whatsapp-chat-button' ); ?></option>
				<?php foreach ( $category_options as $category_option ) : ?>
					<option value="<?php echo esc_attr( (string) $category_option['id'] ); ?>" <?php selected( 'category' === $rule['rule_type'] ? (int) $rule['target_id'] : 0, $category_option['id'] ); ?>>
						<?php echo esc_html( $category_option['title'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</td>
	<td class="wacb-routing-rule-cell">
		<input
			type="text"
			class="regular-text"
			inputmode="numeric"
			name="<?php echo esc_attr( $field_name_prefix ); ?>[number]"
			value="<?php echo esc_attr( (string) $rule['number'] ); ?>"
			placeholder="15551234567"
		/>
	</td>
	<td class="wacb-routing-rule-cell wacb-routing-rule-actions">
		<button type="button" class="button button-secondary" data-wacb-remove-rule>
			<?php echo esc_html__( 'Remove', 'whatsapp-chat-button' ); ?>
		</button>
	</td>
</tr>
