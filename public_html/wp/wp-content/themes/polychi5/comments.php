
<?php

// Do not delete these lines
	if (isset($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	
	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e('Эта запись защищена паролем. Введите пароль для просмотра комментариев.', 'Encyclopedia'); ?></p>
	<?php
		return;
	}
?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>
<div id="comments">
<?php comments_number('Пока нет комментариев.', 'Есть 1 комментарий.', 'Есть % коммент.');?> <?php printf(__('к сообщению: &#8220;%s&#8221;', 'Encyclopedia'), the_title('', '', false)); ?>
</div>

<ol class="commentlist">
<?php wp_list_comments();?>
</ol>

<div class="navigation">
<div class="alignleft"><?php previous_comments_link() ?></div>
<div class="alignright"><?php next_comments_link() ?></div>
</div>

<?php else : // this is displayed if there are no comments so far ?>

<?php if ( comments_open() ) : ?>
<!-- If comments are open, but there are no comments. -->

<?php else : // comments are closed ?>
<!-- If comments are closed. -->
<p class="nocomments"><?php _e('Комментарии закрыты.', 'Encyclopedia'); ?></p>

<?php endif; ?>
<?php endif; ?>

<?php if ( comments_open() ) : ?>

<div id="respond">

<h3><?php comment_form_title( __('Написать комментарий', ''), __('Написать комментарий к %s' , '') ); ?></h3>

<div id="cancel-comment-reply">
<small><?php cancel_comment_reply_link() ?></small>
</div>

<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
<p><?php printf(__('Вы должны <a href="%s">войти</a> чтобы добавить комментарий.', 'Encyclopedia'), wp_login_url( get_permalink() )); ?></p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( is_user_logged_in() ) : ?>

<p><?php printf(__('Вы вошли как: <a href="%1$s">%2$s</a>.', 'Encyclopedia'), get_option('siteurl') . '/wp-admin/profile.php', $user_identity); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'Encyclopedia'); ?>"><?php _e('Выйти &raquo;', 'Encyclopedia'); ?></a></p>

<?php else : ?>

<p><input type="text" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
<label for="author"><small><?php _e('Имя', 'Encyclopedia'); ?> <?php if ($req) _e("(обязательно)", "Encyclopedia"); ?></small></label></p>

<p><input type="text" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
<label for="email"><small><?php _e('Почта (скрыто)', 'Encyclopedia'); ?> <?php if ($req) _e("(обязательно)", "Encyclopedia"); ?></small></label></p>

<p><input type="text" name="url" id="url" value="<?php echo  esc_attr($comment_author_url); ?>" size="22" tabindex="3" />
<label for="url"><small><?php _e('Сайт', 'Encyclopedia'); ?></small></label></p>

<?php endif; ?><?php /* Локализация шаблона - FreeWordpressThemes.ru */ ?>

<p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>

<p><input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Добавить комментарий', 'Encyclopedia'); ?>" />
<?php comment_id_fields(); ?>
</p>
<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>

