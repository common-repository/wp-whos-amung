<?php
/*
Plugin Name: Whos Amung Stats
Plugin URI: http://linkloco.net
Description: Show statistics for you Whos Amung ID.
Version: 0.1
Author: Felipe
Author URI: http://linkloco.net
License: GPL2
*/
function wp_codex_search_form() {
global $wp_admin_bar, $wpdb;
if ( !is_super_admin() || !is_admin_bar_showing() )
return;
    $url = 'http://whos.amung.us/sitecount/'. get_option('whosamung_id') .'/';
	$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
/* Add the main siteadmin menu item */
$wp_admin_bar->add_menu( array( 'id' => 'codex_search', 'title' => __( '<img src="'.$x.'/favicon.png" align="absmiddle"> Actual users Online: ' . file_get_contents($url), 'textdomain' ), 'href' => 'http://whos.amung.us/stats/'. get_option('whosamung_id') .'/' ) );
}
add_action( 'admin_bar_menu', 'wp_codex_search_form', 1000 );

  //settings
  add_action( 'admin_init', 'regi_mysettings' );
function regi_mysettings() {
	//register our settings
	register_setting( 'whosamung_settings', 'whosamung_id' );
}
  
  //Menus
add_action('admin_menu', 'my_plugin_menu');
function my_plugin_menu() {
	add_options_page('Whos Amung', 'Whos Amung', 'manage_options', 'whosamung-config', 'my_plugin_options');
	add_dashboard_page('Whos Amung', 'Whos Amung', 'manage_options', 'whosamung-stats', 'my_plugin_options2');
}

function my_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	} ?>
<div class='wrap'><h2><div id="icon-options-general" class="icon32"></div>Whos Amung Stats - Configuration</h2><br>
<?php if ($_GET['updated']==true) { ?>
    <br><div id="message" class="updated">
        <p>
        Updated.
        </p> <?php } ?>
    </div>
    <form method="post" action="options.php">
    <?php settings_fields( 'whosamung_settings' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">ID</th>
        <td><input type="text" name="whosamung_id" size="40" value="<?php echo get_option('whosamung_id'); ?>" /></td>
        </tr>

    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
<?php }

function my_plugin_options2() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	} 
	
	?>
    <script type="text/javascript" src="http://assets.amung.us/scripts/stats/stats.js" charset="utf-8"></script>
    <script type="text/javascript">google.load('mootools', '1.2.4');</script>
    <script src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js" type="text/javascript"></script>
    <script type="text/javascript" src="http://assets.amung.us/scripts/locale/pt-br.js"></script>
    <script type="text/javascript" src="http://assets.amung.us/scripts/stats/components.js" charset="utf-8"></script>
    <script type="text/javascript" src="http://assets.amung.us/scripts/3rdparty/moomore.js"></script>
    <script type="text/javascript" src="http://assets.amung.us/scripts/3rdparty/uvumidropdown.js"></script>
    <script type="text/javascript" src="http://assets.amung.us/scripts/global.js"></script>
    <script type="text/javascript" src="http://assets.amung.us/scripts/nav.js"></script>
    <script type="text/javascript" charset="utf-8">    
        sitekey = '<?php echo get_option('whosamung_id'); ?>';
        siteObserver = new SiteObserverKlass(sitekey, '0');
        updater = new SyncUpdater();
        updater.add_item(siteObserver.update.bind(siteObserver));
		var sep = '.';
        
        function title_updater(count) {
						var ctext = 'xxx visitantes online';
			ctext = '( ' + ctext.replace('xxx', addCommas(count, sep)) + ' )';
			            $('title_count').set('text', ctext);
        }                                
        window.addEvent('domready', function() {
            siteObserver.add_observer(title_updater);
						
			//delayed script loads, stat pages specific
			loadScript("http://s7.addthis.com/js/250/addthis_widget.js#pub=xa-4afb4aa83de1f877");
        });
    </script>
    <script type="text/javascript" charset="utf-8">
        window.addEvent('domready', function() {
            nav = new NavBar(75);
            nav.launch('gn_whos');
			new UvumiDropdown('langmenu');
        });
        var lang = 'pt-br';
        
        //addthis - global button config, all buttons get config from here
		var addthis_config = {ui_language: lang, data_use_flash: false};
		
		//delayed script loads, global
		loadScript("http://s7.addthis.com/js/250/addthis_widget.js#pub=xa-4af4a1e16eefc226");
    </script>
    	<script type="text/javascript" charset="utf-8">
	    	
        function timeline_updater(count) {
           try {
               getFlashMovieObject("timeline").update_avg_now(count);
           } catch(err) {  }
        }
        
        function timeline_ready() {
            siteObserver.add_observer(timeline_updater);
        } 
           
        window.addEvent('domready', function() {
			var lst = new ListKlass('whos_data', sitekey, 5, 'pages', true, sep);
			lst.update();
			updater.add_item(lst.update.bind(lst));
			loadScript("http://wz.tynt.com/javascripts/TracerWidget.js?user=<?php echo get_option('whosamung_id'); ?>");
			
			//check if we need to reload after 30 seconds on the page
            //setTimeout("reload_charts()", 30000);
        });
        
        //reloaders for dashboard components (timeline only for now required)
        function reload_timeline() {
            try {
                getFlashMovieObject("timeline").reload();
            } catch(err) {  }
        }    
        
        function reload_charts() {
            var currentTime = new Date()
            var minutes     = currentTime.getMinutes()
            
            //reload charts at  minute past the hour
            if(minutes <= 2) {
                reload_timeline();
            }
                
            //check every 2 minutes to see if charts need to be updated
            setTimeout("reload_charts()", 120000);
        }
    </script>
