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
		register_rest_route( self::NAMESPACE, self::REST_BASE . '/markers-search', [
			'show_in_index' => false,
			'methods'       => [ WP_REST_Server::READABLE ],
			'callback'      => [ self::class, 'markers_search' ],
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
					
					$args=array(
						'post_type' => 'markers',
						'posts_per_page' => -1,
						'post_status' => 'publish'
					);

					if($params){
						if(isset($params['tags'])){
							if($params['tags']!=''){
							$sel_tags=explode(',',$params['tags']);
							$args['tax_query']=array(
												array(
													'taxonomy' => 'tags',
													'field' => 'term_id',
													'terms' => $sel_tags,
												)
											);
							}
						}else if(isset($params['user_id'])){
							$args['author']=$params['user_id'];
							
						}else if(isset($params['marker'])){
							$args['post__in']=array($params['marker']);
							
						}
					}
					
					$markers=array();


					$query = new \WP_Query($args);
					  
					if ($query->have_posts()) {
					$posts = $query->posts;
					$map_points = [];
					
					$markers=array(
						'type'=>'FeatureCollection'
					);
					
					

					foreach($posts as $post ) {
						//print_r($post);
							$tags=get_the_terms($post->ID, 'tags');
							$map_point = [
							'type' => 'Feature',
							'geometry' => array(
								'type' => 'Point',
								'coordinates' => [floatval(get_post_meta($post->ID, 'longitude', true)), floatval(get_post_meta($post->ID, 'latitude', true))]
							),
							'properties'=> array(
								'title' => $post->post_title,
								'description' => $post->post_title,
								'user_id' => get_post_meta($post->ID, 'user_id', true),
								'tags'=> $tags
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
				//print_r($params['tags']);
				$taxonomy = 'tags';
				$tags=array();
				foreach ($params['tags'] as $key => $tag) {
					$termObj  = get_term_by( 'id', $tag, $taxonomy);
					if($termObj->term_id){
						$tags[]=$termObj->term_id;
					}else{
						$tags[]=$tag;
					}
					
				}

			
				$post_id = wp_insert_post([
				  'post_type' => 'markers',
				  'post_title' => $params['name'],
				  'post_author' => $params['user_id'],
				  'post_status' => 'publish',
				  'meta_input' => [
					'latitude' => $params['lat'],
					'longitude' => $params['long'],
					'user_id' => $params['user_id']
				  ]
				]);
				$post_body['id'] = $post_id;

				$taxonomy = 'tags';
				wp_set_object_terms($post_body['id'],  $tags, $taxonomy);
				

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

				$tags_list=array();
				foreach($tags as $tag ) {
					$new_tag = [
					'id' => $tag->term_id,
					'text' => $tag->name,
					
					];
					
					array_push($tags_list, $new_tag);
				}
				$result["results"]=$tags_list;

				return new WP_REST_Response( $result );
			} catch (Exception $exc) {
				return new WP_REST_Response( $exc );
		  	}

		}else if($request->get_method()=='POST'){
		
			
		}

		
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public static function markers_search( WP_REST_Request $request ) {

		if($request->get_method()=='GET'){
			try {

				$params = $request->get_query_params();
				
				$args = array("post_type" => "markers");
				if($params){
						if($params['search']){
						$args['s']=$params['search'];
					}
				}
				$query = new \WP_Query($args);
					  
				if ($query->have_posts()) {
					$posts = $query->posts;
					$posts_list=array();
					foreach($posts as $post ) {
						$new_post = [
						'id' => $post->ID,
						'text' => $post->post_title,
						
						];
						
						array_push($posts_list, $new_post);
					}
				}
				$result["results"]=$posts_list;
				

				return new WP_REST_Response( $result );
			} catch (Exception $exc) {
				return new WP_REST_Response( $exc );
		  	}

		}

		
	}

}
