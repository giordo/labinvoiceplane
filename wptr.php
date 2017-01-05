<?php
/**
 * Plugin Name: WP Transolad
 * Plugin URI: http://www.wptransload.com/
 * Description: Plugin takes remote page from the entered URL, examines its content and lists all found images for your choice. Then you are able to select which images you want to import, apply some options such as resize, rename, add your personal titles and more and then in one click to import all images into your wordpress media gallery and automatically create post with imported images in it (as gallery shortcode or embedded with titles).
 * Version: 1.0
 * Author: Giordo
 * Author URI: http://www.wptransload.com/
 */

require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . 'wp-admin/admin-functions.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) ) return false;

define('WPTL_AGENT', $_SERVER['HTTP_USER_AGENT']);

add_post_type_support( 'attachment', 'custom-fields' );

/*
 * **************************
 * Custom Credit field
 * **************************
 */
add_filter( 'attachment_fields_to_edit', 'wpTransload_field_credit', 11, 2 );

function wpTransload_field_credit( $form_fields, $post = null ) {
    $form_fields['wptl-credit-name'] = array(
        'label' => 'Credit Page Name',
        'input' => 'text',
        'value' => get_post_meta( $post->ID, '_wptl-credit-name', true ),
        'helps' => 'If provided, credit sitename will be displayed',
    );

    $form_fields['wptl-credit-url'] = array(
        'label' => 'Credit Page URL',
        'input' => 'text',
        'value' => get_post_meta( $post->ID, '_wptl-credit-url', true ),
        'helps' => 'Add credit URL',
    );

    $form_fields['wptl-credit-img'] = array(
        'label' => 'Credit Image Name',
        'input' => 'text',
        'value' => get_post_meta( $post->ID, '_wptl-credit-img', true ),
        'helps' => 'If provided, credit original image filename will be displayed',
    );

    return $form_fields;
}

add_filter( 'attachment_fields_to_save', 'wpTransload_field_credit_save', 11, 2 );

function wpTransload_field_credit_save( $post, $attachment ) {
    if( isset( $attachment['wptl-credit-name'] ) )
        update_post_meta( $post['ID'], 'wptl-credit-name', $attachment['wptl-credit-name'] );

    if( isset( $attachment['wptl-credit-url'] ) )
        update_post_meta( $post['ID'], 'wptl-credit-url', esc_url( $attachment['wptl-credit-url'] ) );

    if( isset( $attachment['wptl-credit-img'] ) )
        update_post_meta( $post['ID'], 'wptl-credit-img', esc_url( $attachment['wptl-credit-img'] ) );

    return $post;
}

/*
 * **************************
 * Cookie - where to save json
 * **************************
 */

add_action( 'init', 'wpTransload_session' );

function wpTransload_session() {
    global $current_user;
    get_currentuserinfo();
    $cookiename = 'wp-transload-'.$current_user->ID;

    $act = sanitize_text_field($_GET['action']);

    if(!session_id()) {
        session_start();
    }

    if(!isset($_SESSION[$cookiename]))
        $_SESSION[$cookiename] = "[]";

    if($act == 'remove-all'){
        $_SESSION[$cookiename] = "[]";
        return;
    }

    $uids = (is_array($_GET['uid'])? $_GET['uid'] : array());

    $uids = ($_GET['wptl-single-uid'])? array($_GET['wptl-single-uid']) : $uids ;

    $arr = $_SESSION[$cookiename];
    $arr = stripslashes($arr);
    $arr = json_decode($arr, true);

    switch ($act) {
        case 'import':
        case 'import-selected':
            wpTransload_save_imgs($uids);
        case 'remove':
        case 'remove-selected':
            $arr = array_diff_key($arr, array_flip($uids));
            $arr = json_encode($arr);
            $_SESSION[$cookiename] = $arr;
            break;
    }
    return;
}

function wptl_mimearray(){
    $mimearray = array('gif'=>'image/gif', 'jpg'=>'image/jpeg','png'=>'image/png');
    return $mimearray;
}

