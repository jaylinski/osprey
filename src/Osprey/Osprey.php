<?php

namespace Osprey;

class Osprey
{
	public function __construct()
	{
		
	}
	
	/**
	 * Gets Module
	 * 
	 * @param string $moduleName
	 * @return \Module\Module 
	 */
	public function getModule($moduleName)
	{
		$moduleName = '\Osprey\Modules\\'.$moduleName;
		return new $moduleName();
	}
	
	/**
	 * Prints modules as list elements
	 */
	public function printModules()
	{
		$dir = __DIR__.'/Modules/';
		$iterator = new \DirectoryIterator(realpath($dir));
		foreach($iterator as $file) {
			if($file->isDot() || $file->getFilename() === 'Module.php') {
				continue;
			}
			if($file->isFile()) {
				echo '<li><a href="?module='.$file->getBasename('.php').'">'.$file->getBasename('.php').'</a></li>';
			}
		}
	}
}
