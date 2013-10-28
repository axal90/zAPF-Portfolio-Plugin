<!doctype html>
<html>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	    <head>
		    <meta charset="utf-8">
	        <meta name="viewport" content="width=device-width, initial-scale=1">
	        <title><?php the_title(); ?></title>
	        <?php wp_head(); ?>
	    </head>
	    <body>
    	
       		<article <?php post_class(''); ?> style="padding: 15px;">
				
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				<footer class="entry-meta">

					<a target="_<?php echo $open_link; ?>" href="<?php the_field('portfolio_item_url'); ?>">Link to Website</a>

					<time><?php the_date(); ?></time><p><?php the_tags('Tags:',','); ?></p><p>Categories: <?php the_category(', '); ?></p>

				</footer>
			</article>
		
   		</body>
    <?php endwhile; endif; ?>
</html>