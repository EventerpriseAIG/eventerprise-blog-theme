<?php
/**
 * Template Name: Modules
 */
?>
<?php get_header(); ?>
<?php //get_template_part( 'sections/featured-area' ); ?>
<?php //get_template_part( 'sections/ads/below-header' ); ?>
<?php
$curr_page = is_front_page() ? absint(get_query_var('page')) : absint(get_query_var('paged'));
$display_content = vce_get_page_meta( get_the_ID(), 'display_content' );
?>
<?php if( $display_content['position'] == 'up' ): ?>
	<?php get_template_part( 'sections/content-modules-page' ); ?>
<?php endif; ?>
	<div id="content" class="container site-content">
		<?php global $vce_sidebar_opts; ?>
		<?php if ( $vce_sidebar_opts['use_sidebar'] == 'left' ) { get_sidebar(); } ?>
		<div id="primary" class="vce-main-content vce-border-top">
			<?php global $wp_query; ?>
			<?php $modules = vce_get_page_meta( get_the_ID(), 'modules' ); ?>
			<?php if ( !empty( $modules ) ) : ?>
			<?php foreach ( $modules as $k => $mod ) : ?>
			<?php
			$mod = vce_define_module_type($mod);
			$mod['curr_page'] = $curr_page;
			$apply_paged = $curr_page > 1 && $k == (count($modules) -1 ) ? $curr_page : false;
			?>
			<?php $wp_query = vce_get_module_query( $mod, $apply_paged ); ?>
			<?php
			if($curr_page > 1 ){
				if( $k < (count($modules) - 1)  ){
					wp_reset_query();
					continue; //skip modules, only show last one if paginated page
				} else {
					$modules[$k]['top_layout'] = 0;
					$mod['top_layout'] = 0; //remove top layout from last module if paginated page
				}
			}
			?>

			<?php
			$args = [
				'post_type'=>'post',
				'posts_per_page'=>3,
				'meta_query'=> [
					[
						'key' => 'is_featured',
						'value' => '1',
						'compare' => '==',
					]
				],
			];
			$loop = new WP_Query($args);
			?>
			<div class="main-box">
				<h3 class="main-box-title " data-fontsize="22" data-lineheight="25">Featured</h3>
			</div>
			<div class="featured-post">
				<?php if($loop->have_posts()) : ?>
					<?php while ($loop->have_posts()) : $loop->the_post(); ?>
						<article class="featured-img col-3">
							<div class="meta-image">
								<?php
								$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ),'full' );
								$image = $image[0];
								?>
								<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
									<div class="featured" style="background-image: url('<?php echo $image; ?>')"></div>
								</a>
							</div>
							<div class="content">
								<header class="entry-header">
									<?php if ( vce_get_option( 'lay_c_cat' ) ) : ?>
										<span class="meta-category"><?php echo vce_get_category(); ?></span>
									<?php endif; ?>
									<h6 class="entry-title single-line" data-fontsize="22" data-lineheight="28"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h6>
									<?php if ( $meta = vce_get_meta_data( 'lay-c' ) ): ?><div class="entry-meta"><?php echo $meta; ?></div><?php endif; ?>
								</header>
								<div class="entry-content">
									<p class="single-line" title="<?php echo get_the_excerpt(); ?>"><?php echo get_the_excerpt(); ?></p>
								</div>
							</div>
						</article>
					<?php endwhile; ?>
				<?php endif; wp_reset_postdata(); ?>
			</div>
			<?php echo vce_open_column_wrap($mod); ?>
			<div id="main-box-<?php echo ($k+1); ?>" class="main-box vce-border-top <?php echo vce_get_column_class($mod); ?>">
				<?php if(!empty($mod['title'])): ?>
					<h3 class="main-box-title <?php echo vce_get_cat_class( $mod ); ?>"><?php echo vce_get_module_title($mod); ?></h3>
				<?php endif; ?>
				<div class="main-box-inside <?php echo vce_get_mainbox_class( $mod ); ?>">
					<div class="featured-post home-page-post">
						<?php if($mod['type'] == 'blank'): ?>
							<?php echo do_shortcode( wpautop($mod['content'])); ?>
						<?php endif; ?>
						<?php if ( $mod['type'] != 'blank' && have_posts() ) : ?>
						<?php $i = 0; while ( have_posts() ) : the_post(); $i++; ?>
						<?php echo vce_loop_wrap_div($mod, $i, count( $wp_query->posts ) ); ?>
						<?php get_template_part( 'sections/loops/layout-'.vce_module_layout($mod, $i) ); ?>

						<?php if ( $i == ( count( $wp_query->posts ) ) ) : ?>
					</div>
				</div>
			<?php endif;?>
			<?php endwhile; ?>

			<?php  echo vce_check_module_action($modules, $k); ?>
			<?php else: ?>
				<?php if ( $mod['type'] != 'blank' && current_user_can( 'manage_options' ) ): ?>
					<p class="no-modules-msg"><?php printf( __( 'No posts match your criteria. Please choose different options in <a href="%s">Module Generator</a>.', THEME_SLUG ), admin_url( 'post.php?post='.get_the_ID().'&action=edit#vce_hp_modules' ) ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
				<?php wp_reset_query(); ?>
			</div>
		</div>
	<?php echo vce_close_column_wrap($modules, $k ); ?>
	<?php endforeach;?>
	<?php else: ?>
		<?php if ( current_user_can( 'manage_options' ) ): ?>
			<div class="main-box">
				<h3 class="main-box-title"><?php _e( 'Oooops!', THEME_SLUG ); ?></h3>
				<div class="main-box-inside">
					<p class="no-modules-msg"><?php printf( __( 'You don\'t have any modules yet. Hurry up and create your <a href="%s">first module</a>.', THEME_SLUG ), admin_url( 'post.php?post='.get_the_ID().'&action=edit#vce_hp_modules' ) ); ?></p>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	</div>
<?php if ( $vce_sidebar_opts['use_sidebar'] == 'right' ) { get_sidebar(); } ?>
	</div>
<?php if($display_content['position'] == 'down'): ?>
	<?php get_template_part( 'sections/content-modules-page' ); ?>
<?php endif; ?>
<?php get_footer(); ?>