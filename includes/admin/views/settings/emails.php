<?php
/**
 * Display the table of emails. 
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */

$helper     = charitable_get_helper( 'emails' );
$emails     = $helper->get_available_emails();

if ( count( $emails ) ) : 
?>
    <?php foreach ( $emails as $email ) : 
        $email      = new $email;
        $is_enabled = $helper->is_enabled_email( $email::ID );
        $action_url = esc_url( add_query_arg( array(
            'charitable_action' => $is_enabled ? 'disable_email' : 'enable_email',
            'email_id' => $email::ID, 
            '_nonce' => wp_create_nonce( 'email' )
        ), admin_url( 'admin.php?page=charitable-settings&tab=emails' ) ) );
        ?>
        <div class="charitable-settings-object charitable-email cf">
            <h4><?php echo $email->get_name() ?></h4>
            <span class="actions">
                <?php if ( $is_enabled ) : 
                    $settings_url = esc_url( add_query_arg( array(
                        'group' => 'emails_' . $email::ID
                    ), admin_url( 'admin.php?page=charitable-settings&tab=emails' ) ) );
                    ?>
                    <a href="<?php echo $settings_url ?>" class="button button-primary"><?php _e( 'Email Settings', 'charitable' ) ?></a>
                <?php endif ?>          
                <?php if ( $is_enabled ) : ?>
                    <a href="<?php echo $action_url ?>" class="button"><?php _e( 'Disable Email', 'charitable' ) ?></a>
                <?php else : ?>
                    <a href="<?php echo $action_url ?>" class="button"><?php _e( 'Enable Email', 'charitable' ) ?></a>
                <?php endif ?>
            </span>
        </div>
    <?php endforeach ?>
<?php else : ?>
    <?php _e( 'There are no emails available in your system.', 'charitable' ) ?>
<?php endif ?>