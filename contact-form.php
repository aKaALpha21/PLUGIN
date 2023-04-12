<?php 
/*
Plugin Name: Simple form
Description: Adds a form page to your website.
Version: 1.0
*/
function contact_form_add_menu_item() {
    add_menu_page(
        'Contact Form',
        'Contact Form',
        'manage_options',
        'contact-form',
        'contact_form_display_page'
    );
}
function contact_form_display_page() {
  global $wpdb;
 
  $wp_contact_form = $wpdb->prefix . 'contact_form';
  
   $results = $wpdb->get_results( "SELECT * FROM $wp_contact_form" );
 
   echo '<h1>Contact form</h1>';
   echo '<table class="mytable">';
   echo ' <thead>';
   echo "<tr><th>Nom</th><th>Prenom</th><th>Email</th><th>Sujet</th><th>Message</th><th>Date d'envoie</th></tr>";
   echo " </thead>";
   echo " <tbody>";
   foreach ( $results as $row ) {
     echo '<tr>';
     echo '<td>' . $row->nom . '</td>';
     echo '<td>' . $row->prenom . '</td>';
     echo '<td>' . $row->email . '</td>';
     echo '<td>' . $row->sujet . '</td>';
     echo '<td>' . $row->message . '</td>';
     echo '<td>' . $row->date_envoi . '</td>';
     echo '</tr>';
   }
   echo " </tbody>";
   echo '</table>';
}
add_action( 'admin_menu', 'contact_form_add_menu_item' );

function contact_form_create_table()
{
    global $wpdb;
    $wp_contact_form = $wpdb->prefix . 'contact_form';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $wp_contact_form (
            id INT(9) NOT NULL AUTO_INCREMENT,
            sujet VARCHAR(200) NOT NULL,
            nom VARCHAR(200) NOT NULL,
            prenom VARCHAR(200) NOT NULL,
            email VARCHAR(200) NOT NULL,
            message VARCHAR(300) NOT NULL,
            date_envoi DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);
}
     //register activation
    register_activation_hook( __FILE__, 'contact_form_create_table' );
    // Register deactivation hook
    register_deactivation_hook( __FILE__, 'my_plugin_deactivation' );
        function my_plugin_deactivation() {
            global $wpdb;
            $wp_contact_form = $wpdb->prefix . 'contact_form';
            $wpdb->query( "DROP TABLE IF EXISTS $wp_contact_form" );
        }
        // Affichage du formulaire
function mon_plugin_form() {
    ob_start();
    ?>
    <form method="post">
    <label for="name">Sujet :</label>
      <input type="text" name="sujet" required>
      <br>
      <label for="name">Nom :</label>
      <input type="text" name="nom" required>
      <br>
      <label for="name">Prénom :</label>
      <input type="text" name="prenom" required>
      <br>
      <label for="email">Email :</label>
      <input type="email" name="email" required>
      <br>
      <label for="message">Message :</label>
      <textarea name="message" required></textarea>
      <br>
      <input type="submit" name="submit" value="Envoyer">
    </form>
    <?php
    return ob_get_clean();
  }
  add_shortcode( 'my_form', 'mon_plugin_form' );
  // Traitement du formulaire
function mon_plugin_process_form() {
    global $wpdb;
    $wp_contact_form = $wpdb->prefix . 'contact_form';
    if ( isset( $_POST['submit'] ) ) {
      $sujet = sanitize_text_field( $_POST['sujet'] );
      $name = sanitize_text_field( $_POST['nom'] );
      $prenom = sanitize_text_field( $_POST['prenom'] );
      $email = sanitize_email( $_POST['email'] );
      $message = sanitize_textarea_field( $_POST['message'] );
      $wpdb->insert(
        $wp_contact_form,
        array(
          'sujet' => $sujet,
          'nom' => $name,
          'prenom' => $prenom,
          'email' => $email,
          'message' => $message,
        )
      );
    }
  }
  add_action( 'init', 'mon_plugin_process_form' );


   ?>