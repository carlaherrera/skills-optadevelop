<?php
/**
 * Form field: Phone with mask for Elementor Addon.
 *
 * @package ElementorAddon
 */

namespace ElementorAddon\FormFields;

use ElementorPro\Modules\Forms\Fields\Field_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Addon_Phone_Mask_Field extends Field_Base {

	public function get_type(): string {
		return 'elementor_addon_phone_mask';
	}

	public function get_name(): string {
		return esc_html__( 'Phone (Mask)', 'elementor-addon' );
	}

	public function render( $item, $item_index, $form ): void {
		$form->add_render_attribute(
			'input' . $item_index,
			[
				'type'        => 'tel',
				'name'        => $item['custom_id'] ?? 'field_' . $item_index,
				'id'          => $form->get_attribute_id( $item ),
				'placeholder' => $item['elementor_addon_phone_placeholder'] ?? '',
				'class'       => 'elementor-addon-phone-mask-input',
				'pattern'     => $item['elementor_addon_phone_pattern'] ?? '[0-9+\-\(\)\s]*',
				'maxlength'   => $item['elementor_addon_phone_maxlength'] ?? 20,
			]
		);

		if ( ! empty( $item['elementor_addon_phone_mask'] ) ) {
			$form->add_render_attribute(
				'input' . $item_index,
				'data-phone-mask',
				$item['elementor_addon_phone_mask']
			);
		}

		if ( ! empty( $item['required'] ) ) {
			$form->add_render_attribute( 'input' . $item_index, 'required', 'required' );
		}

		?>
		<div class="elementor-addon-phone-mask-wrapper">
			<input <?php $form->print_render_attribute_string( 'input' . $item_index ); ?> />
		</div>
		<?php
	}

	public function validation( $record, $ajax_handler ): void {
		$raw_fields = $record->get( 'fields' );
		$field      = null;

		foreach ( $raw_fields as $field_data ) {
			if ( $this->get_type() === $field_data['type'] ) {
				$field = $field_data;
				break;
			}
		}

		if ( ! $field ) {
			return;
		}

		$value       = $field['value'] ?? '';
		$is_required = $field['required'] ?? false;

		if ( empty( $value ) && $is_required ) {
			$ajax_handler->add_error(
				$field['id'],
				esc_html__( 'Phone number is required.', 'elementor-addon' )
			);
			return;
		}

		if ( empty( $value ) ) {
			return;
		}

		$pattern = $field['elementor_addon_phone_pattern'] ?? '[0-9+\-\(\)\s]*';
		$sanitized_value = preg_replace( '/[^0-9+\-\(\)\s]/', '', $value );

		if ( $sanitized_value !== $value ) {
			$ajax_handler->add_error(
				$field['id'],
				esc_html__( 'Phone number contains invalid characters.', 'elementor-addon' )
			);
			return;
		}

		$digits_only = preg_replace( '/[^0-9]/', '', $value );
		$min_digits  = intval( $field['elementor_addon_phone_min_digits'] ?? 7 );
		$max_digits  = intval( $field['elementor_addon_phone_max_digits'] ?? 15 );

		if ( strlen( $digits_only ) < $min_digits ) {
			$ajax_handler->add_error(
				$field['id'],
				sprintf(
					/* translators: %d: minimum number of digits */
					esc_html__( 'Phone number must contain at least %d digits.', 'elementor-addon' ),
					$min_digits
				)
			);
			return;
		}

		if ( strlen( $digits_only ) > $max_digits ) {
			$ajax_handler->add_error(
				$field['id'],
				sprintf(
					/* translators: %d: maximum number of digits */
					esc_html__( 'Phone number must not exceed %d digits.', 'elementor-addon' ),
					$max_digits
				)
			);
		}
	}

	public function update_controls( $widget ): void {
		$elementor = \Elementor\Plugin::$instance;

		$widget->update_control(
			'field_type',
			[
				'options' => array_merge(
					$widget->get_control( 'field_type' )->get_options(),
					[
						'elementor_addon_phone_mask' => esc_html__( 'Phone (Mask)', 'elementor-addon' ),
					]
				),
			]
		);

		$widget->start_injection(
			[
				'at' => 'after',
				'of' => 'field_label',
			]
		);

		$widget->add_control(
			'elementor_addon_phone_mask',
			[
				'label'       => esc_html__( 'Input Mask', 'elementor-addon' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					''             => esc_html__( 'No Mask', 'elementor-addon' ),
					'(999) 999-9999'     => esc_html__( 'US: (999) 999-9999', 'elementor-addon' ),
					'+1 (999) 999-9999'  => esc_html__( 'US with country: +1 (999) 999-9999', 'elementor-addon' ),
					'99 99 99 99'        => esc_html__( 'FR: 99 99 99 99', 'elementor-addon' ),
					'+33 9 99 99 99 99'  => esc_html__( 'FR with country: +33 9 99 99 99 99', 'elementor-addon' ),
					'9999 999 999'       => esc_html__( 'UK: 9999 999 999', 'elementor-addon' ),
					'+44 9999 999 999'   => esc_html__( 'UK with country: +44 9999 999 999', 'elementor-addon' ),
					'9999-9999'          => esc_html__( 'Custom: 9999-9999', 'elementor-addon' ),
				],
				'default'     => '',
				'condition'   => [
					'field_type' => $this->get_type(),
				],
				'label_block' => true,
			]
		);

		$widget->add_control(
			'elementor_addon_phone_placeholder',
			[
				'label'       => esc_html__( 'Placeholder', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( '(999) 999-9999', 'elementor-addon' ),
				'condition'   => [
					'field_type' => $this->get_type(),
				],
				'label_block' => true,
			]
		);

		$widget->add_control(
			'elementor_addon_phone_min_digits',
			[
				'label'       => esc_html__( 'Min Digits', 'elementor-addon' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 7,
				'min'         => 1,
				'max'         => 15,
				'condition'   => [
					'field_type' => $this->get_type(),
				],
			]
		);

		$widget->add_control(
			'elementor_addon_phone_max_digits',
			[
				'label'       => esc_html__( 'Max Digits', 'elementor-addon' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 15,
				'min'         => 5,
				'max'         => 20,
				'condition'   => [
					'field_type' => $this->get_type(),
				],
			]
		);

		$widget->add_control(
			'elementor_addon_phone_maxlength',
			[
				'label'       => esc_html__( 'Max Length', 'elementor-addon' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 20,
				'min'         => 5,
				'max'         => 30,
				'description' => esc_html__( 'Maximum character length for the input field.', 'elementor-addon' ),
				'condition'   => [
					'field_type' => $this->get_type(),
				],
			]
		);

		$widget->add_control(
			'elementor_addon_phone_pattern',
			[
				'label'       => esc_html__( 'Validation Pattern', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '[0-9+\-\(\)\s]*',
				'description' => esc_html__( 'HTML5 pattern attribute for client-side validation.', 'elementor-addon' ),
				'condition'   => [
					'field_type' => $this->get_type(),
				],
				'label_block' => true,
			]
		);

		$widget->end_injection();
	}

	public function get_script_depends(): array {
		return [ 'elementor-addon-phone-mask' ];
	}

	public function get_style_depends(): array {
		return [ 'elementor-addon-phone-mask' ];
	}
}
