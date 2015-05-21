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
        $this->assertContains('<img class="emojione" alt="&#x1f604;" src="//cdn.jsdelivr.net/emojione/assets/png/1F604.png?v=1.2.4"/>', p_wiki_xhtml('emoji_page'),
            'Emoji shortname does not convert.');
    }
    function test_emoji_unicode() {
        saveWikiText('emoji_page', "\xF0\x9F\x98\x84", 'Test');
        $this->assertContains("<img class=\"emojione\" alt=\"\xF0\x9F\x98\x84\" src=\"//cdn.jsdelivr.net/emojione/assets/png/1F604.png?v=1.2.4\"/>", p_wiki_xhtml('emoji_page'),
            'Emoji code point does not convert.');
    }
    function test_emoji_smiley() {
        saveWikiText('emoji_page', ':-)', 'Test');
        $this->assertContains('<img class="emojione" alt="&#x1f604;" src="//cdn.jsdelivr.net/emojione/assets/png/1F604.png?v=1.2.4"/>', p_wiki_xhtml('emoji_page'),
            'Emoji smiley does not convert.');
    }
}
