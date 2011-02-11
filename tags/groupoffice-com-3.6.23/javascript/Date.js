Date.prototype.getLastSunday = function()
{
	//Calculate the first day of the week		
	var weekday = this.getDay();
	return this.add(Date.DAY, -weekday);
}