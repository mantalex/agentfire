<?php

declare( strict_types=1 );

namespace AgentFire\Plugin;

use AgentFire\Plugin\Test\Traits\Singleton;
use AgentFire\Plugin\Test\Rest;
use AgentFire\Plugin\Test\Admin;
use AgentFire\Plugin\Test\Template;
use AgentFire\Plugin\Test\Markers;

/**
 * Class Test
 * @package AgentFire\Plugin\Test
 */
class Test {
	use Singleton;

	public function __construct() {
		Rest::getInstance();
		Admin::getInstance();
		Markers::getInstance();
		add_action( 'wp_enqueue_scripts', [ $this, 'agentfire_enqueue_scripts' ] );
		add_shortcode( 'agentfire_test', [ $this, 'agentfiretest' ] );
		
	}

	
	public function agentfire_enqueue_scripts() {
		wp_enqueue_script(
		  'mapboxgl-js',
		  'https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js'
		);
		wp_enqueue_script(
			'bootstrap',
			'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js'
		  );
		
		wp_enqueue_style(
			'mapboxgl-css',
			'https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css'
		);
		wp_enqueue_style( 
			'bootstrap', 
			'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css' 
		);
	}
	
	public function agentfiretest(){
		$atts=array();
		$atts['mapbox_token'] = get_field('mapbox_token', 'option');
		return Template::getInstance()->render( 'main.twig' ,$atts );
	}

	
}

