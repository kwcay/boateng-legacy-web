/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
var Utilities =
{
    /**
     * @param string str
     * @return string
     */
    urlencode : function(str)
    {
        return encodeURIComponent((str + '').toString())
            .replace(/!/g, '%21')
            .replace(/'/g, '%27')
            .replace(/\(/g, '%28')
            .replace(/\)/g, '%29')
            .replace(/\*/g, '%2A')
            .replace(/%20/g, '+');
    },

    /**
     * @param string|int key
     * @param string value
     */
    setData: function(key, value) {
        if (window.localStorage && typeof window.localStorage.setItem == 'function') {
            window.localStorage.setItem(key, value);
        }

        else
        {
            if (!this.localStorage) {
                this.localStorage = {};
            }

            this.localStorage[key] = value;
        }
    },

    /**
     * @param string|int key
     * @param string value
     */
    setState: function(key, value) {
        if (window.sessionStorage && typeof window.sessionStorage.setItem == 'function') {
            window.sessionStorage.setItem(key, value);
        }

        else
        {
            if (!this.sessionStorage) {
                this.sessionStorage = {};
            }

            this.sessionStorage[key] = value;
        }
    },

    /**
     * @param string|int key
     * @param string value
     */
    getState: function(key, defaultValue) {
        if (window.sessionStorage && typeof window.sessionStorage.getItem == 'function') {
            window.sessionStorage.getItem(key, value);
        }

        else
        {
            if (!this.sessionStorage) {
                this.sessionStorage = {};
            }

            this.sessionStorage[key] = value;
        }
    },

    /**
     * Logs a message to the console.
     *
     * @param mixed msg
     * @return void
     */
    log: function(msg) {
        if (this.isLocalEnvironment && console) {
            console.log('App.js - '+ msg);
        }
    },

    /**
     * Logs an info message to the console.
     *
     * @param mixed msg
     * @return void
     */
    info: function(msg) {
        if (this.isLocalEnvironment && console && typeof console.info == 'function') {
            console.info(msg);
        }
    },

    error: function(msg) {
        if (this.isLocalEnvironment && console && typeof console.error == 'function') {
            console.error(msg);
        }
    }
};
