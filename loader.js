// * placeholder script to load in entire html files inside a div (to be replaced with php...)

function loadHTMLAt(url, div)
{
	let request = new XMLHttpRequest();

	request.onload = (evt) => {
		div.outerHTML = request.responseText;
	};

	request.open("GET", url);
	request.send();
}

let elems = document.querySelectorAll(".js-autoload");

elems.forEach(elem => {
	let src = elem.innerHTML;
	elem.innerHTML = "";
	loadHTMLAt(src, elem);
});