/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * Swedish translation (utf8-encoding)
 * By Erik Andersson, Monator Technologies
 * 24 April 2007
 * Changed by Cariad, 29 July 2007
 * Updated by Mathias Näsström, ITSAM, 28 june 2010
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Laddar...</div>';

if(Ext.data.Types){
    Ext.data.Types.stripRe = /[\$,%]/g;
}

if(Ext.DataView){
  Ext.DataView.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
  Ext.grid.GridPanel.prototype.ddText = "{0} markerade rad(er){1}";
}

if(Ext.LoadMask){
  Ext.LoadMask.prototype.msg = "Laddar...";
}

Date.monthNames = [
  "Januari",
  "Februari",
  "Mars",
  "April",
  "Maj",
  "Juni",
  "Juli",
  "Augusti",
  "September",
  "Oktober",
  "November",
  "December"
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
  "Söndag",
  "Måndag",
  "Tisdag",
  "Onsdag",
  "Torsdag",
  "Fredag",
  "Lördag"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

Date.parseCodes.S.s = "(?:a|a|e|e)";

if(Ext.MessageBox){
  Ext.MessageBox.buttonText = {
    ok     : "OK",
    cancel : "Avbryt",
    yes    : "Ja",
    no     : "Nej"
  };
}

if(Ext.util.Format){
  Ext.util.Format.date = function(v, format){
    if(!v) return "";
    if(!(v instanceof Date)) v = new Date(Date.parse(v));
    return v.dateFormat(format || "Y-m-d");
  };
}

if(Ext.DatePicker){
  Ext.apply(Ext.DatePicker.prototype, {
    todayText         : "Idag",
    minText           : "Detta datum inträffar före det tidigast tillåtna",
    maxText           : "Detta datum inträffar efter det senast tillåtna",
    disabledDaysText  : "",
    disabledDatesText : "",
    monthNames        : Date.monthNames,
    dayNames          : Date.dayNames,
    nextText          : 'Nästa månad (Ctrl + högerpil)',
    prevText          : 'Föregående månad (Ctrl + vänsterpil)',
    monthYearText     : 'Välj en månad (Ctrl + uppåtpil/neråtpil för att ändra årtal)',
    todayTip          : "{0} (mellanslag)",
    format            : "Y-m-d",
    okText            : "&#160;OK&#160;",
    cancelText        : "Avbryt",
    startDay          : 0
  });
}

if(Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : "Sida",
    afterPageText  : "av {0}",
    firstText      : "Första sidan",
    prevText       : "Föregående sida",
    nextText       : "Nästa sida",
    lastText       : "Sista sidan",
    refreshText    : "Uppdatera",
    displayMsg     : "Visar {0} - {1} av {2}",
    emptyMsg       : 'Det finns inget att visa'
  });
}

if(Ext.form.BasicForm){
    Ext.form.BasicForm.prototype.waitTitle = "Vänligen vänta..."
}

if(Ext.form.Field){
  Ext.form.Field.prototype.invalidText = "Värdet i detta fält är inte tillåtet";
}

if(Ext.form.TextField){
  Ext.apply(Ext.form.TextField.prototype, {
    minLengthText : "Minsta tillåtna längd för detta fält är {0}",
    maxLengthText : "Största tillåtna längd för detta fält är {0}",
    blankText     : "Detta fält är obligatoriskt",
    regexText     : "",
    emptyText     : null
  });
}

if(Ext.form.NumberField){
  Ext.apply(Ext.form.NumberField.prototype, {
    decimalSeparator : ".",
    decimalPrecision : 2,
    minText : "Minsta tillåtna värde för detta fält är {0}",
    maxText : "Största tillåtna värde för detta fält är {0}",
    nanText : "{0} är inte ett tillåtet nummer"
  });
}

if(Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : "Inaktiverad",
    disabledDatesText : "Inaktiverad",
    minText           : "Datumet i detta fält måste inträffa efter {0}",
    maxText           : "Datumet i detta fält måste inträffa före {0}",
    invalidText       : "{0} är inte ett tillåtet datum - datum ska anges i formatet {1}",
    format            : "Y-m-d",
    altFormats        : "m/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d"
  });
}

