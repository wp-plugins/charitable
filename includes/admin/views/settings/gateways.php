<?php
/**
 * Display the table of payment gateways. 
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */

$helper     = charitable_get_helper( 'gateways' );
$gateways   = $helper->get_available_gateways();
$default    = $helper->get_default_gateway();

if ( count( $gateways ) ) : 
    
    foreach ( $gateways as $gateway ) :   

        $gateway    = new $gateway;      
        $is_active  = $helper->is_active_gateway( $gateway::ID );
        $action_url = esc_url( add_query_arg( array(
            'charitable_action' => $is_active ? 'disable_gateway' : 'enable_gateway',
            'gateway_id'        => $gateway::ID, 
            '_nonce'            => wp_create_nonce( 'gateway' )
        ), admin_url( 'admin.php?page=charitable-settings&tab=gateways' ) ) );

        $make_default_url = esc_url( add_query_arg( array(
            'charitable_action' => 'make_default_gateway',
            'gateway_id'        => $gateway::ID, 
            '_nonce'            => wp_create_nonce( 'gateway' )
        ), admin_url( 'admin.php?page=charitable-settings&tab=gateways' ) ) );

        ?>
        <div class="charitable-settings-object charitable-gateway cf">
            <h4><?php echo $gateway->get_name() ?></h4>
            <?php if ( $gateway::ID == $default ) : ?>

                <span class="default-gateway"><?php _e( 'Default gateway', 'charitable' ) ?></span>

            <?php elseif ( $is_active ) : ?>

                <a href="<?php echo $make_default_url ?>" class="make-default-gateway"><?php _e( 'Make default gateway', 'charitable' ) ?></a>

            <?php endif ?>
            <span class="actions">
                <?php if ( $is_active ) : 
                    $settings_url = esc_url( add_query_arg( array(
                        'group' => 'gateways_' . $gateway::ID
                    ), admin_url( 'admin.php?page=charitable-settings&tab=gateways' ) ) );
                    ?>

                    <a href="<?php echo $settings_url ?>" class="button button-primary"><?php _e( 'Gateway Settings', 'charitable' ) ?></a>

                <?php endif;
                
                if ( $is_active ) : ?>

                    <a href="<?php echo $action_url ?>" class="button"><?php _e( 'Disable Gateway', 'charitable' ) ?></a>

                <?php else : ?>

                    <a href="<?php echo $action_url ?>" class="button"><?php _e( 'Enable Gateway', 'charitable' ) ?></a>

                <?php endif ?>
            </span>
        </div>
    <?php endforeach ?>
<?php else : ?>
    <?php _e( 'There are no gateways available in your system.', 'charitable' ) ?>
<?php endif ?>