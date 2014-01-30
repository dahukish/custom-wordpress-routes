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
    
    	<?php woo_main_before(); ?>
    	
		<section id="main" class="col-left"> 			

        <?php
        	if ( have_posts() ) { $count = 0;
        		while ( have_posts() ) { the_post(); $count++;
        ?>                                                           
            <article <?php post_class(); ?>>
				
                <section class="entry doctors">
                    <header>
                        <h1><a href="<?php echo get_permalink($post->ID); ?>"><?php the_title(); ?></a></h1>
                    </header>
                    <?php echo get_the_post_thumbnail($post->ID, 'content', array('class'=>'thumb')); ?>

                    <?php $post_m = $postMetaHandler($post->ID); ?>

                    <?php if(isset($post_m['ecpt_education'])&&!empty($post_m['ecpt_education'])) { ?>
                        <div class="column first education">
                        <span class="col-header">Credentials</span>
                        <ul class="column-list first education">
                        <?php foreach ($post_m['ecpt_education'] as $edu) { ?>
                            <li><?php echo $edu; ?></li>
                        <?php } ?>
                        </ul>
                        </div>
                    <?php } ?>

                    <?php if(isset($post_m['ecpt_diagnosticspecialties'])&&!empty($post_m['ecpt_diagnosticspecialties'])) { ?>
                        <div class="column second specialites">
                        <span class="col-header">Specialties</span>
                        <ul class="column-list">
                        <?php foreach ($post_m['ecpt_diagnosticspecialties'] as $spec) { ?>
                            <li><?php echo $spec; ?></li>
                        <?php } ?>
                        </ul>
                        </div>
                    <?php } ?>
                    <?php $post_m['locations']  = array(
                            '1234 main street, Beverly Hills, Ca, 90120',
                            '1234 main street, Beverly Hills, Ca, 90120'
                            ); 
                        ?>
                    <?php if(isset($post_m['locations'])&&!empty($post_m['locations'])) { ?>
                        <div class="column third location">
                        <span class="col-header">Locations</span>
                        <ul class="column-list">
                        <?php foreach ($post_m['locations'] as $loc) { ?>
                            <li><?php echo $loc; ?></li>
                        <?php } ?>
                        </ul>
                        </div>
                    <?php } ?>


					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
               	</section><!-- /.entry -->
                
            </article><!-- /.post -->
            
            <?php
            	// Determine wether or not to display comments here, based on "Theme Options".
            	if ( isset( $woo_options['woo_comments'] ) && in_array( $woo_options['woo_comments'], array( 'page', 'both' ) ) ) {
            		comments_template();
            	}

				} // End WHILE Loop
			} else {
		?>
			<article <?php post_class(); ?>>
            	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
            </article><!-- /.post -->
        <?php } // End IF Statement ?>  
        
		</section><!-- /#main -->

        <?php woo_main_after(); ?>

        <section id="sidebar" class="col-right">            
            <?php load_template(CDS_PLUGIN_BASE.'/doctor_search/templates/_form.php'); ?>
        </section><!-- /#sidebar -->

    </div><!-- /#content -->
		
<?php #get_footer(); ?>
<?php load_template(CDS_PLUGIN_BASE.'/doctor_search/templates/footer.php'); ?>