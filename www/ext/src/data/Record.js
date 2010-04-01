/*!
 * Ext JS Library 3.2.0
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.data.Record
 * <p>Instances of this class encapsulate both Record <em>definition</em> information, and Record
 * <em>value</em> information for use in {@link Ext.data.Store} objects, or any code which needs
 * to access Records cached in an {@link Ext.data.Store} object.</p>
 * <p>Constructors for this class are generated by passing an Array of field definition objects to {@link #create}.
 * Instances are usually only created by {@link Ext.data.Reader} implementations when processing unformatted data
 * objects.</p>
 * <p>Note that an instance of a Record class may only belong to one {@link Ext.data.Store Store} at a time.
 * In order to copy data from one Store to another, use the {@link #copy} method to create an exact
 * copy of the Record, and insert the new instance into the other Store.</p>
 * <p>When serializing a Record for submission to the server, be aware that it contains many private
 * properties, and also a reference to its owning Store which in turn holds references to its Records.
 * This means that a whole Record may not be encoded using {@link Ext.util.JSON.encode}. Instead, use the
 * <code>{@link #data}</code> and <code>{@link #id}</code> properties.</p>
 * <p>Record objects generated by this constructor inherit all the methods of Ext.data.Record listed below.</p>
 * @constructor
 * <p>This constructor should not be used to create Record objects. Instead, use {@link #create} to
 * generate a subclass of Ext.data.Record configured with information about its constituent fields.<p>
 * <p><b>The generated constructor has the same signature as this constructor.</b></p>
 * @param {Object} data (Optional) An object, the properties of which provide values for the new Record's
 * fields. If not specified the <code>{@link Ext.data.Field#defaultValue defaultValue}</code>
 * for each field will be assigned.
 * @param {Object} id (Optional) The id of the Record. The id is used by the
 * {@link Ext.data.Store} object which owns the Record to index its collection
 * of Records (therefore this id should be unique within each store). If an
 * <code>id</code> is not specified a <b><code>{@link #phantom}</code></b>
 * Record will be created with an {@link #Record.id automatically generated id}.
 */
Ext.data.Record = function(data, id){
    // if no id, call the auto id method
    this.id = (id || id === 0) ? id : Ext.data.Record.id(this);
    this.data = data || {};
};

/**
 * Generate a constructor for a specific Record layout.
 * @param {Array} o An Array of <b>{@link Ext.data.Field Field}</b> definition objects.
 * The constructor generated by this method may be used to create new Record instances. The data
 * object must contain properties named after the {@link Ext.data.Field field}
 * <b><tt>{@link Ext.data.Field#name}s</tt></b>.  Example usage:<pre><code>
// create a Record constructor from a description of the fields
var TopicRecord = Ext.data.Record.create([ // creates a subclass of Ext.data.Record
    {{@link Ext.data.Field#name name}: 'title', {@link Ext.data.Field#mapping mapping}: 'topic_title'},
    {name: 'author', mapping: 'username', allowBlank: false},
    {name: 'totalPosts', mapping: 'topic_replies', type: 'int'},
    {name: 'lastPost', mapping: 'post_time', type: 'date'},
    {name: 'lastPoster', mapping: 'user2'},
    {name: 'excerpt', mapping: 'post_text', allowBlank: false},
    // In the simplest case, if no properties other than <tt>name</tt> are required,
    // a field definition may consist of just a String for the field name.
    'signature'
]);

// create Record instance
var myNewRecord = new TopicRecord(
    {
        title: 'Do my job please',
        author: 'noobie',
        totalPosts: 1,
        lastPost: new Date(),
        lastPoster: 'Animal',
        excerpt: 'No way dude!',
        signature: ''
    },
    id // optionally specify the id of the record otherwise {@link #Record.id one is auto-assigned}
);
myStore.{@link Ext.data.Store#add add}(myNewRecord);
</code></pre>
 * @method create
 * @return {Function} A constructor which is used to create new Records according
 * to the definition. The constructor has the same signature as {@link #Record}.
 * @static
 */
Ext.data.Record.create = function(o){
    var f = Ext.extend(Ext.data.Record, {});
    var p = f.prototype;
    p.fields = new Ext.util.MixedCollection(false, function(field){
        return field.name;
    });
    for(var i = 0, len = o.length; i < len; i++){
        p.fields.add(new Ext.data.Field(o[i]));
    }
    f.getField = function(name){
        return p.fields.get(name);
    };
    return f;
};

Ext.data.Record.PREFIX = 'ext-record';
Ext.data.Record.AUTO_ID = 1;
Ext.data.Record.EDIT = 'edit';
Ext.data.Record.REJECT = 'reject';
Ext.data.Record.COMMIT = 'commit';


