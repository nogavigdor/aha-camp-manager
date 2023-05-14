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

 */

class AhaCampManager {
    function __construct(){
        add_action('plugins_loaded', array($this, 'load_text_domain'));
        add_action('admin_menu', array($this, 'admin_page'));
        add_action('admin_init', array($this, 'settings'));
        
    }

//loads the plugin's translation
function load_text_domain() {
        load_plugin_textdomain( 'aha-camp-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
 }
       
//Setting up the plugin's page, sections and fields
function settings(){
   
    add_settings_section('aha_form_first_section', 'Form', array($this, 'parent_details'), 'aha-settings');
    add_settings_field('aha_form_parentFirstName', 'First Name', array($this, 'parentFirstNameHTML'),'aha-settings', 'aha_form_first_section' );
    register_setting('ahaplugin','aha_form_parentFirstName', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0', 'section'=>'aha_form_first_section'));
    add_settings_field('aha_form_parentLastName', 'Last Name', array($this, 'parentLastNameHTML'),'aha-settings', 'aha_form_first_section' );
    register_setting('ahaplugin','aha_form_parentLasttName', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0', 'section'=>'aha_form_first_section'));

  
}

function parent_details() {
    echo 'This is the parent details section.';
}

function parentFirstNameHTML() {
   
?>
 <input name='aha_form_parentFirstName' type="text">
<?php
}

function parentLastNameHTML() {
   
    ?>
     <input name='aha_form_parentLastName' type="text">
    <?php
    }
    

//creating the dashboard settings funcgtionality and out
function admin_page(){
    //first parameter: the title of the page that will appear on the tab,
    //second parameter: the title of the page that will be used in the dashboard setting menu
    //third parameter: only a user with 'manage_options' rights will be able to see the plugin settings
    //forth parameter: the name of the slug
    //fifth parameter: the function that will create the content for these settings page
    //Sixth parameter: the item position in the menu - in this case first position
    add_menu_page('Aha Camp Manager', 'AHA Manager  ', 'manage_options', 'aha-settings', array($this, 'settingsHTML'), plugins_url( 'aha-camp-manager/img/soccer-player.png' ),
    2);
}

//The content of the plugin page once the Aha Settings is being clicked at the dashboard settings menu

function settingsHTML() { ?>
    <div class="wrap">
        <h1>Aha Camp Manager Settings</h1>
        
        <form action="options.php" method="POST">
            <?php
                settings_fields('ahaplugin');
                do_settings_sections('aha-settings');
                submit_button();
            ?>
        </form>
    </div>
   <?php } 

}



$ahaCampManager=new AhaCampManager();


?>