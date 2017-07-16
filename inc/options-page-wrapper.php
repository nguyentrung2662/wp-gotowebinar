<?php 
global $time_zone_list; 
global $gotowebinar_is_pro;
$options = get_option('gotowebinar_settings');

//call upcoming webinars function and store responses as variables    
list($jsondata,$json_response) = wp_gotowebinar_upcoming_webinars('gtw_key', 600);

?>

    <!-- start wrap -->
    <div class="wrap">
    <div id="poststuff">
        
        
    <!-- pro ad -->
        <?php if ($gotowebinar_is_pro != "YES"){ ?> 
        <a id="wpgotowebinar-ad" target="_blank" href="https://northernbeacheswebsites.com.au/wp-gotowebinar-pro/">
            <div style="width: 100%; background-color: #3fa5bf; margin-bottom: 20px;">
                <div style="padding: 25px 20px 20px 20px; text-align: center;">
                    
                    <iframe style="vertical-align: middle;" width="560" height="315" src="https://www.youtube.com/embed/M3rty3sV9lU?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
                    
                    <img style="vertical-align: middle; padding: 20px 0px; max-width: 100%; height: auto;" src="<?php echo plugins_url( '/images/upgrade-notice.png', __FILE__ ); ?>">
                </div>
            </div>
        </a>
        <?php } ?> 
        
        
        

        
        
    <!-- heading -->
    <?php if ($gotowebinar_is_pro == "YES"){ ?>  
        <h1><i class="fa fa-video-camera" aria-hidden="true"></i> <?php esc_attr_e( 'WP GoToWebinar Pro Options', 'wp_admin_style' ); ?></h1>
        <?php } else { ?>
        <h1><i class="fa fa-video-camera" aria-hidden="true"></i> <?php esc_attr_e( 'WP GoToWebinar Options', 'wp_admin_style' ); ?></h1>    
    <?php } ?>

    
    <!-- authentication note -->    
    <?php
    if(strlen($options['gotowebinar_organizer_key'])>0 && strlen($options['gotowebinar_authorization'])>0) {
        
        $authenticationStatusCode = wp_gotowebinar_authentication_check();
        
        if($authenticationStatusCode == 200) {
            //plugin is authenticated message
            ?>
            <div class="notice notice-success gotowebinar-authentication-notice">
                  <p>The connection to your GoToWebinar account is working :)</p> 
            </div>    
            <?php
            
        } else {
            //plugin isn't authenticated
            ?>
            <div class="notice notice-error gotowebinar-authentication-notice">
                
                <table>
                <tr>
                <td><i style="margin-right: 10px; color: #dc3232; font-size: 64px;" class="fa fa-exclamation-triangle" aria-hidden="true"></i></td>  
                <td><p>The connection to your GoToWebinar account hasn't been successful. Even though the Authorization and Organizer Key have been successfully generated the API calls to your GoToWebinar account are failing. This means that the plugin won't work! This can be because of a few reasons, including you could be on a trial account, your GoToWebinar account has expired or you are using login details for another Citrix product like GoToMeeting. If this message is showing please contact Citrix first by clicking <a href="https://care.citrixonline.com/gotowebinar/contactus" target="_blank"><strong>here</strong></a> as I can't assist you with this particular issue. Thanks and I hope you get the account issue sorted out soon :)</p></td>    
                </tr>
                
                </table>
     
            </div>    
            <?php
        }
        
        
    }        
        
    ?>    
        
        
    <!-- welcome note -->         
    <?php    
    if($options['gotowebinar_welcome_message'] != 1) {
    ?>
    <div class="notice notice-warning is-dismissible wpgotowebinar-welcome">
        <h3>Quickstart Guide</h3>
        <p>Thanks for using WP GoToWebinar! The first thing you need to do to get started is to get your Authorization and Organizer Key which can be found in the <a class="open-tab" href="#general-options">General Options</a> tab. Once this is done you can now utilise the various plugin features. This includes adding an upcoming webinar table to a post or page by adding the <code>[gotowebinar]</code> shortcode to your page/post content (or if you want to show this in a sidebar use the included widget). To make the register links in the upcoming webinar table go to a registration page on your website create a new page and add the <code>[gotowebinar-reg]</code> shortcode to it and select this page in the settings on the <a class="open-tab" href="#general-options">General Options</a> tab.</p>
        <p>To learn more about all the great stuff you can do with WP GoToWebinar check out the <a class="open-tab" href="#faq">FAQ</a> tab.</p>
        
    </div>
    <?php } ?>    
        
        
     <!-- function to show locked or unlocked icon -->
     <?php 
     function wpgotowebinar_lock_icon_display(){
     global $gotowebinar_is_pro; 
        if ($gotowebinar_is_pro == "YES"){ 
            echo '<i class="fa fa-unlock" aria-hidden="true"></i>';    
        } else {
            echo '<i class="fa fa-lock" aria-hidden="true"></i>'; 
        }
         
     } ?>   


    <!--start tabs-->
    <div id="tabs" class="nav-tab-wrapper"> 
    <ul class="tab-titles">
        <li><a class="nav-tab" href="#general-options">General Options</a></li>
        <li><a class="nav-tab" href="#translation">Translation</a></li>
        <li><a class="nav-tab" href="#clear-cache">Clear Cache</a></li>
        <li><a class="nav-tab" href="#faq">FAQ</a></li>
        <li><a class="nav-tab" href="#support">Support</a></li> 
        <li><a class="nav-tab" href="#webinar-product-manager">Webinar Product Manager <?php wpgotowebinar_lock_icon_display(); ?></a></li>
        <li><a class="nav-tab" href="#create-a-webinar">Create a Webinar <?php wpgotowebinar_lock_icon_display(); ?></a></li>
        <li><a class="nav-tab" href="#email-signup">Email Signup <?php wpgotowebinar_lock_icon_display(); ?></a></li>
        <li><a class="nav-tab" href="#pro-options">Pro Options <?php wpgotowebinar_lock_icon_display(); ?></a></li>
         
    </ul>
        
        
    <!--start form-->    
    <form name="gotowebinar_settings_form" id="gotowebinar_settings_form" method="post" action="">
    <input type="hidden" name="gotowebinar_settings_form_submitted" value="Y">    
        
    <div class="tab-content" id="general-options">
        <?php require('options-page-main-settings.php'); ?>
    </div>
    <div class="tab-content" id="translation">
        <?php require('options-page-translation.php'); ?>
    </div>
    <div class="tab-content" id="clear-cache">
        <?php require('options-page-cache.php'); ?>
    </div>     
    <div class="tab-content" id="faq">
        <?php require('options-page-help.php'); ?>
    </div>
    <div class="tab-content" id="webinar-product-manager">
        <?php if ($gotowebinar_is_pro == "YES"){ 
           require('pro/options-page-pro-product-manager.php');
           } else {
           require('options-page-locked.php');
           }
        ?>
    </div>
    <div class="tab-content" id="create-a-webinar">
        <?php if ($gotowebinar_is_pro == "YES"){ 
           require('pro/options-page-pro-create-webinar.php');
           } else {
           require('options-page-locked.php');
           }
        ?>
    </div>
    <div class="tab-content" id="email-signup">
        <?php if ($gotowebinar_is_pro == "YES"){ 
           require('pro/options-page-pro-email-signup.php');
           } else {
           require('options-page-locked.php');
           }
        ?>
    </div>
        
    <div class="tab-content" id="pro-options">
        <?php if ($gotowebinar_is_pro == "YES"){ 
           require('pro/options-page-pro-options.php');
           } else {
           require('options-page-locked.php');
           }
        ?>
    </div>    
        
    </form>  
    <!--end form-->         
    <div class="tab-content" id="support">
        <?php if ($gotowebinar_is_pro == "YES"){ 
        require('pro/options-page-pro-support.php'); 
        } else {
        require('options-page-support.php');
        } ?>
    </div>
    </div> <!--end tabs div-->    
    </div> <!--end post stuff-->
        
    <?php if ($gotowebinar_is_pro != "YES"){ ?>
        <p> <?php esc_attr_e( 'Thank you for using WP GoToWebinar. Your donation contributes to the development of this plugin. Any donation amount is much appreciated.'); ?></p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="VGVE97KF74FVN">
        <input type="image" src="https://northernbeacheswebsites.com.au/root-nbw/wp-content/uploads/2016/11/donate-button.png" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
        </form>

    <?php } ?>
        
        
        
        
    </div> <!-- .wrap -->