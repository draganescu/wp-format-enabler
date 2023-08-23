<?php
/*
Plugin Name: Format enabler
Plugin URI: https://github.com/draganescu/wp-format-enabler
Description: Enables post formats for block themes and adds admin entries for each format.
Version: 1.0
Author: Draganescu Stefan Andrei
Author URI: https://www.andrei.draganescu.info
*/

// Enable post formats for block themes.
function block_theme_post_formats() {
    if ( wp_is_block_theme() ) {
        add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );
    }
}
add_action( 'after_setup_theme', 'block_theme_post_formats' );

// add the has-format class to the editor body if the GET param is set
function block_theme_add_format_class( $classes ) {
	if ( ! wp_is_block_theme() ) {
		return $classes;
	}
	if ( isset( $_GET['post_format'] ) ) {
		$classes .= ' has-format ';
	}
	return $classes;
}

add_filter( 'admin_body_class', 'block_theme_add_format_class' );

// enqueue editor style to hide .wp-block-post-title if has-format exists on body
function block_theme_hide_title() {
	if ( ! wp_is_block_theme() ) {
		return;
	}
	if ( isset( $_GET['post_format'] ) ) {
		wp_enqueue_style( 'block-theme-hide-title', plugin_dir_url( __FILE__ ) . 'hide-title.css' );
	}
}

add_action( 'enqueue_block_editor_assets', 'block_theme_hide_title', 1 );

// Add admin entries for each format.
function block_theme_post_format_admin_menu() {
    if ( wp_is_block_theme() ) {
        foreach ( get_post_format_strings() as $format => $label ) {
						if ( $format === 'standard' ) {
							continue;
						}
            add_submenu_page( 'edit.php', 'New ' . $label, 'New ' . $label, 'edit_posts', 'post-new.php?post_format=' . $format );
        }
    }
}
add_action( 'admin_menu', 'block_theme_post_format_admin_menu' );


// Add a template to the post type object on init
function block_theme_setup_format_block_template() {
	if ( ! wp_is_block_theme() ) {
		return;
	}
	// if current page is not the post editor page in admin do nothing
	if ( ! is_admin() || ! isset( $_GET['post_format'] ) ) {
		return;
	}

	$template = null;
	switch( $_GET['post_format'] ):
		case 'aside':
			$template = array(
				array( 'core/paragraph', array(
					'placeholder' => 'Write an aside...',
				) ),
			);
			break;
		case 'gallery':
			$template = array(
				array( 'core/gallery', array(
					'columns' => 3,
				) ),
			);
			break;
		case 'link':
			$template = array(
				array( 'core/paragraph', array(
					'placeholder' => 'Write a link...',
				) ),
			);
			break;
		case 'image':
			$template = array(
				array( 'core/image', array(
					'align' => 'center',
				) ),
			);
			break;
		case 'quote':
			$template = array(
				array( 'core/quote', array(
					'placeholder' => 'Write a quote...',
				) ),
			);
			break;
		case 'status':
			$template = array(
				array( 'core/paragraph', array(
					'placeholder' => 'Write a status...',
				) ),
			);
			break;
		case 'video':
			$template = array(
				array( 'core/video', array(
					'align' => 'center',
				) ),
			);
			break;
		case 'audio':
			$template = array(
				array( 'core/audio', array(
					'align' => 'center',
				) ),
			);
			break;
		case 'chat':
			$template = array(
				array( 'core/paragraph', array(
					'placeholder' => 'Write a chat...',
				) ),
			);
			break;
	endswitch;

  $post_type_object = get_post_type_object( 'post' );
	$post_type_object->template_lock = 'all';
  $post_type_object->template = $template;
	add_filter( 'option_default_post_format', function() {
		return $_GET['post_format'];
	} );
}

add_action( 'init', 'block_theme_setup_format_block_template',20 );


// filter admin post title in listing to be name of format
function block_theme_post_format_admin_title( $title, $post_id ) {
	if ( ! wp_is_block_theme() ) {
		return $title;
	}
	$format = get_post_format( $post_id );
	if ( $format === false ) {
		return $title;
	}
	return get_post_format_string( $format );
}

add_filter( 'the_title', 'block_theme_post_format_admin_title', 10, 2 );

// Hook into the rendering of the query loop block.
function block_theme_post_format_post_template(  $block_content ) {
	// get the currently rendered post
	$post = get_post();
	// get permalink
	$permalink = get_permalink( $post );
	// get format name of post
	$format = get_post_format( $post );
	// check if post has any post format
	if ( get_post_format( $post ) === false ) {
		return $block_content;
	}

	$format_classes = implode( ' ', array(
		'wp-format-enabler',
		'is-' . $format
	) );
	return '<div class="'. $format_classes .'">'
		 . $post->post_content . '
		<a href="'. $permalink . '">'. __( 'Read more' ) .'</a>
	</div>';
}
add_action( 'render_block_core/null', 'block_theme_post_format_post_template' );

// register a dashboard widget to show buttons to create new posts for each format
function block_theme_post_format_dashboard_widget() {
	if ( ! wp_is_block_theme() ) {
		return;
	}
	add_meta_box(
		'block_theme_post_format_dashboard_widget',
		__( 'New Post' ),
		'block_theme_post_format_dashboard_widget_content',
		'dashboard', 'side', 'high' 
	);
}

function block_theme_post_format_dashboard_widget_content() {
	$formats = get_post_format_strings();
	unset( $formats['standard'] );
	?>
	<div class="post-format-buttons">
		<?php foreach ( $formats as $format => $label ): ?>
			<a href="<?php echo admin_url( 'post-new.php?post_format=' . $format ); ?>" class="button button-primary"><?php echo $label; ?></a>
		<?php endforeach; ?>
	</div>
	<?php
}

add_action( 'wp_dashboard_setup', 'block_theme_post_format_dashboard_widget' );