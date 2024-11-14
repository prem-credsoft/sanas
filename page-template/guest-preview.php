<?php 
/**
    Template Name: Guest Preview    
    * The template for displaying all pages
    *
    * This is the template that displays all pages by default.
    * Please note that this is the WordPress construct of pages
    * and that other 'pages' on your WordPress site may use a
    * different template.
    *
    * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
    *
    * @package sanas
*/
get_header();


// Example usage for decryption (assuming encrypted invite is passed via URL):
// URL: https://example.com/?invite=ENCRYPTED_BASE64_STRING
if (isset($_GET['invite'])) {
    // Retrieve the encrypted invite from the URL
    $invite_from_url = $_GET['invite'];

    // Decrypt the data
    $decrypted_data = sanas_decrypt_data($invite_from_url);

    // Check if the decrypted data is in the expected format (key=value pairs)
    if ($decrypted_data) {
        parse_str($decrypted_data, $params);

        // Ensure that the keys exist before accessing them
        $card_id = isset($params['card_id']) ? $params['card_id'] : 'Not Found';
        $event_id = isset($params['event_id']) ? $params['event_id'] : 'Not Found';
        $entry = isset($params['entry']) ? $params['entry'] : 'Not Found';

        $guestid='';    

    } else {
        echo "Failed to decrypt the data or the data is not in the expected format.";
    }
}
else{
    $entry=$card_id=$event_id=$guestid='';

    if(isset($_GET['entry']))
    {
        $entry=$_GET['entry'];    
    }
    if(isset($_GET['card_id']))
    {
        $card_id=$_GET['card_id'];    
    }
    if(isset($_GET['event_id']))
    {
        $event_id=$_GET['event_id'];    
    }
    if(isset($_GET['guestid']))
    {
        $guestid=$_GET['guestid'];    
    }    


}


if(isset($event_id))
{
global $post, $wpdb;


$sanas_card_event_table = $wpdb->prefix . 'sanas_card_event';  

$guest_details_info_table = $wpdb->prefix . 'guest_details_info';  


$guest_status='';
if(!empty($guestid))
{
    $guest_status_query = $wpdb->prepare(
          "SELECT guest_status FROM $guest_details_info_table WHERE guest_id = %d",
           $guestid
     );
    $guest_status = $wpdb->get_var($guest_status_query);
}



$event_user_query = $wpdb->prepare(
      "SELECT event_user FROM $sanas_card_event_table WHERE event_no = %d",
       $event_id
 );
$event_userid = $wpdb->get_var($event_user_query);


$get_event_date = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $sanas_card_event_table WHERE event_no = %d",
        $event_id
    ));

$event_front_bg_link='';



$rsvp_bg_img = $wpdb->prepare(
      "SELECT event_rsvp_bg_link FROM $sanas_card_event_table WHERE event_no = %d",
       $event_id
 );
$rsvp_bg_img_url = $wpdb->get_var($rsvp_bg_img);
$rsvp_bg_img_url_value=get_template_directory_uri() . '/assets/img/preview-bg.jfif';
if($rsvp_bg_img_url)
{
    $rsvp_bg_img_url_value=$rsvp_bg_img_url;
}  

$color_bg_link = $wpdb->prepare(
      "SELECT event_front_bg_color FROM $sanas_card_event_table WHERE event_no = %d",
       $event_id
 );
$colorbg = $wpdb->get_var($color_bg_link);
$colorbgvalue='';
if($colorbg)
{
    $colorbgvalue=$colorbg;
}    

if(isset($get_event_date))
{
$event_front_card_preview=$get_event_date[0]->event_front_card_preview;
$event_back_card_preview=$get_event_date[0]->event_back_card_preview;
$event_rsvp_id=$get_event_date[0]->event_rsvp_id;
$event_front_bg_link=$get_event_date[0]->event_front_bg_link;
$event_card_id=$get_event_date[0]->event_card_id;
$event_user=$get_event_date[0]->event_user;
}

$guest_preview_url = site_url().'/guest-preview/?card_id='.$card_id.'&event_id='.$event_id;


function is_youtube_url($url) {
  return preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/', $url);
}    

