<?php
if ( ! defined( 'ABSPATH' ) ) exit;

# wp hack to get data into the templates
global $view_data;

if(isset($view_data)&&!empty($view_data))
    extract($view_data, EXTR_SKIP);

/**
 * Page Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a page ('page' post_type) unless another page template overrules this one.
 * @link http://codex.wordpress.org/Pages
 *
 * @package WooFramework
 * @subpackage Template
 */
	//get_header();
   load_template(CDS_PLUGIN_BASE.'/doctor_search/templates/header.php');

	global $woo_options;
?>
       
    <div id="content" class="page col-full">
       <?php 
        
        #TODO make this now bad -SH

        if(isset($_GET['form_errors'])&&!empty($_GET['form_errors'])) {
            
            $form_errors = array('An error has occured please check your form input.');

            if(isset($form_errors)&&!empty($form_errors)) {
                foreach ((array)$form_errors as $form_error) {
                    ?>
                    <p class="error"><?php echo $form_error?></p>
                    <?php
                }

                $form_errors = null;
            }
        }
        ?>
        <?php load_template(CDS_PLUGIN_BASE.'/doctor_search/templates/_form.php'); ?>
    </div><!-- /#content -->
		
<?php #get_footer(); ?>
<?php load_template(CDS_PLUGIN_BASE.'/doctor_search/templates/footer.php'); ?>