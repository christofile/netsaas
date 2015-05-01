<div class="my_meta_control rythm-shortcode-metabox">


<table class="form-table">
    <tbody>
        <tr>
            <th colspan="2">
                <strong><?php esc_html_e('Google Map', THEMICO_DOMAIN); ?></strong>
            </th>
        </tr>

                <tr>
                    <th><label for="rythm-google-map-api-key"><?php esc_html_e('API Key', THEMICO_DOMAIN); ?></label></th>
                    <td>
                        <?php $mb->the_field('apikey'); ?>
                        <input type="text" value="<?php $mb->the_value(); ?>" name="<?php $mb->the_name(); ?>" id="rythm-google-map-api-key"/>

                        <p class="description">
                            <?php esc_html_e('To be able to use nice marker information you should load the Maps API using an API key.', THEMICO_DOMAIN) ?>
                            <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key"><?php esc_html_e('Obtaining an API Key', THEMICO_DOMAIN); ?></a>
                        </p>

                    </td>
                </tr>

                <tr>
                    <th><label for="rythm-google-map-marker-title"><?php esc_html_e('Marker Title', THEMICO_DOMAIN); ?></label></th>
                    <td>
                        <?php $mb->the_field('marker_title'); ?>
                        <input type="text" value="<?php $mb->the_value(); ?>" name="<?php $mb->the_name(); ?>" id="rythm-google-map-marker-title"/>
                    </td>
                </tr>


                <tr>
                    <th><label for="rythm-google-map-latitude"><?php esc_html_e('Point', THEMICO_DOMAIN); ?></label></th>
                    <td>

                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><label for="rythm-google-map-latitude"><span style="color: red">*</span> <?php esc_html_e('Latitude', THEMICO_DOMAIN); ?></label></th>
                                    <td>
                                    <?php $mb->the_field('latitude'); ?>
                                    <input type="text" value="<?php $mb->the_value(); ?>" name="<?php $mb->the_name(); ?>" id="rythm-google-map-latitude"/>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="rythm-google-map-longitude"><span style="color: red">*</span> <?php esc_html_e('Longitude', THEMICO_DOMAIN); ?></label>
                                    </th>
                                    <td>
                                    <?php $mb->the_field('longitude'); ?>
                                    <input type="text" value="<?php $mb->the_value(); ?>" name="<?php $mb->the_name(); ?>" id="rythm-google-map-longitude"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>


                        <p class="description">
                            <?php esc_html_e('Set the point in geographical coordinates: latitude and longitude.', THEMICO_DOMAIN) ?>
                        </p>

                    </td>
                </tr>

        <tr>
            <th colspan="2">
                <strong><?php esc_html_e('Contact Form', THEMICO_DOMAIN); ?></strong>
            </th>
        </tr>

        <tr>
            <th><label for="rythm-contact-subject"><?php esc_html_e('Subject', THEMICO_DOMAIN); ?></label></th>
            <td>
                <?php $mb->the_field('subject'); ?>
                <input type="text" value="<?php esc_attr_e('Rythm Contact Form', THEMICO_DOMAIN); ?>" name="<?php $mb->the_name(); ?>" id="rythm-contact-subject"/>
            </td>
        </tr>

        <tr>
            <th><label for="rythm-contact-emailto"><?php esc_html_e('Email To', THEMICO_DOMAIN); ?></label></th>
            <td>
                <?php $mb->the_field('emailto'); ?>
                <input type="text" value="<?php echo esc_attr( get_bloginfo('admin_email') ); ?>" name="<?php $mb->the_name(); ?>" id="rythm-contact-emailto"/>
            </td>
        </tr>

        <tr>
            <th><label for="rythm-contact-success"><?php esc_html_e('Success Message', THEMICO_DOMAIN); ?></label></th>
            <td>
                <?php $mb->the_field('success_msg'); ?>
                <input type="text" value="<?php esc_attr_e('Your email was successfully sent and I will be in touch with you soon.', THEMICO_DOMAIN) ?>" name="<?php $mb->the_name(); ?>" id="rythm-contact-success"/>
            </td>
        </tr>

        <tr>
            <th><label for="rythm-contact-error"><?php esc_html_e('Error Message', THEMICO_DOMAIN); ?></label></th>
            <td>
                <?php $mb->the_field('error_msg'); ?>
                <input type="text" value="<?php esc_attr_e('Please check if you\'ve filled all the fields with valid information.', THEMICO_DOMAIN) ?>" name="<?php $mb->the_name(); ?>" id="rythm-contact-error"/>
            </td>
        </tr>



    </tbody>
</table>

    <p class="alignleft">
        <input id="insert-contact-and-map" type="button" value="<?php esc_attr_e('Insert Shortcodes', THEMICO_DOMAIN) ?>" class="button button-highlighted" />
    </p>

    <div class="clear"></div>


</div>