function wptl_normalizeString ($str = '')
{
    $str = trim($str);
    $str = strip_tags($str);
    $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
    $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
    $str = strtolower($str);
    $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
    $str = htmlentities($str, ENT_QUOTES, "utf-8");
    $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
    $str = str_replace(' ', '-', $str);
    $str = rawurlencode($str);
    $str = str_replace('%', '-', $str);
    return $str;
}

function wptl_uniquefilename($wptl_filename, $wptl_imgmime){
    global $uploads;

    //purge extension if one
    //verifica il quart'ultimo carattere
    if(substr($wptl_filename, -4, 1) == '.'){
        $wptl_filename = substr($wptl_filename, 0, strlen($wptl_filename) - 4);
    }

    //real extension
    $_ext = array_search($wptl_imgmime, wptl_mimearray());

    $wptl_filename = wptl_normalizeString($wptl_filename);

    $n = 1;
    while(file_exists($uploads['path'] . "/$wptl_filename".".".$_ext)){
        $lastp = strrpos($wptl_filename, '-');
        if($lastp){
            $suffisso = substr($wptl_filename, $lastp + 1);
            if(is_numeric($suffisso)){
                $wptl_filename = substr($wptl_filename, 0, $lastp);
                $n = $suffisso + 1;
            }
            else{
                $n = 1;
            }
        }
        $wptl_filename = $wptl_filename.'-'.$n;
    }

    return $wptl_filename.'.'.$_ext;
}

function wptl_uniqueFileNameJson($json){

    $filename = (count($_GET['wptl_filename']) == 0)? $_GET['wptl_unique'] : $_GET['wptl_filename'];
    $wptl_filename = wptl_uniquefilename($filename, $_GET['wptl_mimetype']);
    echo json_encode(array(wptl_filename => $wptl_filename, err => 0, msg => 'done'));
    die;
}

function wpTransload_save_imgs($uids){
    global $uploads;

    $resize = false;

    $log = $attached = $urls = array();

    if(is_array($uids)){
        foreach($uids as $uid){
            $wptl_uid = sanitize_text_field($_GET[$uid.'_uid']);
            // echo $_GET[$uid.'_wptl_imgurl'];
            $wptl_imgurl = esc_url($_GET[$uid.'_wptl_imgurl']);
            $wptl_filename = sanitize_text_field($_GET[$uid.'_wptl_filename']);
            $wptl_imgmime = sanitize_text_field($_GET[$uid.'_wptl_imgmime']);

            $wptl_pageurl = esc_url($_GET[$uid.'_wptl_pageurl']);
            $wptl_pagetitle = sanitize_text_field($_GET[$uid.'_wptl_pagetitle']);
            $wptl_alt = sanitize_text_field($_GET[$uid.'_wptl_alt']);
            $wptl_title = sanitize_text_field($_GET[$uid.'_wptl_title']);
            $wptl_caption = sanitize_text_field($_GET[$uid.'_wptl_caption']);
            $wptl_description = sanitize_text_field($_GET[$uid.'_wptl_description']);

            $wptl_w = absint($_GET[$uid.'_wptl_w']);
            $wptl_h = absint($_GET[$uid.'_wptl_h']);

            $wptl_filename = wptl_uniquefilename($wptl_filename, $wptl_imgmime);

            $data = wpTransload_remote_get($wptl_imgurl);

            if(is_wp_error($data)) {
                $log[] = 'Unable to import <em>' . $wptl_imgurl . '</em>';
                continue;
            }

            $new_file = $uploads['path'] . "/$wptl_filename";
            $new_url = $uploads['url'] . "/$wptl_filename";

            $fb = fopen( $new_file, 'wb' );

            if( $fb ) {
                fwrite( $fb, $data['body'], strlen($data['body']) );
                fclose($fb);
            }

            if(($wptl_w+$wptl_h) > 0) {
                $img = wp_get_image_editor($new_url);
                if ( ! is_wp_error( $img ) ) {
                    $img->resize( $wptl_w ? $wptl_w  : NULL , $wptl_h ? $wptl_h : NULL, false );
                    $saved = $img->save($new_file);
                }
                unset($img);

                if ($saved === FALSE) {
                    $log[] = 'Unable to resize <em>' . $wptl_filename . '</em>';
                }
            }

            if( file_exists($new_file) ) {
                $info = @getimagesize($new_file);

                $title = "Image added";

                $trim_wptl_title = trim($wptl_title);
                // Construct the attachment array
                $attachment = array(
                    'post_title' => (count($trim_wptl_title) > 0? $wptl_title: $wptl_filename ), //sanitize_title_with_dashes($filename),
                    'post_content' => $wptl_description,
                    'post_status' => 'inherit',
                    'post_parent' => 0,
                    'post_mime_type' => $wptl_imgmime,
                    'guid' => $new_url,
                    '_wp_attachment_image_alt' => $wptl_alt,
                    'post_excerpt' => $wptl_caption,
                );

                // Save the data
                $att_id = wp_insert_attachment($attachment, $new_file, '');

                update_post_meta($att_id, '_wptl-credit-name', $wptl_pagetitle);
                update_post_meta($att_id, '_wptl-credit-url', $wptl_pageurl);
                update_post_meta($att_id, '_wptl-credit-img', $wptl_imgurl);
                update_post_meta($att_id, '_wp_attachment_image_alt', $wptl_alt);

                if ( !is_wp_error($att_id) ) {
                    $imagedata = wp_generate_attachment_metadata( $att_id, $new_file );

                    wp_update_attachment_metadata( $att_id, $imagedata );

                    $attached[] = $att_id;
                    $urls[] = $new_url;
                    $titles[] = $title;
                }
            }
        }
    }
}

