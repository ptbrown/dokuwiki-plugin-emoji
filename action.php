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
        $event->data['link'][] = array(
            'type' => 'text/css',
            'rel' => 'stylesheet',
            'href' => $assetsrc.'assets/css/emojione.min.css'
        );
    }

}

