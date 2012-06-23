<?php
/**
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package    Libs
 * @subpackage Logger
 * @author     Rodrigo Garcia <rodrigo.garcia@corp.terra.com.ar>
 * @access private
 */

/**
 *
 */
require_once(dirname(__FILE__).'/../logger.class.php');

define_syslog_variables();


/**
 * Logger_Syslog
 *
 * @package    Libs
 * {@inheritdoc}
 * @access private
 */
class Logger_Syslog extends Logger
{
	function open() {
    	openlog($this->name(), LOG_NDELAY | LOG_PID, LOG_USER);
	}

	function close() {
		closelog();
	}

	function log($msg, $level) {
		if ($this->has_level($level)) {
			syslog($this->level_to_syslog($level), sprintf("[ %s ] %s", $this->level_to_s($level), $msg));
			return true;
		} else {
			return false;
		}
	}

	function level_to_syslog($level) {
		$syslog = array(
			LOGGER_ERROR => LOG_ERR,
			LOGGER_WARNING => LOG_WARNING,
			LOGGER_DEBUG => LOG_DEBUG,
			LOGGER_NOTICE => LOG_NOTICE,
			LOGGER_INFO => LOG_INFO
		);

		return $syslog[$level];
	}
}

?>