<?php
/**
 * The template for displaying search forms in simplog
 *
 * @package simplog
 * @since simplog 1.0
 */

if (is_404()) {
    $placeholder = __( 'Perhaps making a search would be handy right now&hellip;', THEMICO_DOMAIN );
} else {
    $placeholder = __( 'Search &hellip;', THEMICO_DOMAIN );
}

?>
<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
    <fieldset>
        <input type="text" class="field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" id="s" placeholder="<?php echo esc_attr($placeholder); ?>" />
    </fieldset>
</form>
