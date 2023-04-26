<?php

declare( strict_types=1 );

namespace AgentFire\Plugin\Test;

use AgentFire\Plugin\Test\Traits\Singleton;


/**
 * Class Rest
 * @package AgentFire\Plugin\Test
 */
class Markers {
	use Singleton;

	public $slug = 'markers';
	public $key = 'markers';

	public function __construct() {
		add_action('init', array($this, 'register'));
	}

	

	public function register() {

		register_post_type($this->key,
			array(
				'labels'      => array(
					'name'          => __('Markers', 'agentfire'),
					'singular_name' => __('Marker', 'agentfire'),
				),
				'public'      => false,
				'has_archive' => false,
				//'show_in_rest'       => true,
			)
		);
		
		register_taxonomy('tags',array('markers'), array(
			'hierarchical' => true,
			'labels' => array(
				'name'          => __('Tags', 'agentfire'),
				'singular_name' => __('Tag', 'agentfire'),
			),
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'subject' ),
		  ));

	}
	

}