add_action( 'wp_ajax_wptl-uniqueFileNameJson', 'wptl_uniqueFileNameJson' );


add_action( 'wp_ajax_wp-transload', 'wpTransload_callback' );

function wpTransload_callback() {
    header('Content-type: text/html');
    header('Access-Control-Allow-Origin: *');

    global $current_user;
    get_currentuserinfo();

    if ( $current_user->ID  == 0) {
        echo json_encode(array('ret' => 0, 'msg' => 'not_user'));
        wp_die();
    }

    if(!current_user_can('upload_files')){
        echo json_encode(array('ret' => 0, 'msg' => 'not_perms'));
        wp_die();
    }

    $cookiename = 'wp-transload-'.$current_user->ID;

    $present_images = array();

    if(isset($_SESSION[$cookiename])){
        $present_images = $_SESSION[$cookiename];
        $present_images = stripslashes($present_images);
        $present_images = json_decode($present_images, true);
    }

    $new_image = array();
    /*
    action=wp-transload&
    wptl_uid=4d5dd43e-3bec-81cc-9e24-ef2d3ed70c2f&
    wptl_imgurl=https%3A%2F%2Fladoppiaversione.files.wordpress.com%2F2016%2F01%2Fnodo.jpg%3Fw%3D632&
            wptl_imgmime=image%2Fjpeg&
            wptl_pageurl=https%3A%2F%2Fladoppiaversione.wordpress.com%2F&
            wptl_pagetitle=ladoppiaversione&
            wptl_alt=nodo&
            wptl_title=&
            wptl_caption=%20&
            wptl_w=800&
            wptl_h=600&
            wptl_imgsize=38.39%20KB%20(39%2C313%20bytes)
    */
    //$path_parts = pathinfo( $new_file );
    //$basename = $path_parts['basename']; // 'whatever.jpeg'


    if($_POST['wptl_uid']){

        $new_image = array(
            sanitize_text_field($_POST['wptl_uid']) => array(
                'wptl_uid' => sanitize_text_field($_POST['wptl_uid']),
                'wptl_imgurl' => esc_url($_POST['wptl_imgurl']),
                'wptl_imgname' => sanitize_text_field($_POST['wptl_uid']),// sanitize_text_field($basename),
                'wptl_imgmime' => sanitize_text_field($_POST['wptl_imgmime']),
                'wptl_pageurl' => esc_url($_POST['wptl_pageurl']),
                'wptl_pagetitle' => sanitize_text_field($_POST['wptl_pagetitle']),
                'wptl_alt' => sanitize_text_field($_POST['wptl_alt']),
                'wptl_title' => sanitize_text_field($_POST['wptl_title']),
                'wptl_caption' => sanitize_text_field($_POST['wptl_caption']),
                'wptl_description' => '',
                'wptl_w' => sanitize_text_field($_POST['wptl_w']),
                'wptl_h' => sanitize_text_field($_POST['wptl_h'])
            )
        );
    }

    if(is_array($present_images))
        $current_images = array_merge($present_images, $new_image);
    else
        $current_images = $new_image;

    $_SESSION[$cookiename] = json_encode($current_images);

    echo json_encode( array('ret' => count($current_images), 'msg' => 'done'));

    wp_die();
}


