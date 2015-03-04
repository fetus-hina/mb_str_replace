<?php
require_once(dirname(__FILE__) . '/../src/mb_str_replace.function.php');

class FuncTest extends PHPUnit_Framework_TestCase
{
    private $savedInternalEncoding;

    public function setUp()
    {
        $this->savedInternalEncoding = mb_internal_encoding();
    }

    public function tearDown()
    {
        mb_internal_encoding($this->savedInternalEncoding);
    }

    /**
     * @dataProvider scalarTestProvider
     */
    public function testScalar($expect, $replFrom, $replTo, $subject, $charset)
    {
        $this->assertEquals(
            $expect,
            mb_str_replace($replFrom, $replTo, $subject, $charset)
        );
    }

    /**
     * @dataProvider scalarTestProvider
     */
    public function testAutoEncoding($expect, $replFrom, $replTo, $subject, $charset)
    {
        mb_internal_encoding($charset);
        $this->assertEquals(
            $expect,
            mb_str_replace($replFrom, $replTo, $subject)
        );
    }

    /**
     * @dataProvider arrayTestProvider
     */
    public function testArray($expect, $replFrom, $replTo, $subject, $charset)
    {
        $this->assertEquals(
            $expect,
            mb_str_replace($replFrom, $replTo, $subject, $charset)
        );
    }

    public function scalarTestProvider()
    {
        $set = array(
            array(
                "<body text='black'>",
                "%body%",
                "black",
                "<body text='%body%'>",
                'UTF-8'
            ),
            array(
                "Hll Wrld f PHP",
                array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U"),
                "",
                "Hello World of PHP",
                "UTF-8"
            ),
            array(
                "H*ll* W*rld *f PHP",
                array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U"),
                "*",
                "Hello World of PHP",
                "UTF-8"
            ),
            array(
                "You should eat pizza, beer, and ice cream every day.",
                array("fruits", "vegetables", "fiber"),
                array("pizza", "beer", "ice cream"),
                "You should eat fruits, vegetables, and fiber every day.",
                "UTF-8"
            ),
            array(
                "赤信号青信号黄信号",
                "パジャマ",
                "信号",
                "赤パジャマ青パジャマ黄パジャマ",
                "UTF-8"
            ),
            array(
                "foobar",
                array("hoge", "fuga", "piyo", "foo", "bar", "baz"),
                array("foo", "bar", "baz", "***", "+++", "---"),
                "hogefuga",
                "UTF-8"
            ),
            array(
                "aeiou",
                "",
                "*",
                "aeiou",
                "UTF-8"
            ),
        );
        $data = $set;
        foreach (array("CP932", "Shift-JIS", "EUCJP-WIN", "EUC-JP") as $charset) {
            foreach ($set as $test) {
                $data[] = array(
                    $this->convertEncodingRecursive($test[0], $charset, "UTF-8"),
                    $this->convertEncodingRecursive($test[1], $charset, "UTF-8"),
                    $this->convertEncodingRecursive($test[2], $charset, "UTF-8"),
                    $this->convertEncodingRecursive($test[3], $charset, "UTF-8"),
                    $charset
                );
            }
        }
        return $data;
    }

    public function arrayTestProvider()
    {
        return array(
            array(
                array('あいうえお', 'あいうえお', '亜伊宇江於'),
                array('ア', 'イ', 'ウ', 'エ', 'オ'),
                array('あ', 'い', 'う', 'え', 'お'),
                array('あいうえお', 'アイウエオ', '亜伊宇江於'),
                'UTF-8'
            ),
        );
    }

    private function convertEncodingRecursive($value, $toEncoding, $fromEncoding)
    {
        if (is_array($value)) {
            $ret = array();
            foreach ($value as $k => $v) {
                $ret[$k] = $this->convertEncodingRecursive($v, $toEncoding, $fromEncoding);
            }
            return $ret;
        }
        return mb_convert_encoding($value, $toEncoding, $fromEncoding);
    }
}
