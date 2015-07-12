<?php
/**
 * @license     GPL 2 (http://www.gnu.org/licenses/gpl.html
 * @author      Patrick Brown <ptbrown@whoopdedo.org>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_emoji extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'tplMetaheaderOutput');
    }

    public function tplMetaheaderOutput(Doku_Event &$event, $param) {
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
        /* Insert JS variable for CDN server. */
        /* Use a global variable because otherwise there would need to be yet */
        /* another hook to modify the JSINFO array that has already been written. */
        $json = new JSON();
        $event->data['script'][] = array(
            'type'  => 'text/javascript',
            '_data' => 'var emoji_assetsrc = '.$json->encode($assetsrc).';'
        );
    }

}

