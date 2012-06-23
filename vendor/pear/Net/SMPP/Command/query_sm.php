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
class Net_SMPP_Command_query_sm extends Net_SMPP_Command
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
	* Type of Number of message
    * originator. This is used for
	* verification purposes, and must match
	* that supplied in the original request
	* PDU (e.g. submit_sm).
	* If not known, set to NULL.
	*
	* @var int
	*/
    var $source_addr_ton = null;

	/**
	 * Numbering Plan Identity of message
	 * originator. This is used for
	 * verification purposes, and must match
	 * that supplied in the original request
	 * PDU (e.g. submit_sm).
	 * If not known, set to NULL
	 *
	 * @var int
	 */
    var $source_addr_npi = null;

	/**
	 *
	 * Address of message originator.
     * This is used for verification purposes,
     * and must match that supplied in the
     * original request PDU (e.g.
     * submit_sm).
     * If not known, set to NULL.
     *
     * @var string
	 */

    var $source_addr = null;

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
        'source_addr_ton' => array(
            'type' => 'int',
            'size' => 1
        ),
        'source_addr_npi' => array(
            'type' => 'int',
            'size' => 1
        ),
        'source_addr' => array(
            'type' => 'string',
            'max' => 21
        )
    );
}
?>