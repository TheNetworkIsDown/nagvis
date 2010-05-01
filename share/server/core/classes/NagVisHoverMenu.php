<?php
/*****************************************************************************
 *
 * NagVisHoverMenu.php - Class for handling the hover menus
 *
 * Copyright (c) 2004-2010 NagVis Project (Contact: info@nagvis.org)
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/
 
/**
 * @author	Lars Michelsen <lars@vertical-visions.de>
 */
class NagVisHoverMenu {
	private $CORE;
	private $OBJPAGE;
	private $CACHE;
	
	private $templateName;
	private $pathHtmlBase;
	private $pathTemplateFile;
	
	private $code;
	
	/**
	 * Class Constructor
	 *
	 * @param 	GlobalCore 	$CORE
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function __construct($CORE, $templateName, $OBJ = NULL) {
		$this->CORE = $CORE;
		$this->OBJPAGE = $OBJ;
		
		$this->templateName = $templateName;
		
		$this->pathHtmlBase = $this->CORE->getMainCfg()->getValue('paths','htmlbase');
		$this->pathTemplateFile = $this->CORE->getMainCfg()->getValue('paths','templates').$this->templateName.'.hover.html';
		
		$this->CACHE = new GlobalFileCache($this->CORE, $this->pathTemplateFile, $this->CORE->getMainCfg()->getValue('paths','var').'hover-'.$this->templateName.'-'.$this->CORE->getLang()->getCurrentLanguage().'.cache');
		
		// Only use cache when there is
		// a) Some valid cache file
		// b) Some valid main configuration cache file
		// c) This cache file newer than main configuration cache file
		if($this->CACHE->isCached() !== -1
		  && $this->CORE->getMainCfg()->isCached() !== -1
		  && $this->CACHE->isCached() >= $this->CORE->getMainCfg()->isCached()) {
			$this->code = $this->CACHE->getCache();
		} else {
			// Read the contents of the template file
			if($this->readTemplate()) {
				// The static macros should be replaced before caching
				$this->replaceStaticMacros();
				
				// Build cache for the template
				$this->CACHE->writeCache($this->code, 1);
			}
		}
	}
	
	/**
	 * readHoverTemplate 
	 *
	 * Reads the contents of the hover template file
	 *
	 * @return	String		HTML Code for the hover menu
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function readTemplate() {
		if($this->checkTemplateReadable(1)) {
			$this->code =  file_get_contents($this->pathTemplateFile);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * replaceStaticMacros
	 *
	 * Replaces static macros like paths and language strings in template code
	 *
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	private function replaceStaticMacros() {
		// Replace the static macros (language, paths)
		if(strpos($this->code,'[lang_alias]') !== FALSE) {
			$this->code = str_replace('[lang_alias]',$this->CORE->getLang()->getText('alias'),$this->code);
		}
		
		if(strpos($this->code,'[lang_address]') !== FALSE) {
			$this->code = str_replace('[lang_address]',$this->CORE->getLang()->getText('address'),$this->code);
		}
		
		if(strpos($this->code,'[lang_state]') !== FALSE) {
			$this->code = str_replace('[lang_state]',$this->CORE->getLang()->getText('state'),$this->code);
		}
		
		if(strpos($this->code,'[lang_summary_state]') !== FALSE) {
			$this->code = str_replace('[lang_summary_state]',$this->CORE->getLang()->getText('summaryState'),$this->code);
		}
		
		if(strpos($this->code,'[lang_output]') !== FALSE) {
			$this->code = str_replace('[lang_output]',$this->CORE->getLang()->getText('output'),$this->code);
		}
		
		if(strpos($this->code,'[lang_perfdata]') !== FALSE) {
			$this->code = str_replace('[lang_perfdata]',$this->CORE->getLang()->getText('perfdata'),$this->code);
		}
		
		if(strpos($this->code,'[lang_summary_output]') !== FALSE) {
			$this->code = str_replace('[lang_summary_output]',$this->CORE->getLang()->getText('summaryOutput'),$this->code);
		}
		
		if(strpos($this->code,'[lang_overview]') !== FALSE) {
			$this->code = str_replace('[lang_overview]',$this->CORE->getLang()->getText('overview'),$this->code);
		}
		
		if(strpos($this->code,'[lang_instance]') !== FALSE) {
			$this->code = str_replace('[lang_instance]',$this->CORE->getLang()->getText('instance'),$this->code);
		}
		
		if(strpos($this->code,'[lang_next_check]') !== FALSE) {
		$this->code = str_replace('[lang_next_check]',$this->CORE->getLang()->getText('nextCheck'),$this->code);
		}
		
		if(strpos($this->code,'[lang_last_check]') !== FALSE) {
			$this->code = str_replace('[lang_last_check]',$this->CORE->getLang()->getText('lastCheck'),$this->code);
		}
		
		if(strpos($this->code,'[lang_state_type]') !== FALSE) {
			$this->code = str_replace('[lang_state_type]',$this->CORE->getLang()->getText('stateType'),$this->code);
		}
		
		if(strpos($this->code,'[lang_current_attempt]') !== FALSE) {
			$this->code = str_replace('[lang_current_attempt]',$this->CORE->getLang()->getText('currentAttempt'),$this->code);
		}
		
		if(strpos($this->code,'[lang_last_state_change]') !== FALSE) {
			$this->code = str_replace('[lang_last_state_change]',$this->CORE->getLang()->getText('lastStateChange'),$this->code);
		}
		
		if(strpos($this->code,'[lang_state_duration]') !== FALSE) {
			$this->code = str_replace('[lang_state_duration]',$this->CORE->getLang()->getText('stateDuration'),$this->code);
		}
		
		if(strpos($this->code,'[lang_service_description]') !== FALSE) {
			$this->code = str_replace('[lang_service_description]',$this->CORE->getLang()->getText('servicename'),$this->code);
		}
		
		if(strpos($this->code,'[lang_notes]') !== FALSE) {
			$this->code = str_replace('[lang_notes]', $this->CORE->getLang()->getText('notes'), $this->code);
		}
		
		if(strpos($this->code,'[lang_last_status_refresh]') !== FALSE) {
			$this->code = str_replace('[lang_last_status_refresh]', $this->CORE->getLang()->getText('lastStatusRefresh'), $this->code);
		}
		
		if(strpos($this->code,'[html_base]') !== FALSE) {
			$this->code = str_replace('[html_base]',$this->CORE->getMainCfg()->getValue('paths','htmlbase'),$this->code);
		}
		
		if(strpos($this->code,'[html_templates]') !== FALSE) {
			$this->code = str_replace('[html_templates]',$this->CORE->getMainCfg()->getValue('paths','htmltemplates'),$this->code);
		}
		
		if(strpos($this->code,'[html_template_images]') !== FALSE) {
			$this->code = str_replace('[html_template_images]',$this->CORE->getMainCfg()->getValue('paths','htmltemplateimages'),$this->code);
		}
	}
	
	/**
	 * Print the HTML code
	 *
	 * return   String  HTML Code
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function __toString () {
		return $this->code;
	}
	
	/**
	 * PRIVATE checkTemplateReadable()
	 *
	 * Checks if the requested hover template file is readable
	 *
	 * @param		Boolean		Switch for enabling/disabling error messages
	 * @return	Boolean		Check Result
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function checkTemplateReadable($printErr) {
		if($this->checkTemplateExists($printErr) && is_readable($this->pathTemplateFile)) {
			return TRUE;
		} else {
			if($printErr == 1) {
				new GlobalMessage('ERROR', $this->CORE->getLang()->getText('hoverTemplateNotReadable', 'FILE~'.$this->pathTemplateFile));
			}
			return FALSE;
		}
	}
	
	/**
	 * PRIVATE checkTemplateExists()
	 *
	 * Checks if the requested hover template file exists
	 *
	 * @param		Boolean		Switch for enabling/disabling error messages
	 * @return	Boolean		Check Result
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function checkTemplateExists($printErr) {
		if(file_exists($this->pathTemplateFile)) {
			return TRUE;
		} else {
			if($printErr == 1) {
				new GlobalMessage('ERROR', $this->CORE->getLang()->getText('hoverTemplateNotExists','FILE~'.$this->pathTemplateFile));
			}
			return FALSE;
		}
	}
}
?>
