(function(customer_id) {
    tinymce.create('tinymce.plugins.ItStream_AttachToPost', {
        customer_id: customer_id,

        init : function(editor, plugin_url) {
            editor.addButton('player_scheduling', {
                title : 'Embed ItStream Player',
                cmd : 'itm_scheduling',
                image : plugin_url + '/scheduling.gif'
            });

            // Register a new TinyMCE command
            editor.addCommand('itm_scheduling', this.render_attach_to_post_interface, {
                editor: editor,
                plugin:	editor.plugins.ItStream_AttachToPost
            });
        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : 'ItStream Scheduling Button',
                author : 'It-Marketing',
                authorurl : 'http://www.itmarketingsrl.it/',
                infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',
                version : "0.1"
            };
        },

        wm_close_event: function() {
            // Restore scrolling for the main content window when the attach to post interface is closed
            jQuery('html,body').css('overflow', 'auto');
            tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.dom.select('p')[0]);
            tinyMCE.activeEditor.selection.collapse(0);
        },

        render_attach_to_post_interface: function() {
            var attach_to_post_url = itstream_ajax.attach_to_post;
            if (typeof(customer_id) != 'undefined') {
                attach_to_post_url += "?id=" + itstream_ajax.customer_id;
            }

            var win = window;
            while (win.parent != null && win.parent != win) {
                win = win.parent;
            }

            win = jQuery(win);
            var winWidth    = win.width();
            var winHeight   = win.height();
            var popupWidth  = 680;
            var popupHeight = 560;
            var minWidth    = 320;
            var minHeight   = 200;
            var maxWidth    = winWidth  - (winWidth  * 0.05);
            var maxHeight   = winHeight - (winHeight * 0.05);

            if (maxWidth    < minWidth)  { maxWidth    = winWidth - 10;  }
            if (maxHeight   < minHeight) { maxHeight   = winHeight - 10; }
            if (popupWidth  > maxWidth)  { popupWidth  = maxWidth;  }
            if (popupHeight > maxHeight) { popupHeight = maxHeight; }

            // Open a window
            this.editor.windowManager.open({
                url: attach_to_post_url,
                id: 'its_attach_to_post_dialog',
                width: popupWidth,
                height: popupHeight,
                title: 'ItStream - Embed Player',
                inline: 1
                /*buttons: [{
                    text: 'Close',
                    onclick: 'close'
                }]*/
            });

            // Ensure that the window cannot be scrolled - XXX actually allow scrolling in the main window and disable it for the inner-windows/frames/elements as to create a single scrollbar
            jQuery('html,body').css('overflow', 'hidden');
            jQuery('#its_attach_to_post_dialog_ifr').css('overflow-y', 'auto');
            jQuery('#its_attach_to_post_dialog_ifr').css('overflow-x', 'hidden');
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'itstream', tinymce.plugins.ItStream_AttachToPost );
})(itstream_ajax.customer_id);