<?php
/**
 * EmojiOne extension (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Patrick Brown <ptbrown@whoopdedo.org></ptbrown>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

require __DIR__.'/Emojione.php';
use Emojione\Emojione;
Emojione::$unicodeAlt = true;
Emojione::$imageType = 'png';
Emojione::$sprites = true;
/* Don't replace copyright/registration mark */
Emojione::$unicodeRegexp = str_replace('\\xC2[\\xA9\\xAE]|', '', Emojione::$unicodeRegexp);

class syntax_plugin_emoji extends DokuWiki_Syntax_Plugin {

    private $smileys;
    private $smileyRegexp;

    public function __construct() {
        $this->smileys = Emojione::$ascii_replace;
        $smileys = array_keys($this->smileys);
        /* Inserts smileys into the shortcode list so I can use a single callback to handle both. */
        Emojione::$shortcode_replace = array_merge(Emojione::$shortcode_replace, Emojione::$ascii_replace);
        $this->smileyRegexp = '(?:'.join('|',array_map('preg_quote_cb', $smileys)).')';
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
        $unicode = $this->toUnicode($match);
        $shortname = $this->toShortname($match);
        return array($match,$unicode,$shortname);
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
        list($match,$unicode,$shortname) = $data;
        switch($mode) {
            case 'xhtml':
                if(isset(Emojione::$shortcode_replace[$match]))
                    $renderer->doc .= $this->shortnameToImage($match);
                else
                    $renderer->doc .= $this->unicodeToImage($unicode);
            break;
            default:
                $renderer->cdata($unicode);
            break;
        }
        return true;
    }

    private function getUnicodeRegexp() {
        return preg_replace('/\((?!\?)/', '(?:', Emojione::$unicodeRegexp);
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
