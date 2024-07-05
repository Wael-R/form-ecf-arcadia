function getDate(props)
{
	const {day, month, year, hours, minutes} = props;

	return new Date(year.value, month.value, day.value, hours.value, minutes.value, 0, 0);
}

function setDate(props, date)
{
	const {day, month, year, hours, minutes, min, max, onChange} = props;

	let oldDate = props._oldDate;

	if(oldDate)
	{
		date.setSeconds(0, 0);
		oldDate.setSeconds(0, 0);

		if(oldDate.getTime() == date.getTime())
			return false;
	}

	if(date < min)
		date = min;
	else if(date > max)	
		date = max;

	year.value = date.getFullYear();
	month.value = date.getMonth();
	day.value = date.getDate();
	hours.value = date.getHours();
	minutes.value = date.getMinutes();

	updateDatePickerOptions(props);

	if(props._oldDate && onChange)
		onChange(props._oldDate, getDate(props));

	props._oldDate = date;

	return true;
}

function setDateMinimum(props, min)
{
	min.setSeconds(0, 0);

	if(!min)
		min = new Date(0);

	if(min > props.max)
		min = props.max;

	props.min = min;
	setDate(props, getDate(props));
}

function setDateMaximum(props, max)
{
	max.setSeconds(0, 0);

	if(!max)
		max = new Date();

	if(max < props.min)
		max = props.min;

	props.max = max;
	setDate(props, getDate(props));
}

function updateDatePickerOptions(props)
{
	const {day, month, year, hours, minutes, min, max} = props;
	const monthDays = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

	let currDate = getDate(props);
	let currMonth = currDate.getMonth();
	let maxDays = monthDays[currMonth];

	let old = day.value;

	day.options.length = 0;

	for(let i = 1; i <= maxDays; i++)
	{
		let option = document.createElement("option");
		option.value = i;
		option.innerHTML = i;

		day.options.length++;
		day.options[day.options.length - 1] = option;
	}

	day.value = old;

	if(day.value == "")
		day.value = 1;

	applyDateLimits(year, currDate, min, max, (date, option) => {date.setFullYear(option.value, 0, 1); date.setHours(0, 0)});
	applyDateLimits(month, currDate, min, max, (date, option) => date.setMonth(option.value, 1));
	applyDateLimits(day, currDate, min, max, (date, option) => date.setDate(option.value));
	applyDateLimits(hours, currDate, min, max, (date, option) => date.setHours(option.value));
	applyDateLimits(minutes, currDate, min, max, (date, option) => date.setMinutes(option.value));
}

function applyDateLimits(select, now, min, max, dateCallback)
{
	for(const option of select.options)
	{
		let date = new Date(now);
		dateCallback(date, option);

		if(date < min || date > max)
			option.disabled = true;
		else
			option.disabled = false;
	}
}

function setupDatePicker(props)
{
	const {button, day, month, year, hours, minutes} = props;

	props._oldDate = null;
	updateDatePickerOptions(props);
	setDate(props, new Date(0));

	day.addEventListener("change", (evt) => {
		let date = getDate(props);
		date.setDate(day.value);
		setDate(props, date);
	});

	month.addEventListener("change", (evt) => {
		let date = getDate(props);
		date.setMonth(month.value);
		setDate(props, date);
	});

	year.addEventListener("change", (evt) => {
		let date = getDate(props);
		date.setFullYear(year.value);
		setDate(props, date);
	});

	hours.addEventListener("change", (evt) => {
		let date = getDate(props);
		date.setHours(hours.value);
		setDate(props, date);
	});

	minutes.addEventListener("change", (evt) => {
		let date = getDate(props);
		date.setMinutes(minutes.value);
		setDate(props, date);
	});
}