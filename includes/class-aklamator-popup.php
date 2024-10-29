<?php

class aklamatorPopWidget
{

    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function init()
    {

        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    public $aklamator_url;
    public $api_data;
    protected $application_id;

    public function __construct() {

        $this->aklamator_url = "https://aklamator.com/";
//        $this->aklamator_url = "http://127.0.0.1/aklamator/www/";
        $this->cdn_aklamator_url = "https://cdn.aklamator.com/";
//        $this->cdn_aklamator_url = "http://127.0.0.1/aklamator/www/";
        $this->application_id = get_option('aklamatorPopApplicationID');

        $this->hooks();

        if (isset($_GET['page']) && $_GET['page'] == 'aklamator-popup' ) {

            if ($this->application_id != '') {

                $this->api_data = $this->addNewWebsiteApi();

                if (isset($this->api_data->flag) && $this->api_data->flag == true) {
                    update_option('aklamatorPopupWidgets', $this->api_data);
                }
                
                if (isset($this->api_data->flag) && $this->api_data->flag == true) {
                    $popUpWidgets = array_values(array_filter($this->api_data->data, function ($a) {
                        return $a->title == 'Initial PopUp widget created';
                    }));
                    $otherWidgets = array_values(array_filter($this->api_data->classic, function ($a) {
                        return $a->title != 'Initial PopUp widget created';
                    }));

                    // PopUp dropdown section
                    if (get_option('aklamatorPopUpWidgetID') !== 'none') {

                        if (get_option('aklamatorPopUpWidgetID') == '') {
                            if ($popUpWidgets[0]) {
                                update_option('aklamatorPopUpWidgetID', $popUpWidgets[0]->uniq_name);
                            }

                        }
                    }

                    //Single post dropdown section
                    if (get_option('aklamatorPopSingleWidgetID') !== 'none') {

                        if (get_option('aklamatorPopSingleWidgetID') == '') {
                            if ($otherWidgets[0]) {
                                update_option('aklamatorPopSingleWidgetID', $otherWidgets[0]->uniq_name);
                            }

                        }

                    }
                    // Page dropdown section
                    if (get_option('aklamatorPopPageWidgetID') !== 'none') {

                        if (get_option('aklamatorPopPageWidgetID') == '') {
                            if ($otherWidgets[0]) {
                                update_option('aklamatorPopPageWidgetID', $otherWidgets[0]->uniq_name);
                            }

                        }

                    }
                }
            }

        }

    }

    private function hooks(){

        add_filter('plugin_row_meta', array($this, 'aklamatorPop_plugin_meta_links'), 10, 2);
        add_filter("plugin_action_links_".POP_AKLA_PLUGIN_NAME, array($this, 'aklamatorPop_plugin_settings_link'));

        add_action("admin_menu", array($this,"adminMenuPop"));
        add_action('admin_init', array($this,"setOptionsPop"));
        add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_style_script'));
        add_action( 'after_setup_theme', array($this,'vw_setup_vw_widgets_init_aklamatorPop'));
        if ($this->application_id != "")
            add_filter('the_content', array($this,'bottom_of_every_post_pop'));

        // Add popUp only if user selected "Enable popUp in settings"
        if(get_option("aklamatorPopUpActive") == "on")
            add_action('wp_footer', array($this, 'render_akla_popup_script'));

        /*
        * Adds featured images from posts to your site's RSS feed output,
        */
        if(get_option('aklamatorPopFeatured2Feed')){
            add_filter('the_excerpt_rss', array($this, 'akla_featured_images_in_rss'), 1000, 1);
            add_filter('the_content_feed', array($this, 'akla_featured_images_in_rss'), 1000, 1);
        }

    }

    function load_custom_wp_admin_style_script($hook) {

        if ( 'toplevel_page_aklamator-popup' != $hook ) {
            return;
        }
        // Load necessary css files
        wp_enqueue_style('custom-wp-admin', POP_AKLA_PLUGIN_URL . 'assets/css/admin-style.css', false, '1.0.0' );
        wp_enqueue_style('dataTables-plugin', POP_AKLA_PLUGIN_URL . 'assets/dataTables/jquery.dataTables.min.css', false, '1.10.5', false );

        // Load script files
        wp_enqueue_script('dataTables_plugin', POP_AKLA_PLUGIN_URL . 'assets/dataTables/jquery.dataTables.min.js', array('jquery'), '1.10.5', true );
        wp_register_script('my_custom_akla_script', POP_AKLA_PLUGIN_URL . 'assets/js/main.js', array('jquery'), '1.0', true);

        $data = array(
            'site_url' => $this->aklamator_url
        );
        wp_localize_script('my_custom_akla_script', 'akla_vars', $data);
        wp_enqueue_script('my_custom_akla_script');

    }

    function setOptionsPop()
    {
        register_setting('aklamatorPop-options', 'aklamatorPopApplicationID');
        register_setting('aklamatorPop-options', 'aklamatorPopPoweredBy');
        register_setting('aklamatorPop-options', 'aklamatorPopSingleWidgetID');
        register_setting('aklamatorPop-options', 'aklamatorPopPageWidgetID');
        register_setting('aklamatorPop-options', 'aklamatorPopSingleWidgetTitle');
        register_setting('aklamatorPop-options', 'aklamatorPopFeatured2Feed');
        register_setting('aklamatorPop-options', 'aklamatorPopUpActive');
        register_setting('aklamatorPop-options', 'aklamatorPopUpWidgetID');
        register_setting('aklamatorPop-options', 'aklamatorPopUpTitle');
        register_setting('aklamatorPop-options', 'aklamatorPopUpSubTitle');

    }

    public function adminMenuPop() {
        add_menu_page('aklamatorPop Digital PR', 'Aklamator PopUP', 'manage_options', 'aklamator-popup', array($this,'createAdminPagePop'), POP_AKLA_PLUGIN_URL. 'images/aklamator-icon.png');
    }

    
    public function getSignupUrl()
    {
        $user_info =  wp_get_current_user();

        return $this->aklamator_url . 'login/application_id?utm_source=wordpress&utm_medium=wppopup&e=' . urlencode(get_option('admin_email')) .
        '&pub=' .  preg_replace('/^www\./','',$_SERVER['SERVER_NAME']).
        '&un=' . urlencode($user_info->user_login). '&fn=' . urlencode($user_info->user_firstname) . '&ln=' . urlencode($user_info->user_lastname) .
        '&pl=popup&return_uri=' . admin_url("admin.php?page=aklamator-popup");

    }

    private function addNewWebsiteApi()
    {
        

        $service     = $this->aklamator_url . "wp-authenticate/popup";
        $p['ip']     = $_SERVER['REMOTE_ADDR'];
        $p['domain'] = site_url();
        $p['source'] = "wordpress";
        $p['AklamatorApplicationID'] = $this->application_id;
        $p['AklamatorPopTittle'] = get_option('aklamatorPopUpTitle');
        $p['AklamatorPopSubTitle'] = get_option('aklamatorPopUpSubTitle');



        $data = wp_remote_post( $service, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => $p,
                'cookies' => array()
            )
        );

        $ret_info = new stdClass();
        if(is_wp_error($data))
        {
            $this->curlfailovao=1;
        }
        else
        {
            $this->curlfailovao=0;
            $ret_info = json_decode($data['body']);
        }

        return $ret_info;

    }

