<?php
/**
 * Search results page
 * 
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 
if ( have_posts() ): ?>
<div class="intro row">

<h1>Search Results for '<?php echo get_search_query(); ?>'</h1>	
</div>
<div class="content row">


<p class="results-found"><strong><?php	global $wp_query;
echo $wp_query->found_posts.' results found'; ?></strong></p>

<ul class="results">
<?php while ( have_posts() ) : the_post(); ?>
	<li>
		<article>
			<h2><a href="<?php esc_url( the_permalink() ); ?>" title="Permalink to <?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<p><?php if( get_field('author') ): ?><strong>Written by <?php the_field('author'); ?></strong></br><?php endif; ?>
  <?php
// Configuration
$max_length = 400; // Max length in characters
$min_padding = 30; // Min length in characters of the context to place around found search terms

// Load content as plain text
global $wp_query, $post;
$content = (!post_password_required($post) ? strip_tags(preg_replace(array("/\r?\n/", '@<\s*(p|br\s*/?)\s*>@'), array(' ', "\n"), apply_filters('the_content', $post->post_content))) : '');

// Search content for terms
$terms = $wp_query->query_vars['search_terms'];
if ( preg_match_all('/'.str_replace('/', '\/', join('|', $terms)).'/i', $content, $matches, PREG_OFFSET_CAPTURE) ) {
    $padding = max($min_padding, $max_length / (2*count($matches[0])));

  // Construct extract containing context for each term
  $output = '';
  $last_offset = 0;
  foreach ( $matches[0] as $match ) {
    list($string, $offset) = $match;
    $start  = $offset-$padding;
    $end = $offset+strlen($string)+$padding;
    // Preserve whole words
    while ( $start > 1 && preg_match('/[A-Za-z0-9\'"-]/', $content{$start-1}) ) $start--;
    while ( $end < strlen($content)-1 && preg_match('/[A-Za-z0-9\'"-]/', $content{$end}) ) $end++;
    $start = max($start, $last_offset);
    $context = substr($content, $start, $end-$start);
    if ( $start > $last_offset ) $context = '...'.$context;
    $output .= $context;
    $last_offset = $end;
  }

  if ( $last_offset != strlen($content)-1 ) $output .= '...';
} else {
  $output = $content;
}

if ( strlen($output) > $max_length ) {
  $end = $max_length-3;
  while ( $end > 1 && preg_match('/[A-Za-z0-9\'"-]/', $output{$end-1}) ) $end--;
  $output = substr($output, 0, $end) . '...';
}

// Highlight matches
$context = nl2br(preg_replace('/'.str_replace('/', '\/', join('|', $terms)).'/i', '<strong>$0</strong>', $output));
?>

<p class="search_result_context">
  <?php echo $context ?>
</p>
<p>
<?php echo "Score: $post->relevance_score"; ?>


<?php 
if( get_post_type() == 'articles' ) {  

foreach((get_the_terms($post->ID, 'journals')) as $term) {
$term_link = get_term_link( $term );
 ?> 




<p class="type">Journal: <?php echo '<a href="' . esc_url( $term_link ) . '" >' . $term->name . ' </a>'; ?> | <?php the_field('year', $term); ?></p>


 <?php   } 
}
 
elseif( get_post_type() == 'features' ) {  


 echo '<p class="type">Feature</p>';

} else {
    //do other stuff
}

 ?>
</p>




		</article>
	</li>
<?php endwhile; ?>
</ul>
<?php wpbeginner_numeric_posts_nav(); ?>

<?php else: ?>
<h2>No results found for '<?php echo get_search_query(); ?>'</h2>
<?php endif; ?>
</div>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>