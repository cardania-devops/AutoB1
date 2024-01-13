<?php
// This is a template for displaying custom post types
get_header();

while ( have_posts() ) : the_post();
    // Your custom post type content here
endwhile;

get_footer();
