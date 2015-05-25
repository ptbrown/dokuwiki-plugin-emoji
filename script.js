/**
 * Autocomplete script for Emoji plugin
 *
 * @license     GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author      Patrick Brown <ptbrown@whoopdedo.org>
 */
/* DOKUWIKI:include_once script/jquery.textcomplete.js */

+function(){
'use strict';

var byLengthCompare = function(a,b) { return a.length>b.length; };
Array.prototype.sortByLength = function() {
    return this.sort(byLengthCompare);
};

jQuery(function(){

    var $editForm = jQuery('#wiki__text');
    if($editForm) {
        jQuery.getJSON(DOKU_BASE + 'lib/plugins/emoji/assets/emoji_strategy.json',
              function(emojiStrategy) {
                    var assetUri = '//cdn.jsdelivr.net/emojione/assets/png/';
                    var cacheBustParam = '?v=1.2.4';
                    var langFooter = '<a href="http://www.emoji.codes" target="_blank">&nbsp;' +
                                      LANG.plugins.emoji.browseall +
                                      '<span class="arrow">&#10697;</span></a>';
                    if(emoji_assetsrc) {
                        assetUri = emoji_assetsrc + 'assets/png/';
                    }
                    $editForm.textcomplete([{
                        match: /(^|\s):([\-+]?[\-+\w]+)$/,
                        index: 2,
                        /* TODO disable where emoji is not allowed (code blocks, headings, etc)
                        context: function(text) {
                        },
                        */
                        search: function(term, addTerm) {
                            var names = [], aliases = [], keywords = [];
                            jQuery.each(emojiStrategy, function(shortname, data) {
                                if(shortname.indexOf(term) > -1) {
                                    names.push(shortname);
                                } else if((data.aliases !== null) && (data.aliases.indexOf(term) > -1)) {
                                    aliases.push(shortname);
                                } else if((data.keywords !== null) && (data.keywords.indexOf(term) > -1)) {
                                    keywords.push(shortname);
                                }
                            });

                            if(term.length >= 3) {
                                names.sortByLength();
                                aliases.sortByLength();
                                keywords.sort();
                            }
                            addTerm(names.concat(aliases).concat(keywords));
                        },
                        template: function(shortname) {
                            var fileName = assetUri + emojiStrategy[shortname].unicode + '.png' + cacheBustParam;
                            return '<img class="emojione" src="' + fileName + '"/> :' + shortname + ':';
                        },
                        replace: function(shortname) {
                            return '$1:' + shortname + ': ';
                        },
                        cache: true
                    }], { footer: langFooter });
              });
    }

});
}();
