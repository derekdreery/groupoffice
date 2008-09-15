
Ext.namespace('Ext.ux.form');
/**
 * @class Ext.ux.form.TinyMCE
 * @author Maxim Bazhenov (http://www.max-bazhenov.com)
 */
Ext.ux.form.TinyMCE = Ext.extend(Ext.form.TextArea, {

  autoCreate : true,
  
  editor_config : null,
  
  editor : null,
  
  editor_rendered : false,
  
  fs_patch_settings : null,
  
  initComponent : function()
  {
  	// Add events
		this.addEvents({
			"editorcreated": true
		});
		
    Ext.ux.form.TinyMCE.superclass.initComponent.call(this);
  },
  
  onRender : function(ct, position)
  {
    Ext.ux.form.TinyMCE.superclass.onRender.call(this, ct, position);
    
    if (tinymce) {
      var defaults = Ext.apply({}, tinymce.EditorManager.settings);
      defaults.id = this.id;
      this.editor = new tinymce.Editor(
        this.id, 
        Ext.apply(
          defaults, 
          Ext.applyIf(this.editor_config || {}, {add_form_submit_trigger: true})
        )
      );
      
      this.editor.onPreInit.add(this.onEditorPreInit, this);
      this.editor.onPostRender.add(this.onEditorPostRender, this);
      this.editor.onExecCommand.add(this.onEditorExecCommand, this);
      this.editor.onBeforeExecCommand.add(this.onEditorBeforeExecCommand, this);
      this.editor.render();
    }
    
    // Indicate that editor is created
		this.fireEvent( "editorcreated" );
  },
  
  onEditorPreInit : function(ed)
  {
    // Setting custom showMenu handler
    var controls = ed.controlManager.controls, ctrl;
    for (var ctl_id in controls) {
      ctrl = controls[ctl_id];
      if (ctrl.showMenu) {
        ctrl.showMenu = this.myMenuControlShowMenu.createDelegate(
          this, [ctrl, ctrl.showMenu], true
        );
      }
    }
    
    // Setting custom windowManager.open handler
    ed.windowManager.open = this.myEditorWindowManagerOpen.createDelegate(
      this, [ed.windowManager, ed.windowManager.open], true 
    );
  },
  
  onEditorPostRender : function(ed, cm)
  {
    this.editor_rendered = true;
    this.resizeEditor(this.el.getWidth());
    ed.load();
    
    Ext.fly(ed.getContainer()).on('focus', this.onFocus, this);
    Ext.fly(ed.getContainer()).on('blur', this.onBlur, this);
    ed.onActivate.add(this.onFocus, this);
    ed.onDeactivate.add(this.onBlur, this);
    
    // Finding parent form and attaching to beforeaction
    var fp = this.findFormPanel();
    if (fp) {
      fp.getForm().on('beforeaction', this.getValue, this);
    }
  },
  
  onEditorBeforeExecCommand : function(ed, cmd, ui, val)
  {
    // We should intercept the inline editor created with fullscreen plugin.
    if (cmd == 'mceFullScreen' && !this.editor.getParam('fullscreen_new_window')) {
        this.fs_patch_settings = Ext.apply({}, tinymce.EditorManager.settings);
      tinymce.EditorManager.add = this.myEditorManagerAdd.createDelegate(
        this, [tinymce.EditorManager.add], true
      );
    }
  },
  
  onEditorExecCommand : function(ed, cmd, ui, val)
  {
    if (cmd == 'mceFullScreen') {
      var active_win = Ext.WindowMgr.getActive();
      var fs_cont = Ext.fly('mce_fullscreen_container');
      if (fs_cont && active_win) {
        fs_cont.setStyle('z-index', active_win.lastZIndex + 5);
      }
      tinymce.EditorManager.settings = Ext.apply({}, this.fs_patch_settings);
      this.fs_patch_settings = null;
    }
  },
  
  myEditorManagerAdd : function(ed, original)
  {
    ed.onPreInit.add(this.onEditorPreInit, this);
    tinymce.EditorManager.add = original;
    return original.call(tinymce.EditorManager, ed);
  },
  
  myEditorWindowManagerOpen : function(f, p, oWM, oF)
  {
    var active_win = Ext.WindowMgr.getActive();
    if (active_win) {
      oWM.zIndex = active_win.lastZIndex + 10;
    }
    if (oF) {
        return oF.call(oWM, f, p);
    }
    else {
        return oWM.call(p, f);
    }
  },
  
  myMenuControlShowMenu : function(event, ctrl, oF)
  {
    oF.call(ctrl, event);
    var active_win = Ext.WindowMgr.getActive();
    if (active_win) {
      var mEl = Ext.fly('menu_' + ctrl.id) || Ext.fly(ctrl.id + '_menu') || (ctrl.menu ? Ext.fly('menu_' + ctrl.menu.id) : null); 
      if (mEl) { 
        mEl.setStyle('z-index', active_win.lastZIndex + 10);
      }
    }
  },
  
  resizeEditor : function(w, h)
  {
    if (this.editor && this.editor_rendered) {
      var cel = Ext.fly(this.editor.getContainer());
      if (w && h) {
        if (!Ext.isBorderBox) {
          w -= cel.getBorderWidth('lr');
          h -= cel.getBorderWidth('tb');
        }
        cel.setSize(w, h);
      }
      else if (w) {
        if (!Ext.isBorderBox) {
          w -= cel.getBorderWidth('lr');
        }
        cel.setWidth(w);
      }
      else if (h) {
        if (!Ext.isBorderBox) {
          h -= cel.getBorderWidth('tb');
        }
        cel.setHeight(h);
      }
    }
  },
  
  onResize : function( aw, ah ){
		if( this.rendered ){
			this.withEd( function() {
			
				if( Ext.type( aw ) != "number" ) aw = this.el.getWidth();
				if( Ext.type( ah ) != "number" ) ah = this.el.getHeight();
				
				this.editor.theme.resizeTo( aw, ah );
			});
		}
	},
	
	withEd: function( func ){
		
		// If editor is not created yet, reschedule this call.
		if( !this.editor ) this.on( 
			"editorcreated", 
			function() { this.withEd( func ) }, 
			this );

		// Else if editor is created and initialized
		else if( this.editor.initialized ) func.call( this );
		
		// Else if editor is created but not initialized yet.
		else this.editor.onInit.add( function(){ func.defer( 10, this ); }.createDelegate( this ));
	},
  
  /*setSize : function(w, h) 
  {
    if (this.editor && this.editor_rendered) {
      this.resizeEditor(w || undefined, h || undefined);
    }
    else {
      return Ext.ux.form.TinyMCE.superclass.setSize.call(this, w, h);
    }
  },*/
  
  getValue : function()
  {
    if (this.editor && this.editor_rendered) {
      this.editor.save();
    }
    return Ext.ux.form.TinyMCE.superclass.getValue.call(this);
  },
  
  setValue : function(value)
  {
    Ext.ux.form.TinyMCE.superclass.setValue.call(this, value);
    if (this.editor && this.editor_rendered) {
      this.editor.load();
    }
  },
  
  focus : function()
  {
    if (this.editor) {
      this.editor.focus();
    }
    else {
      Ext.ux.form.TinyMCE.superclass.focus.call(this);
    }
  },
  
  onFocus : function()
  {
    Ext.ux.form.TinyMCE.superclass.onFocus.call(this);
    if (this.editor && this.editor_rendered) {
      Ext.fly(this.editor.getContainer()).addClass(this.focusClass);
    }
  },
  
  onBlur : function()
  {
    Ext.ux.form.TinyMCE.superclass.onBlur.call(this);
    if (this.editor && this.editor_rendered) {
      Ext.fly(this.editor.getContainer()).removeClass(this.focusClass);
    }
  },
  
  markInvalid : function(msg)
  {
    Ext.ux.form.TinyMCE.superclass.markInvalid.call(this, msg);
    if (this.editor && this.editor_rendered) {
      Ext.fly(this.editor.getContainer()).addClass(this.invalidClass);
    }
  },
  
  clearInvalid : function(msg)
  {
    Ext.ux.form.TinyMCE.superclass.clearInvalid.call(this);
    if (this.editor && this.editor_rendered) {
      Ext.fly(this.editor.getContainer()).removeClass(this.invalidClass);
    }
  },
  
  alignErrorIcon : function() 
  {
    if (this.editor && this.editor_rendered) {
      this.errorIcon.alignTo(this.editor.getContainer(), 'tl-tr', [2, 0]);
    }
    else {
      Ext.ux.form.TinyMCE.superclass.alignErrorIcon.call(this);
    }
  },
  
  destroy : function()
  {
    if (this.editor) {
      this.editor.remove();
      this.editor = null;
      this.editor_rendered = false;
    }
    Ext.ux.form.TinyMCE.superclass.destroy.call(this);
  },
  
  findFormPanel : function()
  {
    var owner = this.ownerCt;
    while (owner && owner.xtype != 'form') {
      owner = owner.ownerCt || null;
    }
    return owner;
  }
});