if(Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : "Laddar...",
    valueNotFoundText : undefined
  });
}

if(Ext.form.VTypes){
  Ext.apply(Ext.form.VTypes, {
    emailText    : 'Detta fält ska innehålla en e-postadress i formatet "användare@domän.se"',
    urlText      : 'Detta fält ska innehålla en länk (URL) i formatet "http:/'+'/www.domän.se"',
    alphaText    : 'Detta fält får bara innehålla bokstäver och "_"',
    alphanumText : 'Detta fält får bara innehålla bokstäver, siffror och "_"'
  });
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'Fyll i den adress du vill länka till:',
    buttonTips : {
      bold : {
        title: 'Fet (Ctrl+B)',
        text: 'Gör den markerade texten fetstilt.',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'Kursiv (Ctrl+I)',
        text: 'Gör den markerade texten kursiv.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'Understruken (Ctrl+U)',
        text: 'Gör den markerade texten understruken.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'Öka storlek',
        text: 'Öka textens storlek.',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: 'Minska storlek',
        text: 'Minska textens storlek.',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'Textmarkeringsfärg',
        text: 'Ändra bakgrundsfärgen för den markerade texten.',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'Teckenfärg',
        text: 'Ändra teckenfärg för den markerade texten.',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'Vänsterjustera',
        text: 'Justera text till vänster.',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'Centrera',
        text: 'Centrera texten.',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'Högerjustera',
        text: 'Justera text till höger.',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: 'Punktlista',
        text: 'Påbörja en lista i punktform.',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: 'Numrerad lista',
        text: 'Påbörja en numrerad lista.',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'Länk',
        text: 'Gör den markerade texten till en länk.',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'Källkodsredigering',
        text: 'Ändra till läge för redigering av HTML-koden.',
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext.grid.GridView){
  Ext.apply(Ext.grid.GridView.prototype, {
    sortAscText  : "Sortera stigande",
    sortDescText : "Sortera fallande",
    columnsText  : "Kolumner"
  });
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : '(Ingen)',
    groupByText    : 'Gruppera efter det här fältet',
    showGroupsText : 'Visa i Grupper'
  });
}

if(Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : "Namn",
    valueText  : "Vädre",
    dateFormat : "Y-m-d",
    trueText: "sant",
    falseText: "falskt"
  });
}

if(Ext.grid.BooleanColumn){
   Ext.apply(Ext.grid.BooleanColumn.prototype, {
      trueText  : "sant",
      falseText : "falskt",
      undefinedText: '&#160;'
   });
}

if(Ext.grid.NumberColumn){
    Ext.apply(Ext.grid.NumberColumn.prototype, {
        format : '0,000.00'
    });
}

if(Ext.grid.DateColumn){
    Ext.apply(Ext.grid.DateColumn.prototype, {
        format : 'Y-m-d'
    });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : "Dra för att ändra storleken.",
    collapsibleSplitTip : "Dra för att ändra storleken. Dubbelklicka för att gömma."
  });
}

if(Ext.form.TimeField){
  Ext.apply(Ext.form.TimeField.prototype, {
    minText : "Tiden i det här fältet måste vara lika med eller senare än {0}",
    maxText : "Tiden i det här fältet måste vara lika med eller tidigare än {0}",
    invalidText : "{0} är inte en giltig tid",
    format : "g:i A",
    altFormats : "g:ia|g:iA|g:i a|g:i A|h:i|g:i|H:i|ga|ha|gA|h a|g a|g A|gi|hi|gia|hia|g|H"
  });
}

if(Ext.form.CheckboxGroup){
  Ext.apply(Ext.form.CheckboxGroup.prototype, {
    blankText : "Du måste välja minst ett objekt i den här gruppen"
  });
}

if(Ext.form.RadioGroup){
  Ext.apply(Ext.form.RadioGroup.prototype, {
    blankText : "Du måste välja ett objekt i den här gruppen"
  });
}
