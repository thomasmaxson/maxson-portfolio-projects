<?php
/**
 * Plugin-specific Admin Permalink(s)
 * 
 * @author      Thomas Maxson
 * @category    Class
 * @package     Maxson_Portfolio_Projects/includes/admin
 * @license     GPL-2.0+
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_Permalinks' ) )
{ 
	class Maxson_Portfolio_Projects_Admin_Permalinks { 

		/**
		 * Post Type
		 */

		const POST_TYPE = 'portfolio_project';


		/**
		 * Construct
		 */

		public function __construct()
		{ 
			add_action( 'admin_init', array( &$this, 'register' ) );
			add_action( 'admin_init', array( &$this, 'save' ) );
		}


		/**
		 * Determine site URL prefix
		 * 
		 * @return      string
		 */

		public function get_blog_prefix()
		{ 
			$blog_prefix = '';
			$structure   = get_option( 'permalink_structure' );

			if( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( $structure, '/blog/' ) )
			{
				$blog_prefix = '/blog/';

			} // endif

			return $blog_prefix;
		}


		/**
		 * Add slash to slug prefix
		 * 
		 * @return      string
		 */

		public function prepend_slash( $slug )
		{ 
		//	if( ! empty( $slug ) )
		//		$slug = '/' . $slug;

			return $slug;
		}


		/**
		 * Add plugin-specific permalink settings
		 * 
		 * @return      void
		 */

		public function register()
		{ 
			$taxonomies = get_object_taxonomies( array( self::POST_TYPE ) );

			if( ! empty( $taxonomies ) )
			{ 
				$tab     = 'permalink';
				$section = 'optional';

				register_setting( $tab, 'maxson_portfolio_permalink_project' );

				add_settings_section( "portfolio_projects_{$tab}_{$section}", 
					__( 'Portfolio Project Settings', 'maxson' ), array( $this, 'permalink_options' ), $tab );

				foreach( $taxonomies as $taxonomy )
				{ 
					$taxonomy_obj = get_taxonomy( $taxonomy );

					if( ! $taxonomy_obj->public )
					{
						continue;

					} // endif

					register_setting( $tab, "maxson_portfolio_permalink_{$taxonomy}" );

					add_settings_field( "maxson_portfolio_setting_{$tab}_{$taxonomy}_slug", 
						sprintf( __( '%1$s base', 'maxson' ), ucfirst( strtolower( $taxonomy_obj->labels->name ) ) ), 
						array( $this, 'taxonomy_slug_field' ), $tab, $section, array( 
							'taxonomy'    => $taxonomy, 
							'placeholder' => str_replace( '_', '-', $taxonomy )
						) );

				} // endforeach
			} // endif
		}
	

		/** 
		 * Plugin-specific category slug permalink options
		 * 
		 * @return      string
		 */

		public function taxonomy_slug_field( $args = array() )
		{ 
			$taxonomy    = $args['taxonomy'];
			$placeholder = $args['placeholder'];

			$blog_prefix = $this->get_blog_prefix();

			$name  = "maxson_portfolio_permalink_{$taxonomy}_slug";
			$value = maxson_portfolio_get_option( "permalink_{$taxonomy}", false );

			printf( '%1$s <input type="text" name="%2$s" class="regular-text code" placeholder="%3$s" value="%4$s">', $blog_prefix, esc_attr( $name ), esc_attr( $placeholder ), esc_attr( $value ) );
		}


		/** 
		 * Plugin-specific  permalink options
		 * 
		 * @return      string
		 */

		public function permalink_options()
		{ 
			$blog_prefix = $this->get_blog_prefix();

			if( ! empty( $blog_prefix ) )
			{
				$blog_prefix = $blog_prefix . '/';

			} // endif

			$page_id = maxson_portfolio_get_option( 'archive_page_id', false );

			$default_permalink = get_option( 'permalink_structure' );

			if( '' == $default_permalink )
			{ 
				$permalink = '';

			} else
			{ 
				$setting_permalink = maxson_portfolio_get_option( 'permalink_project' );

				$permalink = ( '' != $setting_permalink ) ? "/{$setting_permalink}/" : '';

			} // endif

			$base_slug = ( $page_id && get_page( $page_id ) ) ? get_page_uri( $page_id ) : _x( 'portfolio_projects', 'default-slug', 'maxson' );

			$base_url = home_url( '/' . $blog_prefix . $base_slug );

			$structures = array( 
				0 => '', 
				1 => '/' . trailingslashit( $base_slug ), 
				2 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%project_category%' )
			);

			echo wpautop( __( 'These settings control the permalinks used for Portfolio Projects.', 'maxson' ) );

			?>
			<table class="form-table portfolio-project-permalink-structure" id="maxson_portfolio_permalinks"><tbody>
				<tr>
					<th>
						<input type="radio" name="maxson_portfolio_permalink_structure" id="portfolio_projects_default_permalink_structure" class="do-change" value="<?php echo $structures[0]; ?>"<?php checked( $structures[0], $permalink ); ?>>
						<label for="portfolio_projects_default_permalink_structure"><?php _e( 'Default', 'maxson' ); ?></label></th>
					<td>
						<code><?php echo home_url(); ?>?portfolio_project=sample-project</code>
					</td>
				</tr>
				<tr>
					<th>
						<input type="radio" name="maxson_portfolio_permalink_structure" id="portfolio_projects_base_permalink_structure" class="do-change" value="<?php echo $structures[1]; ?>"<?php checked( $structures[1], $permalink ); ?>>
						<label for="portfolio_projects_base_permalink_structure"><?php _e( 'Project base', 'maxson' ); ?></label></th>
					<td>
						<code><?php echo $base_url; ?>/sample-project/</code>
					</td>
				</tr>
				<?php if( maxson_portfolio_taxonomy_exists( 'category' ) )
				{ ?>
					<tr>
						<th>
							<input type="radio" name="maxson_portfolio_permalink_structure" id="portfolio_projects_base_category_permalink_structure" class="do-change" value="<?php echo $structures[2]; ?>"<?php checked( $structures[2], $permalink ); ?>>
							<label for="portfolio_projects_base_category_permalink_structure"><?php _e( 'Project base with category', 'maxson' ); ?></label></th>
						<td><code><?php echo $base_url; ?>/portfolio-category/sample-project/</code></td>
					</tr>
				<?php } // endif ?>

				<tr>
					<th>
						<input type="radio" name="maxson_portfolio_permalink_structure" id="portfolio_projects_custom_permalink_structure" value="custom"<?php checked( in_array( $permalink, $structures ), false ); ?>>
						<label for="portfolio_projects_custom_permalink_structure"><?php _e( 'Custom Base', 'maxson' ); ?></label></th>
					<td>
						<code><?php echo home_url() . $blog_prefix; ?></code><input type="text" name="maxson_portfolio_permalink_settings" id="maxson_portfolio_permalink_settings" value="<?php echo esc_attr( $permalink ); ?>" class="regular-text code">

						<input type="hidden" name="maxson_portfolio_permalink_base" value="<?php esc_attr_e( $base_slug ); ?>">

						<p class="description"><?php printf( __( 'Enter a custom base to use. A base %1$smust%2$s be set or WordPress will use default instead.', 'maxson' ), '<strong>', '</strong>' ); ?></p>
					</td>
				</tr>
			</tbody></table>

			<script type="text/javascript">
				jQuery(function(){ 
					jQuery( '#maxson_portfolio_permalinks' ).on( 'change', 'input[type="radio"].do-change', function( event ){ 
						jQuery( '#maxson_portfolio_permalink_settings' ).val( jQuery( this ).val() );
					});

					jQuery( '#maxson_portfolio_permalink_settings' ).on( 'focus', function(){
						jQuery( '#portfolio_projects_custom_permalink_structure' ).click();
					});

					// Set permalink to default when main is set to default
					jQuery( '.permalink-structure:first' ).on( 'change', 'input[type="radio"]:first', function( event ){ 
						jQuery( '#portfolio_projects_default_permalink_structure' ).click();
					});
				});
			</script>

			<?php 
		}


		/** 
		 * Save admin permalinks
		 * 
		 * @return      void
		 */

		function save()
		{ 
			// We need to save the options ourselves; settings API does not trigger save for the permalinks page
			if( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) )
			{ 
				$taxonomies = get_object_taxonomies( array( self::POST_TYPE ) );

				$private_taxonomies = apply_filters( 'maxson_portfolio_private_taxonomies', array( 'type' ) );

			//	$permalinks = get_option( 'permalink' );

				foreach( $taxonomies as $taxonomy )
				{ 
					if( isset( $_POST["maxson_portfolio_permalink_{$taxonomy}_slug"] ) )
					{ 
						$value = sanitize_text_field( $_POST["maxson_portfolio_permalink_{$taxonomy}_slug"] );

						$value = ltrim( untrailingslashit( $value ), '/' );

						update_option( "maxson_portfolio_permalink_{$taxonomy}", $value );

					} // endif
				} // foreach

				// Save permalink structure
				if( isset( $_POST['maxson_portfolio_permalink_settings'] ) )
				{ 
					$permalink = trim( sanitize_text_field( $_POST['maxson_portfolio_permalink_settings'] ), '/' );

					if( $_POST['maxson_portfolio_permalink_structure'] == 'custom' )
					{ 
						if( in_array( $permalink, array( '%project_category%', '%project_role%', '%project_tag%' ) ) )
						{ 
							$default_base = _x( 'portfolio', 'slug', 'maxson' );

							$permalink_base = ( isset( $_POST['maxson_portfolio_permalink_base'] ) ) ? sanitize_text_field( $_POST['maxson_portfolio_permalink_base'] ) : $default_base;

							$permalink = trailingslashit( $permalink_base ) . $permalink;

						} // endif
					} // endif

					// Prepending slash, if not empty
					$value = ( $permalink ) ? $permalink : '';

					update_option( 'maxson_portfolio_permalink_project', $value );

				} // endif
			} // endif
		}

	} // endclass
} // endif

return new Maxson_Portfolio_Projects_Admin_Permalinks();

?>