<?php
/**
 * EmojiOne extension (Helper Component)
 *
 * @license     GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author      Patrick Brown <ptbrown@whoopdedo.org></ptbrown>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

require 'emojione/Emojione.php';
use Emojione\Emojione;
Emojione::$unicodeAlt = true;
Emojione::$imageType = 'png';
Emojione::$sprites = false;

class syntax_plugin_emoji extends DokuWiki_Syntax_Plugin {

    /**
     * Match emoji code points:
     *   - Most characters followed by variant selector 16
     *   - No characters followed by variant selector 15
     *   - Characters above U+1F000
     *   - Numbers with combining keycap U+20E3
     *   - Miscellaneous Technical
     *   - Control Pictures
     *   - Miscellaneous Symbols and Dingbats
     *   - Miscellaneous Symbols and Arrows
     */
    public $unicodeRegexp = '(?:[#0-9](?>\\xEF\\xB8\\x8F)?\\xE2\\x83\\xA3(?!\\xEF\\xB8\\x8E)|[#0-9]\\xEF\\xB8\\x8F|\\xC2[\\xA9\\xAE]\\xEF\\xB8\\x8F|\\xE2..\\xEF\\xB8\\x8F|\\xE2[\\x8C-\\x90\\x98-\\x9E\\xAC-\\xAF].(?!\\xEF\\xB8\\x8E)|\\xE3(?>\\x80[\\xB0\\xBD]|\\x8A[\\x97\\x99])\\xEF\\xB8\\x8F|\\xF0\\x9F(?>\\x87.\\xF0\\x9F\\x87.|..(?>\\xEF\\xB8\\x8F)?)(?!\\xEF\\xB8\\x8E))';

    private $smileys = array(
        '8-O' => '1F62F',
        '8-o' => '1F62F',
        ':-\\' => '1F615',
        ':-?' => '1F616',
        ':-|' => '1F601',
        '^_^' => '1F604',
        ':?:' => '2753',
        ':!:' => '26A0',
    );
    private $smileyRegexp;

    public function __construct() {
        $this->smileys = array_merge($this->smileys, Emojione::$ascii_replace);
        $smileys = array_keys($this->smileys);
        /* Inserts smileys into the shortcode list so I can use a single callback to handle both. */
        Emojione::$shortcode_replace = array_merge(Emojione::$shortcode_replace, $this->smileys);
        $this->smileyRegexp = '(?:'.join('|',array_map('preg_quote_cb', $smileys)).')';

        $assetsrc = DOKU_BASE.'lib/plugins/emoji/';
        switch($this->getConf('assetsrc')) {
            case 'cdn':
                $assetsrc = '//cdn.jsdelivr.net/emojione/';
                break;
            case 'external':
                $asseturi = $this->getConf('asseturi');
                if($asseturi)
                    $assetsrc = $asseturi;
                break;
        }
        Emojione::$imagePathPNG = $assetsrc.'assets/png/';
    }

    public function getType() {
        return 'substition';
    }

    public function getSort() {
        return 229;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern($this->getUnicodeRegexp(), $mode, 'plugin_emoji');
        $this->Lexer->addSpecialPattern('(?<=\W|^)'.$this->getShortnameRegexp().'(?=\W|$)', $mode, 'plugin_emoji');
        $this->Lexer->addSpecialPattern('(?<=\W|^)'.$this->getSmileyRegexp().'(?=\W|$)', $mode, 'plugin_emoji');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
        /* Clean up variant selector, I don't trust the library to do this. */
        $match = str_replace("\xEF\xB8\x8F", "", $match);
        $unicode = $this->toUnicode($match);
        return array($match,$unicode);
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
        list($match,$unicode) = $data;
        switch($mode) {
            case 'xhtml':
                if(isset(Emojione::$shortcode_replace[$match]))
                    $renderer->doc .= $this->shortnameToImage($match);
                else
                    $renderer->doc .= $this->unicodeToImage($unicode);
                break;
            default:
                /* Adds the text variant selector */
                $renderer->cdata($unicode . "\xEF\xB8\x8E");
                break;
        }
        return true;
    }

    private function getUnicodeRegexp() {
        return $this->unicodeRegexp;
    }

    private function getShortnameRegexp() {
        return preg_replace('/\((?!\?)/', '(?:', Emojione::$shortcodeRegexp);
    }

    private function getSmileyRegexp() {
        return $this->smileyRegexp;
    }

    private function toUnicode($shortname) {
        if(isset($this->smileys[$shortname])) {
            $unicode = $this->smileys[$shortname];
        }
        elseif(isset(Emojione::$shortcode_replace[$shortname])) {
            $unicode = Emojione::$shortcode_replace[$shortname];
        }
        else {
            return $shortname;
        }
        if(stristr($unicode,'-')) {
            $pairs = explode('-',$unicode);
        }
        else {
            $pairs = array($unicode);
        }
        return unicode_to_utf8(array_map('hexdec', $pairs));
    }

    private function toShortname($unicode) {
        return Emojione::toShortCallback(array($unicode,$unicode));
    }

    private function shortnameToImage($shortname) {
        return Emojione::shortnameToImageCallback(array($shortname,$shortname));
    }

    private function unicodeToImage($unicode) {
        return Emojione::unicodeToImageCallback(array($unicode,$unicode));
    }

}
