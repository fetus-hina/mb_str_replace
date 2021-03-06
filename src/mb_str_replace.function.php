<?php
/*
 * マルチバイト対応 str_replace()
 * 
 * @version 4.0.0
 * @copyright 2006,2007,2011,2012,2015 by AIZAWA Hina <hina@bouhime.com>
 * @license https://github.com/fetus-hina/mb_str_replace/blob/master/LICENSE MIT
 */
if (!function_exists('mb_str_replace')) {
    /**
     * マルチバイト対応 str_replace()
     *
     * @param  mixed  $search   検索文字列（またはその配列）
     * @param  mixed  $replace  置換文字列（またはその配列）
     * @param  mixed  $subject  対象文字列（またはその配列）
     * @param  string $encoding 文字列のエンコーディング(省略: 内部エンコーディング)
     * @return mixed  subject 内の search を replace で置き換えた文字列
     *
     * この関数の $search, $replace, $subject は配列に対応していますが、
     * $search, $replace が配列の場合の挙動が PHP 標準の str_replace() と異なります。
     */
    function mb_str_replace($search, $replace, $subject, $encoding = 'auto')
    {
        if (!is_array($search)) {
            $search = array($search);
        }
        if (!is_array($replace)) {
            // PHP manual:
            //      search が配列で replace が文字列の場合、
            //      この置換文字列が search の各値について使用されます。
            //
            // array_fill_keysは5.2以上なので使えない...
            $replace = array_combine(
                array_keys($search),
                array_fill(0, count($search), $replace)
            );
        }
        if (strtolower($encoding) === 'auto') {
            $encoding = mb_internal_encoding();
        }

        // $subject が複数ならば各要素に繰り返し適用する
        if (is_array($subject) || $subject instanceof Traversable) {
            $result = array();
            foreach ($subject as $key => $val) {
                $result[$key] = mb_str_replace($search, $replace, $val, $encoding);
            }
            return $result;
        }

        $currentpos = 0;    // 現在の検索開始位置
        while (true) {
            // $currentpos 以降で $search のいずれかが現れる位置を検索する
            $index = -1;    // 見つけた文字列（最も前にあるもの）の $search の index
            $minpos = -1;   // 見つけた文字列（最も前にあるもの）の位置
            foreach ($search as $key => $find) {
                if ($find == '') {
                    continue;
                }
                $findpos = mb_strpos($subject, $find, $currentpos, $encoding);
                if ($findpos !== false) {
                    if ($minpos < 0 || $findpos < $minpos) {
                        $minpos = $findpos;
                        $index = $key;
                    }
                }
            }

            // $search のいずれも見つからなければ終了
            if ($minpos < 0) {
                break;
            }

            // 置換実行
            $replaced = array_key_exists($index, $replace) ? $replace[$index] : '';
            $subject =
                mb_substr($subject, 0, $minpos, $encoding) .
                $replaced .
                mb_substr(
                    $subject,
                    $minpos + mb_strlen($search[$index], $encoding),
                    mb_strlen($subject, $encoding),
                    $encoding
                );

            // 「現在位置」を $r の直後に設定
            $currentpos = $minpos + mb_strlen($replaced, $encoding);
        }
        return $subject;
    }
}
