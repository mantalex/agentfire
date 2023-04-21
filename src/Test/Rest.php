<?php

declare( strict_types=1 );

namespace AgentFire\Plugin\Test;

use AgentFire\Plugin\Test\Traits\Singleton;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Rest
 * @package AgentFire\Plugin\Test
 */
class Rest {
	use Singleton;

	/**
	 * @var string Endpoint namespace
	 */
	const NAMESPACE = 'agentfire/v1/';

	/**
	 * @var string Route base
	 */
	const REST_BASE = 'test';

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
	}

	/**
	 * Register endpoints
	 */
	public static function registerRoutes() {
		register_rest_route( self::NAMESPACE, self::REST_BASE . '/markers', [
			'show_in_index' => false,
			'methods'       => [ WP_REST_Server::READABLE, WP_REST_Server::CREATABLE ],
			'callback'      => [ self::class, 'markers' ],
			'args'          => [],

		] );
		register_rest_route( self::NAMESPACE, self::REST_BASE . '/tags', [
			'show_in_index' => false,
			'methods'       => [ WP_REST_Server::READABLE, WP_REST_Server::CREATABLE ],
			'callback'      => [ self::class, 'tags' ],
			'args'          => [],

		] );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public static function markers( WP_REST_Request $request ) {

		if($request->get_method()=='GET'){
			try {
				
					$params = $request->get_query_params();

			
					$query = new \WP_Query([
						'post_type' => 'markers',
						'posts_per_page' => -1,
						'post_status' => 'publish'
					  ]);
					  
					if ($query->have_posts()) {
					$posts = $query->posts;
					$map_points = [];
					
					$markers=array(
						'type'=>'FeatureCollection'
					);

					

					foreach($posts as $post ) {
						$map_point = [
						'type' => 'Feature',
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => [floatval(get_post_meta($post->ID, 'longitude', true)), floatval(get_post_meta($post->ID, 'latitude', true))]
						),
						'properties'=> array(
							'title' => $post->post_title,
							'description' => $post->post_title
						)
						//'id' => intval($post->ID),
						//'name' => $post->post_title,
						//'latitude' => floatval(get_post_meta($post->ID, 'latitude', true)),
						//'longitude' => floatval(get_post_meta($post->ID, 'longitude', true)),
						];
						
						array_push($map_points, $map_point);
					}
						$markers['features']=$map_points;
					}
					
					return new WP_REST_Response( $markers );
				
			} catch (Exception $exc) {
			  return new WP_REST_Response( $exc );
			}

		}else if($request->get_method()=='POST'){
		
			try {
				$params = json_decode($request->get_body(), TRUE);

				$post_id = wp_insert_post([
				  'post_type' => 'markers',
				  'post_title' => $params['name'],
				  'post_status' => 'publish',
				  'meta_input' => [
					'latitude' => $params['lat'],
					'longitude' => $params['long']
				  ]
				]);
				$post_body['id'] = $post_id;

				$taxonomy = 'tags';
				$termObj  = get_term_by( 'id', 3, $taxonomy);
				//print_r($termObj);
				
				wp_set_object_terms($post_body['id'], 3, $taxonomy);

				return new WP_REST_Response( $post_body );
			  } catch (Exception $exc) {
				return new WP_REST_Response( $exc );
			  }
		}

		
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public static function tags( WP_REST_Request $request ) {

		if($request->get_method()=='GET'){
			try {
				$tags = get_terms(
					'tags', 
					array(
						'hide_empty' => false,
					)
				);
				return new WP_REST_Response( $tags );
			} catch (Exception $exc) {
				return new WP_REST_Response( $exc );
		  	}

		}else if($request->get_method()=='POST'){
		
			
		}

		
	}

}
