<?php 
	get_header(); 
	// var_dump($memberAccess);
	wp_reset_query();		
	// get value in wordpress loop 
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$postId = get_the_ID();
			$title = get_the_title();
			$postDate = mysql2date('Y / n / j', get_the_date());
			$categories = get_the_category();
			$catIdStr = '';
			foreach($categories as $category){
				$catIdStr .= $category->term_id.',';
				if($category->slug === 'public'){
					$memberAccess = true;
				}
			}
			/* get content */
			$setMoreFlag = false;
			global $more;
			$fullContent = get_the_content();
			$more = false;
			$beforeContent = get_the_content('');
			$more = true;
			$afterContent = get_the_content('',true);

			if($fullContent == $beforeContent){
				$setMoreFlag = true;
			}

			if(!$memberAccess && $setMoreFlag){
				$beforeContent = mb_substr($fullContent, 0, 300);
			}

			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($postId), 'full' );
			$thumbnailUrl =  $thumbnail[0];
			if ( function_exists( 'get_coauthors' ) ) {
				$authors = get_coauthors($topnews->ID);
			} else {
				$authorName = get_the_author_meta( 'display_name' );
				$authorDescription = get_the_author_meta( 'description' );
			}
			$newsletterFlag = false;
			foreach($categories as $category){
				if($category->slug === 'newsletter'){
					$newsletterFlag = true;
				}
			}
			if($newsletterFlag){
				$newsletterAuthor = get_field('newsletter_author');
			}
			$takeaway = get_field('the_takeaway');
			$relatedPosts = get_posts( array( 'category__in' => wp_get_post_categories($post->ID), 'numberposts' => 3, 'post__not_in' => array($post->ID) ) );			
			$permalink = get_permalink();
		} // end while
	} // end if
?>

