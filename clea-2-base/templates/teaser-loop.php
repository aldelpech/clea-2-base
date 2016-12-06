<!-- Ceci est une copie d'un des templates de Shortcodes ultimates
	Il est modifié pour inclure les résumés et des classes spécifiques (pour le css)
-->

<!-- was "su-posts su-posts-teaser-loop" -->
<div class="al-category-loop">
	<?php
		// Posts are found
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) :
				$posts->the_post();
				global $post;
				?>
				<div id="su-post-<?php the_ID(); ?>" class="su-post">
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="su-post-thumbnail" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
					<?php endif; ?>
					<h3 class="su-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				</div>
				<div class="su-post-excerpt">
					<?php the_excerpt(); ?>
				</div>				
				<?php
			endwhile;
		}
		// Posts not found
		else {
			echo '<h4>' . __( 'Posts not found', 'su' ) . '</h4>';
		}
	?>
</div>