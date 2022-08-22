<?php

declare( strict_types=1 );

namespace AgentFire\Plugin\Test;

use AgentFire\Plugin\Test\Traits\Singleton;


/**
 * Class Rest
 * @package AgentFire\Plugin\Test
 */
class Admin {
	use Singleton;

	public $slug = 'test-settings';

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'menu' ] );
		add_action( 'acf/init', [ $this, 'init' ] );
	}

	/**
	 * Add settings page
	 */
	public function menu() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			// acf_add_options_page( [ ... ] );
		}
	}

	/**
	 * Set settings page fields
	 */
	public function init() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {
			acf_add_local_field_group( [
				'key'                   => 'test_settings',
				'title'                 => 'Test Settings',
				'fields'                => [
					// ...
				],
				'location'              => [
					[
						[
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => $this->slug,
						],
					],
				],
				'menu_order'            => 10,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => 1,
				'description'           => '',
			] );
		}
	}

}
