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
			'jQuery',
			AGENTFIRE_TEST_URL .'bower_components/jQuery/dist/jquery.min.js'
		);
		wp_enqueue_script(
			'bootstrap',
			AGENTFIRE_TEST_URL .'bower_components/bootstrap/dist/js/bootstrap.bundle.min.js'
		  );
		wp_enqueue_script(
			'select2',
			AGENTFIRE_TEST_URL .'bower_components/select2/dist/js/select2.min.js'
		  );
		
		wp_enqueue_style(
			'mapboxgl-css',
			'https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css'
		);
		wp_enqueue_style( 
			'bootstrap', 
			AGENTFIRE_TEST_URL .'bower_components/bootstrap/dist/css/bootstrap.min.css' 
		);
		wp_enqueue_style( 
			'select2', 
			AGENTFIRE_TEST_URL .'bower_components/select2/dist/css/select2.min.css' 
		);
		wp_enqueue_style( 
			'main-style', 
			AGENTFIRE_TEST_URL .'assets/css/style.css' 
		);
	}

	public function agentfiretest(){
		$atts=array();
		$atts['mapbox_token'] = get_field('mapbox_token', 'option');
		if ( is_user_logged_in() ) {
			$atts['is_logged'] = true;
			$current_user = wp_get_current_user();
			$atts['user_id'] =$current_user->ID;
			$atts['plugin_url'] =AGENTFIRE_TEST_URL;
			$atts['site_url'] =AGENTFIRE_SITE_URL;
			
		}else{
			$atts['is_logged'] = false;
			$atts['plugin_url'] =AGENTFIRE_TEST_URL;
			$atts['site_url'] =AGENTFIRE_SITE_URL;
		}
		return Template::getInstance()->render( 'main.twig' ,$atts );
	}

	
}

