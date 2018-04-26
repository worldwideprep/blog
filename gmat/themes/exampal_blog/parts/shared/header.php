<script type="text/javascript">
jQuery(window).load(function(){
	var mres=jQuery('.mc4wp-alert').html();
	if(typeof mres!='undefined'){
		if(mres!='')jQuery('.newsletter_top .mc4wp-form-fields').html('<div class="mresp">'+mres+'</div>');
	}
<?php if(is_user_logged_in()){?>
	jQuery('#pum-197').remove();<?php } else { ?>
	var vide='<div class="fullscreen-bg"><video loop muted autoplay poster="<?php echo get_post_meta('197','vid_img',true);?>" class="fullscreen-bg__video"><source src="<?php echo get_post_meta('197','vid_url',true);?>" type="video/mp4"></video></div>';
	jQuery('#pum-197 .pum-container').append(vide);
	var lgo='<div class="pum-logo"><img src="<?php echo get_template_directory_uri().'/images/newsletter_logo.png'; ?>" alt="<?php bloginfo('name'); ?>" /></div>';
	jQuery('.pum-title').prepend(lgo);
<?php }?>
});
</script>
<!-- k check check -->
<?php if(is_user_logged_in()){?>
<style>
html.pum-open.pum-open-overlay, html.pum-open.pum-open-overlay.pum-open-fixed .pum-overlay {
    overflow: visible!important;
}
</style>
<?php } ?>
<div id="header_container" class="row">
<header class="container row">


	<div class="row navigation-wrapper">

<?php $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div'; ?>
                <<?php echo $heading_tag; ?> id="logo"><a href="https://exampal.com" title="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>">
                <img src="<?php global $website_options; echo str_replace('http://','https://',$website_options['website-logo']['url']); ?>" alt="<?php bloginfo('name'); ?>" /></a>
                </<?php echo $heading_tag; ?>>
<div id="navigation-wrapper">
<?php wp_nav_menu( array( 'theme_location' => 'primary') ); ?>

        <?php
if ( is_user_logged_in() ) { ?>
    <?php global $current_user;
      wp_get_current_user(); ?>
<div class="logged-in-header login-header">
<a class="my_acc" href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a>
<?php /*?>
 <a href="/my-account/" class="sign_up_menu my_acc">My Account</a>
 <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout"><i class="fa mobile-only fa-lg fa-sign-out"></i><span class="non-mobile">Logout</span></a>
*/
?>

</div>

 <?php } else {
    echo '<div class="login-header"><a href="https://exampal.com/gmat/login" class="sign_in_menu">Sign In</a><a href="https://exampal.com/gmat" target="_blank" class="sign_up_menu isgin">START YOUR GMAT COURSE</a></div>';
}
?>
        <?php  ?>

</div>
</div>


</header>
</div>
<div id="main" class="row">
	<section class="container">
