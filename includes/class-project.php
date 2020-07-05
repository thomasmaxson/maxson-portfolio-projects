<?php
/**
 * Plugin-specific project handler - handle individual project data
 * 
 * @author 		Thomas Maxson
 * @category	Class
 * @package 	Maxson_Portfolio/includes
 * @license     GPL-2.0+
 * @version		1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! function_exists( 'Maxson_Portfolio_Projects_Project_Data' ) )
{ 
	class Maxson_Portfolio_Projects_Project_Data { 

		/**
		 * The project (post) ID.
		 * 
		 * @var         int
		 */

		public $id = 0;


		/**
		 * $post Stores post data
		 * 
		 * @var         $post WP_Post
		 */

		public $post = null;


		/**
		 * Constructor gets the post object and sets the ID for the loaded project.
		 * 
		 * @param       int|Maxson_Portfolio_Projects|object $project Project ID, post object, or project object
		 */

		public function __construct( $project = false )
		{ 
			if( is_numeric( $project ) )
			{ 
				$this->id   = absint( $project );
				$this->post = get_post( $this->id );

			} elseif( $project instanceof Maxson_Portfolio_Projects )
			{ 
				$this->id   = absint( $project->id );
				$this->post = $project->post;

			} elseif( isset( $project->ID ) )
			{ 
				$this->id   = absint( $project->ID );
				$this->post = $project;

			} // endif
		}


		/**
		 * __isset function
		 * 
		 * @param       mixed $key
		 * @return      bool
		 */

		public function __isset( $key )
		{ 
			return metadata_exists( 'post', $this->id, "_{$key}" );
		}


		/**
		 * __get function
		 * 
		 * @param       string $key
		 * @return      mixed
		 */

		public function __get( $key )
		{ 
			$value = get_post_meta( $this->id, "_{$key}", true );

			if( ! empty( $value ) )
			{ 
				$this->$key = $value;

			} // endif

			return $value;
		}


		/**
		 * Get the project's post data
		 * 
		 * @return      object
		 */

		public function get_post_data()
		{ 
			return $this->post;
		}


		/**
		 * Return whether or not the project post exists
		 * 
		 * @return      bool
		 */

		public function exists()
		{ 
			return ( empty( $this->post ) ) ? false : true;
		}


		/**
		 * Return whether or not the project is active
		 * 
		 * @return      bool
		 */

		public function is_active()
		{ 
			if( ! $this->post )
			{
				$is_active = false;

			} elseif( 'publish' !== $this->post->post_status )
			{ 
				$is_active = false;

			} else 
			{ 
				$is_active = true;

			} // endif

			return apply_filters( 'maxson_portfolio_is_active', $is_active, $this->id );
		}


		/**
		 * Get the parent of the post
		 * 
		 * @return      int
		 */

		public function get_ID()
		{ 
			return $this->id;
		}


		/**
		 * Get the parent of the post
		 * 
		 * @return      int
		 */

		public function get_parent_ID()
		{ 
			return wp_get_post_parent_id( $this->id );
		}


		/**
		 * Get the parent of the post
		 * 
		 * @return      int
		 */

		public function get_parent()
		{ 
			return apply_filters( 'maxson_portfolio_the_parent', absint( $this->post->post_parent ), $this );
		}


		/**
		 * Get the post created date
		 * 
		 * @param       string $format (optional) Format to use for retrieving the date was written
		 * @param       string $before (optional) Output before the date
		 * @param       string $after  (optional) Output after the date
		 * @return      string
		 */

		public function get_date( $format = null, $before = '', $after = '' )
		{ 
			if( is_null( $format ) || empty( $format ) )
			{
				$format = maxson_portfolio_get_date_format();

			} // endif

			$date = $before . get_the_date( $format, $this->id ) . $after;

			return apply_filters( 'maxson_portfolio_the_date', $date, $before, $after, $this );
		}


		/**
		 * Get the post modified date
		 * 
		 * @param       string $format (optional) Format to use for retrieving the date was written
		 * @param       string $before (optional) Output before the date
		 * @param       string $after  (optional) Output after the date
		 * @return      string
		 */

		public function get_modified_date( $format = null, $before = '', $after = '' )
		{ 
			if( is_null( $format ) || empty( $format ) )
			{ 
				$format = maxson_portfolio_get_date_format();

			} // endif

			$date = the_modified_date( $format, $before, $after, false );

			return apply_filters( 'maxson_portfolio_the_modified_date', $date, $before, $after, $this );
		}


		/**
		 * Get the post time
		 * 
		 * @param       string $format (optional) Format to use for retrieving the time was written
		 * @param       string $before (optional) Output before the time
		 * @param       string $after  (optional) Output after the time
		 * @return      string
		 */

		public function get_time( $format = null, $before = '', $after = '' )
		{ 
			if( is_null( $format ) || empty( $format ) )
			{
				$format = maxson_portfolio_get_time_format();

			} // endif

			$time = $before . get_the_time( $format, $this->id ) . $after;

			return apply_filters( 'maxson_portfolio_the_time', $time, $before, $after, $this );
		}


		/**
		 * Get the post modified time
		 * 
		 * @param       string $format (optional) Format to use for retrieving the time was written
		 * @param       string $before (optional) Output before the time
		 * @param       string $after  (optional) Output after the time
		 * @return      string
		 */

		public function get_modified_time( $format = null, $before = '', $after = '' )
		{ 
			if( is_null( $format ) || empty( $format ) )
			{
				$format = maxson_portfolio_get_date_format();

			} // endif

			$time = $before . get_the_modified_time( $format ) . $after;

			return apply_filters( 'maxson_portfolio_the_modified_date', $time, $before, $after, $this );
		}


		/**
		 * Wrapper for get_permalink
		 * 
		 * @return      string
		 */

		public function get_permalink()
		{ 
			return get_permalink( $this->id );
		}


		/**
		 * Gets the image by ID
		 * 
		 * @param       int    $id   (optional) Post ID
		 * @param       string $size (optional) Size of WordPress thumbnail
		 * @param       array  $args (optional) An array of arguments
		 * @return      string
		 */

		public function get_image( $id = null, $size = 'project_thumbnail', $args = array() )
		{ 
			if( is_null( $id ) )
			{
				$id = $this->id;

			} // endif

			$image = wp_get_attachment_image( $id, $size, false, $args );

			$image = preg_replace( '/(width|height)=\"\d*\"\s/', '', $image );

			return apply_filters( 'maxson_portfolio_the_image', $image, $id, $size, $args );
		}


		/**
		 * Get the title of the post
		 * 
		 * @param       string $before (optional) String to use before the title
		 * @param       string $after  (optional) String to use after the title
		 * @return      string
		 */

		public function get_title( $before = '', $after = '' )
		{ 
			$title = apply_filters( 'maxson_portfolio_the_title', ( ( $this->post ) ? $this->post->post_title : '' ), $this );

			return $before . $title . $after;
		}


		/**
		 * Get the excerpt of the post
		 * 
		 * @param       bool   $raw (optional) Return unfiltered content
		 * @return      string
		 */

		public function get_excerpt( $raw = false )
		{ 
			if( has_excerpt( $this->id ) )
			{ 
				$excerpt = get_post_field( 'post_excerpt', $this->id );

				if( ! $raw )
				{
					$excerpt = apply_filters( 'maxson_portfolio_the_excerpt', $excerpt );

				} // endif

				return $excerpt;

			} else
			{ 
				return false;

			} // endif
		}


		/**
		 * Get the content of the post
		 * 
		 * @param       bool   $raw (optional) Return unfiltered content
		 * @return      string
		 */

		public function get_content( $raw = false )
		{ 
			$content = get_post_field( 'post_content', $this->id );

			if( ! $raw )
			{
				$content = apply_filters( 'maxson_portfolio_the_content', $content );

			} // endif

			return $content;
		}


		/**
		 * Return the project categories
		 * 
		 * @param       array  $args (optional) An array of arguments
		 * @return      string|bool
		 */

		public function get_categories( $args = array() )
		{ 
			if( maxson_portfolio_taxonomy_exists( 'category' ) )
			{ 
				// Cannot override
				$args['taxonomy'] = 'portfolio_category';

				return $this->get_terms( $args );

			} else
			{ 
				 return false;

			} // endif
		}


		/**
		 * Return the project tags
		 * 
		 * @param       array  $args (optional) An array of arguments
		 * @return      string|bool
		 */

		public function get_roles( $args = array() )
		{ 
			if( maxson_portfolio_taxonomy_exists( 'role' ) )
			{
				// Cannot override
				$args['taxonomy'] = 'portfolio_role';

				return $this->get_terms( $args );

			} else
			{ 
				 return false;

			} // endif
		}


		/**
		 * Return the project tags
		 * 
		 * @param       array  $args (optional) An array of arguments
		 * @return      string|bool
		 */

		public function get_tags( $args = array() )
		{ 
			if( maxson_portfolio_taxonomy_exists( 'tag' ) )
			{ 
				// Cannot override
				$args['taxonomy'] = 'portfolio_tag';

				return $this->get_terms( $args );

			} else
			{ 
				 return false;

			} // endif
		}


		/**
		 * Checks the project type
		 * 
		 * @param       string $type (optional) Type to check for
		 * @param       string $key  (optional) Type of data to compare
		 * @return      bool
		 */

		public function is_type( $type = null, $key = 'meta' )
		{ 
			return ( $this->get_type( $key ) == $type ) ? true : false;
		}


		/**
		 * Return the project type
		 * 
		 * @param       string $key  (optional) Type of data to return
		 * @param       array  $args (optional) An array of arguments
		 * @return      string
		 */

		public function get_type( $key = 'name', $args = array() )
		{ 
			$value = '';
			$terms = wp_get_post_terms( $this->id, 'portfolio_type', $args );

			if( ! empty( $terms ) && ! is_wp_error( $terms ) )
			{ 
				$term = current( $terms );

				if( in_array( strtolower( $key ), array( 'raw', 'meta' ) ) )
				{ 
					$term_meta = maxson_portfolio_get_term_type( $term->term_id );

					if( ! empty( $term_meta ) )
					{
						$value = $term_meta;

					} // endif
				} else
				{ 
					switch( strtolower( $key ) )
					{ 
						case 'name': 
						case 'title': 
							$value = $term->name;
							break;

						case 'slug': 
							$value = $term->slug;
							break;

						case 'id': 
						case 'term_id': 
							$value = $term->term_id;
							break;

					} // endswitch
				} // endif
			} // endif

			return apply_filters( 'maxson_portfolio_the_type', $value, $this, $key, $args );
		}


		/**
		 * Checks for a project thumbnail
		 * 
		 * @return      string
		 */

		public function has_thumbnail()
		{ 
			return has_post_thumbnail( $this->id );
		}


		/**
		 * Gets the project thumbnail ID
		 * 
		 * @return      int
		 */

		public function get_thumbnail_id()
		{ 
			if( has_post_thumbnail( $this->id ) )
			{ 
				$image_id = get_post_thumbnail_id( $this->id );

			} elseif( ( $parent_id = wp_get_post_parent_id( $this->id ) ) && has_post_thumbnail( $parent_id ) )
			{ 
				$image_id = get_post_thumbnail_id( $parent_id );

			} else
			{ 
				$image_id = 0;

			} // endif

			return apply_filters( 'maxson_portfolio_the_thumbnail_id', $image_id, $this );
		}


		/**
		 * Gets the project thumbnail src
		 * 
		 * @param       string $size (optional) Size of WordPress thumbnail
		 * @return      string
		 */

		public function get_thumbnail_src( $size = 'project_thumbnail' )
		{ 
			if( has_post_thumbnail( $this->id ) )
			{ 
				$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $this->id ), $size );

			} elseif( ( $parent_id = wp_get_post_parent_id( $this->id ) ) && has_post_thumbnail( $parent_id ) )
			{ 
				$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $parent_id ), $size );

			} else
			{ 
				$image_src = false;

			} // endif

			if( is_array( $image_src ) )
			{
				$image_src = $image_src[0];

			} // endif

			return apply_filters( 'maxson_portfolio_the_thumbnail_src', $image_src, $this );
		}


		/**
		 * Gets the main project thumbnail
		 * 
		 * @param       string $size (optional) Size of WordPress thumbnail
		 * @param       array  $args (optional) An array of arguments
		 * @return      string
		 */

		public function get_thumbnail( $size = 'project_thumbnail', $args = array() )
		{ 
			$defaults = array( 
				'class' => 'img-responsive project-thumbnail'
			);

			$args = wp_parse_args( $args, $defaults );

			if( has_post_thumbnail( $this->id ) )
			{ 
				$image = get_the_post_thumbnail( $this->id, $size, $args );

			} elseif( ( $parent_id = wp_get_post_parent_id( $this->id ) ) && has_post_thumbnail( $parent_id ) )
			{ 
				$image = get_the_post_thumbnail( $parent_id, $size, $args );

			} else
			{ 
				$image = false;

			} // endif

			// Remove img width and height attributes
			$image = preg_replace( '/(width|height)=\"\d*\"\s/', '', $image );

			return apply_filters( 'maxson_portfolio_the_thumbnail', $image, $this );
		}


		/**
		 * Gets the project media source
		 * 
		 * @return      string|bool
		 */

		public function get_media_src( $size = 'project_large' )
		{ 
			return array( 
				'type' => 'none', 
				'src'  => $this->get_thumbnail_src( $size )
			);
		}


		/**
		 * Gets the project media
		 * 
		 * @return      string|bool
		 */

		public function get_media( $size = 'project_large' )
		{ 
			return $this->get_thumbnail( $size );
		}


		/**
		 * Checks whether or not the project is promoted
		 * 
		 * @return      bool
		 */

		public function is_promoted( $true = true, $false = false )
		{ 
			if( ! maxson_portfolio_get_option( 'setup_promoted' ) )
			{
				return $false;

			} // endif

			$meta = apply_filters( 'maxson_portfolio_is_promoted', $this->promoted, $this );

			return ( $meta ) ? $true : $false;
		}


		/**
		 * Return the project promoted label
		 * 
		 * @return      string
		 */

		public function get_promoted_label()
		{ 
			$label = ( $this->is_promoted() ) ? $this->promoted_label : false;

			if( empty( $label ) || false === $label )
			{ 
				$label = maxson_portfolio_get_default_promoted_label();

			} // endif

			return apply_filters( 'maxson_portfolio_the_promoted_label', $label, $this );
		}


		/**
		 * Return the project client
		 * 
		 * @return      string|bool
		 */

		public function get_client()
		{ 
			return apply_filters( 'maxson_portfolio_the_client', $this->client, $this );
		}


		/**
		 * Return the project url
		 * 
		 * @return      string|bool
		 */

		public function get_url()
		{ 
			return apply_filters( 'maxson_portfolio_the_url', $this->url, $this );
		}


		/**
		 * Return the project start date
		 * 
		 * @param       string $format (optional) Requested output format, should be a PHP date format string
		 * @return      string
		 */

		private function get_start_end_date( $type = null, $format = null )
		{ 
			$type = strtolower( $type );

			if( is_null( $type ) || ! in_array( $type, array( 'start', 'end' ) ) )
			{
				return false;

			} // endif

			switch( $type )
			{ 
				case 'start': 
					$value = $this->start_date;
					break;

				case 'end': 
					$value = $this->end_date;
					break;

			} // endswitch

			$date = apply_filters( "maxson_portfolio_the_{$type}_date_raw", $value, $this );

			if( ! empty( $date ) )
			{ 
				if( 'raw' == $format )
				{ 
					$output = $date;

				} else
				{ 
					if( is_null( $format ) || empty( $format ) )
					{ 
						$format = maxson_portfolio_get_date_format();

					} // endif

					$output = mysql2date( $format, $date );

				} // endif
			} else
			{ 
				$output = false;

			} // endif

			return apply_filters( "maxson_portfolio_the_{$type}_date", $output, $format, $this );
		}



		/**
		 * Return the project start date
		 * 
		 * @param       string $format (optional) Requested output format, should be a PHP date format string
		 * @return      string
		 */

		public function get_start_date( $format = null )
		{ 
			return $this->get_start_end_date( 'start', $format );
		}


		/**
		 * Return the project end date
		 * 
		 * @param       string $format (optional) Requested output format, should be a PHP date format string
		 * @return      string
		 */

		public function get_end_date( $format = null )
		{ 
			return $this->get_start_end_date( 'end', $format );
		}


		/**
		 * Return the project dates
		 * 
		 * @param       array       $args (optional) An array of arguments
		 * @return      string|bool
		 */

		public function get_start_end_date_html( $args = array() )
		{ 
			$defaults = array( 
				'format'        => maxson_portfolio_get_date_format(), 
				'separator'     => ' - ', 
				'before_start'  => '<span itemprop="dateCreated" class="project-start-date entry-date updated">', 
				'after_start'   => '</span>', 
				'before_end'    => '<span class="project-end-date">', 
				'after_end'     => '</span>'
			);

			$args = wp_parse_args( $args, $defaults );

			$start_date = $this->get_start_date( $args['format'] );
			$end_date   = $this->get_end_date( $args['format'] );

			if( ! empty( $start_date ) && ! empty( $end_date ) )
			{ 
				$output  = $args['before_start'] . $start_date . $args['after_start'];
				$output .= $args['separator'];
				$output .= $args['before_end'] . $end_date . $args['after_end'];

			} elseif( ! empty( $start_date ) )
			{ 
				$output  = $args['before_start'] . $start_date . $args['after_start'];

			} elseif( ! empty( $end_date ) )
			{ 
				$output = $args['before_end'] . $end_date . $args['after_end'];

			} else 
			{ 
				$output = false;

			} // endif

			return apply_filters( 'maxson_portfolio_the_start_end_date_html', $output, $args, $this );
		}


		/**
		 * Return the project taxonomy terms
		 * 
		 * @param       array       $args (optional) An array of arguments
		 * @return      string|bool
		 */

		public function get_terms( $args = array() )
		{ 
			$defaults = array( 
				'link'       => true, 
				'taxonomy'   => 'portfolio_category', 
				'separator'  => ', ', 
				'before'     => false, 
				'after'      => false, 
				'beforeitem' => false, 
				'afteritem'  => false
			);

			$args = wp_parse_args( $args, $defaults );

			if( ! taxonomy_exists( $args['taxonomy'] ) )
				return false;

			$terms = wp_get_object_terms( $this->id, $args['taxonomy'] );

			if( ! empty( $terms ) && ! is_wp_error( $terms ) )
			{ 
				$items = array();

				if( $args['link'] )
				{ 
					foreach( $terms as $term )
					{ 
						$link = get_term_link( $term );

						$items[] = sprintf( '<a href="%1$s">%2$s</a>', esc_attr( $link ), esc_html( $term->name ) );

					} // endforeach

				} else
				{ 
					foreach( $terms as $term )
					{ 
						$items[] = esc_html( $term->name );

					} // endforeach

				} // endif

				$output  = $args['before'];
				$output .= $args['beforeitem'];
				$output .= join( $args['afteritem'] . $args['separator'] . $args['beforeitem'], $items );
				$output .= $args['afteritem'];
				$output .= $args['after'];

				return $output;

			} else 
			{ 
				return false;

			} // endif
		}


		/**
		 * Retrieves related project terms
		 * 
		 * @param       string $taxonomy (optional) Taxonomy name
		 * @return      array
		 */

		protected function get_related_terms( $taxonomy = 'portfolio_category' )
		{ 
			if( ! taxonomy_exists( $taxonomy ) )
			{
				return array();

			} // endif

			$terms_array = array( 0 );

			$terms = apply_filters( "maxson_portfolio_related_{$taxonomy}_terms", wp_get_post_terms( $this->id, $taxonomy ), $this->id, $this );

			foreach( $terms as $term )
			{ 
				$terms_array[] = $term->term_id;

			} // endif

			return array_map( 'absint', $terms_array );
		//	return wp_parse_id_list( $terms_array );
		}


		/**
		 * Get and return related projects
		 * 
		 * @param       int    $limit    (optional) Number of posts to return
		 * @param       array  $args     (optional) An array of arguments
		 * @param       string $taxonomy (optional) Taxonomy name
		 * @return      array  Array of post IDs
		 */

		public function get_related( $limit = 5, $args = array(), $taxonomy = 'portfolio_category' )
		{ 
			$post_id = $this->id;

			$transient_name     = "maxson_portfolio_related_{$post_id}_{$taxonomy}_{$limit}";
			$transient_duration = apply_filters( 'maxson_portfolio_related_projects_transient_duration', ( DAY_IN_SECONDS * 30 ), $taxonomy, $post_id );

			if( false === ( $related_posts = get_transient( $transient_name ) ) )
			{ 
				// Related projects are found from category, role, tag or type
				$project_terms = $this->get_related_terms( $taxonomy );

				// Don't bother if none are set
				if( sizeof( $project_terms ) == 1 )
				{ 
					$related_posts = array();

				} else
				{ 
					$exclude_ids = wp_parse_id_list( array( 0, $post_id ) );

					$defaults = array( 
						'not_in'            => $exclude_ids, 
						'orderby'           => 'rand', 
						'require_thumbnail' => true
					);

					switch( $taxonomy )
					{ 
						case 'portfolio_category': 
							$defaults['category']       = $project_terms;
							$defaults['category_field'] = 'id';
							break;

						case 'portfolio_role': 
							$defaults['role']       = $project_terms;
							$defaults['role_field'] = 'id';
							break;

						case 'portfolio_tag': 
							$defaults['tag']       = $project_terms;
							$defaults['tag_field'] = 'id';
							break;

						case 'portfolio_type': 
							$defaults['type']       = $project_terms;
							$defaults['type_field'] = 'id';
							break;

					} // endswitch

					$args = wp_parse_args( $args, $defaults );

					$related_args = maxson_portfolio_query_args( $args );

					// Cannot override, it is passed in
					$related_args['posts_per_page'] = $limit;

					// Get the related projects
					$queried_posts = new WP_Query( $related_args );

					$related_posts = wp_list_pluck( $queried_posts->posts, 'ID' );

				} // endif

				set_transient( $transient_name, $related_posts, $transient_duration );

			} // endif

			shuffle( $related_posts );

			return $related_posts;
		}


	} // endclass
} // endif

?>