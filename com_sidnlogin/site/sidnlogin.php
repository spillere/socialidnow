<?php
defined('_JEXEC') or die;
jimport('joomla.user.authentication');
jimport('joomla.application.component.helper');

$app =& JFactory::getApplication();
$db =& JFactory::getDBO();
$session =& JFactory::getSession();

$db->setQuery("SELECT params FROM `#__extensions` WHERE name='sidnlogin'");
$params = json_decode($db->loadResult());
$api_secret = $params->params->api_secret;
$sidn_debug = $params->params->sidn_debug;

if(JRequest::getVar('token', '') != '') {
    $session->set('sidn_token', JRequest::getVar('token', ''));
}
$token = $session->get('sidn_token', false);
$new_user = false;
if($token) {
    $login_info = json_decode(file_get_contents("https://api.socialidnow.com/v1/marketing/login_info?api_secret=$api_secret&token=$token"));
    // var_dump($login_info);die();
    if($sidn_debug) var_dump($login_info);

    $ids = array('facebook' => 'facebook_id', 'twitter' => 'twitter_id', 'linkedin' => 'linkedin_id');
    $credential = $login_info->credential;
    $temp_id = $ids[$credential->type];

    $email = $db->quote($login_info->email);
    if($login_info->email == NULL && $credential->type == 'twitter') {
        $email = $db->quote($credential->screen_name.'@twitter.com');
    }
    $screen_name = $db->quote($credential->type == 'linkedin' ? $credential->public_profile_url : $credential->screen_name);
    $type = $db->quote($credential->type);
    $social_network_id = $db->quote($credential->$temp_id);
    $username = $db->quote('sidn_'.$credential->$temp_id);
    $sidn_id = $db->quote($login_info->id);
    $picture_url = $db->quote($login_info->picture_url);
    $name = $db->quote($login_info->name);

    // get the user from the database
    $db->setQuery("SELECT l.*, u.* FROM `#__sidn_login` AS l, `#__users` AS u WHERE l.sidn_id = $sidn_id AND l.type = $type AND l.email = u.email");
    $user = $db->loadObject();

    // register the user in the database if it doesnt exist
    if(!$user) {
        $new_user = true;
        $query = "INSERT INTO `#__sidn_login` (name, screen_name, type, social_network_id, email, sidn_id, picture_url) VALUES ($name, $screen_name, $type, $social_network_id, $email, $sidn_id, $picture_url)";
        if($sidn_debug) var_dump($query);
        $db->setQuery($query);
        $db->query();
        
        // user_id
        $db->setQuery("SELECT id FROM `#__users` ORDER BY id DESC LIMIT 1");
        $curr = $db->loadObject();
        $user_id = intval($curr->id) + 5;
        if($sidn_debug) var_dump("MAX USER ID: $user_id");
        
        // register Joomla! user
        $query = "INSERT INTO `#__users` (`id`, `name`, `username`, `email`, `password`, `registerDate`, `lastvisitDate`, `params`) VALUES ($user_id, $name, $username, $email, MD5('IOJWEORIEJoijsfuwirhIEUR'), NOW(), NOW(), '{}')";
        if($sidn_debug) var_dump($query);
        $db->setQuery($query); 
        $db->query();
        
        $query = "INSERT INTO `#__user_usergroup_map` (`user_id`, `group_id`) VALUES ($user_id, 13)";
        if($sidn_debug) var_dump($query);
        $db->setQuery($query); 
        $db->query();
        
        $db->setQuery("SELECT l.*, u.* FROM `#__sidn_login` AS l, `#__users` AS u WHERE l.sidn_id = $sidn_id AND l.type = $type AND l.email = u.email");
        $user = $db->loadObject();
    }
    
    // login 
    if($sidn_debug) var_dump($user);
    $user_id = $user->id;
    if ($user_id) {
        $user =& JUser::getInstance((int)$user_id);
        $session->set('user', $user);
        $table =& JTable::getInstance('session');
        $table->load($session->getId());
        $table->guest = '0';
        $table->username = $user->get('username');
        $table->userid = intval($user->get('id'));
        $table->usertype = $user->get('usertype');
        $table->gid  = $user->get('gid');
        $table->update();
        $user->setLastVisit();
        $user =& JFactory::getUser();
        $app->redirect(JURI::base());
    }
}

$app->redirect(JURI::base());