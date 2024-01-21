<?php
/**
 * Plugin-specific admin list tables - portfolio projects
 * 
 * @package     Maxson_Portfolio_Projects/includes/admin/list-tables
 * @version     1.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();

} // endif


if( ! class_exists( 'Maxson_Portfolio_Projects_Admin_List_Table', false ) )
{ 
	include_once( 'abstract-class-admin-list-table.php' );

} // endif


class Maxson_Portfolio_Projects_Admin_List_Table_Portfolio_Projects extends Maxson_Portfolio_Projects_Admin_List_Table { 

	/**
	 * Post type
	 * 
	 * @var         string
	 */

	protected $list_table_type = 'portfolio_project';


	/**
	 * Constructor
	 */

	public function __construct()
	{ 
		parent::__construct();

		add_filter( 'disable_months_dropdown', '__return_true' );
	}


	/**
	 * Render blank state
	 */

	protected function render_blank_state()
	{ 
		$new_project_url = add_query_arg( array( 
			'post_type' => $this->list_table_type, 
			'tutorial'  => true
		), admin_url( 'post-new.php' ) );

		$new_project_label = esc_html__( 'Create your first project', 'maxson' );

		echo '<div class="maxson-pp-blankstate">';
			printf( '<h2 class="maxson-pp-blankstate--message">%1$s</h2>', __( 'Ready to showcase your projects?', 'maxson' ) );
			echo '<div class="maxson-pp-blankstate--buttons">';

				printf( '<a class="button button-primary maxson-pp-button maxson-pp-button-primary maxson-pp-button-large" href="%1$s">%2$s</a>', esc_url( $new_project_url ), $new_project_label );

			echo '</div>';
		echo '</div>';
	}


	/**
	 * Define primary column
	 * 
	 * @return      string
	 */

	protected function get_primary_column()
	{ 
		return 'name';
	}


	/**
	 * Get row actions to show in the list table
	 * 
	 * @param       array   $actions Array of actions
	 * @param       WP_Post $post Current post object
	 * @return      array
	 */

	protected function get_row_actions( $actions, $post )
	{ 
		if( true === apply_filters( 'maxson_portfolio_project_row_actions_disable_project_id', '__return_false' ) )
		{ 
			return $actions;

		} else
		{ 
			// translators: %d: portfolio project ID
			return array_merge( $actions, array( 
				'id' => sprintf( __( 'ID:&nbsp;%d', 'maxson' ), $post->ID )
			) );

		} // endif
	}


	/**
	 * Define which columns are sortable
	 * 
	 * @param       array $columns Existing columns
	 * @return      array
	 */

	public function define_sortable_columns( $columns )
	{ 
		$custom_columns = array( 
			'name'  => 'title'
		);

		return wp_parse_args( $custom_columns, $columns );
	}


	/**
	 * Define which columns to show on this screen
	 * 
	 * @param       array $columns Existing columns
	 * @return      array
	 */

	public function define_columns( $columns )
	{ 
		if( empty( $columns ) && ! is_array( $columns ) )
		{ 
			$columns = array();

		} // endif

		unset( 
			$columns['cb'], 
			$columns['title'], 
			$columns['taxonomy-portfolio_type'], 
			$columns['taxonomy-portfolio_role'], 
			$columns['taxonomy-portfolio_category'], 
			$columns['taxonomy-portfolio_tag'], 
			$columns['comments'], 
			$columns['author'], 
			$columns['date']
		);


		$show_columns = array();

		$show_columns['cb']   = '<input type="checkbox" />';
		$show_columns['name'] = __( 'Project', 'maxson' );

		$show_columns['taxonomy-portfolio_type'] = _x( 'Type', 'Admin post column name', 'maxson' );
		$show_columns['taxonomy-portfolio_role'] = _x( 'Roles', 'Admin post column name', 'maxson' );
		$show_columns['taxonomy-portfolio_category'] = _x( 'Categories', 'Admin post column name', 'maxson' );
		$show_columns['taxonomy-portfolio_tag'] = _x( 'Tags', 'Admin post column name', 'maxson' );

		$show_columns['start_end'] = _x( 'Start/End Date', 'Admin post column name', 'maxson' );
		$show_columns['client']    = _x( 'Client', 'Admin post column name', 'maxson' );
		//$show_columns['callout']   = _x( 'Callout', 'Admin post type column name', 'maxson' );

		if( post_type_supports( $this->list_table_type, 'author' ) )
		{
			$show_columns['author'] = _x( 'Author', 'Admin post column name', 'maxson' );

		} // endif

		if( post_type_supports( $this->list_table_type, 'comments' ) )
		{
			$show_columns['comments'] = sprintf( '<span title=%1$s" class="comment-grey-bubble"></span>', _x( 'Reviews', 'Comments column title attribute', 'maxson' ) );

		} // endif

		$show_columns['date'] = _x( 'Date', 'Admin post type column name', 'maxson' );

		return array_merge( $show_columns, $columns );
	}


	/**
	 * Render column: Name
	 * 
	 * @return      null
	 */

	protected function render_name_column( $post_id, $post )
	{ 
		$parent_id  = wp_get_post_parent_id( $post );
		$archive_id = maxson_portfolio_get_archive_page_id();

		$post_edit_link = get_edit_post_link( $post_id );
		$post_title     = _draft_or_post_title();

		if( $parent_id > 0 && ( $archive_id !== $parent_id ) )
		{ 
			$parent_edit_link = get_edit_post_link( $parent_id );
			$parent_title     = get_the_title( $parent_id );

			printf( '<a href="%1$s" class="row-parent-title">%2$s</a>', esc_url( $parent_edit_link ), $parent_title );
			echo '<span class="row-parent-divider">&nbsp;&rarr;&nbsp;</span>';

		} // endif


		echo '<strong>';
			printf( '<a href="%1$s" class="row-title">%2$s</a>', esc_url( $post_edit_link ), $post_title );
			_post_states( $post );
		echo '</strong>';

		get_inline_data( $post );
	}


	/**
	 * Render column: Start/End Date
	 * 
	 * @return      null
	 */

	protected function render_start_end_column( $post_id )
	{ 
		$date_args = array( 
			'separator'    => '<br>', 
			'before_start' => sprintf( '<span class="project-start-date"><em>%1$s</em> ', _x( 'Start: ', 'Post type column start date label', 'maxson' ) ), 
			'before_end'   => sprintf( '<span class="project-end-date"><em>%1$s</em> ', _x( 'End: ', 'Post type column start date label', 'maxson' ) )
		);

		$date = maxson_project_get_start_end_date_html( $post_id, $date_args );

		echo ( ! empty( $date ) ) ? $date : '<span class="na">&mdash;</span>';
	}


	/**
	 * Render column: Thumbnail
	 * 
	 * @return      null
	 */

	protected function render_thumb_column()
	{ 
		global $project;

		$post_id        = $this->object->get_id();
		$post_edit_link = get_edit_post_link( $post_id );

		if( $project->has_thumbnail() )
		{ 
			$post_image = $project->get_thumbnail( array( 60, 60 ) );

		} else
		{ 
			$type = $project->get_type( 'slug' );

			$post_image = maxson_portfolio_placeholder_image( $type );

		} // endif

		printf( '<a href="%1$s" class="media-thumbnail">%2$s</a>', esc_url( $post_edit_link ), $post_image );

	//	echo '<a href="' . esc_url( get_edit_post_link( $this->object->get_id() ) ) . '">' . $this->object->get_image( 'thumbnail' ) . '</a>'; // WPCS: XSS ok.
	}


	/**
	 * Render column: Client
	 * 
	 * @return      null
	 */

	protected function render_client_column( $post_id )
	{ 
		$text = maxson_project_get_client( $post_id );
		$url  = maxson_project_get_url( $post_id );

		if( ! empty( $client ) && ! empty( $url ) )
		{ 
			printf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( $url ), $text );

		} elseif( ! empty( $url ) )
		{ 
			$text = url_shorten( $url );

			printf( '<a href="%1$s" target="_blank">%1$s</a>', esc_url( $url ), $text );

		} elseif( ! empty( $client ) ) 
		{ 
			echo $client;

		} else 
		{ 
			echo '<span class="na">&mdash;</span>';

		} // endif
	}


	/**
	 * Render column: Callout
	 * 
	 * @return      null
	 */

	protected function render_callout_column( $post_id )
	{ 
		$has_callout = maxson_project_has_callout( $post_id );

		if( $has_callout )
		{ 
			echo maxson_project_get_callout_label( $post_id );

		} else
		{ 
			echo '&ndash;';

		} // endif
	}

} // endclass

?>