    public function createAdminPagePop() {

        include_once POP_AKLA_PLUGIN_DIR. 'views/admin-page.php';
    }

    public function bottom_of_every_post_pop($content){

        /*  we want to change `the_content` of posts, not pages
            and the text file must exist for this to work */

        if (is_single()){
            $widget_id = get_option('aklamatorPopSingleWidgetID');
        }elseif (is_page()) {
            $widget_id = get_option('aklamatorPopPageWidgetID');
        }else{

            /*  if `the_content` belongs to a page or our file is missing
                the result of this filter is no change to `the_content` */

            return $content;
        }

        $return_content = $content;

        if(strlen($widget_id) >=7){
            $title = "";
            if(get_option('aklamatorPopSingleWidgetTitle') !== ''){
                $title .= "<h2>". get_option('aklamatorPopSingleWidgetTitle'). "</h2>";
            }
            /*  append the text file contents to the end of `the_content` */

            $return_content.=  $title. $this->show_widget($widget_id);

        }

        return $return_content;

    }

    public function show_widget($widget_id){

        $code  = '<!-- Start Aklamator Widget -->';
        $code .= '<div id="akla'.$widget_id.'"></div>';
        $code .= '<script async>(function(d, s, id) ';
        $code .= '{ var js, fjs = d.getElementsByTagName(s)[0];';
        $code .= 'if (d.getElementById(id)) return;';
        $code .= 'js = d.createElement(s); js.id = id;';
        $code .= 'js.src = "'.$this->aklamator_url.'widget/'.$widget_id.'";';
        $code .= 'fjs.parentNode.insertBefore(js, fjs);';
        $code .= '}(document, \'script\', \'aklamator-'.$widget_id.'\'))</script>';
        $code .= '<!-- end -->';
        return $code;

    }


