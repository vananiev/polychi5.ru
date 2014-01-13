<?php get_header(); ?>
		<!--Content-->
		<div class='content'>
		
	
		<div id="home">

	
			<?php
			if (is_category()) { echo '<h1>'; single_cat_title(); echo '</h1>'; }
			elseif (is_search()) {echo '<h1>'; bloginfo('name'); echo '</h1>';}
			//else wp_title('',true);
			?>

			<?php while ( have_posts() ) : the_post() ?>

			  <div id="<?php the_ID() ?>" class="excerpt">
				<h2><a href="<?php the_permalink() ?>"> <?php the_title() ?></a></h2>
				<div class="summary">
				  <?php the_excerpt(); ?>
				</div>
			  
				   <div class="homeinfo">
						<span class="author" title="Author of the article">Автор <?php the_author_link();  ?>, </span>
						<span class="date" title="First publised"><?php the_date(); ?></span> | 
						<span class="category" title="Категории статьи"><?php the_category(', ') ?></span>
						<span class="comments"><?php
					 if(comments_open())
					 {
						echo " | ";
						comments_popup_link(
						 __('0 коммент.', ''),
						 __('1 коммент.', ''),
						 __('% коммент.', ''),
						 'comments-link',
						 'Shuttt'); 
					 } 
					?> 
					</span>  
					<span class="edit"><?php edit_post_link(__('edit?')); ?></span>
					<br>
					<span class="tags"><?php the_tags(); ?></span>
				   </div>

			  </div><!--id/excerpt-->
			  
			<div id="pagination">
			<?php wp_link_pages(); ?>
			</div>

			<?php  endwhile; ?>

			<div class="navigation">
			<div class="alignleft"><?php previous_posts_link(__('Следующие записи', 'encyclopedia')) ?></div>
			<div class="alignright"><?php next_posts_link(__('Предыдущие записи', 'encyclopedia')) ?></div>
			</div>

			<br />

			<?php	if(is_home()){ ?>
				<script type="text/javascript" >
					location.href="http://polychi5.ru/";
				</script>
			<?php }
			if(!is_home())
			{
			  $categories = get_categories('number=8');
			  foreach ($categories as $cat) 
			  {
				global $post;

				$id = $cat->cat_ID;
				$cn = get_cat_name($id);
				if($cn == "") continue;
				$cn = ucfirst($cn);
			   
				echo "<fieldset class='cat_fieldset'><legend class='cat_legend'>$cn</legend>\n";
				echo "<ul>\n";

				$myposts = get_posts("numberposts=5&offset=0&category=$id");
				foreach($myposts as $post) :
			?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php 
				endforeach; 
				echo "</ul>";
				echo "</fieldset>";
			}
			}
			?>

			</div><!--home-->
		
		
		</div>
		<!--/Content-->
	</div>
	<!--/Main-->	

	
	<?php if( !isset($_COOKIE['how_show_page']) ||  false!==strpos($_COOKIE['how_show_page'], 'header') ){ ?>
	<a class='transparent_button_2' title='Открыть меню' style='position:absolute;top:300px;right:3px;' href="javascript:slide('left-colon', 'show', 'up', null, 295, 0)"><img src='<?php echo THEME_MEDIA_RELATIVE;?>/images/yes.png' alt="[i]"></a>	
	<!--Left-colon-->
	<div class='left-colon' id='left-colon' >
		<!--User-info-->
		<div class='user'>
			<?php 
				show_box('user', BOX_HEAD);
				require(SCRIPT_ROOT.$INCLUDE_MODULES['USERS']['PATH']."/in.php");
				show_box('user', BOX_FOOTER);	
			?>
		</div>
		<!--User-info-->
		<!--submenu-->
		<div id='undermenu_button' style='position:relative;'>	
			<a class='button' title='Отобразить меню' style='position:absolute;top:-35px;right:15px;' href="javascript:	if(document.getElementById('umnu').style.display=='none')
																															{resize('umnu','height',null,500);}
																														else
																															{resize('umnu','height',0,500);}
																														"><img class='button-img' src='<?php echo THEME_MEDIA_RELATIVE;?>/images/arrow-down.png'></a>			
		</div>
		<div id="umnu" class="undermenu">
			<?php 
				show_box('standart', BOX_HEAD);
				require(THEME_ROOT."/submenu.php");
				show_box('standart', BOX_FOOTER);	
			?>
		</div>
		<!--/submenu-->
		<a class='transparent_button_2' title='Скрыть меню' style='position:absolute;top:5px;right:4px;' href="javascript:slide('left-colon', 'hide', 'up', -100, null, 0)"><img style='width:30px;height:30px;'src='<?php echo THEME_MEDIA_RELATIVE;?>/images/no.png' alt="[х]"></a>			
	</div>
	<!--Left-colon-->
	<?php } ?>
<?php get_footer(); ?>

