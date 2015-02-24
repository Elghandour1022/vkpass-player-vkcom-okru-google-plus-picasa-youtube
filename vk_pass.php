<?php
/*
Plugin Name: VKPass Özel Player (Ücretsiz)
Plugin URI: http://vkpass.com
Description: VKPass player ile vk.com, ok.ru, google plus & picasa, vimeo, dailymation, youtube, izlesene, mynettv, myvideo.az vb sitelerdeki videoları size özel playerda oynatabilirsiniz. Ayrıntılı bilgi: http://vkpass.com/
Version: 1.2
Author: Vidrame
Author URI: http://vkpass.com
License: GPL2
*/

define('VK_PASS_FILE', __FILE__);
define('VK_PASS_PATH', plugin_dir_path(__FILE__));

define ("PLUGIN_NAME", "VKPass Özel Player (Ücretsiz)");
define ("PLUGIN_NICK", "wp_vkpass");
define ("PLUGIN_VERSION", "1.2");
define ("PLUGIN_DB_VERSION", "1.2");
define ("PLUGIN_DIR_NAME", trim(basename(dirname(__FILE__), '/' )));
define ("PLUGIN_URL", plugin_dir_url(__FILE__)); // already has trailing slash
define ("PLUGIN_PATH", plugin_dir_path(__FILE__)); // already has trailing slash
define ("ACCESS_TO_USE_THIS_PLUGIN", 0) ;
define ("ACCESS_TO_MANAGE_THIS_PLUGIN", 10);

function vkp_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=vkp_list_options">VKPass Ayarlar</a>';
    array_push($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'vkp_settings_link');

class vk_pass {

    protected $option_name = 'vkp_OPTION';
	
    protected $data = array(
        'vkp_TOKEN' => '',
        'vkp_sifreleme' => '',
        'vkp_MAIL' => '',
        'vkp_PASS' => '',
        'vkp_player_width' => '100%',
        'vkp_player_height' => '400px'
    );

    public function __construct() {

        // Admin sub-menu
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'add_page'));

        // Listen for the activate event
        register_activation_hook(VK_PASS_FILE, array($this, 'activate'));

        // Deactivation plugin
        register_deactivation_hook(VK_PASS_FILE, array($this, 'deactivate'));
    }

    public function activate() {
        update_option($this->option_name, $this->data);
    }

    public function deactivate() {
        delete_option($this->option_name);
    }
	
    public function init() {

        // When a URL like /todo is requested from the,
        // blog (the URL is customizable) we will directly
        // include the index.php file of the application and exit
        $result = get_option('vkp_OPTION');
        
    }

    // White list our options using the Settings API
    public function admin_init() {
        register_setting('vkp_list_options', $this->option_name, array($this, 'validate'));
    }

    // Add entry in the settings menu
    public function add_page() {
        add_options_page('VKPass  Options', 'VKPass Options', 'manage_options', 'vkp_list_options', array($this, 'options_do_page'));
    }

    // Print the menu page itself
    public function options_do_page() {
        $options = get_option($this->option_name);
        ?>
        <link rel="stylesheet" id="wp-fastest-cache-css" href="<?php echo plugin_dir_url(__FILE__); ?>style.css" type="text/css" media="all">
        
        <div class="wrap">
	        <form method="post" action="options.php">
	            <h2>VKPass Options</h2><hr><br>
	            <div class="tabGroup">
		            <input checked="checked" type="radio" id="wpfc-options" name="tabGroup1">
		            <label for="wpfc-options">Genel Ayarlar</label>
		            <input type="radio" id="wpfc-deleteCache" name="tabGroup1">
		            <label for="wpfc-deleteCache">Link Şifreleme</label>
					<div class="tab1">
						<?php settings_fields('vkp_list_options'); ?>
						
						<div class="vkpass_info">VKPass Playeri kendinize özel düzenlemek için, sitemize üye olarak panelinizdeki token kodunuzu buraya giriniz. Tüm player özelliştirmelerini sitemizdeki panelinizden yapabilirsiniz.</div>
						
						<div class="questionCon">
							<div class="question">Token Kodu</div>
							<div class="inputCon"><input type="text" name="<?php echo $this->option_name?>[vkp_TOKEN]" value="<?php echo $options['vkp_TOKEN']; ?>" /></div>
						</div>
						
						
						<div class="questionCon qsubmit">
			                <div class="submit">
			                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			                </div>
						</div>
					</div>
					<div class="tab2">
						<div class="vkpass_info">Kaynaklarınızın başkası tarafından görülmesini(çalınmasını) istemiyorsanız bu alanı aktif yapabilirsiniz. Gireceğiniz bilgiler vkpass.com'a kayıt olduğunuz mail ve şifrenizdir.</div>
						
						<div class="questionCon">
							<div class="question">Kaynak Şifreleme</div>
							<div class="inputCon"><input class="hasher_check" type="checkbox" name="<?php echo $this->option_name?>[vkp_sifreleme]"<?php if($options['vkp_sifreleme'] == "on") {echo ' checked="checked"';} ?> ></div>
						</div>
						<div class="questionCon">
							<div class="question">VKPass Mail</div>
							<div class="inputCon"><input type="text" name="<?php echo $this->option_name?>[vkp_MAIL]" value="<?php echo $options['vkp_MAIL']; ?>" /></div>
						<div class="questionCon">
						</div>
							<div class="question">VKPass Şifre</div>
							<div class="inputCon"><input type="password" name="<?php echo $this->option_name?>[vkp_PASS]" value="<?php echo $options['vkp_PASS']; ?>" /></div>
						</div>
						
						
						<div class="questionCon qsubmit">
			                <div class="submit">
			                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			                </div>
						</div>
					</div>
				</div>
				<div class="omni_admin_sidebar">
					<div class="omni_admin_sidebar_section">
					<h3>Kişiselleştirme</h3>
					<ul>
				    	<li>
				    		<label><a target="_blank" href="http://vkpass.com/panel">Buradan</a> panelinizi ziyaret edin.</a>
				    	</li>
				    	<li>
				    		<label>Panelde size verilen Token kodunu Genel Ayarlardan buraya kaydedin.</label>
				    	</li>
				    	<li>
				    		<label>Bundan sonra tüm kişiselleştirme işlemlerini VKPass Panelinizden yapabiliriniz</label>
				    	</li>
				  	</ul>
				  </div>
				</div>
			</form>
        </div>
        <script>
	        var hasher_check = document.getElementsByClassName("hasher_check")[0];

			hasher_check.onclick = function() {
			    var hasher_infos = document.getElementsByClassName("hasher_infos");
			    if(hasher_check.checked == true)
			       for(var i = 0; i < hasher_infos.length; i++) hasher_infos[i].style.display = "table-row";
			    else for(var i = 0; i < hasher_infos.length; i++) hasher_infos[i].style.display = "none";
			}
			
			hasher_check.onclick();
        </script>
        <?php
    }

    public function validate($input) {

        $valid = array();
        $valid['vkp_TOKEN'] = sanitize_text_field($input['vkp_TOKEN']);
        $valid['vkp_MAIL'] = sanitize_text_field($input['vkp_MAIL']);
        $valid['vkp_PASS'] = sanitize_text_field($input['vkp_PASS']);
        $valid['vkp_sifreleme'] = sanitize_text_field($input['vkp_sifreleme']);
        $valid['vkp_player_width'] = sanitize_text_field($input['vkp_player_width']);
        $valid['vkp_player_height'] = sanitize_text_field($input['vkp_player_height']);
		
        return $valid;
    }
}


