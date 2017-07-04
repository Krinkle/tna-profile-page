<?php
/*
 * WordPress Breadcrumbs
 * author: Dimox
 * version: 2015.09.14
 * license: MIT
*/
function dimox_breadcrumb() {
    /* === OPTIONS === */
    $text['home']     = 'Home'; // text for the 'Home' link
    $text['category'] = 'Archive by Category "%s"'; // text for a category page
    $text['search']   = 'Search Results for "%s" Query'; // text for a search results page
    $text['tag']      = 'Posts Tagged "%s"'; // text for a tag page
    $text['author']   = 'Articles Posted by %s'; // text for an author page
    $text['404']      = 'Error 404'; // text for the 404 page
    $text['page']     = 'Page %s'; // text 'Page N'
    $text['cpage']    = 'Comment Page %s'; // text 'Comment Page N'
    $wrap_before    = '<div class="breadcrumbs">'; // the opening wrapper tag
    $wrap_after     = '</div><!-- .breadcrumbs -->'; // the closing wrapper tag
    $sep            = '>'; // separator between crumbs
    $sep_before     = '<span class="sep">'; // tag before separator
    $sep_after      = '</span>'; // tag after separator
    $show_home_link = 1; // 1 - show the 'Home' link, 0 - don't show
    $show_on_home   = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $show_current   = 1; // 1 - show current page title, 0 - don't show
    $before         = '<span class="current">'; // tag before the current crumb
    $after          = '</span>'; // tag after the current crumb
    /* === END OF OPTIONS === */
    global $post;
    global $pre_path;
    $home_link      = 'http://www.nationalarchives.gov.uk/';
    $link_before    = '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';
    $link_after     = '</span>';
    $link_attr      = ' itemprop="url"';
    $link_in_before = '<span itemprop="title">';
    $link_in_after  = '</span>';
    $link           = $link_before . '<a href="%1$s"' . $link_attr . '>' . $link_in_before . '%2$s' . $link_in_after . '</a>' . $link_after;
    $frontpage_id   = get_option('page_on_front');
    $parent_id      = $post->post_parent;
    $sep            = ' ' . $sep_before . $sep . $sep_after . ' ';
    if (is_home() || is_front_page()) {
        // TNA additional breadcrumbs for front page
        global $pre_crumbs;
        if ( $pre_crumbs ) {
            $numItems = count($pre_crumbs);
            $i = 0;
            global $pre_crumbs_st;
            foreach ($pre_crumbs as $crumb_name => $crumb_path) {
                if (++$i === $numItems) {
                    $pre_crumbs_st .= ' <span class="sep">&gt;</span> <span>'. $crumb_name . '</span> ';
                } else {
                    $pre_crumbs_st .= ' <span class="sep">&gt;</span> <span><a href="' . $crumb_path . '">'. $crumb_name . '</a></span> ';
                }
            }
        }
        global $pre_crumbs_st;
        if ($show_on_home) echo $wrap_before . '<a href="' . $home_link . '">' . $text['home'] . '</a>';
        if ($pre_crumbs_st) echo $pre_crumbs_st;
        if ($show_on_home) echo $wrap_after;
    } else {
        // TNA additional breadcrumbs
        global $pre_crumbs;
        if ( $pre_crumbs ) {
            global $pre_crumbs_st;
            foreach ($pre_crumbs as $crumb_name => $crumb_path) {
                $pre_crumbs_st .= ' <span class="sep">&gt;</span> <span><a href="' . $crumb_path . '">'. $crumb_name . '</a></span> ';
            }
        }
        echo $wrap_before;
        if ($show_home_link) echo sprintf($link, $home_link, $text['home']);
        if ( is_page() && !$parent_id ) {
            global $pre_crumbs_st;
            if ($pre_crumbs_st) echo $pre_crumbs_st;
            if ($show_current) echo $sep . $before . get_the_title() . $after;
        } elseif ( is_page() && $parent_id ) {
            global $pre_crumbs_st;
            if ($pre_crumbs_st) echo $pre_crumbs_st;
            if ($show_home_link) echo $sep;
            if ($parent_id != $frontpage_id) {
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_page($parent_id);
                    if ($parent_id != $frontpage_id) {
                        $breadcrumbs[] = sprintf($link, str_replace(home_url(), $pre_path, get_permalink($page->ID)), get_the_title($page->ID));
                    }
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                for ($i = 0; $i < count($breadcrumbs); $i++) {
                    echo $breadcrumbs[$i];
                    if ($i != count($breadcrumbs)-1) echo $sep;
                }
            }
            if ($show_current) echo $sep . $before . get_the_title() . $after;
        }  elseif ( is_404() ) {
            global $pre_crumbs_st;
            if ($pre_crumbs_st) echo $pre_crumbs_st;
            if ($show_home_link && $show_current) echo $sep;
            if ($show_current) echo $before . $text['404'] . $after;
        } elseif ( is_single() && !is_attachment() ) {
            global $pre_crumbs_st, $pre_crumbs_post;
            if ($pre_crumbs_st) echo $pre_crumbs_st;
            if ($pre_crumbs_post) echo $pre_crumbs_post;
            if ($show_home_link) echo $sep;
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $pieces = explode("/", $actual_link);
            $reverse = array_reverse($pieces);
            $url = substr($actual_link, 0, strpos($actual_link, $reverse[2]));
            echo '<a href='. home_url() . $pre_path . '/our-research-and-people/' .$reverse[2] .'>Staff profiles</a>';
            echo $sep;
            echo $parent_title = get_the_title( $post->post_parent );
        }
        echo $wrap_after;
    }
} // end of dimox_breadcrumbs()