<section class="single-post">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="article-top">
					<div class="img-threshold">
						<?php if($thumbnailUrl): ?>
						<img src="<?php echo $thumbnailUrl; ?>" alt="<?php echo $title; ?>">
						<?php else: ?>
						<div style="width: 100%; height: 360px; background: #000; text-align: center;">	
							<img style="width: 100%; max-width: 270px; padding: 10% 0;>" src="<?php bloginfo( 'stylesheet_directory' ); ?>/css/img/yowu-logo.png" alt="yowureport">
						</div>
						<?php endif; ?>
					</div>
					<div class="bottom-fixed">
						<h1 class="post-title"><?php echo $title; ?></h1>
					</div>
				</div>
				<div class="article-cat col-md-10 col-md-offset-1 col-sm-12">
					<div class="post-date col-sm-5 no-padding">
						<?php echo $postDate; ?>
					</div>
					<div class="category-tags col-sm-7 no-padding">
						<?php 
							foreach($categories as $category){
								echo '<a href="'.get_category_link( $category->term_id ).'"><span class="label label-cat">'.$category->name.'</span></a>';
							}
						?>
					</div>					
				</div>
				<!-- before content part -->
				<div class="article-content col-md-10 col-md-offset-1 col-sm-12">
					<?php echo do_shortcode(wpautop($beforeContent)); ?>
				</div>				
				<!-- takeaway part -->
				<?php if(!empty($takeaway)): ?>
				<div class="article-takeaway col-md-10 col-md-offset-1 col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">The Takeaway</h3>
						</div>
						<div class="panel-body">
							<?php echo $takeaway; ?>
						</div>
					</div>				
				</div>
				<?php endif; ?>
				<!-- after content part -->
				<?php if(!$setMoreFlag && $memberAccess): ?>
				<div class="article-content col-md-10 col-md-offset-1 col-sm-12">
					<?php echo do_shortcode(wpautop($afterContent)); ?>
				</div>				
				<?php endif; ?>
						
				<div class="article-promotion col-md-10 col-md-offset-1 col-sm-12">
					<?php if(!$memberAccess): ?>
					<div class="read-more-title">繼續閱讀全文:</div>
					<?php endif; ?>
					<div class="col-md-6 col-sm-6 no-padding promo-left">
						<?php if(!$memberAccess): ?>
						<a class="btn btn-subscribe-big" href="/pick-register-plans">訂閱</a>
						<?php else: ?>
						<div class="share-container">
							<a class="btn btn-share" href="#">
								<i class="fa fa-share-alt"></i><span>分享</span>
							</a>							
							<ul class="share-menu">
								<li><a target="_blank" href="https://www.facebook.com/sharer.php?u=<?php echo $permalink; ?>&amp;p[images][0]=<?php echo $thumbnailUrl; ?>"><i class="fa fa-facebook"></i>facebook分享</a></li>
								<li><a href="mailto:?subject=推薦您一個好站&amp;body=有物報告 <?php echo $permalink; ?>"><i class="fa fa-envelope"></i>電子郵件分享</a></li>
							</ul>							
						</div>

						<?php endif; ?>
					</div>
					<div class="col-md-6 col-sm-6 no-padding promo-right">
						<?php if($memberAccess&&is_user_logged_in()): ?>
						<a class="btn btn-go-forum" href="#"><i class="fa fa-comment"></i>前往會員專屬論壇</a>
						<?php else: ?>
						<a class="btn btn-login-big" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">登入</a>
						<?php endif; ?>
					</div>					
				</div>

				<?php if($newsletterFlag): ?>
				<div class="newsletter-author col-md-10 col-md-offset-1 col-sm-12">
					<?php echo $newsletterAuthor; ?>	
				</div>		
				<?php else: ?>
				<div class="article-author col-md-10 col-md-offset-1 col-sm-12">
					<?php 
						if(count($authors)>0):
							foreach($authors as $author):								
					?>
								<div class="author-row row">
									<div class="avatar-place col-md-2 col-sm-3">
										<a href="<?php echo get_author_posts_url( $author->ID ); ?>">
											<?php echo get_avatar( $author->ID, 150 ); ?>
										</a>
									</div>
									<div class="avatar-content col-md-10 col-sm-9">
										<a href="<?php echo get_author_posts_url( $author->ID ); ?>"><h3><?php echo $author->display_name; ?></h3></a>
										<p><?php echo get_the_author_meta( 'description', $author->ID ); ?></p>
									</div>
								</div>							
							<?php endforeach; ?>

					<?php else: ?>
						<div class="author-row row">
							<div class="avatar-place col-md-2">
								<a href="<?php echo get_author_posts_url( $author->ID ); ?>">
									<?php echo get_avatar( get_the_author_meta( 'ID' ), 150 ); ?>
								</a>
							</div>
							<div class="avatar-content col-md-10">
								<a href="<?php echo get_the_author_link(); ?>"><h3><?php echo $authorName; ?></h3></a>
								<p><?php echo $authorDescription; ?></p>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php
					$args = array(
						'post_type' => 'ads',
						'cat' => $catIdStr,
						'orderby' => 'date',
						'order' => 'DESC',
						'posts_per_page' => 1,
					);
					// $query = new WP_Query( $args );
					$ads = query_posts($args);
					/* Restore original Post Data */
					wp_reset_postdata();				
				?>
				<?php if(!empty($ads)): ?>
				<div class="support-bar col-md-10 col-md-offset-1 col-sm-12">
					<span class="label label-support">贊助訊息</span>
					<?php 
					foreach($ads as $ad){
						echo '<div>'.do_shortcode(wpautop($ad->post_content)).'</div>';
					}
					?>							
				</div>
			<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<section class="related-article">
	<div class="container">
		<div class="row">
			<h3 class="col-md-12">推薦文章</h3>
			<?php 
				foreach($relatedPosts as $relatedPost):
					$authorName = get_the_author_meta( 'display_name', $relatedPost->post_author );
					$authorId = get_the_author_meta( 'id', $relatedPost->post_author );
					$postDate = mysql2date('Y / n / j', $relatedPost->post_date);
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($relatedPost->ID), 'full' );
					$thumbnailUrl =  $thumbnail[0];				
			?>
			<div class="related-article-block col-md-4 col-sm-4">
				<div class="img-threshold">
					<?php if($thumbnailUrl): ?>
					<img src="<?php echo $thumbnailUrl; ?>" alt="<?php echo $relatedPost->post_title; ?>">	
					<?php else: ?>
					<div style="width: 100%; height: 360px; background: #000; text-align: center;">	
						<img style="width: 100%; max-width: 270px; padding: 10% 0;>" src="<?php bloginfo( 'stylesheet_directory' ); ?>/css/img/yowu-logo.png" alt="yowureport">
					</div>					
					<?php endif; ?>
				</div>
				<a href="<?php echo get_post_permalink($relatedPost->ID); ?>"><h5><?php echo $relatedPost->post_title; ?></h5></a>
				<p><a href="<?php echo get_author_posts_url( $authorId ); ?>"><span class="author"><?php echo $authorName; ?></span></a><span class="date"><?php echo $postDate; ?></span></p>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
</section>

<script>
	jQuery(document).ready(function($) {
		/* share slide toggle */
		$('.share-container .btn-share').click(function(e){
			e.preventDefault();
			$(this).siblings('.share-menu').slideToggle();
		});
	});
</script>

<?php  get_footer(); ?>