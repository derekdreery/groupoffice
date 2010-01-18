/**
 * List compiled by mystix on the extjs.com forums.
 * Thank you Mystix!
 *
 * English Translations
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">กำลังโหลด...</div>';

if(Ext.View){
  Ext.View.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
  Ext.grid.GridPanel.prototype.ddText = "{0} เลือกแถว(s)";
}

if(Ext.TabPanelItem){
  Ext.TabPanelItem.prototype.closeText = "ปิดแท็บนี้";
}

if(Ext.LoadMask){
  Ext.LoadMask.prototype.msg = "กำลังโหลด...";
}

Date.monthNames = [
  "มกราคม",
  "กุมภาพันธ์",
  "มีนาคม",
  "เมษายน",
  "พฤษภาคม",
  "มิถุนายน",
  "กรกฏาคม",
  "สิงหาคม",
  "กันยายน",
  "ตุลาคม",
  "พฤศจิกายน",
  "ธันวาคม"
];

Date.getShortMonthName = function(month) {
  return Date.monthNames[month].substring(0, 3);
};

Date.monthNumbers = {
  Jan : 0,
  Feb : 1,
  Mar : 2,
  Apr : 3,
  May : 4,
  Jun : 5,
  Jul : 6,
  Aug : 7,
  Sep : 8,
  Oct : 9,
  Nov : 10,
  Dec : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};

Date.dayNames = [
  "อาทิตย์",
  "จันทร์",
  "อังคาร",
  "พุธ",
  "พฤหัสบดี",
  "ศุกร์",
  "เสาร์"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

if(Ext.MessageBox){
  Ext.MessageBox.buttonText = {
    ok     : "ตกลง",
    cancel : "ยกเลิก",
    yes    : "ใช่",
    no     : "ไม่"
  };
}

if(Ext.util.Format){
  Ext.util.Format.date = function(v, format){
    if(!v) return "";
    if(!(v instanceof Date)) v = new Date(Date.parse(v));
    return v.dateFormat(format || "m/d/Y");
  };
}

if(Ext.DatePicker){
  Ext.apply(Ext.DatePicker.prototype, {
    todayText         : "วันนี้",
    minText           : "This date is before the minimum date",
    maxText           : "This date is after the maximum date",
    disabledDaysText  : "",
    disabledDatesText : "",
    monthNames        : Date.monthNames,
    dayNames          : Date.dayNames,
    nextText          : 'เดือนถัดไป (Control+Right)',
    prevText          : 'เดือนก่อนหน้านี้ (Control+Left)',
    monthYearText     : 'เลือกเดือน (Control+Up/Down to move years)',
    todayTip          : "{0} (Spacebar)",
    format            : "m/d/y",
    okText            : "&#160;OK&#160;",
    cancelText        : "ยกเลิก",
    startDay          : 0
  });
}

if(Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : "หน้า",
    afterPageText  : "จาก {0}",
    firstText      : "หน้าแรก",
    prevText       : "ก่อนหน้า",
    nextText       : "หน้าถัดไป",
    lastText       : "หน้าล่าสุด",
    refreshText    : "รีเฟรซ",
    displayMsg     : "แสดงผล {0} - {1} of {2}",
    emptyMsg       : 'ไม่มีข้อมูลแสดง'
  });
}

if(Ext.form.Field){
  Ext.form.Field.prototype.invalidText = "ค่าในฟิล์ดนี้ไม่ถูกต้อง";
}

if(Ext.form.TextField){
  Ext.apply(Ext.form.TextField.prototype, {
    minLengthText : "ความยาวตัวอักษรอย่างน้อยคือ  {0}",
    maxLengthText : "ความยาวตัวอักษรมากสุดคือ {0}",
    blankText     : "กรุณาระบุ",
    regexText     : "",
    emptyText     : null
  });
}

if(Ext.form.NumberField){
  Ext.apply(Ext.form.NumberField.prototype, {
    minText : "ระบุค่าต่ำสุดคือ {0}",
    maxText : "ระบุค่าสูงสุดคือ {0}",
    nanText : "{0} ไม่ใช่ตัวเลข"
  });
}

if(Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : "แสดงผล",
    disabledDatesText : "แสดงผล",
    minText           : "ข้อมูลที่ระบุจะต้องหลังจาก {0}",
    maxText           : "ข้อมูลที่ระบุจะต้องก่อน {0}",
    invalidText       : "{0} ข้อมูลไม่ถูกต้อง - จะต้องเป็นรูปแบบ {1}",
    format            : "m/d/y",
    altFormats        : "m/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d"
  });
}

if(Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : "กำลังโหลด...",
    valueNotFoundText : undefined
  });
}

if(Ext.form.VTypes){
  Ext.apply(Ext.form.VTypes, {
    emailText    : 'จะต้องระบุที่อยู่อีเมล์ในรูปแบบ "user@domain.com"',
    urlText      : 'จะต้องระบุ URL ในรูปแบบ "http:/'+'/www.domain.com"',
    alphaText    : 'สามารถระบุตัวอักษรและเครื่องหมาย _',
    alphanumText : 'สามารถระบุตัวอักษร ตัวเลข และเครื่องหมาย _'
  });
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'กรุณาระบุ URL เพื่อเชื่อมไปยังลิงค์:',
    buttonTips : {
      bold : {
        title: 'ตัวหนา (Ctrl+B)',
        text: 'เลือกตัวอักษรหนา',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'ตัวเอียง (Ctrl+I)',
        text: 'เลือกตัวอักษรเอียง.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'ขีดเส้นใต้ (Ctrl+U)',
        text: 'เลือกตัวหนังสือขีดเส้นใต้.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'ขยายตัวอักษร',
        text: 'เพิ่มขนาดตัวอักษร',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: 'ลดตัวอักษร',
        text: 'ลดขนาดตัวอักษร',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'ไฮไลท์สี',
        text: 'เปลี่ยนพื้นหลังตัวอักษรเพื่อกำหนดสี',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'สีตัวอักษร',
        text: 'เปลี่ยนสีตัวอักษร',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'ชิดขอบซ้าย',
        text: 'ชิดขอบซ้าย',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'ตรงกลาง',
        text: 'ตรงกลาง',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'ชิดขวา',
        text: 'ชิดขวา',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: 'บูลเลท',
        text: 'บูลเลท',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: 'ตัวเลขรายการ',
        text: 'เริ่มต้นตัวเลขรายการ',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'เชื่อมโยง',
        text: 'สร้างการเชื่อมโยงจากตัวอักษร',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'แก้ไขโค้ด',
        text: 'สลับสู่โหมดแก้ไขโคด',
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext.form.BasicForm){
  Ext.form.BasicForm.prototype.waitTitle = "กรุณารอสักครู่...";
}

if(Ext.grid.GridView){
  Ext.apply(Ext.grid.GridView.prototype, {
    sortAscText  : "ลำดับจากบนลงล่าง",
    sortDescText : "ลำดับจากล่างขึ้นบน",
    lockText     : "ล็อคคอลั่ม",
    unlockText   : "ปลดล็อคคอลั่ม",
    columnsText  : "คอลั่ม"
  });
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : '(None)',
    groupByText    : 'Group By This Field',
    showGroupsText : 'Show in Groups'
  });
}

if(Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : "Name",
    valueText  : "Value",
    dateFormat : "m/j/Y"
  });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : "ลากเม้าเพื่อปรับขนาด",
    collapsibleSplitTip : "ลากเม้าเพื่อปรับขนาด และดับเบิ้ลคลิ๊กเพื่อซ้อน"
  });
}
