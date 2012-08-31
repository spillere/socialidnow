<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SidnloginViewSidnlogin extends JView
{
    /**
     * Display the view
     *
     * @param string $tpl Template
     *
     * @return void
     * @access public
     * @since  2.0
     */
	public function display($tpl = null)
	{
      		JToolBarHelper::preferences('com_sidnlogin', '500', '570', JText::_('Parameters'));
      		parent::display($tpl);
	}
}