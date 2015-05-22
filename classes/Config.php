<?php

class Config {
	public static function get($setting = null) {
		//If argument is set and setting exists in $GLOBALS['config'], extract it.
		return (isset($GLOBALS['config'][$setting])) ? $GLOBALS['config'][$setting] : false;
	}
}