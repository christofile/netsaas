<div class="cf-elm-block" id="cfpf-format-video-fields" style="display: none;">
	<label for="cfpf-format-video-embed"><?php echo sprintf(__('Video URL (supported by <a href="%s">embeds</a>), shortcode or Raw HTML', THEMICO_DOMAIN), 'http://codex.wordpress.org/Embeds'); ?></label>
        <div class="description">
            <br />
            <strong>Examples:</strong>
            <ul>
                <li>oEmbed Provider: <?php echo esc_url('http://www.youtube.com/watch?v=UrX0x5qxlnU'); ?></li>
                <li>Raw HTML: <?php echo esc_html('<iframe width="590" height="332" src="http://www.youtube.com/embed/UrX0x5qxlnU?fs=1&feature=oembed" frameborder="0" allowfullscreen></iframe>'); ?></li>
                <li>Shortcode: <?php echo esc_html('[embed width="480"]http://www.youtube.com/watch?v=UrX0x5qxlnU[/embed]'); ?></li>
            </ul>

        </div>
	<textarea name="_format_video_embed" id="cfpf-format-video-embed" tabindex="1"><?php echo esc_textarea(get_post_meta($post->ID, '_format_video_embed', true)); ?></textarea>
</div>