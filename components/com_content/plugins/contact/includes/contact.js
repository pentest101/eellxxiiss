/*
Elxis CMS - plugin Contact
Created by Ioannis Sannos
Copyright (c) 2006-2016 elxis.org
http://www.elxis.org
*/

/* ADD CONTACT CODE */
function addContactCode() {
	var cmail = document.getElementById('contact_email').value;
	if ((cmail == '') || (cmail.indexOf('\@') == -1)) { return false; }
	var pcode = '{contact}'+cmail+'{/contact}';
	addPluginCode(pcode);
}


function plgContactValidate(prf) {
	if (document.getElementById(prf+'firstname').value == '') {
		elxFocus(prf+'firstname');
		return false;
	}
	if (document.getElementById(prf+'lastname').value == '') {
		elxFocus(prf+'lastname');
		return false;
	}

	var formelements = ['address', 'city', 'postalcode', 'phone', 'mobile', 'email', 'website', 'field1', 'field2', 'field3', 'message', 'conseccode'];
	for (var i = 0; i < formelements.length; i++) {
		var idx = prf+''+formelements[i];
		if (!document.getElementById(idx)) { continue; }
		if (document.getElementById(idx).hasAttribute('required')) {
			if (document.getElementById(idx).value == '') {
				elxFocus(idx);
				return false;
			}
		}
		if ((formelements[i] == 'phone') || (formelements[i] == 'mobile')) {
			if (document.getElementById(idx).value != '') {
				if (!elxValidateNumericBox(idx, true)) {
					elxFocus(idx);
					return false;
				}
			}
		}
		if (formelements[i] == 'email') {
			if (!elxValidateEmailBox(idx, false)) {
				elxFocus(idx);
				return false;
			}
		}
		if (formelements[i] == 'website') {
			if (document.getElementById(idx).value != '') {
				if (!elxValidateURLBox(idx, true)) {
					elxFocus(idx);
					return false;
				}
			}
		}
		if (formelements[i] == 'conseccode') {
			if (!elxValidateNumericBox(idx, false)) {
				elxFocus(idx);
				return false;
			}
		}
	}

	document.getElementById(prf+'contactform').submit();
}
