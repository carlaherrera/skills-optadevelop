<?php
/**
 * Custom control example for Elementor Addon.
 *
 * @package ElementorAddon
 */

namespace ElementorAddon\Controls;

use Elementor\Base_Data_Control;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Addon_My_Control extends Base_Data_Control {

	public function get_type(): string {
		return 'my-control';
	}

	public function enqueue(): void {
		wp_register_style(
			'elementor-addon-my-control',
			plugins_url( 'assets/css/controls/my-control.css', ELEMENTOR_ADDON_PLUGIN_FILE ),
			[],
			ELEMENTOR_ADDON_VERSION
		);

		wp_register_script(
			'elementor-addon-my-control',
			plugins_url( 'assets/js/controls/my-control.js', ELEMENTOR_ADDON_PLUGIN_FILE ),
			[ 'jquery' ],
			ELEMENTOR_ADDON_VERSION,
			true
		);

		wp_enqueue_style( 'elementor-addon-my-control' );
		wp_enqueue_script( 'elementor-addon-my-control' );
	}

	protected function get_default_settings(): array {
		return [
			'label_block'  => true,
			'show_label'   => true,
			'options'      => [],
			'multiple'     => false,
			'min'          => 0,
			'max'          => 100,
			'placeholder'  => '',
			'description'  => '',
			'show_preview' => true,
		];
	}

	public function get_default_value(): array {
		return [
			'value'   => '',
			'label'   => '',
			'options' => [],
		];
	}

	protected function content_template(): void {
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) { #>
				<label class="elementor-control-title" for="<?php echo $this->get_control_uid( 'my-control-input' ); ?>">
					{{{ data.label }}}
				</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-addon-my-control-wrapper">
				<div class="elementor-addon-my-control-inner">
					<div class="elementor-addon-my-control-field">
						<input
							type="text"
							id="<?php echo $this->get_control_uid( 'my-control-input' ); ?>"
							class="elementor-addon-my-control-input"
							placeholder="{{ data.placeholder }}"
							data-setting="value"
						/>
						<button type="button" class="elementor-addon-my-control-btn elementor-button elementor-button-default">
							<span class="elementor-addon-my-control-btn-label">Choose</span>
							<span class="eicon-control-arrows-horizontal" aria-hidden="true"></span>
						</button>
					</div>
					<# if ( data.description ) { #>
						<div class="elementor-addon-my-control-description elementor-control-field-description">
							{{{ data.description }}}
						</div>
					<# } #>
					<# if ( data.show_preview && data.value ) { #>
						<div class="elementor-addon-my-control-preview">
							<div class="elementor-addon-my-control-preview-inner" data-value="{{ data.value }}">
								<span class="elementor-addon-my-control-preview-text">{{{ data.value }}}</span>
							</div>
						</div>
					<# } #>
				</div>
			</div>
		</div>
		<?php
	}

	public function get_value( array $control, array $settings ): array {
		$value = parent::get_value( $control, $settings );

		if ( ! is_array( $value ) ) {
			return $this->get_default_value();
		}

		return array_merge( $this->get_default_value(), $value );
	}

	public function sanitize_value( array $value, array $control ): array {
		if ( ! is_array( $value ) ) {
			$value = [];
		}

		if ( isset( $value['value'] ) ) {
			$value['value'] = sanitize_text_field( $value['value'] );
		}

		if ( isset( $value['label'] ) ) {
			$value['label'] = sanitize_text_field( $value['label'] );
		}

		return $value;
	}
}
