<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMPP v3.4 query_sm command class and data
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @copyright  Portions of the documentation (c) Copyright 1999 SMPP Developers
 *             Forum.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision: 1.1 $
 * @since      Release 0.0.1dev2
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Command.php';

/**
 * query_sm class
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @copyright  Portions of the documentation (c) Copyright 1999 SMPP Developers
 *             Forum.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision: 1.1 $
 * @since      Release 0.0.1dev2
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Command_query_sm_resp extends Net_SMPP_Command
{

	/**
	 * Message ID of the message whose
	 * state is to be queried. This must be the
	 * SMSC assigned Message ID allocated
	 * to the original short message when
	 * submitted to the SMSC by the
     * submit_sm, data_sm or submit_multi
     * command, and returned in the
     * response PDU by the SMSC.
     * @var string
	 */
    var $message_id = null;

	/**
	* Date and time when the queried
	* message reached a final state. For
	* messages which have not yet
	* reached a final state this field will
	* contain a single NULL octet.
	*
	* @var string
	*/
    var $final_date = null;

	/**
	 * Specifies the status of the queried
	 * short message.
	 *
	 * @var int
	 */
    var $message_state = null;

	/**
	 *
	 * Where appropriate this holds a
	 * network error code defining the
	 * reason for failure of message
	 * delivery.
     *
     * @var int
	 */
    var $error_code = null;

    /**
     * Paramater definitions
     *
     * @var     array
     * @access  protected
     * @see     Net_SMPP_Command::$_defs
     */
    var $_defs = array(
        'message_id' => array(
            'type' => 'string',
            'max' => 65
        ),
        'final_date' => array(
            'type' => 'string',
            'max' => 17
        ),
        'message_state' => array(
            'type' => 'int',
            'size' => 1
        ),
        'error_code' => array(
            'type' => 'int',
            'size' => 1
        )
    );

    function fieldIsOptional($field)
    {
    	if (isset($this->_defs[$field])) return false;
    	return parent::fieldIsOptional($field);
    }

}