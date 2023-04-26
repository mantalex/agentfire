<?php

/**
 * Area Test
 *
 * @link https://agentfire.com
 * @since 1.0.0
 * @package AgentFire\Plugin\Test
 *
 * @wordpress-plugin
 * Plugin Name: AgentFire Test
 * Description: Area Test plugin
 * Plugin URI: https://agentfire.com
 * Version: 1.0.0
 * Author: Author Name
 * License: Proprietary
 * Network: false
 */

namespace AgentFire\Plugin;

define( 'AGENTFIRE_TEST_VERSION', '1.0.0' );
define( 'AGENTFIRE_TEST_PATH', plugin_dir_path( __FILE__ ) );
define( 'AGENTFIRE_TEST_URL', plugin_dir_url( __FILE__ ) );
define( 'AGENTFIRE_SITE_URL', get_site_url( ) );


require AGENTFIRE_TEST_PATH . 'vendor/autoload.php';

Test::getInstance();




