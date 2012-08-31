<?php
/**
 * Entry Point file for the Sidnlogin Component
 *
 * PHP versions 5
 *
 * @category  Entry_Point
 * @package   Sidnlogin
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Require the base controller
 */
require_once JPATH_COMPONENT . DS . 'controller.php';

// Create the controller
$controller	= new SidnloginController();

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();

