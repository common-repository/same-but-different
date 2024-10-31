<?php
	// Create the Same but Different widget
	class OTB_Same_But_Different_Widget extends WP_Widget {
	
		function __construct() {
			parent::__construct(
				// Base ID of your widget
				'otb_same_but_different_widget', 
	
				// Widget name will appear in UI
				__( 'Related Posts', 'same-but-different' ), 
	
				// Widget description
				array( 'description' => __( 'Display related posts based on common categories and tags.', 'same-but-different' ), ) 
			);
		}
	
		// Create the widget front-end
		public function widget( $args, $instance ) {
			global $post;
			
			if ( !is_single() || $post->post_type != 'post' ) {
				return false;
			}
			
			$otb_same_but_different = OTB_Same_But_Different();
			
			$amount 		   = $this->getIfSet( $instance['amount'], 5 );
			$order 			   = $this->getIfSet( $instance['order'], 'DESC' );
			$title_heading_tag = $this->getIfSet( $instance['title_heading_tag'], '' );
			
			$display_fields	= $instance['display_fields'];
			$show_thumbnail = in_array( 'thumbnail', $display_fields );
			$show_title 	= in_array( 'title', $display_fields );
			
			$relate_by = $instance['relate_by'];
			
			if ( !is_array($relate_by) ) {
				return;
			}
			
			$relate_by_categories = in_array( 'category', $relate_by );
			$relate_by_tags 	  = in_array( 'post_tag', $relate_by );

			$excluded_categories = $this->getIfSet( $instance['excluded_categories'], '' );
			$excluded_tags 		 = $this->getIfSet( $instance['excluded_tags'], '' );
			
			$assets_url = $otb_same_but_different->assets_url;
			
			include($otb_same_but_different->assets_dir .'/includes/get-related-posts.php');
			
			if ( empty($posts) ) {
				return;
			}
			
			$title = apply_filters( 'widget_title', $instance['title'] );
			
			// before and after widget arguments are defined by themes
			echo $args['before_widget'];
			
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
	
			// This is where you run the code and display the output
			include( $otb_same_but_different->assets_dir .'/template-parts/related-posts.php' );
			
			echo $args['after_widget'];
		}
		
		// Create the widget back-end
		public function form( $instance ) {
			$title 			   = $this->getIfSet( $instance[ 'title' ], 'Other posts you might like...' );
			$amount 		   = $this->getIfSet( $instance[ 'amount' ], 5 );
			$order 			   = $this->getIfSet( $instance[ 'order' ], 'DESC' );
			$title_heading_tag = $this->getIfSet( $instance[ 'title_heading_tag' ], '' );
			$show_thumbnail    = $this->getIfSet( $instance[ 'show_thumbnail' ], true );
			$show_title 	   = $this->getIfSet( $instance[ 'show_title' ], true );
			$display_fields    = $this->getIfSet( $instance[ 'display_fields' ], array( 'title' ) );
			
			// This will be needed when allowing users to choose from any taxonomy, not just 2 hardcoded ones
			/*
			if ( isset( $instance[ 'old_relate_by' ] ) ) {
				$old_relate_by = $instance[ 'old_relate_by' ];
			} else {
				$old_relate_by = null;
			}
			*/
			
			$relate_by = $this->getIfSet( $instance[ 'relate_by' ], array( 'category', 'post_tag' ) );
// 			This will be needed when allowing users to choose from any taxonomy, not just 2 hardcoded ones
// 			} else if ( $old_relate_by ) {
// 				$relate_by = $old_relate_by;
			
			$relate_by_categories = $this->getIfSet( $instance[ 'relate_by_categories' ], true );
			$relate_by_tags 	  = $this->getIfSet( $instance['relate_by_tags'], true );
			$excluded_categories  = $this->getIfSet( $instance['excluded_categories'], array() );
			
			// Create the widget settings UI
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title:', 'same-but-different' ); ?>
				</label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'amount' ); ?>">
					<?php _e( 'Number of posts to show:', 'same-but-different' ); ?>
				</label> 
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'amount' ); ?>" name="<?php echo $this->get_field_name( 'amount' ); ?>" type="number" step="1" min="1" value="<?php echo intval( $amount ); ?>" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'order' ); ?>">
					<?php _e( 'Order:', 'same-but-different' ); ?>
				</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
					<option value="rand" <?php echo $order == 'rand' ? 'selected' : ''; ?>><?php _e( 'Random', 'same-but-different' ); ?></option>
					<option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>><?php _e( 'Date ascending', 'same-but-different' ); ?></option>
					<option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>><?php _e( 'Date descending', 'same-but-different' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'title_heading_tag' ); ?>">
					<?php _e( 'Title Heading Tag:', 'same-but-different' ); ?>
				</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'title_heading_tag' ); ?>" name="<?php echo $this->get_field_name( 'title_heading_tag' ); ?>">
					<option value="" <?php echo $title_heading_tag == '' ? 'selected' : ''; ?>>None</option>
					<option value="h2" <?php echo $title_heading_tag == 'h2' ? 'selected' : ''; ?>>H2</option>
					<option value="h3" <?php echo $title_heading_tag == 'h3' ? 'selected' : ''; ?>>H3</option> 
					<option value="h4" <?php echo $title_heading_tag == 'h4' ? 'selected' : ''; ?>>H4</option>
					<option value="h5" <?php echo $title_heading_tag == 'h5' ? 'selected' : ''; ?>>H5</option>
					<option value="h6" <?php echo $title_heading_tag == 'h6' ? 'selected' : ''; ?>>H6</option>
				</select>
			</p>
			
			<p>
				<label class="full-width" for="<?php echo $this->get_field_id( 'diplay_fields' ); ?>">
					<?php _e( 'Show:', 'same-but-different' ); ?>
				</label>
				<input type="checkbox" name="<?php echo $this->get_field_name( 'display_fields' ); ?>[]" <?php checked( in_array( 'thumbnail', $display_fields ) ); ?> value="thumbnail" /> <?php _e( 'Thumbnail', 'same-but-different' ); ?><br />
				<input type="checkbox" name="<?php echo $this->get_field_name( 'display_fields' ); ?>[]" <?php checked( in_array( 'title', $display_fields ) ); ?> value="title" /> <?php _e( 'Title', 'same-but-different' ); ?>
			</p>
			
			<p>
				<label class="full-width" for="<?php echo $this->get_field_id( 'relate_by' ); ?>">
					<?php _e( 'Relate by:', 'same-but-different' ); ?>
				</label>
				<input type="checkbox" name="<?php echo $this->get_field_name( 'relate_by' ); ?>[]" <?php checked( in_array( 'category', $relate_by ) ); ?> value="category" /> <?php _e( 'Category', 'same-but-different' ); ?><br />
				<input type="checkbox" name="<?php echo $this->get_field_name( 'relate_by' ); ?>[]" <?php checked( in_array( 'post_tag', $relate_by ) ); ?> value="post_tag" /> <?php _e( 'Tag', 'same-but-different' ); ?>
			</p>

			<p>
				<label class="full-width" for="<?php echo $this->get_field_id( 'excluded_categories' ); ?>">
					<?php _e( 'Exclude posts in these categories:', 'same-but-different' ); ?>
				</label>
				<?php
				$categories = get_categories( array(
  		  			'hide_empty' => false
				) );

				foreach ( $categories as $category ) {
				?>
				<input 
					type="checkbox" 
					name="<?php echo esc_attr( $this->get_field_name( 'excluded_categories' ) ); ?>[]" 
					value="<?php echo $category->term_id; ?>" 
					<?php checked( in_array( $category->term_id, $excluded_categories ) ); ?> />
					<?php echo $category->name; ?><br />
				<?php
				}
				?>
			</p>
			
		<?php 
		}
		
		// Save the widget settings
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			
			$instance['title'] 			   = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['amount'] 		   = ( ! empty( $new_instance['amount'] ) ) ? intval( $new_instance['amount'] ) : '';
			$instance['order'] 			   = ( ! empty( $new_instance['order'] ) ) ? $new_instance['order'] : '';
			$instance['title_heading_tag'] = ( ! empty( $new_instance['title_heading_tag'] ) ) ? $new_instance['title_heading_tag'] : '';
			
			$display_fields = ( ! empty ( $new_instance['display_fields'] ) ) ? (array) $new_instance['display_fields'] : array();
			$instance['display_fields'] = array_map( 'sanitize_text_field', $display_fields );
			
			$relate_by = ( ! empty ( $new_instance['relate_by'] ) ) ? (array) $new_instance['relate_by'] : array();
			$instance['relate_by'] = array_map( 'sanitize_text_field', $relate_by );

			$excluded_categories = ( ! empty ( $new_instance['excluded_categories'] ) ) ? (array) $new_instance['excluded_categories'] : array();
			$instance['excluded_categories'] = array_map( 'sanitize_text_field', $excluded_categories );
			
			//$instance['relate_by_categories'] = ( ! empty( $new_instance['relate_by_categories'] ) ) ? intval( (bool) $new_instance['relate_by_categories'] ) : '';
			//$instance['relate_by_tags'] 	  = ( ! empty( $new_instance['relate_by_tags'] ) ) ? intval( (bool) $new_instance['relate_by_tags'] ) : '';
			
			return $instance;
		}
		
		public function getIfSet( &$var, $defaultValue ) {
			if(isset($var)) {
				return $var;
			} else {
				return $defaultValue;
			}
		}

	}