/**
 * Generates a sequential id. This method is typically called when a record is {@link #create}d
 * and {@link #Record no id has been specified}. The returned id takes the form:
 * <tt>&#123;PREFIX}-&#123;AUTO_ID}</tt>.<div class="mdetail-params"><ul>
 * <li><b><tt>PREFIX</tt></b> : String<p class="sub-desc"><tt>Ext.data.Record.PREFIX</tt>
 * (defaults to <tt>'ext-record'</tt>)</p></li>
 * <li><b><tt>AUTO_ID</tt></b> : String<p class="sub-desc"><tt>Ext.data.Record.AUTO_ID</tt>
 * (defaults to <tt>1</tt> initially)</p></li>
 * </ul></div>
 * @param {Record} rec The record being created.  The record does not exist, it's a {@link #phantom}.
 * @return {String} auto-generated string id, <tt>"ext-record-i++'</tt>;
 */
Ext.data.Record.id = function(rec) {
    rec.phantom = true;
    return [Ext.data.Record.PREFIX, '-', Ext.data.Record.AUTO_ID++].join('');
};

Ext.data.Record.prototype = {
    /**
     * <p><b>This property is stored in the Record definition's <u>prototype</u></b></p>
     * A MixedCollection containing the defined {@link Ext.data.Field Field}s for this Record.  Read-only.
     * @property fields
     * @type Ext.util.MixedCollection
     */
    /**
     * An object hash representing the data for this Record. Every field name in the Record definition
     * is represented by a property of that name in this object. Note that unless you specified a field
     * with {@link Ext.data.Field#name name} "id" in the Record definition, this will <b>not</b> contain
     * an <tt>id</tt> property.
     * @property data
     * @type {Object}
     */
    /**
     * The unique ID of the Record {@link #Record as specified at construction time}.
     * @property id
     * @type {Object}
     */
    /**
     * <p><b>Only present if this Record was created by an {@link Ext.data.XmlReader XmlReader}</b>.</p>
     * <p>The XML element which was the source of the data for this Record.</p>
     * @property node
     * @type {XMLElement}
     */
    /**
     * <p><b>Only present if this Record was created by an {@link Ext.data.ArrayReader ArrayReader} or a {@link Ext.data.JsonReader JsonReader}</b>.</p>
     * <p>The Array or object which was the source of the data for this Record.</p>
     * @property json
     * @type {Array|Object}
     */
    /**
     * Readonly flag - true if this Record has been modified.
     * @type Boolean
     */
    dirty : false,
    editing : false,
    error : null,
    /**
     * This object contains a key and value storing the original values of all modified
     * fields or is null if no fields have been modified.
     * @property modified
     * @type {Object}
     */
    modified : null,
    /**
     * <tt>true</tt> when the record does not yet exist in a server-side database (see
     * {@link #markDirty}).  Any record which has a real database pk set as its id property
     * is NOT a phantom -- it's real.
     * @property phantom
     * @type {Boolean}
     */
    phantom : false,

    // private
    join : function(store){
        /**
         * The {@link Ext.data.Store} to which this Record belongs.
         * @property store
         * @type {Ext.data.Store}
         */
        this.store = store;
    },

    /**
     * Set the {@link Ext.data.Field#name named field} to the specified value.  For example:
     * <pre><code>
// record has a field named 'firstname'
var Employee = Ext.data.Record.{@link #create}([
    {name: 'firstname'},
    ...
]);

// update the 2nd record in the store:
var rec = myStore.{@link Ext.data.Store#getAt getAt}(1);

// set the value (shows dirty flag):
rec.set('firstname', 'Betty');

// commit the change (removes dirty flag):
rec.{@link #commit}();

// update the record in the store, bypass setting dirty flag,
// and do not store the change in the {@link Ext.data.Store#getModifiedRecords modified records}
rec.{@link #data}['firstname'] = 'Wilma'; // updates record, but not the view
rec.{@link #commit}(); // updates the view
     * </code></pre>
     * <b>Notes</b>:<div class="mdetail-params"><ul>
     * <li>If the store has a writer and <code>autoSave=true</code>, each set()
     * will execute an XHR to the server.</li>
     * <li>Use <code>{@link #beginEdit}</code> to prevent the store's <code>update</code>
     * event firing while using set().</li>
     * <li>Use <code>{@link #endEdit}</code> to have the store's <code>update</code>
     * event fire.</li>
     * </ul></div>
     * @param {String} name The {@link Ext.data.Field#name name of the field} to set.
     * @param {String/Object/Array} value The value to set the field to.
     */
    set : function(name, value){
        var encode = Ext.isPrimitive(value) ? String : Ext.encode;
        if(encode(this.data[name]) == encode(value)) {
            return;
        }        
        this.dirty = true;
        if(!this.modified){
            this.modified = {};
        }
        if(this.modified[name] === undefined){
            this.modified[name] = this.data[name];
        }
        this.data[name] = value;
        if(!this.editing){
            this.afterEdit();
        }
    },

    // private
    afterEdit : function(){
        if (this.store != undefined && typeof this.store.afterEdit == "function") {
            this.store.afterEdit(this);
        }
    },

    // private
    afterReject : function(){
        if(this.store){
            this.store.afterReject(this);
        }
    },

    // private
    afterCommit : function(){
        if(this.store){
            this.store.afterCommit(this);
        }
    },

    /**
     * Get the value of the {@link Ext.data.Field#name named field}.
     * @param {String} name The {@link Ext.data.Field#name name of the field} to get the value of.
     * @return {Object} The value of the field.
     */
    get : function(name){
        return this.data[name];
    },

    /**
     * Begin an edit. While in edit mode, no events (e.g.. the <code>update</code> event)
     * are relayed to the containing store.
     * See also: <code>{@link #endEdit}</code> and <code>{@link #cancelEdit}</code>.
     */
    beginEdit : function(){
        this.editing = true;
        this.modified = this.modified || {};
    },

    /**
     * Cancels all changes made in the current edit operation.
     */
    cancelEdit : function(){
        this.editing = false;
        delete this.modified;
    },

    /**
     * End an edit. If any data was modified, the containing store is notified
     * (ie, the store's <code>update</code> event will fire).
     */
    endEdit : function(){
        this.editing = false;
        if(this.dirty){
            this.afterEdit();
        }
    },

    /**
     * Usually called by the {@link Ext.data.Store} which owns the Record.
     * Rejects all changes made to the Record since either creation, or the last commit operation.
     * Modified fields are reverted to their original values.
     * <p>Developers should subscribe to the {@link Ext.data.Store#update} event
     * to have their code notified of reject operations.</p>
     * @param {Boolean} silent (optional) True to skip notification of the owning
     * store of the change (defaults to false)
     */
    reject : function(silent){
        var m = this.modified;
        for(var n in m){
            if(typeof m[n] != "function"){
                this.data[n] = m[n];
            }
        }
        this.dirty = false;
        delete this.modified;
        this.editing = false;
        if(silent !== true){
            this.afterReject();
        }
    },

    /**
     * Usually called by the {@link Ext.data.Store} which owns the Record.
     * Commits all changes made to the Record since either creation, or the last commit operation.
     * <p>Developers should subscribe to the {@link Ext.data.Store#update} event
     * to have their code notified of commit operations.</p>
     * @param {Boolean} silent (optional) True to skip notification of the owning
     * store of the change (defaults to false)
     */
    commit : function(silent){
        this.dirty = false;
        delete this.modified;
        this.editing = false;
        if(silent !== true){
            this.afterCommit();
        }
    },

    /**
     * Gets a hash of only the fields that have been modified since this Record was created or commited.
     * @return Object
     */
    getChanges : function(){
        var m = this.modified, cs = {};
        for(var n in m){
            if(m.hasOwnProperty(n)){
                cs[n] = this.data[n];
            }
        }
        return cs;
    },

    // private
    hasError : function(){
        return this.error !== null;
    },

    // private
    clearError : function(){
        this.error = null;
    },

    /**
     * Creates a copy (clone) of this Record.
     * @param {String} id (optional) A new Record id, defaults to the id
     * of the record being copied. See <code>{@link #id}</code>. 
     * To generate a phantom record with a new id use:<pre><code>
var rec = record.copy(); // clone the record
Ext.data.Record.id(rec); // automatically generate a unique sequential id
     * </code></pre>
     * @return {Record}
     */
    copy : function(newId) {
        return new this.constructor(Ext.apply({}, this.data), newId || this.id);
    },

    /**
     * Returns <tt>true</tt> if the passed field name has been <code>{@link #modified}</code>
     * since the load or last commit.
     * @param {String} fieldName {@link Ext.data.Field.{@link Ext.data.Field#name}
     * @return {Boolean}
     */
    isModified : function(fieldName){
        return !!(this.modified && this.modified.hasOwnProperty(fieldName));
    },

    /**
     * By default returns <tt>false</tt> if any {@link Ext.data.Field field} within the
     * record configured with <tt>{@link Ext.data.Field#allowBlank} = false</tt> returns
     * <tt>true</tt> from an {@link Ext}.{@link Ext#isEmpty isempty} test.
     * @return {Boolean}
     */
    isValid : function() {
        return this.fields.find(function(f) {
            return (f.allowBlank === false && Ext.isEmpty(this.data[f.name])) ? true : false;
        },this) ? false : true;
    },

    /**
     * <p>Marks this <b>Record</b> as <code>{@link #dirty}</code>.  This method
     * is used interally when adding <code>{@link #phantom}</code> records to a
     * {@link Ext.data.Store#writer writer enabled store}.</p>
     * <br><p>Marking a record <code>{@link #dirty}</code> causes the phantom to
     * be returned by {@link Ext.data.Store#getModifiedRecords} where it will
     * have a create action composed for it during {@link Ext.data.Store#save store save}
     * operations.</p>
     */
    markDirty : function(){
        this.dirty = true;
        if(!this.modified){
            this.modified = {};
        }
        this.fields.each(function(f) {
            this.modified[f.name] = this.data[f.name];
        },this);
    }
};
