<?php
global $its_logged_in;
$its_options = get_option( 'its_options' );
?>

<h2><?php _e( 'ItStream Account', 'itstream' ); ?></h2>

<?php if( $its_logged_in ): admin_notice_message( 'success', __( 'Account successfully connected', 'itstream' ) ); ?>
<table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row"><label for="its_username"><?php _e( 'Username', 'itstream' ); ?></label></th>
        <td>
            <input type="text" class="regular-text" value="<?php echo $its_options['username']; ?>" id="its_username" name="its_username" disabled="disabled" />
            <span class="description"><?php _e( 'Your ItStream username', 'itstream' ); ?></span>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><label for="its_password"><?php _e( 'Password', 'itstream' ); ?></label></th>
        <td>
            <input type="password" autocomplete="off" value="**********************" class="regular-text" id="its_password" name="its_password" disabled="disabled" />
            <span class="description"><?php _e( 'Your password', 'itstream' ); ?></span>
        </td>
    </tr>
    </tbody>
</table>

<p class="submit">
    <input type="hidden" name="op" value="logout" />
    <input type="submit" value="<?php _e( 'Disconnect to ItStream', 'itstream'); ?>" class="button button-primary" id="submit" name="submit" />
</p>

<?php else: ?>

<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row"><label for="its_username"><?php _e( 'Username', 'itstream' ); ?></label></th>
            <td>
                <input type="text" class="regular-text" value="<?php echo get_option('its_username'); ?>" id="its_username" name="its_username" />
                <span class="description"><?php _e( 'Your ItStream username', 'itstream' ); ?></span>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="its_password"><?php _e( 'Password', 'itstream' ); ?></label></th>
            <td>
                <input type="password" autocomplete="off" value="" class="regular-text" id="its_password" name="its_password" />
                <span class="description"><?php _e( 'Your password', 'itstream' ); ?></span>
            </td>
        </tr>
    </tbody>
</table>

<p class="submit">
    <input type="hidden" name="op" value="login" />
    <input type="submit" value="<?php _e( 'Connect to ItStream', 'itstream'); ?>" class="button button-primary" id="submit" name="submit" />
</p>

<?php endif; ?>

<p>
    <?php echo _e( "If don't have an ItStream account", 'itstream' ); ?> <a href="http://www.itstream.tv/index/login/openLogin/true" target="_blank"><?php echo _e( 'please sign up here', 'itstream' ); ?></a>
</p>
