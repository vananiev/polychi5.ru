<?php
/*
	Template Name: News
*/
?>
<?php get_header(); ?>
<div id="shower" href="#"></div>
<a id="move_up" href="#" name="up-mark" style="padding-top: 40px;">&#9650; Вверх</a>
<script type='text/javascript' src='/files/js/jquery-1.8.2.js'></script>
<script type="text/javascript" >
jQuery(function () {
    jQuery(window).scroll(function () {
        if (jQuery(this).scrollTop() > 100)
            jQuery('a#move_up').fadeIn(600);
        else
            jQuery('a#move_up').fadeOut(600);
    });
    jQuery('a#move_up').click(function () {
        jQuery('body,html').animate({
            scrollTop: 0
        }, 600);
        return false;
    });
});
</script>
<div class="content">

		<?php // Display blog posts on any page @ http://m0n.co/l
		$temp = $wp_query; $wp_query= null;
		$wp_query = new WP_Query(); $wp_query->query('showposts=5' . '&paged='.$paged);
		?>		
		<div class="news_block">
		<fieldset class="cat_fieldset">
		<legend class="cat_legend">
      	Новости
    	</legend>
    	<ul>
    	<?php
    	//query_posts($query_string . '&cat=2');
    	query_posts( array ( 'category_name' => 'news', 'posts_per_page' => -1 ) );
		while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
		<li style="border-bottom:1px solid #999; padding-bottom:20px;min-height:130px;">
		<h2 style="border-bottom:0;padding:0;margin:10px 0 2px 0;"><a href="<?php the_permalink(); ?>" title="Read more"><?php the_title(); ?></a></h2>
		<div class="date" title="First publised"><?php the_date(); ?></div>		
		<?php the_excerpt(); ?>
		
		<a href="<?php the_permalink(); ?>" title="Read more">Подробнее...</a>
		</li>
		<?php endwhile; ?>
		</ul></fieldset></div>

		<?php if ($paged > 1) { ?>

		<nav id="nav-posts">
			<div class="prev"><?php next_posts_link('&laquo; Previous Posts'); ?></div>
			<div class="next"><?php previous_posts_link('Newer Posts &raquo;'); ?></div>
		</nav>

		<?php } else { ?>

		<nav id="nav-posts">
			<div class="prev"><?php next_posts_link('&laquo; Previous Posts'); ?></div>
		</nav>

		<?php } ?>

		<?php wp_reset_postdata(); ?>

</div>
<?php get_footer(); ?> 

