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


if( class_exists( 'Maxson_PP_Admin_List_Table_Portfolio_Projects', false ) )
{ 
	return;

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
			printf( '<h2 class="maxson-pp-blankstate-message">%1$s</h2>', __( 'Ready to showcase your projects?', 'maxson' ) );
			echo '<div class="maxson-pp-blankstate-buttons">';

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
		return 'project';
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
			return array_merge( array( 
				'id' => sprintf( __( 'ID:&nbsp;%d', 'maxson' ), $post->ID )
			), $actions );

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
			'project'  => 'title', 
			'promoted' => 'project_promoted'
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


		$show_columns          = array();

		$show_columns['cb']      = '<input type="checkbox" />';
		$show_columns['thumb']   = __( 'Thumbnail', 'maxson' );
		$show_columns['project'] = __( 'Project', 'maxson' );

		// This is displayed as an icon next to the project title, not as a column of data
		if( maxson_portfolio_taxonomy_exists( 'type' ) &&
			( false === apply_filters( 'maxson_portfolio_post_columns_show_project_type_as_icon', true ) ) )
		{ 
			$show_columns['taxonomy-portfolio_type'] = _x( 'Type', 'Admin post column name', 'maxson' );

		} // endif

		if( maxson_portfolio_taxonomy_exists( 'role' ) )
		{
			$show_columns['taxonomy-portfolio_role'] = _x( 'Roles', 'Admin post column name', 'maxson' );

		} // endif

		if( maxson_portfolio_taxonomy_exists( 'category' ) )
		{
			$show_columns['taxonomy-portfolio_category'] = _x( 'Categories', 'Admin post column name', 'maxson' );

		} // endif

		if( maxson_portfolio_taxonomy_exists( 'tag' ) )
		{
			$show_columns['taxonomy-portfolio_tag'] = _x( 'Tags', 'Admin post column name', 'maxson' );

		} // endif

		$show_columns['start_end'] = _x( 'Start/End Date', 'Admin post column name', 'maxson' );
		$show_columns['client']    = _x( 'Client', 'Admin post column name', 'maxson' );

		if( post_type_supports( $this->list_table_type, 'author' ) )
		{
			$show_columns['author'] = _x( 'Author', 'Admin post column name', 'maxson' );

		} // endif

		if( post_type_supports( $this->list_table_type, 'comments' ) )
		{
			$show_columns['comments'] = sprintf( '<span title=%1$s" class="comment-grey-bubble"></span>', _x( 'Reviews', 'Comments column title attribute', 'maxson' ) );

		} // endif

		$show_columns['date'] = _x( 'Date', 'Admin post type column name', 'maxson' );

		if( maxson_portfolio_get_option( 'setup_promoted' ) )
		{ 
			$show_columns['promoted'] = sprintf( '<span class="comment-grey-bubble promoted-grey-star" data-tip="%1$s"></span><span class="column-label">%1$s</span>', _x( 'Promoted', 'Admin post type column name', 'maxson' ) );

		} // endif

		return array_merge( $show_columns, $columns );
	}


	/**
	 * Pre-fetch any data for the row each column has access to it
	 * 
	 * @param       int $post_id Post ID being shown
	 */

	protected function prepare_row_data( $post_id )
	{ 
		if( empty( $this->object ) || $this->object->get_id() !== $post_id )
		{ 
			$project = maxson_portfolio_get_project( $post_id );

			$this->object = $project;

		} // endif
	}


	/**
	 * Render column: Name
	 * 
	 * @return      null
	 */

	protected function render_project_column()
	{ 
		global $post;
		global $project;

		$post_id    = $this->object->get_id();
		$parent_id  = $this->object->get_parent_id();
		$archive_id = maxson_portfolio_get_archive_page_id();

		$post_edit_link = get_edit_post_link( $post_id );
		$post_title     = _draft_or_post_title();

		// Project Type
		if( taxonomy_exists( 'portfolio_type' ) &&
			( true === apply_filters( 'maxson_portfolio_post_columns_show_project_type_as_icon', true ) ) )
		{ 
			$type_name = $project->get_type( 'name' );
			$type_slug = $project->get_type( 'slug' );
			$type_link = add_query_arg( array( 
				'post_type' => $this->list_table_type, 
				'taxonomy'  => 'portfolio_type', 
				'term'      => $type_slug
			), admin_url( 'edit.php' ) );

			printf( '<a href="%1$s" class="post-state-format post-format-icon post-format-%2$s" data-tip="%3$s">%3$s:</a>', esc_url( $type_link ), $type_slug, $type_name );

		} // endif


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

		printf( '<div class="hidden portfolio-data" id="maxson_portfolio_project_inline_%1$s">', $post_id );
			printf( '<div class="project-promoted">%1$s</div>',       $project->is_promoted() );
			printf( '<div class="project-promoted-label">%1$s</div>', $project->get_promoted_label() );
		echo '</div>';
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

	protected function render_client_column()
	{ 
		global $project;

		$client = $project->get_client();
		$url    = $project->get_url();

		if( ! empty( $client ) && ! empty( $url ) )
		{ 
			printf( '<a href="%1$s" target="_blank">%2$s</a>', $url, $client );

		} elseif( ! empty( $url ) )
		{ 
			printf( '<a href="%1$s" target="_blank">%1$s</a>', $url );

		} elseif( ! empty( $client ) ) 
		{ 
			echo $client;

		} else 
		{ 
			echo '<span class="na">&mdash;</span>';

		} // endif
	}


	/**
	 * Render column: Start/End Date
	 * 
	 * @return      null
	 */

	protected function render_start_end_column()
	{ 
		global $project;

		$date_args = array( 
			'separator'    => '<br>', 
			'before_start' => sprintf( '<span class="project-start-date"><em>%1$s</em> ', _x( 'Start: ', 'Post type column start date label', 'maxson' ) ), 
			'before_end'   => sprintf( '<span class="project-end-date"><em>%1$s</em> ', _x( 'End: ', 'Post type column start date label', 'maxson' ) )
		);

		$date = $project->get_start_end_date_html( $date_args );

		echo ( ! empty( $date ) ) ? $date : '<span class="na">&mdash;</span>';
	}


	/**
	 * Render column: Promoted
	 * 
	 * @return      null
	 */

	protected function render_promoted_column()
	{ 
		global $project;

		$post_id  = $this->object->get_id();
		$user_can = current_user_can( 'edit_project', $post_id );

		$is_promoted    = $project->is_promoted();
		$promoted_label = $project->get_promoted_label();

		if( $user_can )
		{ 
			$nonce = wp_create_nonce( 'portfolio_projects_admin_column_promoted_nonce' );

			if( $is_promoted )
			{ 
				printf( '<a href="#update" class="icon-promoted" id="%1$s" data-nonce="%2$s" data-tip="%3$s">%3$s</a>', $post_id, $nonce, $promoted_label );

			} else
			{ 
				printf( '<a href="#update" class="icon-promoted icon-not-promoted" id="%1$s" data-nonce="%2$s">%3$s</a>', $post_id, $nonce, $promoted_label );

			} // endif
		} else
		{ 
			if( $is_promoted )
			{ 
				printf( '<span class="icon-promoted" id="%1$s" data-tip="%2$s">%2$s</span>', $post_id, $promoted_label );

			} else
			{ 
				printf( '<span class="icon-promoted icon-not-promoted" id="%1$s">%2$s</span>', $post_id, $promoted_label );

			} // endif

		} // endif
	}

} // endclass

?>