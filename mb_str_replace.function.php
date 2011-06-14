<?php
	// THIS FILE WRITTEN IN UTF-8, Japanese.
	
	/**
	 * マルチバイト対応 str_replace
	 * 
	 * @version		Release 2
	 * @author		HiNa (hina@bouhime.com)
	 * @copyright	Copyright (C) 2006-2007 by HiNa(hina@bouhime.com).
	 */
	
	if(! function_exists('mb_str_replace')) {
		/**
		 * マルチバイト対応 str_replace
		 * 
		 * @param	mixed	$search		検索文字列
		 * @param	mixed	$replace	置換文字列
		 * @param	mixed	$subject	対象文字列
		 * @param	string	$encoding	文字列のエンコーディング(省略: 内部エンコーディング)
		 *
		 * @return	mixed	subject 内の search を replace で置き換えた文字列
		 *
		 * @note	この関数は配列に対応(search, replace, subject)しています。
		 */
		function mb_str_replace($search, $replace, $subject, $encoding = 'auto') {
			if(! is_array($search)) {
				$search = array($search);
			}
			if(! is_array($replace)) {
				$replace = array($replace);
			}
			if(strtolower($encoding) === 'auto') {
				$encoding = mb_internal_encoding();
			}
			if(is_array($subject)) {
				$result = array();
				foreach($subject as $key => $val) {
					$result[$key] = mb_str_replace($search, $replace, $val, $encoding);
				}
				return $result;
			}
			
			$currentpos = 0;
			while(true) {
				$index = -1;
				$minpos = -1;
				foreach($search as $key => $find) {
					if($find == '') {
						continue;
					}
					$findpos = mb_strpos($subject, $find, $currentpos, $encoding);
					if($findpos !== false) {
						if($minpos < 0 || $findpos < $minpos) {
							$minpos = $findpos;
							$index = $key;
						}
					}
				}
				if($minpos < 0) {
					break;
				}
				
				$r = array_key_exists($index, $replace) ? $replace[$index] : '';
				$subject = sprintf('%s%s%s',
										mb_substr($subject, 0, $minpos, $encoding),
										$r,
										mb_substr(
											$subject,
											$minpos + mb_strlen($search[$index], $encoding),
											mb_strlen($subject, $encoding),
											$encoding
										)
									);
				$currentpos = $minpos + mb_strlen($r, $encoding);
			}
			return $subject;
		}
	}
?>