<div class="my_meta_control simplog-archives">

<table class="form-table">
    <tbody>

            <tr>
                <th><label for="archives-type"><?php esc_html_e('Type', THEMICO_DOMAIN); ?></label></th>
                <td>
                    <?php $mb->the_field('type'); ?>

                    <select name="<?php $mb->the_name(); ?>" id="archives-type">
                        <option <?php $mb->the_select_state('yearly'); ?> value="yearly"><?php esc_html_e('Yearly', THEMICO_DOMAIN) ?></option>
                        <option <?php $mb->the_select_state('monthly'); ?> value="monthly"><?php esc_html_e('Monthly', THEMICO_DOMAIN) ?></option>
                        <option <?php $mb->the_select_state('weekly'); ?> value="weekly"><?php esc_html_e('Weekly', THEMICO_DOMAIN) ?></option>
                        <option <?php $mb->the_select_state('daily'); ?> value="daily"><?php esc_html_e('Daily', THEMICO_DOMAIN) ?></option>
                    </select>

                </td>
            </tr>

            <tr>
                <th><label for="archives-type-number"><?php esc_html_e('Sections', THEMICO_DOMAIN); ?></label></th>
                <td>
                    <?php $mb->the_field('sections'); $value = $mb->get_the_value(); if (!$value && 0 != $value) $value = get_option('posts_per_page'); ?>

                    <input id="archives-type-number" type="text" name="<?php $mb->the_name(); ?>" value="<?php echo esc_attr($value); ?>" />
                    <p class="description">
                        <?php esc_html_e('Number of type sections per page.', THEMICO_DOMAIN); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th><label for="archives-posts"><?php esc_html_e('Posts', THEMICO_DOMAIN); ?></label></th>
                <td>
                    <?php $mb->the_field('posts'); $value = $mb->get_the_value(); if (!$value && 0 != $value) $value = get_option('posts_per_page'); ?>

                    <input id="archives-posts" type="text" name="<?php $mb->the_name(); ?>" value="<?php echo esc_attr($value); ?>" />
                    <p class="description">
                        <?php esc_html_e('Number of posts per type section.', THEMICO_DOMAIN); ?>
                    </p>
                </td>
            </tr>

    </tbody>
</table>

</div>


