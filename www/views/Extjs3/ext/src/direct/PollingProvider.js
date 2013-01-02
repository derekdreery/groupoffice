/*!
 * Ext JS Library 3.4.0
 * Copyright(c) 2006-2011 Sencha Inc.
 * licensing@sencha.com
 * http://www.sencha.com/license
 */
/**
 * @class Ext.direct.PollingProvider
 * @extends Ext.direct.JsonProvider
 *
 * <p>Provides for repetitive polling of the server at distinct {@link #interval intervals}.
 * The initial request for data originates from the client, and then is responded to by the
 * server.</p>
 * 
 * <p>All configurations for the PollingProvider should be generated by the server-side
 * API portion of the Ext.Direct stack.</p>
 *
 * <p>An instance of PollingProvider may be created directly via the new keyword or by simply
 * specifying <tt>type = 'polling'</tt>.  For example:</p>
 * <pre><code>
var pollA = new Ext.direct.PollingProvider({
    type:'polling',
    url: 'php/pollA.php',
});
Ext.Direct.addProvider(pollA);
pollA.disconnect();

Ext.Direct.addProvider(
    {
        type:'polling',
        url: 'php/pollB.php',
        id: 'pollB-provider'
    }
);
var pollB = Ext.Direct.getProvider('pollB-provider');
 * </code></pre>
 */
Ext.direct.PollingProvider = Ext.extend(Ext.direct.JsonProvider, {
    /**
     * @cfg {Number} priority
     * Priority of the request (defaults to <tt>3</tt>). See {@link Ext.direct.Provider#priority}.
     */
    // override default priority
    priority: 3,
    
    /**
     * @cfg {Number} interval
     * How often to poll the server-side in milliseconds (defaults to <tt>3000</tt> - every
     * 3 seconds).
     */
    interval: 3000,

    /**
     * @cfg {Object} baseParams An object containing properties which are to be sent as parameters
     * on every polling request
     */
    
    /**
     * @cfg {String/Function} url
     * The url which the PollingProvider should contact with each request. This can also be
     * an imported Ext.Direct method which will accept the baseParams as its only argument.
     */

    // private
    constructor : function(config){
        Ext.direct.PollingProvider.superclass.constructor.call(this, config);
        this.addEvents(
            /**
             * @event beforepoll
             * Fired immediately before a poll takes place, an event handler can return false
             * in order to cancel the poll.
             * @param {Ext.direct.PollingProvider}
             */
            'beforepoll',            
            /**
             * @event poll
             * This event has not yet been implemented.
             * @param {Ext.direct.PollingProvider}
             */
            'poll'
        );
    },

    // inherited
    isConnected: function(){
        return !!this.pollTask;
    },

    /**
     * Connect to the server-side and begin the polling process. To handle each
     * response subscribe to the data event.
     */
    connect: function(){
        if(this.url && !this.pollTask){
            this.pollTask = Ext.TaskMgr.start({
                run: function(){
                    if(this.fireEvent('beforepoll', this) !== false){
                        if(typeof this.url == 'function'){
                            this.url(this.baseParams);
                        }else{
                            Ext.Ajax.request({
                                url: this.url,
                                callback: this.onData,
                                scope: this,
                                params: this.baseParams
                            });
                        }
                    }
                },
                interval: this.interval,
                scope: this
            });
            this.fireEvent('connect', this);
        }else if(!this.url){
            throw 'Error initializing PollingProvider, no url configured.';
        }
    },

    /**
     * Disconnect from the server-side and stop the polling process. The disconnect
     * event will be fired on a successful disconnect.
     */
    disconnect: function(){
        if(this.pollTask){
            Ext.TaskMgr.stop(this.pollTask);
            delete this.pollTask;
            this.fireEvent('disconnect', this);
        }
    },

    // private
    onData: function(opt, success, xhr){
        if(success){
            var events = this.getEvents(xhr);
            for(var i = 0, len = events.length; i < len; i++){
                var e = events[i];
                this.fireEvent('data', this, e);
            }
        }else{
            var e = new Ext.Direct.ExceptionEvent({
                data: e,
                code: Ext.Direct.exceptions.TRANSPORT,
                message: 'Unable to connect to the server.',
                xhr: xhr
            });
            this.fireEvent('data', this, e);
        }
    }
});

Ext.Direct.PROVIDERS['polling'] = Ext.direct.PollingProvider;