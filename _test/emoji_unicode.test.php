<?php

/**
 * Test emoji unicode substitution
 */
class emoji_unicode_test extends DokuWikiTest {

    function setUp() {
        $this->pluginsEnabled[] = 'emoji';
        parent::setUp();
    }

    function test_implicittext() {
        $instructions = p_get_instructions("x"
                                          ."\xC2\xA9"
                                          ."\xE2\x84\xA2"
                                          ."\xE2\x96\xB6"
                                          ."\xE3\x80\xB0"
                                          ."x");
        $foundemoji = $this->count_array_values('emoji', $this->flatten_array($instructions), true);
        $this->assertEquals(0, $foundemoji, 'Emoji failed implicit text.');
    }

    function test_implicitemoji() {
        $instructions = p_get_instructions("x"
                                          ."0\xE2\x83\xA3"
                                          ."\xE2\x8C\x9A"
                                          ."\xE2\x9D\x8C"
                                          ."\xE2\xAD\x90"
                                          ."\xF0\x9F\x87\xA9\xF0\x9F\x87\xBC"
                                          ."\xF0\x9F\x8C\xA0"
                                          ."x");
        $foundemoji = $this->count_array_values('emoji', $this->flatten_array($instructions));
        $this->assertEquals(6, $foundemoji, 'Emoji failed implicit emoji.');
    }

    function test_explicittext() {
        $instructions = p_get_instructions("x"
                                          ."0\xE2\x83\xA3\xEF\xB8\x8E"
                                          ."\xE2\x8C\x9A\xEF\xB8\x8E"
                                          ."\xE2\x9D\x8C\xEF\xB8\x8E"
                                          ."\xE2\xAD\x90\xEF\xB8\x8E"
                                          ."\xF0\x9F\x8C\xA0\xEF\xB8\x8E"
                                          ."x");
        $foundemoji = $this->count_array_values('emoji', $this->flatten_array($instructions), true);
        $this->assertEquals(0, $foundemoji, 'Emoji failed explicit text.');
    }

    function test_explicitemoji() {
        $instructions = p_get_instructions("x"
                                          ."#\xEF\xB8\x8F"
                                          ."\xC2\xA9\xEF\xB8\x8F"
                                          ."\xE2\x84\xA2\xEF\xB8\x8F"
                                          ."\xE2\x96\xB6\xEF\xB8\x8F"
                                          ."\xE3\x80\xB0\xEF\xB8\x8F"
                                          ."x");
        $foundemoji = $this->count_array_values('emoji', $this->flatten_array($instructions));
        $this->assertEquals(5, $foundemoji, 'Emoji failed explicit emoji.');
    }

    function flatten_array($array) {
        $array = array_values($array);
        $return = array();
        while($array) {
            $value = array_shift($array);
            if(is_array($value)) {
                array_splice($array, 0, 0, $value);
            } else {
                $return[] = $value;
            }
        }
        return $return;
    }

    function count_array_values($value, $array) {
        $count = 0;
        foreach($array as $item) {
            if($item === $value) ++$count;
        }
        return $count;
    }
}
