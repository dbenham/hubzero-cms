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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Auth;

/**
 * Authentication statuses
 */
class Status
{
	/**
	 * This is the status code returned when the authentication is success (permit login)
	 *
	 * @const  STATUS_SUCCESS  Successful response
	 */
	const SUCCESS = 1;

	/**
	 * Status to indicate cancellation of authentication (unused)
	 *
	 * @const  STATUS_CANCEL  Cancelled request (unused)
	 */
	const CANCEL = 2;

	/**
	 * This is the status code returned when the authentication failed (prevent login if no success)
	 *
	 * @const  STATUS_FAILURE  Failed request
	 */
	const FAILURE = 4;

	/**
	 * This is the status code returned when the account has expired (prevent login)
	 *
	 * @const  STATUS_EXPIRED  An expired account (will prevent login)
	 */
	const EXPIRED = 8;

	/**
	 * This is the status code returned when the account has been denied (prevent login)
	 *
	 * @const  STATUS_DENIED  Denied request (will prevent login)
	 */
	const DENIED = 16;

	/**
	 * This is the status code returned when the account doesn't exist (not an error)
	 *
	 * @const  STATUS_UNKNOWN  Unknown account (won't permit or prevent login)
	 */
	const UNKNOWN = 32;
}