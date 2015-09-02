<?php
/**
 * Display the main settings page wrapper. 
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */

$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
$group = isset( $_GET[ 'group' ] ) ? $_GET[ 'group' ] : $active_tab;

ob_start();
?>
<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <?php foreach( charitable_get_admin_settings()->get_sections() as $tab => $name ) : ?>
            <a href="<?php echo esc_url( add_query_arg( array( 'tab' => $tab ), admin_url( 'admin.php?page=charitable-settings' ) ) ) ?>" class="nav-tab <?php echo $active_tab == $tab ? 'nav-tab-active' : '' ?>"><?php echo $name ?></a>
        <?php endforeach ?>
    </h2>

    <div id="tab_container">
        <form method="post" action="options.php">
            <table class="form-table">
            <?php
                settings_fields( 'charitable_settings' );       
                charitable_do_settings_fields( 'charitable_settings_' . $group, 'charitable_settings_' . $group );             
            ?>
            </table>
            <?php 
                submit_button();
            ?>
        </form> 
    </div>
</div>
<?php
echo ob_get_clean();