$result = get_option('vkp_OPTION');

if($result['vkp_sifreleme'] == "on") {

	$domains = array("vk.com", "ok.ru", "odnoklassniki.ru", "picasaweb.google.com", "plus.google.com", "myvideo.az");

    add_filter('the_content','add_postdata_to_content');
    function add_postdata_to_content($text) {
	    global $post;
        global $domains;
        
        $result = get_option('vkp_OPTION');
        $result_vkp_TOKEN = $result['vkp_TOKEN'] == "" ? 'cve0ejrnbrpq' : $result['vkp_TOKEN'];
        $TOKEN = $result_vkp_TOKEN;
        $MAIL = $result['vkp_MAIL'];
        $PASS = $result['vkp_PASS']; 
        $CONTENT = get_the_content($post->ID, ''); 
        
		$SRCS = explode("src='", $CONTENT);
		array_shift($SRCS);
		
		if(sizeof($SRCS) > 0) {
			foreach($SRCS as $SRC) {
				$SRC = explode("'", $SRC);
				$SRC = $SRC[0];

				if(strposa($SRC, $domains)) {
					$NEW_SRC = urlencode($SRC);
			        $NEW_SRC = file_get_contents("http://{$TOKEN}.vkpass.com/hashlink?mail={$MAIL}&pass={$PASS}&link={$NEW_SRC}");
					$CONTENT = str_replace($SRC, $NEW_SRC, $CONTENT);
					$CONTENT = str_replace("src=", "allowfullscreen src=", $CONTENT);
				}
			}
		}
		
		$SRCS = explode('src="', $CONTENT);
		array_shift($SRCS);
		if(sizeof($SRCS) > 0) {
			foreach($SRCS as $SRC) {
				$SRC = explode('"', $SRC);
				$SRC = $SRC[0];

				if(strposa($SRC, $domains)) {
					$NEW_SRC = urlencode($SRC);
			        $NEW_SRC = file_get_contents("http://{$TOKEN}.vkpass.com/hashlink?mail={$MAIL}&pass={$PASS}&link={$NEW_SRC}");
					$CONTENT = str_replace($SRC, $NEW_SRC, $CONTENT);
					$CONTENT = str_replace("src=", "allowfullscreen src=", $CONTENT);
				}
			}
		}
        
        return $CONTENT;
    }

}

function vkp_head () {
    $result = get_option('vkp_OPTION');
    $result_vkp_TOKEN = $result['vkp_TOKEN'] == "" ? 'cve0ejrnbrpq' : $result['vkp_TOKEN'];
    echo '<script>
  !function(d, h, s, id) { 
    var js, fjs = d.getElementsByTagName(h)[0];
    if(!d.getElementById(id)) {
      js = d.createElement(s);
      js.id = id;
      js.src = "http://vkpass.com/configure/'.$result_vkp_TOKEN.'.js";
      fjs.appendChild(js,fjs);
    }
  } (document, "head", "script", "vkpass-configure");
</script>';
}

add_action('wp_head', 'vkp_head');

function strposa($haystack, $needles=array()) {
    $chr = array();
    foreach($needles as $needle)
    	if (stripos($haystack, $needle) !== false)
    		return true;
    return false;
}

new vk_pass();
?>
