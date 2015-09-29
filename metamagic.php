<?php
/*
Plugin Name: MetaMagic SEO Plugin 
Plugin URI: http://blog.hughestech.com/blog/metamagic/
Description: This SEO WordPress Plugin Generates meta description tags and meta keywords tag for your blog posts automatically.
Version: 1.4
Author: HughesTech Labs
Author URI: http://blog.hughestech.com/blog/

Copyrighted (C)2008 - 2015 Hughes Technologies, Inc. email : plugins@hughestech.com

Hughes Technologies, Inc. grants a license of use for MetaMagic.
It is our hope that it will be useful, but users of MetaMagic use it
without any warranty; without even the implied warranty of merchantability
or fitness for a particular application or purpose. When using MetaMagic,
users use at their own risk! Hughes Technologies, Inc. will not be
responsible for, or held libel for anything pertaining to the use of
MetaMagic! Anyone can use MetaMagic, but modifying, reverse engineering,
selling, Sub-licensing, licensing, bundling with other software for sale,
or for free, or given away as a promotion is strictly forbidden, without
the written permission from Hughes Technologies, Inc. Hughes also reserves
the right to revoke any license at anytime for any reason with or without
cause, or providing a reason for said license revoke! By using MetaMagic
by Hughes Technologies, Inc., you agree to all the above terms!
*/


if ( !get_option('metamagic_options') ) {

$options = array(
		'metamagic_enable'	=> 1,
		'metamagic_description'	=> 1,
		'metamagic_keywords'	=> 1,
		);
					
update_option( 'metamagic_options', $options );

}

function metamagic_main()
{

 $options = get_option( 'metamagic_options' );

      if( $options['metamagic_enable'] == 1 ) {
           if( is_single() ) {  // Make sure for single post, or all posts will have the same meta tags
               global $post; 
               $description = '';
               $keywords = '';
               echo "\n";
               echo '<!-- MetaMagic WordPress plugin; http://blog.hughestech.com/blog/metamagic/ -->' . "\n";
	       if( $options['metamagic_description'] == 1 ) {
		   $recentpost = get_post($post->ID); 
		   $content = $recentpost->post_content;
                   $acontent = strip_tags($content,'<MetaMagic></MetaMagic>');
                   $start = strpos($acontent, '<MetaMagic>');                                                  
                   $end = strpos($acontent, '</MetaMagic>');
                   $content = substr($acontent, $start+11, $end - $start -1);
                   $acontent = strip_tags($content);
                   $content = str_replace("\r", ' ', $acontent);
                   $content = str_replace("\n", ' ', $content);
                   $content = str_replace("\t", '', $content);
                   $description = $content;
                   echo '<meta name="description" content="'. $description .'" />' . "\n";
                 }

    if( $options['metamagic_keywords'] == 1 ) 
    {                
      if ( function_exists("get_the_tags"))
        {
          $wordpress23x = true;  
          $tags = get_the_tags( $post->ID );
        }
	   else if( function_exists("UTW_ShowTagsForCurrentPost") ) {
                 global $utw;
		 $tags = $utw->GetTagsForPost( $post->ID, 8);		
	        }
                             
                             if( $tags ) {
				 $cnt = 0;
				 foreach( $tags as $tboy ) {
                                    if($cnt < 20 ) {
					if( $wordpress23x ) {
						$keywords .= $tboy->name . ', ';
						}
						else {
						      $keywords .=  $tboy->tag . ', '; 
						     }
						     $cnt++;
						            }
						else {
							break;
						}
					}
				}
                                $tempkey = substr_replace($keywords,"",-2);
                                $keywords = $tempkey;
                                echo '<meta name="keywords" content="' . $keywords .'" />' . "\n";	
	             }   
                	
    }
   } //is single post end
        
  if( is_page() ) {  //Single page being displayed New for Version 1.4             
    global $page; 
    $description = '';
    $keywords = '';
    echo "\n";
    echo '<!-- MetaMagic WordPress plugin; http://blog.hughestech.com/blog/metamagic/ -->' . "\n";
    if( $options['metamagic_description'] == 1 )
     {
       $recentpost =  get_page($page->ID); 
       $content = $recentpost->post_content;
       $acontent = strip_tags($content,'<MetaMagic></MetaMagic>');
       $start = strpos($acontent, '<MetaMagic>');                                                  
       $end = strpos($acontent, '</MetaMagic>');
       $content = substr($acontent, $start+11, $end - $start -1);
       $acontent = strip_tags($content);
       $content = str_replace("\r", ' ', $acontent);
       $content = str_replace("\n", ' ', $content);
       $content = str_replace("\t", '', $content);
       $description = $content;
       echo '<meta name="description" content="'. $description .'" />' . "\n";
     }
              	
 } //is page end

} //main function end

