/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Stores.js 0000 2010-12-29 09:13:00 wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.sieve.cmbFieldStore = new Ext.data.ArrayStore({
	idIndex: 1,
	fields: ['field','value'],
	data: [
	[GO.sieve.lang.subject, 'Subject'],
	[GO.sieve.lang.from, 'From'],
	[GO.sieve.lang.to, 'To'],
	[GO.sieve.lang.size, 'size'],
	[GO.sieve.lang.spamflag, 'X-Spam-Flag'],
	[GO.sieve.lang.custom, 'custom']
	]
});

GO.sieve.cmbOperatorStore = new Ext.data.ArrayStore({
	idIndex: 1,
	fields: ['field', 'value'],
	data:[
	[GO.sieve.lang.contains, 'contains'],
	[GO.sieve.lang.notcontains, 'notcontains'],
	[GO.sieve.lang.is, 'is'],
	[GO.sieve.lang.notis, 'notis'],
	[GO.sieve.lang.exists, 'exists'],
	[GO.sieve.lang.notexists, 'notexists']
	]
});

GO.sieve.cmbActionStore = new Ext.data.ArrayStore({
	idIndex: 1,
	fields: ['field', 'value'],
	data:[
  [GO.sieve.lang.fileintoLabel, 'fileinto'],
  [GO.sieve.lang.copytoLabel, 'copyto'],
	[GO.sieve.lang.redirect_copy_to, 'redirect_copy'],
	[GO.sieve.lang.redirect_to, 'redirect'],
	[GO.sieve.lang.replyToMessage, 'vacation'],
	[GO.sieve.lang.reject, 'reject'],
	[GO.sieve.lang.discard, 'discard'],
	[GO.sieve.lang.stop, 'stop']
	]
});

GO.sieve.cmbUnderOverStore = new Ext.data.ArrayStore({
	idIndex: 1,
	fields: ['field', 'value'],
	data:[
  [GO.sieve.lang.under, 'under'],
  [GO.sieve.lang.over, 'over']
	]
});
