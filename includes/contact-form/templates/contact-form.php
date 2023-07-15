<?php if( get_plugin_options('contact_plugin_active') ):?>


<div id="form_success" style="background-color:green; color:#fff;"></div>
<div id="form_error" style="background-color:red; color:#fff;"></div>

<form id="enquiry_form">


      <?php wp_nonce_field('wp_rest');?>

      <label>Name</label><br />
      <input type="text" name="name"><br /><br />

      <label>Phone</label><br />
      <input type="text" name="phone"><br /><br />

      <label>Email</label><br />
      <input type="text" name="email"><br /><br />

      <label>Message</label><br />
      <textarea name="message"></textarea><br /><br />

      <button type="submit">Submit form</button>

</form>

<script>

      jQuery(document).ready(function ($) {

            $('#enquiry_form').submit(function (e) {

                  e.preventDefault();
                  $("#form_error").hide();

                  var form = $(this);

                  var formdata = new FormData(form[0]);

                  formdata.append('action', 'contact_form');

                  $.ajax({
                        url: '<?php echo get_rest_url(null, 'v1/contact-form/submit');?>',
                        type: 'POST',
                        data: formdata,
                        processData: false,
                        contentType: false,
                        success: function (data) {

                              form.trigger('reset');
                              $('#form_success').html(data.message).show().delay(5000).fadeOut();

                        },
                        error: function (data) {

                              $('#form_error').html(data.responseJSON.message).show().delay(5000).fadeOut();

                        }
                  });

            });

      });

</script>

<?php else:?>

<p>This form is not active</p>

<?php endif;?>