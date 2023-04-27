<?php

/**
 * Plugin Name: Aha camp manager
 * Description: Manages camp form, submissions and emailing
 * Version: 1.0
 * Author: Noga Vigdor
 * Plugin URI: https://noga.digital
 * Text Domain: aha-camp-manager
 * License: GPLv2 or later
 *
 * Copyright 2023 noga.digital
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class AhaCampManager {
    function __construct(){
        add_action('admin_menu', array($this, 'admin_page'));
        add_action('admin_init', array($this, 'settings'));
    }

function settings(){

    add_settings_section('aha_form_first_section', 'Form', array($this, 'parent_details'), 'aha-settings');
    add_settings_field('aha_form_parentFirstName', 'First Name', array($this, 'parentFirstNameHTML'),'aha-settings', 'aha_form_first_section' );
    register_setting('ahaplugin','aha_form_parentFirstName', array('sanitize_callback'=>'sanitize_text_field', 'default'=>''));

  
}


function parent_details() {
    echo 'This is the parent details section.';
}

function parentFirstNameHTML() {
    var_dump('parentFirstNameHTML is called');
    die();
?>
 <input name='aha_form_parentFirstName' type="text">
<?php
}

    //creating the dashboard settings funcgtionality
function admin_page(){
    //first parameter: the title of the page that will appear on the tab,
    //second parameter: the title of the page that will be used in the dashboard setting menu
    //third parameter: only a user with 'manage_options' rights will be able to see the plugin settings
    //forth parameter: the name of the slug
    //fifth parameter: the function that will create the content for these settings page
    //Sixth parameter: the item position in the menu - in this case first position
    add_options_page('Aha Camp Manager', 'AHA Settings', 'manage_options', 'aha-settings', array($this, 'settingsHTML'), '0');
}



//The content of the plugin page once the Aha Settings is being clicked at the dashboard settings menu
function settingsHTML() { ?>
    <div class="wrap">
        <h1>Aha Camp Manager Settings</h1>
        
        <form action="options.php" method="POST">
            <?php
                settings_fields('ahaplugin');
                do_settings_sections('aha_form_first_section');
                submit_button();
            ?>
        </form>
    </div>
   <?php } 

}

$ahaCampManager=new AhaCampManager();


?>