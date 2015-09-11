<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Let project members/public subscribe to project activity notifications
 */
class plgProjectsWatch extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas( $alias = NULL )
	{
		$area = array(
			'name'    => 'watch',
			'title'   => 'Watch',
			'submenu' => NULL,
			'show'    => false
		);

		return $area;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param      object  $model           Project model
	 * @param      string  $action			Plugin task
	 * @param      string  $areas  			Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onProject ( $model, $action = '', $areas = NULL)
	{
		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}

		return $this->onProjectMember( $model );
	}

	/**
	 * Return data on a project team member
	 *
	 * @param      object  $project 	Current publication
	 * @return     array
	 */
	public function onProjectMember( $project )
	{
		// Only show for logged-in users
		if (User::isGuest())
		{
			return false;
		}

		// Only show to members
		if (!$project->access('member'))
		{
			return false;
		}

		$this->database = App::get('db');
		$this->project  = $project;

		// Item watch class
		$this->watch   = new \Hubzero\Item\Watch($this->database);
		$this->action  = strtolower(Request::getWord('action', ''));

		switch ($this->action)
		{
			case 'save':
				return $this->_save();
			break;

			case 'manage':
				return array('html' => $this->_manage());
			break;

			default:
				return $this->_status();
			break;
		}
	}

	/**
	 * Show subscription status
	 *
	 * @return  HTML
	 */
	private function _status()
	{
		// Instantiate a view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'watch',
				'name'    =>'index'
			)
		);

		$view->project = $this->project;

		// Is user watching item?
		$view->watch = $this->watch->loadRecord(
			$this->project->get('id'),
			'project',
			User::get('id')
		);

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Show manage subscription screen
	 *
	 * @return  HTML
	 */
	private function _manage()
	{
		// Instantiate a view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'watch',
				'name'    =>'manage'
			)
		);

		$view->project = $this->project;

		// Is user watching item?
		$view->watch = $this->watch->loadRecord(
			$this->project->get('id'),
			'project',
			User::get('id')
		);

		$params = $this->watch && $this->watch->id ? new \Hubzero\Config\Registry($this->watch->params) : NULL;

		$view->cats = array(
			'blog'         => $params ? $params->get('blog') : 0,
			'team'         => $params ? $params->get('team') : 0,
			'files'        => $params ? $params->get('files') : 0,
			'publications' => $params ? $params->get('publications') : 0,
			'todo'         => $params ? $params->get('todo') : 0,
			'notes'        => $params ? $params->get('notes') : 0
		);

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Subscribe
	 *
	 * @return  HTML
	 */
	private function _save()
	{
		// Check for request forgeries
		Request::checkToken();

		// Login required
		if (User::isGuest() || !$this->project->exists())
		{
			App::redirect(
				Route::url($this->publication->link())
			);
		}

		// Incoming
		$email      = User::get('email');
		$categories = Request::getVar('category', array());
		$frequency  = Request::getWord('frequency', 'immediate');

		// Save subscription
		$this->watch->loadRecord(
			$this->project->get('id'),
			'project',
			User::get('id'),
			$email
		);

		$this->watch->item_id    = $this->project->get('id');
		$this->watch->item_type  = 'project';
		$this->watch->created_by = User::get('id');
		$this->watch->state      = empty($categories) ? 2 : 1;

		$cats = array(
			'blog'         => 0,
			'quote'        => 0,
			'team'         => 0,
			'files'        => 0,
			'publications' => 0,
			'todo'         => 0,
			'notes'        => 0
		);

		$in = '';
		foreach ($cats as $param => $value)
		{
			if (isset($categories[$param]))
			{
				$value = intval($categories[$param]);
			}
			if ($param == 'quote' && isset($categories['blog']))
			{
				$value = 1;
			}
			$in .= $in ? "\n" : '';
			$in .= $param . '=' . $value;
		}

		$this->watch->params = $in;

		if ($this->watch->check())
		{
			$this->watch->store();
		}

		if ($this->watch->getError())
		{
			App::redirect(
				Route::url($this->project->link()),
				$this->watch->getError(),
				'error'
			);
		}
		else
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_WATCH_SUCCESS_SAVED'), 'success', 'projects');
			App::redirect(
				Route::url($this->project->link())
			);
		}
	}

	/**
	 * Notify subscribers of new activity
	 *
	 * @param      object  $project 	Project model
	 * @param      string  $area 		Project area of activity
	 * @param      array   $activities  Project activities (array of IDs)
	 * @param      integer $actor       Uid of team member posting the activity (to exclude from subscribers)
	 * @return     array
	 */
	public function onWatch( $project, $area = '', $activities = array(), $actor = 0)
	{
		$database = App::get('db');
		$this->project = $project;

		// Item watch class
		$watch   = new \Hubzero\Item\Watch($database);

		$filters = array(
			'item_type' => 'project',
			'item_id'   => $project->get('id'),
			'state'     => 1,
			'area'      => $area,
			'frequency' => 'immediate'
		);

		// Get subscribers
		$subscribers = $watch->getRecords($filters);

		// Get full activity info from IDs
		if ($activities)
		{
			$activities = $project->table('Activity')->getActivities(
				$project->get('id'),
				$filters = array('id' => $activities)
			);
		}

		if (empty($activities))
		{
			// Nothing to report
			return false;
		}

		$subject = Lang::txt('PLG_PROJECTS_WATCH_EMAIL_SUBJECT');

		// Do we have subscribers?
		if (count($subscribers) > 0)
		{
			foreach ($subscribers as $subscriber)
			{
				if ($actor && $subscriber->created_by == $actor)
				{
					// Skip
					continue;
				}
				// Send message
				if ($subscriber->email)
				{
					$this->_sendEmail($project, $subscriber, $activities, $subject);
				}
			}
		}

		return;
	}

	/**
	 * Handles the actual sending of emails
	 *
	 * @param  object  $project 	Project model
	 * @param  object  $subscriber  Subscriber obj
	 * @param  array   $activities  Project activities (array of IDs)
	 * @param  string  $subject     Email subject
	 * @return bool
	 **/
	private function _sendEmail($project, $subscriber, $activities = array(), $subject)
	{
		$eview = new \Hubzero\Mail\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'site',
			'name'   => 'emails',
			'layout' => 'watch_plain'
		));
		$eview->activities = $activities;
		$eview->subject    = $subject;
		$eview->project    = $project;

		$name = Config::get('sitename') . ' ' . Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBER');
		$email = $subscriber->email;

		// Get profile information
		if ($subscriber->created_by)
		{
			$user  = User::getInstance($subscriber->created_by);
			$name  = $user ? $user->get('name') : $name;
			$email = $user ? $user->get('email') : $email;
		}

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('watch_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		if (empty($email))
		{
			return false;
		}

		// Build message
		$message = new \Hubzero\Mail\Message();
		$message->setSubject($subject)
				->addFrom(Config::get('mailfrom'), Config::get('sitename'))
				->addTo($email, $name)
				->addHeader('X-Component', 'com_projects')
				->addHeader('X-Component-Object', 'projects_watch_email');

		$message->addPart($plain, 'text/plain');
		$message->addPart($html, 'text/html');

		// Send mail
		if (!$message->send())
		{
			$this->setError('Failed to mail %s', $email);
		}
	}
}