/* FOR THIS FUNCTION THANKS TO
 * Plugin Name: Image Gallery Import
 * Author: Michael Shevtsov
 * Author URI: http://www.wptrack.com/
*/
function wpTransload_remote_get($url, $ref = ''){
    $opts = array( 'timeout' => 30, 'httpversion' => '1.1', 'sslverify' => false, 'user-agent'  => WPTL_AGENT);
    if($ref)
        $opts['headers'] = array("Referer: $ref");

    $data = wp_remote_get( $url, $opts );

    return $data;
}

/*
 * **************************
 * Head and Menu
 * **************************
 */

add_action('admin_head', 'wpTransload_purecss');

function wpTransload_purecss() {
    echo '<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">';
    echo '<style>
            th.label{
                vertical-align: top;
            }
            .wptldiv{
                padding: 1px;
            }
            .wptldiv label{
                padding: 5px;
                width: 300px;
                display: block;
                color: #ccc;
            }
            .wptldiv input[type=text]{
                width: 300px;
            }
            .wptlbtndiv input[type=text]{
                width: 80px;
            }
            .wptlimg{
                max-width: 200px;
                max-height:200px;
            }
        </style>
        <script type="text/javascript">
            jQuery( document ).ready(function( $ ) {
                $(".wptl-add-button").click(function(e){
                    var uid = $(this).data("uid");
                    $("#wptl-single-uid").val(uid);
                });


                $(".wptl_setfilename").click(function(e){
                    var uid = $(this).data("uid");
                    var _filename = $(this).prev().val();
                    var _mimetype = $("#"+ uid + "_wptl_imgmime").val();
                    if($(this).hasClass("wptl_setunique")){
                        _filename = uid;
                    }
                    $.get( "./admin-ajax.php", { action: "wptl-uniqueFileNameJson", wptl_filename: _filename, wptl_unique : uid, wptl_mimetype : _mimetype} )
                    .done(function( data ) {
                        data = $.parseJSON(data);
                        if(data.msg == "done"){
                            $("#"+ uid + "_wptl_filename").val(data.wptl_filename);
                        }
                        else{
                            alert( "Something wrong: " + data );
                        }
                    });
                });
            });
        </script>';
}

add_action('admin_menu', 'wpTransload_menu');

function wpTransload_menu() {
    add_submenu_page('upload.php', 'Transload images', 'Transload images', 3, __FILE__, 'wpTransload_view');
}

