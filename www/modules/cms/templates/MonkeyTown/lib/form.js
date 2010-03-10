// validates that the field value string has one or more characters in it
function isNotEmpty(elem, name) {
    var str = elem.value;
    var re = '';
    if(str == '') {
        alert("Make sure you fill out the " + name + " field!");
        return false;
    } else {
        return true;
    }
}

//validates that the entry is a positive or negative number
function isNumber(elem) {
    var str = elem.value;
    var re = '/^[-]?\d*\.?\d*$/';
    str = str.toString( );
    if (!str.match(re)) {
        alert("Enter only numbers into the field.");
        return false;
    }
    return true;
}

// validate that the user has checked one of the radio buttons
function isValidRadio(radio) {
    var valid = false;
    for (var i = 0; i < radio.length; i++) {
        if (radio[i].checked) {
            return true;
        }
    }
    alert("Select a party!");
    return false;
}

function isEMailAddr(elem) {
    var str = elem.value;
    var re = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
    if (!str.match(re)) {
        alert("Check your Email-address");
        return false;
    } else {
        return true;
    }
}

function EMailMatch(elem1, elem2) {
    var str1 = elem1.value;
    var str2 = elem2.value;
    if (str1 != str2) {
        alert("Email-addresses don't match!");
        return false;
    } else {
        return true;
    }
}

function DateCheck(day, month, year){
		var currentTime = new Date()
		var curmonth = currentTime.getMonth() + 1
		var curday = currentTime.getDate()
		var curyear = currentTime.getFullYear()
	if (year == curyear){
		if (month == curmonth){
			if (day > curday){
				var ok = true;
			}
		}
	}
	if(year > curyear){
		var ok = true;
	}
	if(month > curmonth){
		var ok = true;
	}
	if (!ok == true){
		alert('The date you entered ');
		return false;
	}else{
		return true;
	}
}

function validateForm(form) {
    if (isNotEmpty(form.naam, 'NAME')) {
    if (isNotEmpty(form.leeftijd, 'AGE')) {
    if (isNotEmpty(form.tel, 'TELEPHONE NUMBER')) {
    if (isNotEmpty(form.hoeveel, 'HOW MANY KIDS')) {
    if (isNotEmpty(form.email, 'EMAIL')) {
    if (EMailMatch(form.email, form.email2)) {
    if (isEMailAddr(form.email, 'EMAIL')) {
    if (DateCheck(form.dag.value, form.maand.value, form.jaar.value)) {
    if (isValidRadio(form.party, 'PARTY')) {
       return true;
    }
    }
    }
    }
    }
    }
    }
    }
    }
    return false;
}