$existing_rsvp_query = new WP_Query(array(
    'post_type' => 'sanas_rsvp',
    'author' => $event_user,
    'posts_per_page' => 1,  // Limit to 1 post per user
));
if ($existing_rsvp_query->have_posts()) {
    // If an existing RSVP post is found
    $existing_rsvp_query->the_post();
    $edit_id = $event_rsvp_id;
    $rsvpvideo = esc_html(get_post_meta($edit_id, 'opt_upload_video', true));
    $guestName = esc_html(get_post_meta($edit_id, 'guest_name', true));
    $eventtitle = esc_html(get_post_meta($edit_id, 'event_name', true));
    $eventdate = esc_html(get_post_meta($edit_id, 'event_date', true));    
    $guestContact = esc_html(get_post_meta($edit_id, 'guest_contact', true));
    $guestMessage = esc_html(get_post_meta($edit_id, 'guest_message', true));
    $program      = get_post_meta($edit_id, 'listing_itinerary_details', true);
    $registry     = get_post_meta($edit_id, 'registries', true);

    $guest_name_css = get_post_meta($edit_id, 'guest_name_css', true);
    $guest_contact_css = get_post_meta($edit_id, 'guest_contact_css', true);
    $guest_message_css = get_post_meta($edit_id, 'guest_message_css', true);
    $event_title_css = get_post_meta($edit_id, 'event_title_css', true);
    $event_date_css = get_post_meta($edit_id, 'event_date_css', true);


    // Restore original post data
    wp_reset_postdata();
} 
$guest_adult=0;
$guest_kids=0;

    if(!empty($guestid))
    {
    $guest_details_query = $wpdb->prepare(
        "SELECT * FROM $guest_details_info_table WHERE guest_id = %d",
        $guestid
    );

    $guest_details = $wpdb->get_row($guest_details_query, ARRAY_A);

    $guest_name=$guest_details['guest_name'];
    $guest_email=$guest_details['guest_email']; 
    $guest_phone_num=$guest_details['guest_phone_num']; 
    $guest_msg=$guest_details['guest_msg']; 
    $guest_adult=$guest_details['guest_adult']; 
    $guest_kids=$guest_details['guest_kids'];
    $guest_status=$guest_details['guest_status']; 

    }
