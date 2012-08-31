<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
$user =& JFactory::getUser();
?>

<?php if ($user->guest): ?>
    <script>
        <?php $sid_div_id = rand(); ?>
      //<![CDATA[
        var e = document.createElement('script');
        e.type = 'text/javascript';
        e.id = 'socialid_login_script';
        if (document.location.protocol === 'https:') {
          e.src = "https://www.socialidnow.com/javascripts/marketing/login/widgets/login.js?<?php echo $params->get('key'); ?>";
        } else {
          e.src = "http://www.socialidnow.com/javascripts/marketing/login/widgets/login.js?<?php echo $params->get('key'); ?>";
        }
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(e, s);

        function loadSocialIDLoginWidget() { 
          SocialID.SocialLogin.load("<?php echo $params->get('key2'); ?>", 'socialid_login_container<?php echo $sid_div_id ?>');
        };
        if (window.addEventListener) {
          window.addEventListener('load', loadSocialIDLoginWidget, false);
        } else {
          window.attachEvent('onload', loadSocialIDLoginWidget);
        }
      //]]>
    </script>

    <div id='socialid_login_container<?php echo $sid_div_id ?>' style='display: inline-block; padding: 5px'></div>
    
<?php else: ?>
    <?php
    $db =& JFactory::getDBO();
    $db->setQuery("SELECT * FROM `#__sidn_login` AS l, `#__users` AS u WHERE u.email = l.email AND u.email = '$user->email' ORDER BY l.id DESC LIMIT 1");
    $sidn_user = $db->loadObject();
    if($sidn_user) {
        echo "<h2 class='user-name'>$sidn_user->name</h2>";
        echo "<div class='user-img'><img src='$sidn_user->picture_url'></div>";
    } else {
        echo "<h2 class='user-name'>$user->name</h2>";
    }
    ?>
    <form action="<?php echo JRoute::_('index.php', true) ?>" method="post" id="login-form">
        <div class="logout-button">
            <input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT') ?>" />
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.logout" />
            <?php echo JHtml::_('form.token') ?>
        </div>
    </form>
<?php endif ?>