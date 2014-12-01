<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   ItStream
 * @author    it-marketing <info@itmsolution.it>
 * @license   GPL-2.0+
 * @link      http://www.itmarketingsrl.it/
 * @copyright 2014 it-marketing
 */
?>

<div class="wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <?php itstream_admin_tabs( $_GET['tab'] ); ?>

</div>