    function render_akla_popup_script() {


//        $code  = '<script type="text/javascript" src="'.$this->aklamator_url.'js/popup/popupdr.js" ></script>';
//        $code .= '<script type="text/javascript" src="'.$this->aklamator_url.'widget2/type/'.get_option('aklamatorPopUpWidgetID').'" ></script>';
//        $code .= '<script type="text/javascript">';
//        $code .= 'var aklaTest = new AklaPopup({';
//        $code .= 'title: \''.get_option('aklamatorPopUpTitle').'\',';
//        $code .= 'subtitle: \''.get_option('aklamatorPopUpSubTitle').'\',';
//        $code .= 'widgetId: \''.get_option('aklamatorPopUpWidgetID').'\',';
//        $code .= 'targetdevice: vtargetdevice,';
//        $code .= 'trigeraction: vtrigeraction,';
//        $code .= 'trafficsource: vtrafficsource,';
//        $code .= 'visitortype: vvisitortype,';
//        $code .= 'snooze_days: vsnoozedays,';
//        $code .= 'urlinclude: vurlinclude,';
//        $code .= 'urlexclude: vurlexclude,';
//        $code .= '});';
//        $code .= '</script>';

        $code  = '<script async>';
        $code .= 'var r = false;';
        $code .= 'var s = document.createElement(\'script\');';
        $code .= 's.type = \'text/javascript\';';
        $code .= 's.src = \''.$this->cdn_aklamator_url.'js/popup/popupdr.js\';';
        $code .= 's.onload = s.onreadystatechange = function() {';
        $code .= 'if (!r) {r = true;';
        $code .= 'var r1 = false;';
        $code .= 'var s1 = document.createElement("script");';
        $code .= 's1.type = "text/javascript";';
        $code .= 's1.src = "'.$this->cdn_aklamator_url.'js/js_pp/'.get_option('aklamatorPopUpWidgetID').'.js";';
        $code .= 's1.onload = s1.onreadystatechange = function() {';
        $code .= 'if (!r1) {r1 = true;';
        $code .= 'new AklaPopup({';
        $code .= 'title: \''.get_option('aklamatorPopUpTitle').'\',';
        $code .= 'subtitle: \''.get_option('aklamatorPopUpSubTitle').'\',';
        $code .= 'widgetId: \''.get_option('aklamatorPopUpWidgetID').'\',';
        $code .= 'targetdevice: vtargetdevice,';
        $code .= 'trigeraction: vtrigeraction,';
        $code .= 'trafficsource: vtrafficsource,';
        $code .= 'visitortype: vvisitortype,';
        $code .= 'snooze_days: vsnoozedays,';
        $code .= 'urlinclude: vurlinclude,';
        $code .= 'urlexclude: vurlexclude';
        $code .= '});}};';
        $code .= 'var t1 = document.getElementsByTagName(\'script\')[0];';
        $code .= 't1.parentNode.insertBefore(s1, t1);}};';
        $code .= 'var t = document.getElementsByTagName(\'script\')[0];';
        $code .= 't.parentNode.insertBefore(s, t);';
        $code .= '</script>';

        echo $code;

    }

    /*
    * Adds featured images from posts to your site's RSS feed output,
    */

    function akla_featured_images_in_rss($content){
        global $post;
        if (has_post_thumbnail($post->ID)) {
            $featured_images_in_rss_size = 'thumbnail';
            $featured_images_in_rss_css_code = 'display: block; margin-bottom: 5px; clear:both;';
            $content = get_the_post_thumbnail($post->ID, $featured_images_in_rss_size, array('style' => $featured_images_in_rss_css_code)) . $content;
        }
        return $content;
    }

    /*
    * Add rate and review link in plugin section
    */
    function aklamatorPop_plugin_meta_links($links, $file) {
        $plugin = POP_AKLA_PLUGIN_NAME;
        // create link
        if ($file == $plugin) {
            return array_merge(
                $links,
                array('<a href="https://wordpress.org/support/plugin/aklamator-popup/reviews" target=_blank>Please rate and review</a>')
            );
        }
        return $links;
    }

    /*
     * Add setting link on plugin page
    */

    function aklamatorPop_plugin_settings_link($links) {
        $settings_link = '<a href="admin.php?page=aklamator-popup">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    //init widget
    function vw_setup_vw_widgets_init_aklamatorPop() {
        add_action( 'widgets_init',  array($this, 'vw_widgets_init_aklamatorPop'));
    }
    //register widget
    function vw_widgets_init_aklamatorPop() {
        register_widget('Wp_widget_aklamatorPop');
    }

    // Set options fields
    function set_up_options_popup() {
        add_option('aklamatorPopApplicationID', '');
        add_option('aklamatorPopPoweredBy', '');
        add_option('aklamatorPopSingleWidgetID', '');
        add_option('aklamatorPopPageWidgetID', '');
        add_option('aklamatorPopSingleWidgetTitle', '');
        add_option('aklamatorPopFeatured2Feed', 'on');
        add_option('aklamatorPopUpWidgetID', '');
        add_option('aklamatorPopUpTitle', 'Default PopUp Title');
        add_option('aklamatorPopUpSubTitle', 'Default PopUp Subtitle');
        add_option('aklamatorPopUpActive', 'on');
        add_option('aklamatorPopupWidgets', '');
    }

    function aklamatorPop_uninstall() {
        delete_option('aklamatorPopApplicationID');
        delete_option('aklamatorPopPoweredBy');
        delete_option('aklamatorPopSingleWidgetID');
        delete_option('aklamatorPopPageWidgetID');
        delete_option('aklamatorPopSingleWidgetTitle');
        delete_option('aklamatorPopFeatured2Feed');
        delete_option('aklamatorPopUpWidgetID');
        delete_option('aklamatorPopUpActive');
        delete_option('aklamatorPopUpTitle');
        delete_option('aklamatorPopUpSubTitle');
        delete_option('aklamatorPopupWidgets');
    }

}