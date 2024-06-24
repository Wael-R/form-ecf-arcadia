function stripHTML(str)
{
	let final = str;
	final = final.replace(/&/g, "&amp;");
	final = final.replace(/</g, "&lt;");
	final = final.replace(/>/g, "&gt;");
	final = final.replace(/"/g, "&quot;");
	return final;
}