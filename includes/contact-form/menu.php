<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function bookedin_contact_form_submenu_page() {
      
      // Check user capabilities
      if ( ! current_user_can( 'manage_options' ) ) {
            return;
      }

      // Save settings if form is submitted
      if ( isset( $_POST['my_plugin_contact_form_submit'] ) ) {
            // Sanitize and save the options
            $is_active = isset( $_POST['contact_plugin_active'] ) ? 1 : 0;
            $recipients = sanitize_email( $_POST['contact_plugin_recipients'] );
            $confirmation_message = sanitize_textarea_field( $_POST['contact_plugin_message'] );

            update_option( 'contact_plugin_active', $is_active );
            update_option( 'contact_plugin_recipients', $recipients );
            update_option( 'contact_plugin_message', $confirmation_message );
      }

      // Retrieve the saved option values (if they exist)
      $is_active = get_option( 'contact_plugin_active', 0 );
      $recipients = get_option( 'contact_plugin_recipients', '' );
      $confirmation_message = get_option( 'contact_plugin_message', '' );
      
      ?>

            <div class="wrap">
                  <h1>Contact Form Settings</h1>
                  <form method="post">
                  <label for="contact_plugin_active">
                        <input type="checkbox" id="contact_plugin_active" name="contact_plugin_active" <?php checked( $is_active, 1 ); ?>>
                        Activate Contact Form
                  </label>
                  
                  <br>
                  <br>

                  <label for="contact_plugin_recipients">Recipient Email:</label>
                  <br>
                  <input type="email" id="contact_plugin_recipients" name="contact_plugin_recipients" value="<?php echo esc_attr( $recipients ); ?>" placeholder="eg. your@email.com">
                  <p class="description">The email that the form is submitted to</p>
                  <br>

                  <label for="contact_plugin_message">Confirmation Message:</label>
                  <br>
                  <textarea id="contact_plugin_message" name="contact_plugin_message" placeholder="Enter confirmation message"><?php echo esc_textarea( $confirmation_message ); ?></textarea>
                  <p class="description">Type the message you want the submitted to receive</p>

                  <?php submit_button( 'Save Changes', 'primary', 'my_plugin_contact_form_submit' ); ?>
                  </form>
            </div>

      <?php
}

?>