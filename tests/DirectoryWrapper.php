<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Streams\Tests;

class DirectoryWrapperDummy extends \Icewind\Streams\DirectoryWrapper {
	public static function wrap($source) {
		$options = array(
			'dir' => array(
				'source' => $source)
		);
		return self::wrapWithOptions($options);
	}

	public function dir_readdir() {
		$file = parent::dir_readdir();
		if ($file !== false) {
			$file .= '_';
		}
		return $file;
	}
}

class DirectoryWrapper extends IteratorDirectory {

	/**
	 * @param \Iterator | array $source
	 * @return resource
	 */
	protected function wrapSource($source) {
		$dir = \Icewind\Streams\IteratorDirectory::wrap($source);
		return \Icewind\Streams\DirectoryWrapper::wrap($dir);
	}

	public function testManipulateContent() {
		$source = \Icewind\Streams\IteratorDirectory::wrap(['asd', 'bar']);
		$wrapped = DirectoryWrapperDummy::wrap($source);
		$result = [];
		while (($file = readdir($wrapped)) !== false) {
			$result[] = $file;
		}
		$this->assertEquals(['asd_', 'bar_'], $result);
	}
}
