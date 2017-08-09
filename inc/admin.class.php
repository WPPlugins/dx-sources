<?php

/**
 * Create the single post dashboard view's metaboxes for adding
 * the list of sources for each post. The metabox is "repeatable" field
 * so the user can add unlimited ammount of sources without refreshing the page.
 * 
 * @package DXSources
 * @since  v1.0.0
 */
class DX_Sources_Admin {

	public function __construct() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Register all required actions for the plugin.
	 */
	private function add_actions() {
		add_action( 'admin_head', array( $this, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'metabox_register' ) );
		add_action( 'save_post', array( $this, 'metabox_save' ), 10, 3 );
	}

	/**
	 * Set the filters like add the sources after the post.
	 */
	private function add_filters() {}

	/**
	 * The types of styling the user can choose from for the sources listing.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'dx-sources', plugins_url( 'css/style.admin.css', __FILE__ ) );
	}

	/**
	 * Register the custom post meta box(es) for filling the sources.
	 */
	public function metabox_register() {
		add_meta_box( 'dx-sources-metabox', __( 'Add the article\'s sources', 'dxsources' ), array( $this, 'metabox_render' ), 'post' );
	}

	/**
	 * Render the input fields for the meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function metabox_render( $post ) {

		// Add nonce for security and authentication.
		wp_nonce_field( 'dx_sources_nonce_action', 'dx_sources_nonce' );

		$this->generate_fields();
	}

	/**
	 * Save meta box content.
	 *
	 * @param int $post_id Post ID
	 */
	public function metabox_save( $post_id, $post ) {
		$nonce_name = isset( $_POST['dx_sources_nonce'] ) ? $_POST['dx_sources_nonce'] : '';
		$nonce_action = 'dx_sources_nonce_action';

		// Check if nonce is set.
		if ( ! isset( $nonce_name ) ) {
			return;
		}

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Grab the latest input fields data.
		$fields = $_POST['dx_sources'];

		array_walk_recursive( $fields , array( $this, "array_walk_esc_html" ) );

		// And finaly update the fields
		if ( ! empty( $fields ) ) {
			update_post_meta( $post_id, 'dx_sources', $fields );
		}

	}

	/**
	 * Escape the fields data
	 */
	public function array_walk_esc_html( &$item, $key ) {
		$item = esc_html( $item );
	}

	/**
	 * Markup for the existing fields. Only the name is required and if the field
	 * is not filled notice will appear on clicking "save post".
	 */
	private function print_field_elements( $count, $array = null ) {

		// Used for animating the add fields action
		$display = '';

		// If null its called from the jQuery
		if ( null === $array ) {
			$url = '';
			$name = '';
			$description = '';
			$display = "style='display: none;'";
		} else {
			$url = empty( $array['url'] ) ? '' : $array['url'];
			$name = empty( $array['name'] ) ? '' : $array['name'];
			$description = empty( $array['description'] ) ? '' : $array['description'];
		}

		// Final output markup
		$html = '';

		$html .= "<div class='dxsources-row' {$display}>";
		$html .= 	"<div class='dxsources-cell'><label class='dxsources-label' for='dx_sources[{$count}][name]'>[{$count}] Source Name <span class='required'>*</span></label>";
		$html .= 	"<input class='dxsources-input widefat' type='text' value='{$name}' name='dx_sources[{$count}][name]' required /></div>";

		$html .= 	"<div class='dxsources-cell'><label class='dxsources-label' for='dx_sources[{$count}][url]'>Link URL</label>";
		$html .= 	"<input class='dxsources-input widefat' type='text' value='{$url}' name='dx_sources[{$count}][url]' /></div>";

		$html .= 	"<div class='dxsources-cell'><label class='dxsources-label' for='dx_sources[{$count}][description]'>Additional information</label>";
		$html .= 	"<input class='dxsources-input widefat' type='text' value='{$description}' name='dx_sources[{$count}][description]' /></div>";

		$html .= 	"<div class='dxsources-cell delete'><span class='dxsources-delete-item'>Delete</span></div>";

		$html .= "</div>";

		return $html;
	}

	/**
	 * Print all existing and add new fields.
	 */
	private function generate_fields() {

		// Grab the $post object. Will need the ID.
		global $post;

		// Get saved meta as an array.
		$fields = get_post_meta( $post->ID, 'dx_sources', true );

		// Keep track on how many fields were added so far and generate unique names.
		$count = 0;

		// Build the existing fields.
		if ( 0 < count( $fields ) ) {
			foreach ( ( array ) $fields as $fields_item ) {
				if ( ! empty( $fields_item["name"] ) ) {
					echo $this->print_field_elements( $count, $fields_item );
					$count++;
				}
			}
		}

		// The div where fields will be appended to.
		echo "<div class='fields-wrapper'></div>";

		// This will print the initial "add" button to the post page.
		echo "<a href='#' class='button dxsources-add'>Add new source</a>";

		// Add the jQuery needed to add the new fields. php variables will be used.
		?>
		<script>
			var $ = jQuery.noConflict();

			$(document).ready(function() {
				var count = <?php echo $count; ?>;

				// Add new field button.
				$('.dxsources-add').on("click", function() {

					<?php
					// The output must be all on one line for the JS to print it properly.
					$fields_stripped = implode( '', explode( "\n", $this->print_field_elements( 'count' ) ) ); ?>
					console.log( count );

					// Needed for nicer animation
					var inputFields = "<?php echo $fields_stripped ?>".replace( /count/g, count );


					// Append the input fields.
					$(inputFields).appendTo( $( '.fields-wrapper' ) ).slideDown();

					count++;
					return false;
				});


				// Action for deleting the fields.
				$( '#dx-sources-metabox' ).on( "click", ".dxsources-delete-item", function( event ) {
					$( event.target ).closest( '.dxsources-row' ).slideUp( function() {
						$(this).remove();
					} );
				} );

			});
		</script>
		<?php
	}

}
