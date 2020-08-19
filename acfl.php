<?php

/**
 *
 * Plugin Name: ACFL
 * Plugin URI: https://acflx.com/
 * Description: ACF powered collections for Elementor.
 * Version: 1.0.0
 * Author: Casey Milne, Eat/Build/Play
 * Author URI: https://eatbuildplay.com/
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 */

namespace ACFL;

define( 'ACFL_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACFL_URL', plugin_dir_url( __FILE__ ) );
define( 'ACFL_VERSION', '1.0.0' );

class Plugin {

  public function __construct() {

		require_once( ACFL_PATH . 'src/Template.php' );

    add_action( 'elementor/widgets/widgets_registered', [ $this, 'initWidgets' ] );
    add_action( 'elementor/elements/categories_registered', [ $this, 'widgetCategories' ] );

		/*
		 * Test ACF local JSON handling
		 */
		add_filter('acf/settings/load_json', function( $paths ) {
			$paths[] = ACFL_PATH . '/src/fields';
			return $paths;
		});

		add_filter('acf/settings/save_json', function( $paths ) {
			return ACFL_PATH . '/src/fields';
		});

  }

  public function widgetCategories( $elements_manager ) {

  	$elements_manager->add_category(
  		'acfl',
  		[
  			'title' => __( 'ACFL', 'acfl' ),
  			'icon' => 'fa fa-grip-vertical',
  		]
  	);

  }

  public function initWidgets() {

    require_once( ACFL_PATH . 'src/widgets/CollectionWidget.php' );
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new CollectionWidget() );

  }

  public function scripts() {

    wp_enqueue_script(
      'shuffle',
      ACFL_URL . '/assets/shuffle.js',
      array( 'acfl' ),
      '1.0.0',
      true
    );

    wp_enqueue_script(
      'acfl',
      ACFL_URL . '/assets/acfl.js',
      array( 'jquery' ),
      '1.0.0',
      true
    );

		wp_enqueue_style(
		  'acfl',
		  ACFL_URL . '/assets/acfl.css',
		  array(),
		  true
		);

  }

}

new Plugin();
