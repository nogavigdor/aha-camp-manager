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

        // Add the Mailchimp tag based on the selected form
        add_filter('mc4wp_integration_contact-form-7_subscriber_data', array($this,'my_custom_mailchimp_plugin_add_tag'), 10, 2);
        
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
    add_menu_page(
        'Aha Camp Manager',
        'AHA Manager  ',
        'manage_options',
        'aha-camps', //The parent slug
        array($this, 'settingsHTML'),
        plugins_url( 'aha-camp-manager/img/soccer-player.png' ),
        2
    );

    add_submenu_page(
        'aha-camps', //The parent slug
        'Players', 
        'Players',
        'manage_options',
        'players',
        array($this,'players_settings_page')
    );
    add_submenu_page(
        'aha-camps',//The parent slug
        'Parents',
        'Parents',
        'manage_options',
        'parents',
        array($this,'parents_settings_page')
    );
    add_submenu_page(
        'aha-camps',//The parent slug
        'Mailchimp Tags',
        'Mailchimp Tags',
        'manage_options',
        'mailchimp-tags',
        array($this,'mailchimp_tags_settings_page')
    );
}

function players_settings_page(){

}

function parents_settings_page(){
    
}

function mailchimp_tags_settings_page(){
     // Get the saved tag associations
     $tag_associations = get_option('my_custom_mailchimp_plugin_tags_associations', array());

     // Handle form submission
     if (isset($_POST['submit'])) {
         // Generate a random nonce
         $nonce = wp_create_nonce('my_custom_mailchimp_plugin_save_settings');
 
         // Update the tag associations
         $tag_associations = array();
         if (isset($_POST['cf7_form_ids']) && isset($_POST['mailchimp_tags'])) {
             $form_ids = $_POST['cf7_form_ids'];
             $tags = $_POST['mailchimp_tags'];
             foreach ($form_ids as $index => $form_id) {
                 $form_id = sanitize_text_field($form_id);
                 $tag = sanitize_text_field($tags[$index]);
                 if (!empty($form_id) && !empty($tag)) {
                     $tag_associations[$form_id] = $tag;
                 }
             }
         }
 
         // Save the tag associations
         update_option('my_custom_mailchimp_plugin_tags_associations', $tag_associations);
 
         // Redirect to the same page to avoid resubmission
         wp_safe_redirect(add_query_arg('settings-updated', 'true'));
         exit;
     }
 
     // Output the admin page HTML
     ?>
     <div class="wrap">
         <h1>Custom Mailchimp Integration</h1>
         <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') : ?>
             <div class="notice notice-success is-dismissible">
                 <p><strong>Settings saved.</strong></p>
             </div>
         <?php endif; ?>
         <form method="POST">
             <?php wp_nonce_field('my_custom_mailchimp_plugin_save_settings', 'my_custom_mailchimp_plugin_nonce'); ?>
             <div class="cf7-forms">
                 <?php
                 var_dump($tag_associations);
                 $forms = get_posts(array('post_type' => 'wpcf7_contact_form'));
                 foreach ($forms as $form) {
                     $form_id = $form->ID;
                     $tag_value = isset($tag_associations[$form_id]) ? esc_attr($tag_associations[$form_id]) : '';
                     ?>
                     <div class="cf7-form">
                         <h3><?php echo esc_html($form->post_title); ?></h3>
                         <input type="hidden" name="cf7_form_ids[]" value="<?php echo $form_id; ?>">
                         <div class="cf7-form__field">
                             <label for="tag-<?php echo $form_id; ?>">Mailchimp Tag:</label>
                             <input type="text" id="tag-<?php echo $form_id; ?>" name="mailchimp_tags[]" value="<?php echo $tag_value; ?>">
                         </div>
                     </div>
                     <?php
                 }
                 ?>
             </div>
             <p class="submit">
                 <input type="submit" name="submit" class="button-primary" value="Save Changes">
 </p>
 </form>
 </div>
 <?php
    
}

function my_custom_mailchimp_plugin_add_tag($subscriber, $cf7_form_id) {
    // Get the tag associations
    $tag_associations = get_option('my_custom_mailchimp_plugin_tag_associations', array());
    
   // Log the form ID and tag for debugging
   error_log('Form ID: ' . $cf7_form_id);
   error_log('Tag: ' . $tag_associations[$cf7_form_id]);

    // Generate the output
    $output = '<pre>';
    $output .= print_r($cf7_form_id, true);
    $output .= print_r($tag_associations[$cf7_form_id], true);
    $output .= '</pre>';

    // Echo the output
    echo $output;


    // Check if the form ID is associated with a tag
    if (isset($tag_associations[$cf7_form_id])) {
        $tag = $tag_associations[$cf7_form_id];
        
        // Log the tag being added
        error_log('Adding Tag: ' . $tag);
        
        $subscriber->tags[] = $tag;
    }
    
    return $subscriber;
}


/*
    add_action('wpcf7_before_send_mail', 'my_custom_mc2wp_add_tags', 10, 1);
    function my_custom_mc2wp_add_tags($contact_form) {
        // Get the form ID from the submitted contact form
        $cf7_form_id = $contact_form->id();
    
        // Get the tag associations
        $tag_associations = get_option('my_custom_mailchimp_plugin_tags_associations', array());
    
        // Check if the form ID is associated with a tag
        if (isset($tag_associations[$cf7_form_id])) {
            $tag = $tag_associations[$cf7_form_id];
    
            // Add the tag to the submitted form data
            $_POST['mailchimp_tags'][] = $tag;
        }
    }   
*/
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