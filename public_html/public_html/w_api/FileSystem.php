<?php

/**
 * FileSystem utility functions
 */
class FileSystem
{
	/**
	 * Recursively deletes a tree
	 * @param string $dir
	 * @return bool
	 */
	public static function deleteTree($dir)
	{
		if (is_dir($dir) && !is_link($dir)) {
			$files = array_diff(scandir($dir), array('.', '..'));
			foreach ($files as $file) {
				$path = "$dir/$file";

				if (is_file($path) || is_link($path)) {
					unlink($path);
				} else if (is_dir($path)) {
					self::deleteTree($path);
				}
			}
			$ret = rmdir($dir);
		} else {
			$ret = unlink($dir);
		}
		return $ret;
	}

	/**
	 * delete filepath if it's a file and it's an outdated temp file.
	 *
	 * @param $filepath
	 * @return bool
	 */
	public static function isOutdatedTempFile($filepath)
	{
		// only consider if it's a file.
		if (is_dir($filepath) || is_link($filepath)) {
			return false;
		}

		// IF filename satisfies pattern: "<original-filename>.timestamp.tmp.BUILDDATE.tmp",
		// with BUILDDATE != current published configuration build date (tmp file before last publish)
		// THEN consider temp file as outdated, and ready to be cleaned up.
		$path_parts = pathinfo($filepath);
		$filename = $path_parts['basename'];

		$filename_parts = explode(".", $filename);
		if (count($filename_parts) > 3 && array_pop($filename_parts) === 'tmp') {
			$tempFileBuildDate = array_pop($filename_parts);
			if (array_pop($filename_parts) === 'tmp' && $tempFileBuildDate !== Configuration::BUILDDATE) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check path for temp file and remove if temp file is outdated.
	 * * This function is harmless if no correspondingly named file exists.
	 *
	 * @param $path
	 */
	public static function deleteTempFiles($path)
	{
		if (is_dir($path) && !is_link($path)) {
			$files = array_diff(scandir($path), array('.', '..'));
			foreach ($files as $file) {
				$newPath = "$path/$file";
				self::deleteTempFiles($newPath);
			}
		} else {
			// if is_link or is_file
			if (self::isOutdatedTempFile($path)) {
				unlink($path);
			}
		}
	}

}