add_action('admin_menu','metamagic_menu');

function metamagic_menu() {
	
 add_options_page('MetaMagic', 'MetaMagic', 8, basename(__FILE__), 'metamagic_admin');
	
}

function metamagic_admin() {

	if ( isset($_POST['metamagic_submit'] ) ) {
		metamagic_update_options();
	}
	
	$options = get_option( 'metamagic_options' );
	
	$show_enable      =	( $options['metamagic_enable']       == 1 ) 	?	'checked="checked"' : '';
	$show_description =	( $options['metamagic_description']  == 1 ) 	?	'checked="checked"' : '';
	$show_keywords     =	( $options['metamagic_keywords']     == 1 )	?	'checked="checked"' : '';
		
	echo '<div class="wrap">';
	echo '<h2>' . __('MetaMagic Options') . '</h2>';
	echo '<form method="post" action="">';
	echo '<fieldset>';
	echo '<h3>Select Automatic Features:</h3>';
	echo '<p><input type="checkbox" id="metamagic_enable" name="metamagic_enable" '. $show_enable.' /> <label for="MetaMagic_enable">'. __("Enable ( If enabled, meta tags will be added to your header )") .'</label></p>';
	echo '<p><input type="checkbox" id="metamagic_description" name="metamagic_description" '. $show_description.' /> <label for="MetaMagic_description">'. __("Meta Description ( If checked, the meta description will be any text within these tags: <b>&ltMetaMagic&gttext in your Post(s) or Page(s)&lt/MetaMagic&gt)</b>") .'</label></p>';
	echo '<p><input type="checkbox" id="metamagic_keywords" name="metamagic_keywords" '. $show_keywords.' /> <label for="MetaMagic_keywords">'. __("Meta Keywords ( if checked, your tags will be added as meta keywords when Posting Only )") .'</label></p>';
	echo '<p class="submit">';
	echo '<input type="submit" name="metamagic_submit" value="Save settings" />';
	echo '</fieldset>';
	echo '</form>';
      echo 'If you enjoy using MetaMagic, please give it 5 stars on WordPress<br />'; 
      echo '<a href="http://wordpress.org/support/view/plugin-reviews/metamagic" target="_blank" title="MetaMagic Reviews">Rate MetaMagic</a><br /><br />';
      echo 'Please Help our Dev Team:<br />';
      echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">';
      echo '<input name="cmd" type="hidden" value="_s-xclick" /><br />';
      echo '<input name="hosted_button_id" type="hidden" value="YABX66T8E52ZN" /><br />';
      echo '<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" type="image" /><br />';
      echo '<img src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /></form><br /><br />';
      echo '<strong>Important Note:</strong><br />';
      echo 'Only Add the <b>&ltMetaMagic&gt</b> tags wrapped around some phrase<b>&lt/MetaMagic&gt</b><br />';
      echo 'while editing within the <b>Text Editor Tab, Not</b> the <b>Visual Editor Tab</b>!<br /><br />';
      echo 'That\'s all folks, have fun and improve your SE Rankings....';
      echo '</div>';

}

function metamagic_update_options() {

	$options = array();
	$options['metamagic_enable']      =	( $_POST['metamagic_enable']       == "on" )	?	1 : 0;
	$options['metamagic_description'] =	( $_POST['metamagic_description']  == "on" )	?	1 : 0;
	$options['metamagic_keywords']    =	( $_POST['metamagic_keywords']     == "on" )	?	1 : 0;
	        	
	update_option('metamagic_options', $options);
	
	echo '<div class="updated fade"><p><strong>'. __('Options saved.') .'</strong></p></div>';
		
      }

function replaceit($content) {
 if(!detect_preview_mode())
   {
    $content = str_replace( '</MetaMagic>' , '' , $content);
    $content = str_replace( '<MetaMagic>','',$content);
   } 
   return $content;
  }
  
function detect_preview_mode(){
   $query = $_SERVER['QUERY_STRING'];
   if(preg_match('/preview=true/',$query)){
      $theanswer =1;
      }else{
      $theanswer =0;
      }
    return $theanswer;      
 }   
    
add_filter('the_content', 'replaceit');

add_action('wp_head','metamagic_main', 1);
add_filter('plugin_action_links', 'myplugin_plugin_action_links', 10, 2);

function myplugin_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=metamagic">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}?>