<div class='wrap'><h2>Whos Amung Stats</h2>
  <h1 align="right"><a href="<?php echo 'http://whos.amung.us/stats/'. get_option('whosamung_id') .'/'; ?>" target="_blank">Get full Stats!</a></h1> 
  <h2>
  <hr />
    Global Map: <br />
        
    <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
            codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" 
            width="468" height="234" id="dashmap" align="middle">
      <param name="allowScriptAccess" value="always" />
      <param name="allowNetworking" value="all" />
      <param name="allowFullScreen" value="false" />
      <param name="movie" value="http://whos.amung.us/flash/dashmap.swf" />
      <param name="quality" value="high" />
      <param name="bgcolor" value="#ffffff" />
      <param name="flashvars" value="wausitehash=<?php echo get_option('whosamung_id'); ?>&amp;pin=star-red-dashmap&amp;link=yes&amp;map=dashmap.png" />
      <embed src="http://whos.amung.us/flash/dashmap.swf" quality="high" bgcolor="#ffffff" 
            flashvars="wausitehash=<?php echo get_option('whosamung_id'); ?>&amp;pin=star-red-dashmap&amp;link=yes&amp;map=dashmap.png"
            width="468" height="234" name="dashmap" align="middle" allowscriptaccess="always" allowfullscreen="false" 
            allownetworking="all" type="application/x-shockwave-flash" 
			pluginspage="http://www.macromedia.com/go/getflashplayer" />                    
    </object>
        
  <br />
        
    Timeline:
        
        
        
    <br>
    <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
            codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" 
            width="900" height="130" id="timeline" align="middle">
      <param name="allowScriptAccess" value="always" />
      <param name="allowNetworking" value="all" />
      <param name="allowFullScreen" value="false" />
      <param name="movie" value="http://whos.amung.us/flash/timeline.swf?data=http://whos.amung.us/stats/graph_data/<?php echo get_option('whosamung_id'); ?>/daily/timeline/" />
      <param name="quality" value="high" />
      <param name="bgcolor" value="#ffffff" />   
      <param name="wmode" value="transparent" /> 
      <embed src="http://whos.amung.us/flash/timeline.swf?data=http://whos.amung.us/stats/graph_data/<?php echo get_option('whosamung_id'); ?>/daily/timeline/" 
            quality="high" bgcolor="#ffffff" width="900" height="130" 
            name="timeline" align="middle" allowScriptAccess="always" allowNetworking="all" allowFullScreen="false" wmode="transparent"
            type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />                    
          
    </object>
    <br>
        
        
    <?php
	$url = 'http://whos.amung.us/sitecount/'. get_option('whosamung_id') .'/';
	?>
  Actual Users Online: <?php echo file_get_contents($url); ?></h2>
    

        
  </div>
<?php
}
?>