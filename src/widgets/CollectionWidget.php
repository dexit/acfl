<?php

namespace ACFL;

class CollectionWidget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'acfl_collection';
	}

	public function get_title() {
		return __( 'Collection', 'acfl' );
	}

	public function get_icon() {
		return 'fa fa-grip-vertical';
	}

	public function get_categories() {
		return [ 'acfl', 'general' ];
	}

  protected function _register_controls() {

    /*
     * List of Elementor Templates
     */
    $elTemplates = get_posts([
     'posts_per_page' => -1,
     'post_type' => 'elementor_library'
    ]);
    $options = [];
    foreach( $elTemplates as $templatePost ) {
      $options[$templatePost->ID] = $templatePost->post_title;
    }

		/*
		 * List of Post Types Registered
		 */
		$postTypesList = get_post_types( [], 'object' );
		$postTypeOptions = [];
		foreach( $postTypesList as $key => $postType ) {
			$postTypeOptions[ $key ] = $postType->label;
		}

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'acfl' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

    $this->add_control(
			'item_template',
			[
				'label' => __( 'Item Template', 'acfl' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'input_type' => 'select',
        'options' => $options
			]
		);

		$this->add_control(
			'post_type',
			[
				'label' => __( 'Post Type', 'acfl' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'input_type' => 'select',
        'options' => $postTypeOptions
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => __( 'Grid Columns', 'acfl' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'input_type' => 'select',
        'options' => [
					'1fr' => 1,
					'1fr 1fr' => 2,
					'1fr 1fr 1fr' => 3,
					'1fr 1fr 1fr 1fr' => 4
				],
				'selectors' => [
					'{{WRAPPER}} .post-list-grid' => 'grid-template-columns: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'query_section',
			[
				'label' => __( 'Query', 'acfl' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'order_by',
			[
				'label' => __( 'Order By', 'acfl' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => [
					'post_date' => __( 'Date', 'acfl' ),
					'post_title' => __( 'Title', 'acfl' )
				]
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc' => __( 'ASC', 'elementor-pro' ),
					'desc' => __( 'DESC', 'elementor-pro' ),
				]
			]
		);


		$this->add_control(
			'posts_per_page',
			[
				'label' => __( 'Posts Per Page', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => -1,
			]
		);

		$this->end_controls_section();

	}

  protected function render() {

		$settings = $this->get_settings_for_display();
		$postsPerPage = $settings['posts_per_page'];
		$order = $settings['order'];
		$orderBy = $settings['order_by'];
		$postType = $settings['post_type'];

		$posts = query_posts([
			'posts_per_page' => -1,
      'post_type' => $postType,
			'posts_per_page' => $postsPerPage,
			'order' => $order,
			'orderby' => $orderBy
		]);

		global $post;
		$originalPost = $post;

		if( !empty( $posts )) :

			print '<div class="post-list-grid">';

			foreach( $posts as $postItem ) {

				print '<div class="post-list-item">';
				$templatePost = get_post( $settings['item_template'] );

				$post = $postItem;
				setup_postdata($post);

				print \ElementorPro\Plugin::elementor()->frontend->get_builder_content_for_display( $templatePost->ID );
				print '</div>';

			}

			print '</div>'; // close grid

		endif;

		print '<style>
			.post-list-grid {
				display: grid;
				grid-template-columns: 1fr 1fr;
			}
		</style>';

		// reset post so rest of page works normally
		$post = $originalPost;
		wp_reset_query();

	}

}
