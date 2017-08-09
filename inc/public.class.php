<?php

/**
 * Printing the data added from the single post editor to the front-end
 * after the post's content using the_content filter.
 *
 * @package DXSources
 * @since v.1.0.0
 */
class DX_Sources_Public {

	public function __construct() {
		$this->add_actions();
		$this->add_filters();
	}

	public function add_actions() {
		add_action( 'wp_head', array(  $this, 'external_link_styles') );
	}

	public function add_filters() {
		add_filter( 'the_content', array( $this, 'display_sources' ) );
	}

	public function external_link_styles() {

		$svg_url = plugins_url( 'img/external.svg', __FILE__ );

		$output = "<style type='text/css'>.dxsources a.external:after {
			background: url( '{$svg_url}' );
			content: ''; display: inline-block;
			height: 1em; width: 1em;
			margin-left: 5px;
		}</style>";

		echo $output;
	}

	public function display_sources( $content ) {
		$sources = $this->generate_sources_html();
		return $content . $sources;
	}

	private function generate_sources_html() {
		global $post;

		// Init any variables to be used.
		$sources = $output = '';
		$sources = get_post_meta( $post->ID, 'dx_sources' );
		$count = 0;

		$output .= "<ul class='dxsources'>";

		if ( 0 < count( $sources ) ) {
			foreach ( ( array ) $sources[0] as $item ) {
				if ( ! empty( $item["name"] ) ) {
					$output .= $this->generate_source_item( $item, $count );
				}
				$count++;
			}
		}

		$output .= '</ul>';

		return $output;
	}

	/**
	 * Generate the <li> markup for each item separately
	 *
	 * $item_data - array containing the data for only one source item.
	 */
	private function generate_source_item( $item_data = null, $count = null ) {
		if ( null === $item_data ) {
			return;
		}

		if ( null === $count ) {
			return;
		}

		// Setup variables for outbound links.
		$class_external = '';
		$target_blank = '';
		$current_site_url = site_url();

		// Return each item as separate <li>
		$output = '';
		$output .= "<li class='dxsources-item'>";
		$output .= "<span class='dxsources-number'>[{$count}]</span> ";

		if ( empty( $item_data["url"] ) ) {
			$output .= "<strong class='dxsources-name'>{$item_data['name']}</strong>";
		} else {
			// See if the link URL starts with the current site's URL
			$is_external = substr( $item_data['url'], 0, strlen( $current_site_url ) ) !== $current_site_url;

			if ( true === $is_external ) {
				$class_external = "external";
				$target_blank = "target='_blank'";
			}

			$output .= "<a {$target_blank} id='dxs-{$count}' class='dxsources-link {$class_external}' href='{$item_data['url']}'>";
			$output .= "<strong class='dxsources-name'>{$item_data['name']}</strong>";
			$output .= "</a> ";
		}

		if ( ! empty( $item_data["description"] ) ) {
			$output .= "<em class='dxsources-description'>{$item_data['description']}</em>";
		}

		$output .= "</li>";

		return $output;

	}

}
