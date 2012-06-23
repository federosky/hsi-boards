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
 * @subpackage MVC
 * @author     Rodrigo Garcia <rodrigo.garcia@corp.terra.com.ar>
 * @access private
 *
 */


/**
 *
 */
require_once(dirname(__FILE__).'/object.class.php');

/**
 *
 */
function is_hash($var)
{
   return is_array($var) && sizeof($var) > 0 && array_keys($var)!==range(0,sizeof($var)-1);
}

/**
 * Template
 *
 * @package    Libs
 * @access private
 */
class Template extends Object
{
	var $FileRoot = '';
	var $Template = '';
	var $FileName = '';

	function Template($strTemplate)
	{
		$this->Template = $strTemplate;
	}

	function Open($FileName)
	{
		$this->FileName = $FileName;

 		$fd = fopen($this->FileRoot.$this->FileName, "rb");
		if(!$fd)
		{
			$this->Template="<!--[Template] Can't read '$FileName'!-->\n";
			return false;
		}
		$this->Template = '';
		while (!feof($fd)) {
			     $this->Template .= fread($fd, 8192);
		}
		fclose($fd);

		$regs = array();
		while(eregi("<!--#include virtual=\"([A-Z,a-z,0-9,\/,\.,\_,-]+)\" -->",$this->Template,$regs)){
			$this->Template = str_replace("<!--#include virtual=\"".$regs[1]."\" -->", $this->getSSI($regs[1]), $this->Template);
		}

		return true;
	}

	function getSSI($path)
	{
		if( ereg('^/',$path )) {
			$path = $_SERVER['DOCUMENT_ROOT'].$path;
		} else {
			$path = $this->FileRoot.'/'.$path;
		}

		$tpl = new Template();
		$tpl->Open($path);
		return $tpl->Template;
	}

	function setFileRoot($FileRoot)
	{
		$this->FileRoot = $FileRoot;
	}


	function setTemplate($strTemplate)
	{
		$this->Template = $strTemplate;
	}


	function addTemplate($objTemplate)
	{
		$this->Template .= $objTemplate->Template;
	}


	function setVar($VarName, $VarValue)
	{
		if (is_scalar($VarValue) || is_null($VarValue)) {
			$this->Template = str_replace($VarName,
			$VarValue,
			$this->Template);
		} else {
			die("<h1>error por adaptacion a php5 $VarName, $VarValue (".get_class($VarName). " ".get_class($VarValue).")</h1>");
		}
	}

	function setVars(&$vars, $prepend)
	{
		if (is_hash($vars)) {
			foreach($vars as $key => $value)
			{
				if (is_scalar($value) || is_null($value))
					$this->setVar('{'.$prepend.'.'.$key.'}', $value);
				else
					$this->setVars($value, $prepend.'.'.$key);
			}
		} elseif (is_array($vars)) {

			$total = sizeof($vars);
			while ($strblock = $this->getBlock("$prepend.Row", "<!-- BLOCK $prepend.Row -->"))
			{
				$rstblock = '';
				$idx = 0;

				foreach($vars as $key => $value)
				{
					$tpl = new Template($strblock);

					$tpl->setVar('{'.$prepend.'.__idx}', $idx);
					$tpl->setVar('{'.$prepend.'.__parity}', ($idx % 2 == 0) ? 0 : 1);
					if ($idx == 0)
						$tpl->setVar('{'.$prepend.'.__state}', "FIRST");
					elseif ($idx == $total-1)
						$tpl->setVar('{'.$prepend.'.__state}', "LAST");
					else
						$tpl->setVar('{'.$prepend.'.__state}', "BODY");

					if (is_scalar($value) || is_null($value))
						$tpl->setVar('{'.$prepend.'}', $value);
					else
						$tpl->setVars($value, $prepend);

					$rstblock .= $tpl->Template;
					$idx++;
				}
				$this->setVar("<!-- BLOCK $prepend.Row -->", $rstblock);
			}
			$this->setVar('{'.$prepend.'.__total}', $total);
		} elseif (is_scalar($vars) || is_null($vars)) {
			$this->setVar('{'.$prepend.'}', $vars);
		}
		return 1;
	}

	function getBlock($BlockName, $VarName)
	{
		$BeginPos = 0;
		$EndPos   = 0;
		$BeginStr = '';
		$EndStr   = '';
		$BeginLen = 0;
		$EndLen   = 0;
		$strBlock = '';

		$BeginStr = "<!-- BEGIN $BlockName -->";
		$BeginLen = strlen($BeginStr);
		$BeginPos = strpos($this->Template, $BeginStr, 0);
		if (!($BeginPos===false)) {
			$EndStr = "<!-- END $BlockName -->";
			$EndLen = strlen($EndStr);
			$EndPos = strpos($this->Template, $EndStr, $BeginPos);
			if (!($EndPos===false)) {
				$strBlock = substr($this->Template, $BeginPos, $EndPos + $EndLen - $BeginPos);
				$this->setVar($strBlock, $VarName);
				$tplBlock = new Template('');
				$tplBlock->setTemplate($strBlock);
				$tplBlock->setVar($BeginStr, '');
				$tplBlock->setVar($EndStr, '');

				return $tplBlock->Template;
			}
		}
	}

	function isTag($TagName)
	{
		if (strpos($this->Template, $TagName, 0)===false)
			return 0;
		else
			return 1;
	}

	function ApplyDefined($vars)
	{
		foreach($vars as $key => $value)
		{
			while ($this->isTag("<!-- BEGIN IFDEF " . $key . " -->") && $value == 0)
				$this->getBlock("IFDEF " . $key , "");

			while ($this->isTag("<!-- BEGIN IFNDEF " . $key . " -->") && $value == 1)
				$this->getBlock("IFNDEF " . $key , "");
		}
	}

	function eraseEmptyTags() {
		$this->Template = eregi_replace('{[a-z0-9_\-\.]+}', '', $this->Template);
	}
}

?>
