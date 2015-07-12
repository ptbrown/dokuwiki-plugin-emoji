<?php

/**
 * Test emoji conflicts with other syntax
 */
class emoji_conflicts_test extends DokuWikiTest {
    function setup() {
        $this->pluginsEnabled[] = 'emoji';
        parent::setup();
    }
    function test_emoji_spaces() {
        saveWikiText('emoji_page', "C:D ", 'Test');
        $this->assertNotContains('&#x1f603;', p_wiki_xhtml('emoji_page'), 'Emoji without spaces.');
    }
    function test_emoji_vs_path() {
        saveWikiText('emoji_page', "C:\\D ", 'Test');
        $this->assertNotContains('&#x1f615;', p_wiki_xhtml('emoji_page'), 'Emoji conflicts with path.');
    }
    function test_emoji_vs_wiki_path() {
        saveWikiText('emoji_page', "x :path", 'Test');
        $this->assertNotContains('&#x1f61b;', p_wiki_xhtml('emoji_page'), 'Emoji conflicts with wiki path.');
    }
    function test_emoji_vs_monospace() {
        saveWikiText('emoji_page', "x '':::'' x", 'Test');
        $this->assertNotContains('&#039;', p_wiki_xhtml('emoji_page'), 'Emoji conflicts with monospace.');
    }
    function test_emoji_vs_footnote() {
        saveWikiText('emoji_page', '((** X **))', 'Test');
        $this->assertNotContains('&#x1f609;', p_wiki_xhtml('emoji_page'), 'Emoji conflicts with footnote.');
    }
}
