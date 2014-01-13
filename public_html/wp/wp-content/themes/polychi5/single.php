<?php get_header(); ?>

<div id="shower" href="#"></div>
<a id="move_up" href="#" name="up-mark" style="padding-top: 40px;z-index:1;">&#9650; Вверх</a>
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

<?php while ( have_posts() ) : the_post() ?>

<h1 class="postitle"><?php the_title(); ?></h1>
<!--p>< ?php the_category(' ') ?></p-->
<div class="content"><?php  the_content();  ?></div>
<!--div class="postinfo">Автор: <span class="author">< ?php  the_author(); ?></span>, <span class="date">< ?php  the_date(); ?></span> <span class="edit">< ?php edit_post_link(__('edit?')); ?></span>
</div>
Рубрики: <span class="cat">< ?php the_category(', ') ; ?></span>
<br>
<span class="tags"><?php the_tags(); ?></span> 
</div-->

<?php wp_link_pages(array('before' => '<p><strong>Страницы:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

<!--div class="navigation">
<div class="alignleft">< ?php next_post_link('Предыдущие записи: %link') ?></div>
<div class="alignright">< ?php previous_post_link('Следующие записи: %link') ?></div>
</div-->

<div class="comments">
<?php if (comments_open()) comments_template(); ?>
</div>

<?php endwhile ?>

<!--div class="lastlist">
<p>Последние статьи</p>
<ul>
<?php wp_get_archives('type=postbypost&limit=10'); ?>
</ul>
</div-->

</div>

<?php get_footer(); ?>
