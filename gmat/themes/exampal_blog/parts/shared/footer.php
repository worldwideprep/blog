	</div></div>
<div id="search-newsletter" class="row">
	<div class="container">



	</div>
</div>	

<div class="row foot1">
    <div class="row container"><div class="col span12">
    <?php wp_nav_menu( array( 'theme_location' => 'site-info') ); ?>
<div class="foot_icons">
<a href="<?php echo get_field('l2','option');?>" target="_blank"><i class="fa fa-twitter"></i></a>
<a href="<?php echo get_field('l1','option');?>" target="_blank"><i class="fa fa-facebook"></i></a>
<a href="<?php echo get_field('l4','option');?>" target="_blank"><i class="fa fa-google-plus"></i></a>
<a href="<?php echo get_field('l3','option');?>" target="_blank"><i class="fa fa-linkedin"></i></a>
<a href="<?php echo get_field('l4b','option');?>" target="_blank"><i class="fa fa-youtube"></i></a>
</div>
    </div></div>
</div>
	
<div class="row foot2">
    <div class="row container"><div class="col span12">
    <p>
&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
    <?php wp_nav_menu( array( 'theme_location' => 'site-info2') ); ?>
    </div></div>
</div>
	
<?php
/*	<div id="footer" class="row">
	<div class="row container">
		<div class="col span_12">
<p>
&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
<?php wp_nav_menu( array( 'theme_location' => 'site-info') ); ?>
<div class="foot_icons">
<a href="<?php echo get_field('l2','option');?>" target="_blank"><i class="fa fa-twitter"></i></a>
<a href="<?php echo get_field('l1','option');?>" target="_blank"><i class="fa fa-facebook"></i></a>
<a href="<?php echo get_field('l4','option');?>" target="_blank"><i class="fa fa-google-plus"></i></a>
</div>

		<p class="copy_right"><a href="#" target="_blank"><img src="http://codebox-ltd.com/exampal/wp-content/uploads/2016/05/logo_hellit.png" alt="logo footer"/>Say Digital Design</a></p>
		</div>
	</div>
</div>
*/
?>