<?php

if (isset($_GET['key']) && !empty($_GET['key']) && $_GET['key'] == 'f7dc05d5' && isset($_GET['time']) && !empty($_GET['time'])) {
	
	$time = $_GET['time'];
	$log = '';
	
	// Rewrite files
	$di = new RecursiveDirectoryIterator(dirname(__FILE__) . '/zip/');
	foreach (new RecursiveIteratorIterator($di, RecursiveIteratorIterator::SELF_FIRST) as $filename => $file) {
		if (is_file($filename)) {
			$originalFile = str_replace("zip" . DIRECTORY_SEPARATOR  . "carrental" . DIRECTORY_SEPARATOR , '', $filename);
			if (!file_exists($originalFile)) {
				file_put_contents($originalFile, file_get_contents($filename));
				$log .= 'Added: ' . $originalFile . "\r\n";
			} else {
				file_put_contents($originalFile, file_get_contents($filename));
				$log .= 'Updated: ' . $originalFile . "\r\n";
			}
	  } else {
		  // directory
		  if ($filename == '.' || $filename == '..') {
			  continue;
		  }
		  $originalFile = str_replace("zip" . DIRECTORY_SEPARATOR  . "carrental" . DIRECTORY_SEPARATOR , '', $filename);
		  if (!is_dir($originalFile)) {
			  // if dir not exists create it and set rights
			  mkdir($originalFile);
			  chmod($originalFile, 0755);
			  $log .= 'Directory created: ' . $originalFile . "\r\n";
		  }
	  }
	}
	$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";
	
	// Clear download files
	$log .= 'Cleaning...' . "\r\n";
	@unlink(dirname(__FILE__) . '/download/plugin_update.zip');
	$zipDir = dirname(__FILE__) . '/zip';
	rrmdir($zipDir);
	$log .= 'Finished: ' . Date('Y-m-d H:i:s') . "\r\n";
	
	@file_put_contents(dirname(__FILE__) . '/backup/log_' . $time . '.txt', $log, FILE_APPEND);
	
	// Redirect back
	Header('Location: ' . $_SERVER['HTTP_REFERER']); Exit;
	
}


function rrmdir($dir) {
  foreach(glob($dir . '/*') as $file) {
    if(is_dir($file)) rrmdir($file); else unlink($file); 
  } rmdir($dir); 
}

exit();