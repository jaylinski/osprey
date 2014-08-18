<?php

namespace Osprey;

use Modules\ModuleException;

class Osprey
{
	public function __construct()
	{
		
	}
	
	/**
	 * Gets Module
	 * 
	 * @param string $moduleName
	 * @return \Modules\Module
	 */
	public function getModule($moduleName)
	{
		$moduleName = 'Osprey\Modules\\'.$moduleName;
		
		if(class_exists($moduleName))
		{
			return new $moduleName();
		}
		else
		{
			throw new \Exception("Module not available.");
		}		
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
