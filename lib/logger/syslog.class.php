<?php
/**
 *
 */
require_once(dirname(__FILE__).'/../logger.class.php');

define_syslog_variables();


/**
 * Logger_Syslog
 *
 * {@inheritdoc}
 * @access private
 */
class Logger_Syslog extends Logger
{
	public function open() {
    	openlog(sprintf("%s[%s]", $this->name(), getmypid()), LOG_NDELAY, LOG_USER);
	}

	public function close() {
		closelog();
	}

	public function log($msg, $level) {
		if ($this->has_level($level)) {
			syslog($this->level_to_syslog($level), sprintf("[ %s ] %s", $this->level_to_s($level), $msg));
			return true;
		} else {
			return false;
		}
	}

	private function level_to_syslog($level) {
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