?>
<style type="text/css">
body {
    background-color: #F9F9F9;
}
section.wl-main-canvas .inner-container .inner-colum {
    background-image: url(<?php echo $event_front_bg_link; ?>) !important;
    background-size: cover;
    background-color:<?php echo $colorbg;?>; 
}
#previewcanvasElement {
    background-image: url('<?php echo $rsvp_bg_img_url_value ?>') !important;
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; 
}       
</style>
               
     <section class="wl-main-canvas guest-preview">
        <div class="container-fluid">
            <div class="inner-container"  id="previewcanvasElement">
                <div class="inner-colum">
                    <div class="card-canvas row">
                        <div class=" col-md-6 col-sm-12">
                            <div class="preview-img">
                                <img src="<?php echo $event_front_card_preview;?>" alt="">
                            </div>
                        </div>
                        <div class=" col-md-6 col-sm-12">
                            <div class="preview-img">
                                <img src="<?php echo $event_back_card_preview;?>" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content content-3">
                    <div class="row">
                        <div class="divider">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/divider.png" alt="">
                        </div>
                        <div class="wl-card-detaile">
                            <div class="row">
                              <?php if (!empty($rsvpvideo)) { ?>
                            <div class="col-12">
                                    <?php if (is_youtube_url($rsvpvideo)) : ?>
                                    <?php
                                         $youtubevideo=$rsvpvideo ;
                                        // Extract YouTube video ID
                                        preg_match('/\/([^\/]+)$/', $youtubevideo, $matches);
                                        $youtube_id = $matches[1];                                   
                                    ?>
                                    <iframe id="youtube-iframe" width="1000" height="490" src="https://www.youtube.com/embed/<?php echo esc_attr($youtube_id); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    <?php else : ?>

                                    <?php if (!empty($rsvpvideo)) { ?>
                                    <video controls>
                                        <source src="<?php echo esc_url($rsvpvideo); ?>">
                                    </video>
                                    <?php } ?>
                                <?php endif; ?>
                            </div>
                            <?php } ?>
                            </div>
                             <?php if (!empty($rsvpvideo)) { ?>
                            <div class="divider">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/divider.png" alt="">
                            </div>
                             <?php } ?>
                            <div class="wl-inner-card-detaile wl-previewbox">
                                <div class="row">
                                    <div class="col-xxl-5 col-lg-7 col-md-7 col-sm-12 m-auto">
                                        <?php 
                                        if(!empty($eventtitle)) { echo '<div class="mt-3 event-title" style="'.$event_title_css.'">'.esc_html($eventtitle).'</div>'; }

                                            if(!empty($eventdate)) { echo '<div class="mt-2 event-date" style="'.$event_date_css.'">'.esc_html($eventdate).'</div>'; }


                                        ?>
                                        <h4 class="mb-0">Hosted By</h4>
                                        <?php 
                                            if(!empty($guestName)) { echo '<div class="host-name" style="'.$guest_name_css.'">'.esc_html($guestName).'</div>'; }
                                            if(!empty($guestContact)) { echo '<div class="host-contact-no" style="'.$guest_contact_css.'">'.esc_html($guestContact).'</div>'; }
                                            if(!empty($guestMessage)) { echo '<div class="host-message" style="'.$guest_message_css.'">'.esc_html($guestMessage).'</div>'; }
                                         
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                        <?php 
                                           if( !empty($program) && count($program)>0 ){ ?>
                                        <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-8 col-sm-12 m-auto">
                                            <div class="wl-fuc-timing">
                                                <h4 class=" mb-0">Itinerary</h4>
                                                <table>
                                                    <?php 
                                                    if( !empty($program) && count($program)>0 ){
                                                    foreach ($program as $event) :?>
                                                    <tr>
                                                        <td><?php echo esc_attr($event['program_name'])?></td>
                                                        <td><?php echo esc_attr($event['program_time'])?></td>
                                                    </tr>
                                                    <?php endforeach; }?>
                                                </table>
                                            </div>
                                        </div>
                                       <?php } ?>
                                </div>
                                <?php   
                                if($guest_status!='pending' && !empty($guestid)){ ?>
                                    <div class="alert-box mt-5" >You have already submited your response as below.</div>
                                <?php
                                } 

                                ?>  
                                <div class="row">
                                    <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-10 col-sm-12 m-auto">
                                        <div class="wl-joining m-0">
                                            <h4 class="mb-0">Will you be joining us?</h4>
                                            <form action="#">
                                                <div class="check-box-from">
                                                    <div class="check-box-from-field">
                                                        <input id="yes" name="Yes" data-value="Accepted" type="checkbox"
                                                        <?php if($guest_status=='Accepted') echo 'checked'; ?>
                                                        >
                                                        <label for="yes">Yes</label>
                                                    </div>
                                                    <div class="check-box-from-field">
                                                        <input id="no" name="No" data-value="Declined" type="checkbox"
                                                        <?php if($guest_status=='Declined') echo 'checked'; ?>

                                                        >
                                                        <label for="no">No</label>
                                                    </div>
                                                    <div class="check-box-from-field">
                                                        <input id="notSure" name="NotSure" data-value="May Be" type="checkbox" <?php if($guest_status=='May Be') echo 'checked'; ?>>
                                                        <label for="notSure">Not Sure</label>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-10 m-auto">
                                        <div class="guest-count">
                                            <h4 class="mb-0">No. of Guests</h4>
                                            <div class="guest-counter">
                                                <h4>Adults</h4>
                                                <div class="count">
                                                    <span class="mines"><i class="fa-solid fa-minus"></i></span>
                                                    <span class="total-guest" id="adult-guest"><?php echo $guest_adult; ?></span>
                                                    <span class="plues"><i class="fa-solid fa-plus"></i></span>
                                                </div>
                                            </div>
                                            <div class="guest-counter">
                                                <h4>Kids</h4>
                                                <div class="count">
                                                    <span  class="mines"><i class="fa-solid fa-minus"></i></span>
                                                    <span class="total-guest" id="kids-guest"><?php echo $guest_kids; ?></span>
                                                    <span  class="plues"><i class="fa-solid fa-plus"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-8 col-sm-12 m-auto">
                                        <?php 
                                        if(!empty($entry) && !empty($event_id))
                                        {
                                        ?>
                                        <div class="form-group">
                                            <input type="text" name="name" id="name" class="invite-text" placeholder="Name">
                                        </div>

                                        <div class="form-group">
                                            <input type="email" name="email" id="email" class="invite-text" placeholder="Email">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="phone" id="phone" class="invite-text" placeholder="Phone">
                                        </div>
                                        <input type="hidden" id="event_userid" value="<?php echo $event_userid; ?>">
                                        <input type="hidden" id="event_id" value="<?php echo $event_id; ?>">
                                        <input type="hidden" id="guest_preview_url" value="<?php echo $guest_preview_url; ?>">
                                        <?php 
                                        }
                                        ?>                                        
                                        <textarea name="Message" id="mesg" style="margin-bottom: 20px;" rows="5" placeholder="Message to the host..."></textarea>
                                        <div id="guestlist_details_message"  class="guestlist_details_message"></div>
                                        <?php
                                        if(!empty($entry))
                                        {
                                        ?>
                                        <button  type="button" class="btn btn-secondary btn-block" id="open-invite-action-submit">Submit</button>
                                        <?php 
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                                if(!empty($guestid))
                                {
                                ?>                                
                                <div class="row">
                                    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-8 col-sm-12 m-auto text-center ">
                                        <button  id="invite-action-submit" data-eventid="<?php echo $event_id;?>" data-guestid="<?php echo $guestid;?>" type="button"
                                            class="btn btn-secondary m-auto mt-3 ps-4 pe-4">Edit Response</button>
                                    </div>
                                </div>
                                <?php }

                                if($guest_status!='pending' && !empty($guestid) && $guestid=='master'){ ?>
                                    <div class="alert mt-5 alert-success">You have already submited your response as below.</div>
                                <div class="alert mt-5 alert-info">
                                <?php 
        if(!empty($guestid))
        {
        $guest_details_query = $wpdb->prepare(
            "SELECT * FROM $guest_details_info_table WHERE guest_id = %d",
            $guestid
        );

        $guest_details = $wpdb->get_row($guest_details_query, ARRAY_A);

        echo 'Name:'.$guest_details['guest_name'].'<br />';
        if(!empty($guest_details['guest_email'])) { echo 'Email:'.$guest_details['guest_email'].'<br />'; }
        if(!empty($guest_details['guest_phone_num'])) { echo 'Phone:'.$guest_details['guest_phone_num'].'<br />'; }
        if(!empty($guest_details['guest_msg'])) { echo 'Message:'.$guest_details['guest_msg'].'<br />'; }
        echo 'Adults:'.$guest_details['guest_adult'].'<br />';
        echo 'Kids:'.$guest_details['guest_kids'].'<br />';
        echo 'Status:'.ucfirst($guest_details['guest_status']).'<br />';    
        }



                            } ?>
                            </div>
                                <?php wp_nonce_field('ajax-sanas-guest-preview-nonce', 'sanasguestpreviewsecurity');?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
              if( !empty($registry) && count($registry)>0 ){
            ?>       
            <div class="registry">
                <div class="container">
                    <div class="row">
                        <div class=" col-12 m-auto">
                            <h5>Gift Registry</h5>
                            <div class=" row">
                               <?php
                                foreach ($registry as $event) :?>
                                     <div class="col-xl-3 col-lg-6 col-md-6">
                                      <?php 

                                            if (str_contains($event['url'], 'amazon.')) {
                                            ?>
                                            <a class="gift-registry" href="<?php echo esc_url($event['url'])?>" target="_blank">
                                                <?php echo '<img id="img12" src=" ' . get_template_directory_uri() . '/assets/img/Amazon.png" alt=""> '?> </a>
                                            <?php    
                                            } else  if (str_contains($event['url'], 'target.')) {
                                            ?>
                                             <a class="gift-registry" href="<?php echo esc_url($event['url'])?>" target="_blank"><?php echo '<img id="img12" src=" ' . get_template_directory_uri() . '/assets/img/Target.png" alt=""> '?></a>
                                            <?php    
                                            } else {
                                            ?>
                                             <a class="gift-registry" href="<?php echo esc_attr($event['url'])?>" target="_blank"><?php echo esc_attr($event['name'])?></a>
                                            <?php    
                                                
                                            }
                                      ?>                                       
                                </div>
                               <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <?php } ?>
        </div>
    </section>
<?php
}
else{
?>
    <section class="wl-main-canvas guest-preview">
        <div class="container">
          <div class="row">
        <?php
          echo '<p>' . esc_html__('Sorry, enter wrong URL!', 'sanas') . '</p>';
        ?>
          </div>
          </div>
        </section>
<?php 
}
get_footer();