function wpTransload_view (){
    global $current_user;
    get_currentuserinfo();

    $cookiename = 'wp-transload-'.$current_user->ID;

    if(!current_user_can('upload_files')) return; // author min...
    //$actual_link = "http://".$_SERVER["HTTP_HOST"].'/wp-admin/upload.php?page=wp-transload%2Fwp-transload.php';
    $actual_link = admin_url( 'upload.php?page=wp-transload%2Fwp-transload.php');
    ?>
    <h2>Import/transload Images</h2>

    <div class="content wrap">
        <p class="impostazioni">
            <a id="wptl-remove-all" class="wptl-add-button pure-button pure-button-primary" href="<?php echo $actual_link; ?>&action=remove-all">Remove all</a>
        </p>
        <form action="<?php echo $actual_link; ?>">
            <input type="hidden" id="page" name="page" value="wp-transload/wp-transload.php" />
            <input type="hidden" id="action" name="action" value="import" />
            <input type="hidden" id="wptl-single-uid" name="wptl-single-uid" value="" />
            <table class="pure-table pure-table-bordered">
                <thead>
                <tr>
                    <th>
                        <label for="wptl-check-all">Check all</label>
                        <input id="wptl-check-all" type="checkbox" />
                    </th>
                    <th>img</th>
                    <th>Model</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $present_images = array();
                if(isset($_SESSION[$cookiename])){
                    $present_images = $_SESSION[$cookiename];
                    $present_images = stripslashes($present_images);
                    $present_images = json_decode($present_images, true);
                }
                $counter = 0;
                foreach ($present_images as $image){
                    $ext = '.'.pathinfo($image['wptl_imgurl'], PATHINFO_EXTENSION);
                    $ext = substr($ext, 0, 4);
                    $uid = $image['wptl_uid'];
                    $oname = (strpos(basename($image['wptl_imgurl']), '&') === FALSE && strpos(basename($image['wptl_imgurl']), '?') === FALSE)? basename($image['wptl_imgurl']) : $image['wptl_uid'].$ext;
                    $nname = $uid.$ext;

                    $path_parts = pathinfo( $image['wptl_imgurl'] );
                    $basename = $path_parts['basename']; // 'whatever.jpeg'

                    $trim_wptl_imgmime = trim($image['wptl_imgmime']);
                    if(empty($trim_wptl_imgmime)){
                        $image['wptl_imgmime'] = image_type_to_mime_type (exif_imagetype ( $image['wptl_imgurl'] ));
                    }

                    $counter++;
                    ?>
                    <tr>
                        <td>
                            <label for="<?php echo $uid; ?>-chkbox"></label>
                            <input id="<?php echo $uid; ?>-chkbox" name="uid[]" value="<?php echo $uid; ?>" type="checkbox"> <?php echo $counter; ?>
                        </td>
                        <td>
                            <img id="<?php echo $uid; ?>_img" class="wptlimg" src="<?php echo $image['wptl_imgurl']; ?>" />
                            <div class="wptldiv wptlbtndiv input-text-wrap" id="imagecaption-wrap">
                                <label class="zprompt" for="<?php echo $uid; ?>_wptl_w" id="<?php echo $uid; ?>_wptl_width_prompt_text">Max Width & Max Height</label>
                                <input type="text" name="<?php echo $uid; ?>_wptl_w" id="<?php echo $uid; ?>_wptl_w" autocomplete="off" value="<?php echo $image['wptl_w']; ?>" />
                                <input type="text" name="<?php echo $uid; ?>_wptl_h" id="<?php echo $uid; ?>_wptl_h" autocomplete="off" value="<?php echo $image['wptl_h']; ?>" />
                                <input type="text" readonly="readonly" name="<?php echo $uid; ?>_wptl_imgmime" id="<?php echo $uid; ?>_wptl_imgmime" autocomplete="off" value="<?php echo $image['wptl_imgmime']; ?>" />


                            </div>
                            <div class="wptldiv input-text-wrap" id="btn-wrap">
                                </br>
                                <button data-uid="<?php echo $uid; ?>" class="wptl-add-button pure-button pure-button-primary">Add</button>&nbsp;
                                &nbsp;
                                <a href="<?php echo $actual_link.'&action=remove&uid[]='.$uid; ?>"  class="wptl-delete-button pure-button ">Remove</a>

                            </div>
                        </td>
                        <td>
                            <div class="wptldiv input-text-wrap" id="<?php echo $uid; ?>-wptl-ofilename-wrap">
                                <input type="hidden" name="<?php echo $uid; ?>_wptl_imgurl" id="<?php echo $uid; ?>_wptl_imgurl" value="<?php echo $image['wptl_imgurl']; ?>" />
                                <label class="zprompt" for="<?php echo $uid; ?>_wptl_imgname" id="<?php echo $uid; ?>_wptl_imgname_prompt_text">Nome Originale FIle</label>
                                <input disabled="disabled" type="text" name="<?php echo $uid; ?>_wptl_imgname" id="<?php echo $uid; ?>_wptl_imgname" autocomplete="off" value="<?php echo $basename; ?>"><input class="wptl_setfilename" data-uid="<?php echo $uid; ?>" type="button" value="Set filename"/>
                            </div>
                            <div class="wptldiv input-text-wrap" id="<?php echo $uid; ?>_wptl_filename_wrap">
                                <label class="zprompt" for="<?php echo $uid; ?>_wptl_filename" id="<?php echo $uid; ?>_wptl_filename_prompt_text">Nome FIle</label>
                                <input type="text" name="<?php echo $uid; ?>_wptl_filename" id="<?php echo $uid; ?>_wptl_filename" autocomplete="off" value="<?php echo $nname; ?>"><input class="wptl_setfilename wptl_setunique" data-uid="<?php echo $uid; ?>" type="button" value="Set Unique"/>
                            </div>
                            <div class="wptldiv input-text-wrap" id="<?php echo $uid; ?>-wptl-alt-wrap">
                                <label class="zprompt" for="<?php echo $uid; ?>_wptl_alt" id="<?php echo $uid; ?>_wptl_alt_prompt_text">Alt</label>
                                <input type="text" name="<?php echo $uid; ?>_wptl_alt" id="<?php echo $uid; ?>_wptl_alt" autocomplete="off" value="<?php echo $image['wptl_alt']; ?>"><input class="wptl_setfilename" data-uid="<?php echo $uid; ?>" type="button" value="Set filename"/>
                            </div>
                            <div class="wptldiv input-text-wrap" id="<?php echo $uid; ?>wptl-caption-wrap">
                                <label class="zprompt" for="<?php echo $uid; ?>_wptl_caption" id="<?php echo $uid; ?>_wptl_caption_prompt_text">Caption</label>
                                <input type="text" name="<?php echo $uid; ?>_wptl_caption" id="<?php echo $uid; ?>_wptl_caption" autocomplete="off" value="<?php echo $image['wptl_caption']; ?>"><input class="wptl_setfilename" data-uid="<?php echo $uid; ?>" type="button" value="Set filename"/>
                            </div>
                            <div class="wptldiv input-text-wrap" id="<?php echo $uid; ?>wptl-title-wrap">
                                <label class="zprompt" for="<?php echo $uid; ?>_wptl_title" id="<?php echo $uid; ?>_wptl_title_prompt_text">Title</label>
                                <input type="text" name="<?php echo $uid; ?>_wptl_title" id="<?php echo $uid; ?>_wptl_title" autocomplete="off" value="<?php echo $image['wptl_title']; ?>"><input class="wptl_setfilename" data-uid="<?php echo $uid; ?>" type="button" value="Set filename"/>
                            </div>
                            <div class="wptldiv textarea-wrap" id="<?php echo $uid; ?>-wptl-description-wrap">
                                <label class="zprompt" for="<?php echo $uid; ?>_wptl_description" id="<?php echo $uid; ?>_wptl_description_prompt_text">Description</label>
                                <textarea class="mceEditor" rows="3" cols="5" style="overflow:hidden; width: 300px;" name="<?php echo $uid; ?>_wptl_description" id="<?php echo $uid; ?>_wptl_description" autocomplete="off" ><?php echo $image['wptl_description']; ?></textarea>
                            </div>
                            <div class="wptldiv input-text-wrap" id="<?php echo $uid; ?>-wptl-page-wrap">
                                <label class="zprompt" for="<?php echo $uid; ?>-wptl-pagetitle" id="<?php echo $uid; ?>_wptl_page_prompt_text">Title & Page from address</label>
                                <input type="text" name="<?php echo $uid; ?>_wptl_pagetitle" id="<?php echo $uid; ?>_wptl_pagetitle" autocomplete="off" readonly="readonly" value="<?php echo $image['wptl_pagetitle']; ?>"><input class="wptl_setfilename" data-uid="<?php echo $uid; ?>" type="button" value="Set filename"/>
                                <input type="text" name="<?php echo $uid; ?>_wptl_pageurl" id="<?php echo $uid; ?>_wptl_pageurl" autocomplete="off" readonly="readonly" value="<?php echo $image['wptl_pageurl']; ?>">
                            </div>

                        </td>

                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </form>
    </div>
    <?php
}



/*
 * UTILITY
 */

function getDataURI($img, $mime = '') {
    return 'data:'.(function_exists('mime_content_type') ? mime_content_type($img) : $mime).';base64,'.base64_encode(file_get_contents($img));
}