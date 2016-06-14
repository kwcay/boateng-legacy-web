/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
(function ( $ ) {

    /**
     * @param mixed elem
     */
    $.fn.copyOnClick = function( setup ) {

        // Create a new Clipboard instance.
        var cb = new Clipboard(this.selector);

        //
        this.css('cursor', 'pointer');

        // Success message.
        cb.on('success', function(e) {

            e.clearSelection();

            // TODO: element = e.trigger

            alert('Copied to clipboard!');
        });

        // Error message.
        cb.on('error', function(e) {

            var msg = '',
                actionKey = (e.action === 'cut' ? 'X' : 'C');

            if (/iPhone|iPad/i.test(navigator.userAgent)) {
                msg = 'No support :(';
            }

            else if (/Mac/i.test(navigator.userAgent)){
                msg = 'Press ⌘-' + actionKey + ' to ' + action;
            }

            else {
                msg = 'Press Ctrl-' + actionKey + ' to ' + action;
            }

            alert(msg);
        });

        return this;
    };

}( jQuery ));
