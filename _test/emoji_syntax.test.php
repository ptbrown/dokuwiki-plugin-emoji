<?php

/**
 * Test emoji substitution syntax
 */
class emoji_syntax_test extends DokuWikiTest {
    function setup() {
        $this->pluginsEnabled[] = 'emoji';
        parent::setup();
    }
    function test_emoji_shortname() {
        saveWikiText('emoji_page', ':smile:', 'Test');
        $this->assertContains('<span class="emojione-1F604" title=":smile:">&#x1f604;</span>', p_wiki_xhtml('emoji_page'),
            'Emoji shortname does not convert.');
    }
    function test_emoji_unicode() {
        saveWikiText('emoji_page', "\xF0\x9F\x98\x84", 'Test');
        $this->assertContains("<span class=\"emojione-1F604\" title=\":smile:\">\xF0\x9F\x98\x84</span>", p_wiki_xhtml('emoji_page'),
            'Emoji code point does not convert.');
    }
    function test_emoji_smiley() {
        saveWikiText('emoji_page', ':-)', 'Test');
        $this->assertContains('<span class="emojione-1F604" title=":-)">&#x1f604;</span>', p_wiki_xhtml('emoji_page'),
            'Emoji smiley does not convert.');
    }
}
