Ext.namespace("Ext.ux.layout");

Ext.ux.layout.TableFormLayout = Ext.extend(Ext.layout.TableLayout, {
    monitorResize: true,
    trackLabels: Ext.layout.FormLayout.prototype.trackLabels,
    setContainer: function() {
        Ext.layout.FormLayout.prototype.setContainer.apply(this, arguments);
        this.currentRow = 0;
        this.currentColumn = 0;
        this.cells = [];
    },
    renderItem : function(c, position, target) {
        if (c && !c.rendered) {
            var cell = Ext.get(this.getNextCell(c));
            cell.addClass("x-table-layout-column-" + this.currentColumn);
            Ext.layout.FormLayout.prototype.renderItem.call(this, c, 0, cell);
        }
    },
    getLayoutTargetSize : Ext.layout.AnchorLayout.prototype.getLayoutTargetSize,
    parseAnchor : Ext.layout.AnchorLayout.prototype.parseAnchor,
    getTemplateArgs : Ext.layout.FormLayout.prototype.getTemplateArgs,
    isValidParent : Ext.layout.FormLayout.prototype.isValidParent,
    onRemove : Ext.layout.FormLayout.prototype.onRemove,
    isHide : Ext.layout.FormLayout.prototype.isHide,
    onFieldShow : Ext.layout.FormLayout.prototype.onFieldShow,
    onFieldHide : Ext.layout.FormLayout.prototype.onFieldHide,
    adjustWidthAnchor : Ext.layout.FormLayout.prototype.adjustWidthAnchor,
    adjustHeightAnchor : Ext.layout.FormLayout.prototype.adjustHeightAnchor,
    getLabelStyle : Ext.layout.FormLayout.prototype.getLabelStyle,
    onLayout : function(ct, target) {
        Ext.ux.layout.TableFormLayout.superclass.onLayout.call(this, ct, target);
        if (!target.hasClass("x-table-form-layout-ct")) {
            target.addClass("x-table-form-layout-ct");
        }
        var viewSize = this.getLayoutTargetSize();
        var aw, ah;
        if (ct.anchorSize) {
            if (typeof ct.anchorSize == "number") {
                aw = ct.anchorSize;
            } else {
                aw = ct.anchorSize.width;
                ah = ct.anchorSize.height;
            }
        } else {
            aw = ct.initialConfig.width;
            ah = ct.initialConfig.height;
        }
        var cs = this.getRenderedItems(ct), len = cs.length, i, j, c, a, cw, ch;
        var x, w, h, col, colWidth, offset;
        for (i = 0; i < len; i++) {
            c = cs[i];
            // get table cell
            x = c.getEl().parent(".x-table-layout-cell");
            if (this.columnWidths) {
                // get column
                col = parseInt(x.dom.className.replace(/.*x\-table\-layout\-column\-([\d]+).*/, "$1"));
                // get cell width (based on column widths)
                colWidth = 0, offset = 0;
                for (j = col; j < (col + (c.colspan || 1)); j++) {
                    colWidth += this.columnWidths[j];
                    offset += 10;
                }
                w = (viewSize.width * colWidth) - offset;
            } else {
                // get cell width
                w = (viewSize.width / this.columns) * (c.colspan || 1);
            }
            // set table cell width
            x.setWidth(w);
            // get cell width (-10 for spacing between cells) & height to be base width of anchored component
            w = w - 10;
            h = x.getHeight();
            // perform anchoring
            if (c.anchor) {
                a = c.anchorSpec;
                if (!a) {
                    var vs = c.anchor.split(" ");
                    c.anchorSpec = a = {
                        right: this.parseAnchor(vs[0], c.initialConfig.width, aw),
                        bottom: this.parseAnchor(vs[1], c.initialConfig.height, ah)
                    };
                }
                cw = a.right ? this.adjustWidthAnchor(a.right(w), c) : undefined;
                ch = a.bottom ? this.adjustHeightAnchor(a.bottom(h), c) : undefined;
                if (cw || ch) {
                    c.setSize(cw || undefined, ch || undefined);
                }
            }
        }
    }
});

Ext.Container.LAYOUTS["tableform"] = Ext.ux.layout.TableFormLayout;