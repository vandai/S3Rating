<?php
/*
* Plugin Name: S3 Rating
* Plugin URI: http://www.vnetware.com/
* Description: A very simple plugin to display star for rating!
* Version: 1.0
* Author: Miaz Akemapa
* Author URI: http://www.vnetware.com/
* Text Domain: s3rating
*/

Class S3Rating {
    protected $STAR_IMG_SIZE = "32";
    protected $S3R_VERSION = "1.0";
    
    public function __construct() {
        add_action('admin_menu', array($this,'add_s3r_menu'));
        add_shortcode('s3r', array($this,'sss_rating'));
        add_action('admin_init', array($this,'s3rating_options_init'));
        
        //Translation
        add_action('init', array($this,'s3r_lang_init'));
    }
    
    public function add_s3r_menu(){
        add_options_page("S3 Rating", "S3 Rating", 'manage_options', "s3rating", array($this,"s3_settings"));
    }
    
    function s3r_lang_init(){
        load_plugin_textdomain('s3rating', false, dirname(plugin_basename(__FILE__))."/languages/");
    }
    
    public function s3_settings(){
        if(function_exists( 'wp_enqueue_media' )){
            wp_enqueue_media();
        }else{
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
        }
        
        $setting_updated = isset($_GET['settings-updated']) ? $_GET['settings-updated'] : "";
        
        echo '<div><img src="'.plugins_url('assets/img/star32.png', __FILE__).'" style="float:left;padding-right:10px;" />';
        echo "<span><h2 style='padding-top:10px;'>" . __( 'S3-Rating Settings','s3rating') . "</h2></span></div>  ";
        ?>
<script>
jQuery(document).ready(function($) {
   var custom_uploader;
   var input_target;
    $('.s3_upload_button').unbind('click').click(function(e) {
        var input_target = $(this).attr('rel');
        e.preventDefault();
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            library: { type: 'image' },
            multiple: false
        });
        custom_uploader.on('select', function() {
            //attachment = custom_uploader.state().get('selection').first().toJSON();
            var attachments = custom_uploader.state().get( 'selection' ).toJSON();
            img_thumb = attachments[0].url;
            $('#'+input_target).val(img_thumb); 
        });
        custom_uploader.on( 'close', function() {
            var attachments = custom_uploader.state().get( 'selection' ).toJSON();
            img_thumb = attachments[0].url;
            $('#'+input_target).val(img_thumb); 
            $('#s3_'+input_target).attr('src', img_thumb);
        } );
 
        custom_uploader.open();
    });
    
    $("#s3_reset_default").click(function(e){
        $("#s3_img_size").val('');
        $("#star_image_1").val('');
        $("#star_image_2").val('');
        $("#star_image_3").val('');
        $("form#s3_settings_form").submit();
    });
});
</script>
<div id="menu-management">
    <div id="menu-edit">
        <div id="post-body">
            <div id="post-body-content">
                <div class="wrap">
                    <form method="post" name="s3_settings_form" id="s3_settings_form" action="options.php">
                        <?php settings_fields('s3rating_form_options'); ?>
                        <?php $options = get_option('s3rating_options'); ?>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <td colspan="2"><br />
                                        <?php _e('S3-Rating comes with default star image files with different sizes you can choose:','s3rating');?>
                                    </td>
                                </tr>
                                <tr valign="top"><th scope="row"><?php _e('Default Star Image Size','s3rating');?></th>
                                    <td>
                                        <select name="s3rating_options[star_image_size]" id="s3_img_size">
                                            <option value="32" <?php if($options['star_image_size'] == "32") {echo "selected";} ?>>32 x 32 Pixels</option>
                                            <option value="24" <?php if($options['star_image_size'] == "24" OR $options['komper_date_loc'] == "") {echo "selected";} ?>>24 x 24 Pixels (Default)</option>
                                            <option value="16" <?php if($options['star_image_size'] == "16") {echo "selected";} ?>>16 x 16 Pixels</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><br />
                                        <?php _e('You can use your own star image files, upload 3 star images (coloured, half coloured, and gray):','s3rating');?>
                                        <img src="<?php echo plugins_url('assets/img/star16.png', __FILE__)?>" />
                                        <img src="<?php echo plugins_url('assets/img/star16_half.png', __FILE__)?>" />
                                        <img src="<?php echo plugins_url('assets/img/star16_dark.png', __FILE__)?>" />
                                    </td>
                                </tr>
                                <tr valign="top"><th scope="row"><?php _e('Custom Star Image URL','s3rating');?></th>
                                    <td><input type="url" id="star_image_1" name="s3rating_options[star_image_url]" value="<?php echo $options['star_image_url']; ?>" class="regular-text" />
                                        <input id="btn_upload_1" type="button" class="button s3_upload_button" value="<?php _e( 'Select Image', 's3rating' ); ?>" rel="star_image_1" />
                                        <span><img id="s3_star_image_1" src="" class="floatright" /></span>
                                    </td>
                                </tr>
                                
                                <tr valign="top"><th scope="row"><?php _e('Custom Star Image Half','s3rating');?></th>
                                    <td><input type="text" id="star_image_2" name="s3rating_options[star_image_half]" value="<?php echo $options['star_image_half']; ?>" class="regular-text" />
                                    <input id="btn_upload_2" type="button" class="button s3_upload_button" value="<?php _e( 'Select Image', 's3rating' ); ?>" rel="star_image_2" />
                                    <span><img id="s3_star_image_2" src="" class="floatright" /></span>
                                    </td>
                                </tr>
                                
                                <tr valign="top"><th scope="row"><?php _e('Custom Star Image Gray','s3rating');?></th>
                                    <td><input type="text" id="star_image_3" name="s3rating_options[star_image_dark]" value="<?php echo $options['star_image_dark']; ?>" class="regular-text" />
                                    <input id="btn_upload_2" type="button" class="button s3_upload_button" value="<?php _e( 'Select Image', 's3rating' ); ?>" rel="star_image_3" />
                                    <span><img id="s3_star_image_3" src="" class="floatright" /></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Changes','s3rating') ?>" />
                            <input type="button" id="s3_reset_default" class="button-primary" value="<?php _e('Reset To Default','s3rating') ?>" />
                        </p>
                    </form>
                    <div class="tool-box">
                        <h3 class="title"><?php _e('Shortcode Usage:','s3rating');?></h3>
                        <table class="widefat importers" style="width:300px">
                            <tbody>
                                <tr>
                                    <td>
                                        [s3r star=7/10]
                                    </td>
                                    <td class="desc">
                                        <?php echo do_shortcode("[s3r star=7/10]");?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        [s3r star=2.5/5]
                                    </td>
                                    <td class="desc">
                                        <?php echo do_shortcode("[s3r star=2.5/5]");?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        [s3r star=6]
                                    </td>
                                    <td class="desc">
                                        <?php echo do_shortcode("[s3r star=6]");?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    }
    
    public function sss_rating($atts){
        extract(shortcode_atts(array(  
            'star' => "3/5"
        ), $atts));  
        
        $output = $this->show_star($star);
        return $output;
    }
    
    public function show_star($star){
        $options = get_option('s3rating_options');
        $star_size = ($options['star_image_size']) ? $options['star_image_size'] : $this->STAR_IMG_SIZE ;
        
        $star_img = ($options['star_image_url']) ? $options['star_image_url'] : plugins_url('assets/img/star'.$star_size.'.png', __FILE__);
        $star_half = ($options['star_image_half']) ? $options['star_image_half'] : plugins_url('assets/img/star'.$star_size.'_half.png', __FILE__);
        $star_dark = ($options['star_image_dark']) ? $options['star_image_dark'] : plugins_url('assets/img/star'.$star_size.'_dark.png', __FILE__);
        
        $s = "<span id='bintanglima' title='".$star."'>";
        $j = explode("/",$star);
        
        if(count($j) > 1){
            $rating = str_replace(",",".",trim($j[0]));
            $max = round($j[1]);
        }else{
            $rating = $star;
            $max = ceil($star);
        }
        for($i=1;$i<=$max;$i++){
            if($i <= floor($rating)){
                $s .= "<img src=".$star_img." />";
            }elseif($i == ceil($rating) AND $this->is_decimal($rating)){
                $s .= "<img src=".$star_half." />";
            }else{
                $s .= "<img src=".$star_dark." />";
            }
        }
        $s .= "</span>";
        return $s;
    }
    
    private function is_decimal($val){
        return is_numeric( $val ) && floor( $val ) != $val;
    }
    
    //S3 Rating Options Page
    function s3rating_options_init(){
            register_setting( 's3rating_form_options', 's3rating_options', array($this,'s3rating_options_validate') );
    }
    function s3rating_options_validate($input) {
        $input['star_image_size'] = sanitize_title(sanitize_text_field($input['star_image_size']));
        
        return $input;
    }
}

$s3 = new S3Rating();

?>