Ext.reg('xtinymce', Ext.ux.form.TinyMCE); 
tinyMCE.init({
		"debug":false,
		"mode":"none",
		"theme":"advanced",
		"strict_loading_mode":true,
		"auto_reset_designmode":true,
		"browsers":"msie,gecko,opera,safari",
		"button_tile_map":true,
		"language":"en",
		"docs_language":"en",
		"object_resizing":false,
		"table_inline_editing":false,
		"plugins":"preview,fullscreen,inlinepopups,style,media,advlink,advimage,paste,searchreplace,table,xhtmlxtras",
		"apply_source_formatting":true,
		"cleanup":true,
		"cleanup_on_startup":true,
		"convert_fonts_to_spans":true,
		"convert_newlines_to_brs":false,
		"doctype":"<!DOCTYPE html PUBLIC \"-\/\/W3C\/\/DTD XHTML 1.0 Strict\/\/EN\" \"http:\/\/www.w3.org\/TR\/xhtml1\/DTD\/xhtml1-strict.dtd\">",
		"entity_encoding":"raw",
		"fix_content_duplication":true,
		"fix_list_elements":true,
		"fix_table_elements":true,
		"force_p_newlines":true,
		"force_br_newlines":false,
		"force_hex_style_colors":true,
		"forced_root_block":"p",
		"inline_styles":true,
		"remove_trailing_nbsp":false,
		"trim_span_elements":true,
		"verify_css_classes":false,
		"verify_html":false,
		"convert_urls":true,
		"relative_urls":false,
		"remove_script_host":true,
		"width":"100%",
		"visual":true,
		theme_advanced_resizing : false,
		"theme_advanced_layout_manager":"SimpleLayout",
		"theme_advanced_toolbar_location":"top",
		"theme_advanced_toolbar_align":"left",
		"theme_advanced_statusbar_location":"bottom",
		"theme_advanced_resizing":true,
		"theme_advanced_resize_horizontal":false,
		"theme_advanced_resizing_use_cookie":false,
		"theme_advanced_buttons1":"formatselect,styleselect,|,preview,code,|,undo,redo,|,cut,copy,pastetext,pasterword,selectall,|,search,replace,|,cleanup,|,visualaid,|,help",
		"theme_advanced_buttons2":"bold,italic,underline,strikethrough,|,forecolor,backcolor,|,styleprops,removeformat,|,tablecontrols,|,fullscreen",
		"theme_advanced_buttons3":"justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,anchor,|,image,media,|,sub,sup,|,charmap,hr,|,cite,ins,del,abbr,acronym,attribs"
	});