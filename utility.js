function getDateString(date, roundMinutes = true)
{
	if(!date || !date.getTime())
		return "";

	let date2 = new Date(date);
	if(roundMinutes)
		date2.setSeconds(0, 0);

	date2.setMinutes(date2.getMinutes() - date2.getTimezoneOffset());
	return date2.toISOString().split(".")[0]; // we don't need milliseconds or timezone info
}

function stripHTML(str)
{
	let final = str;
	final = final.replace(/&/g, "&amp;");
	final = final.replace(/</g, "&lt;");
	final = final.replace(/>/g, "&gt;");
	final = final.replace(/"/g, "&quot;");
	return final;
}