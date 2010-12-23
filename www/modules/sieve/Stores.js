GO.sieve.cmbFieldStore = new Ext.data.ArrayStore({
	idIndex: 1,
	fields: ['field','value'],
	data: [
	[GO.sieve.lang.subject, 'Subject'],
	[GO.sieve.lang.from, 'From'],
	[GO.sieve.lang.to, 'To'],
	[GO.sieve.lang.size, 'size'],
	//['...', 'Custom'],
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
  [GO.sieve.lang.fileinto, 'fileinto'],
  [GO.sieve.lang.copyto, 'copyto'],
	[GO.sieve.lang.redirect, 'redirect'],
	[GO.sieve.lang.redirect_to, 'redirect_copy'],
	[GO.sieve.lang.vacation, 'vacation'],
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
