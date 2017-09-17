<?php
/**
 * Creates the submenu item for the plugin.
 *
 * @package Mail_Service_Admin_Settings
 */

/**
 * Creates the submenu item for the plugin.
 *
 * Registers a new menu item under 'Tools' and uses the dependency passed into
 * the constructor in order to display the page corresponding to this menu item.
 *
 * @package Mail_Service_Admin_Settings
 */
class Menu
{

    const MENU_SLUG       = 'mail-service-setting';
    const MENU_PAGE_TITLE = 'Mail Service Integration';
    const MENU_TITLE      = 'Mail Service Integration';

    /**
     * Adds a menu for this plugin to the 'Tools' menu.
     */
    public function init()
    {
        add_action(
            'admin_menu',
            array(
                $this,
                'addMailServiceMenu'
            )
        );
        //add_action('admin_enqueue_scripts', 'my_admin_theme_style');
       // add_action('login_enqueue_scripts', 'my_admin_theme_style');
    }

    /**
     * Creates the SubMenu item and calls on the SubMenu Page object to render
     * the actual contents of the page.
     */
    public function addMailServiceMenu()
    {
        add_menu_page(
            self::MENU_PAGE_TITLE,
            self::MENU_TITLE,
            'manage_options',
            self::MENU_SLUG,
            array(
                $this,
                'renderForm'
            )
        );
    }

    public function renderForm(){
        include(MAIL_SERVICE_DIRECTORY_PLUGIN_DIR.'views/icontact-form.php');
    }

   

}
