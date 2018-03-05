/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/app.js":
/***/ (function(module, exports, __webpack_require__) {

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// call plugin 
// inputmask
__webpack_require__("./resources/assets/thunderlab/js/inputmask/jquery.inputmask.bundle.min.js");
// ajax
__webpack_require__("./resources/assets/thunderlab/js/ajax/ajax.js");
__webpack_require__("./resources/assets/thunderlab/js/ux/number_format.js");

// call module
// module inputmask
__webpack_require__("./resources/assets/thunderlab/js/inputmask/module-inputmask.js");

// UX
__webpack_require__("./resources/assets/thunderlab/js/ux.js");

/***/ }),

/***/ "./resources/assets/js/flatpickr.js":
/***/ (function(module, exports, __webpack_require__) {

var _typeof2 = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _extends = Object.assign || function (target) {
	for (var i = 1; i < arguments.length; i++) {
		var source = arguments[i];for (var key in source) {
			if (Object.prototype.hasOwnProperty.call(source, key)) {
				target[key] = source[key];
			}
		}
	}return target;
};

var _typeof = typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol" ? function (obj) {
	return typeof obj === "undefined" ? "undefined" : _typeof2(obj);
} : function (obj) {
	return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj === "undefined" ? "undefined" : _typeof2(obj);
};

/*! flatpickr v3.0.6, @license MIT */
function FlatpickrInstance(element, config) {
	var self = this;

	self._ = {};
	self._.afterDayAnim = afterDayAnim;
	self._bind = bind;
	self._compareDates = compareDates;
	self._setHoursFromDate = setHoursFromDate;
	self.changeMonth = changeMonth;
	self.changeYear = changeYear;
	self.clear = clear;
	self.close = close;
	self._createElement = createElement;
	self.destroy = destroy;
	self.isEnabled = isEnabled;
	self.jumpToDate = jumpToDate;
	self.open = open;
	self.redraw = redraw;
	self.set = set;
	self.setDate = setDate;
	self.toggle = toggle;

	function init() {
		self.element = self.input = element;
		self.instanceConfig = config || {};
		self.parseDate = FlatpickrInstance.prototype.parseDate.bind(self);
		self.formatDate = FlatpickrInstance.prototype.formatDate.bind(self);

		setupFormats();
		parseConfig();
		setupLocale();
		setupInputs();
		setupDates();
		setupHelperFunctions();

		self.isOpen = false;

		self.isMobile = !self.config.disableMobile && !self.config.inline && self.config.mode === "single" && !self.config.disable.length && !self.config.enable.length && !self.config.weekNumbers && /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

		if (!self.isMobile) build();

		bindEvents();

		if (self.selectedDates.length || self.config.noCalendar) {
			if (self.config.enableTime) {
				setHoursFromDate(self.config.noCalendar ? self.latestSelectedDateObj || self.config.minDate : null);
			}
			updateValue(false);
		}

		self.showTimeInput = self.selectedDates.length > 0 || self.config.noCalendar;

		if (self.config.weekNumbers) {
			self.calendarContainer.style.width = self.daysContainer.offsetWidth + self.weekWrapper.offsetWidth + "px";
		}

		if (!self.isMobile) positionCalendar();

		triggerEvent("Ready");
	}

	/**
  * Binds a function to the current flatpickr instance
  * @param {Function} fn the function
  * @return {Function} the function bound to the instance
  */
	function bindToInstance(fn) {
		return fn.bind(self);
	}

	/**
  * The handler for all events targeting the time inputs
  * @param {Event} e the event - "input", "wheel", "increment", etc
  */
	function updateTime(e) {
		if (self.config.noCalendar && !self.selectedDates.length)
			// picking time only
			self.selectedDates = [self.now];

		timeWrapper(e);

		if (!self.selectedDates.length) return;

		if (!self.minDateHasTime || e.type !== "input" || e.target.value.length >= 2) {
			setHoursFromInputs();
			updateValue();
		} else {
			setTimeout(function () {
				setHoursFromInputs();
				updateValue();
			}, 1000);
		}
	}

	/**
  * Syncs the selected date object time with user's time input
  */
	function setHoursFromInputs() {
		if (!self.config.enableTime) return;

		var hours = (parseInt(self.hourElement.value, 10) || 0) % (self.amPM ? 12 : 24),
		    minutes = (parseInt(self.minuteElement.value, 10) || 0) % 60,
		    seconds = self.config.enableSeconds ? (parseInt(self.secondElement.value, 10) || 0) % 60 : 0;

		if (self.amPM !== undefined) hours = hours % 12 + 12 * (self.amPM.textContent === "PM");

		if (self.minDateHasTime && compareDates(self.latestSelectedDateObj, self.config.minDate) === 0) {

			hours = Math.max(hours, self.config.minDate.getHours());
			if (hours === self.config.minDate.getHours()) minutes = Math.max(minutes, self.config.minDate.getMinutes());
		}

		if (self.maxDateHasTime && compareDates(self.latestSelectedDateObj, self.config.maxDate) === 0) {
			hours = Math.min(hours, self.config.maxDate.getHours());
			if (hours === self.config.maxDate.getHours()) minutes = Math.min(minutes, self.config.maxDate.getMinutes());
		}

		setHours(hours, minutes, seconds);
	}

	/**
  * Syncs time input values with a date
  * @param {Date} dateObj the date to sync with
  */
	function setHoursFromDate(dateObj) {
		var date = dateObj || self.latestSelectedDateObj;

		if (date) setHours(date.getHours(), date.getMinutes(), date.getSeconds());
	}

	/**
  * Sets the hours, minutes, and optionally seconds
  * of the latest selected date object and the
  * corresponding time inputs
  * @param {Number} hours the hour. whether its military
  *                 or am-pm gets inferred from config
  * @param {Number} minutes the minutes
  * @param {Number} seconds the seconds (optional)
  */
	function setHours(hours, minutes, seconds) {
		if (self.selectedDates.length) {
			self.latestSelectedDateObj.setHours(hours % 24, minutes, seconds || 0, 0);
		}

		if (!self.config.enableTime || self.isMobile) return;

		self.hourElement.value = self.pad(!self.config.time_24hr ? (12 + hours) % 12 + 12 * (hours % 12 === 0) : hours);

		self.minuteElement.value = self.pad(minutes);

		if (!self.config.time_24hr) self.amPM.textContent = hours >= 12 ? "PM" : "AM";

		if (self.config.enableSeconds === true) self.secondElement.value = self.pad(seconds);
	}

	/**
  * Handles the year input and incrementing events
  * @param {Event} event the keyup or increment event
  */
	function onYearInput(event) {
		var year = event.target.value;
		if (event.delta) year = (parseInt(year) + event.delta).toString();

		if (year.length === 4 || event.key === "Enter") {
			self.currentYearElement.blur();
			if (!/[^\d]/.test(year)) changeYear(year);
		}
	}

	/**
  * Essentially addEventListener + tracking
  * @param {Element} element the element to addEventListener to
  * @param {String} event the event name
  * @param {Function} handler the event handler
  */
	function bind(element, event, handler) {
		if (event instanceof Array) return event.forEach(function (ev) {
			return bind(element, ev, handler);
		});

		if (element instanceof Array) return element.forEach(function (el) {
			return bind(el, event, handler);
		});

		element.addEventListener(event, handler);
		self._handlers.push({ element: element, event: event, handler: handler });
	}

	/**
  * A mousedown handler which mimics click.
  * Minimizes latency, since we don't need to wait for mouseup in most cases.
  * Also, avoids handling right clicks.
  *
  * @param {Function} handler the event handler
  */
	function onClick(handler) {
		return function (evt) {
			return evt.which === 1 && handler(evt);
		};
	}

	/**
  * Adds all the necessary event listeners
  */
	function bindEvents() {
		self._handlers = [];
		self._animationLoop = [];
		if (self.config.wrap) {
			["open", "close", "toggle", "clear"].forEach(function (evt) {
				Array.prototype.forEach.call(self.element.querySelectorAll("[data-" + evt + "]"), function (el) {
					return bind(el, "mousedown", onClick(self[evt]));
				});
			});
		}

		if (self.isMobile) return setupMobile();

		self.debouncedResize = debounce(onResize, 50);
		self.triggerChange = function () {
			triggerEvent("Change");
		};
		self.debouncedChange = debounce(self.triggerChange, 300);

		if (self.config.mode === "range" && self.daysContainer) bind(self.daysContainer, "mouseover", function (e) {
			return onMouseOver(e.target);
		});

		bind(window.document.body, "keydown", onKeyDown);

		if (!self.config.static) bind(self._input, "keydown", onKeyDown);

		if (!self.config.inline && !self.config.static) bind(window, "resize", self.debouncedResize);

		if (window.ontouchstart !== undefined) bind(window.document, "touchstart", documentClick);

		bind(window.document, "mousedown", onClick(documentClick));
		bind(self._input, "blur", documentClick);

		if (self.config.clickOpens === true) {
			bind(self._input, "focus", self.open);
			bind(self._input, "mousedown", onClick(self.open));
		}

		if (!self.config.noCalendar) {
			self.monthNav.addEventListener("wheel", function (e) {
				return e.preventDefault();
			});
			bind(self.monthNav, "wheel", debounce(onMonthNavScroll, 10));
			bind(self.monthNav, "mousedown", onClick(onMonthNavClick));

			bind(self.monthNav, ["keyup", "increment"], onYearInput);
			bind(self.daysContainer, "mousedown", onClick(selectDate));

			if (self.config.animate) {
				bind(self.daysContainer, ["webkitAnimationEnd", "animationend"], animateDays);
				bind(self.monthNav, ["webkitAnimationEnd", "animationend"], animateMonths);
			}
		}

		if (self.config.enableTime) {
			var selText = function selText(e) {
				return e.target.select();
			};
			bind(self.timeContainer, ["wheel", "input", "increment"], updateTime);
			bind(self.timeContainer, "mousedown", onClick(timeIncrement));

			bind(self.timeContainer, ["wheel", "increment"], self.debouncedChange);
			bind(self.timeContainer, "input", self.triggerChange);

			bind([self.hourElement, self.minuteElement], "focus", selText);

			if (self.secondElement !== undefined) bind(self.secondElement, "focus", function () {
				return self.secondElement.select();
			});

			if (self.amPM !== undefined) {
				bind(self.amPM, "mousedown", onClick(function (e) {
					updateTime(e);
					self.triggerChange(e);
				}));
			}
		}
	}

	function processPostDayAnimation() {
		for (var i = self._animationLoop.length; i--;) {
			self._animationLoop[i]();
			self._animationLoop.splice(i, 1);
		}
	}

	/**
  * Removes the day container that slided out of view
  * @param {Event} e the animation event
  */
	function animateDays(e) {
		if (self.daysContainer.childNodes.length > 1) {
			switch (e.animationName) {
				case "fpSlideLeft":
					self.daysContainer.lastChild.classList.remove("slideLeftNew");
					self.daysContainer.removeChild(self.daysContainer.firstChild);
					self.days = self.daysContainer.firstChild;
					processPostDayAnimation();

					break;

				case "fpSlideRight":
					self.daysContainer.firstChild.classList.remove("slideRightNew");
					self.daysContainer.removeChild(self.daysContainer.lastChild);
					self.days = self.daysContainer.firstChild;
					processPostDayAnimation();

					break;

				default:
					break;
			}
		}
	}

	/**
  * Removes the month element that animated out of view
  * @param {Event} e the animation event
  */
	function animateMonths(e) {
		switch (e.animationName) {
			case "fpSlideLeftNew":
			case "fpSlideRightNew":
				self.navigationCurrentMonth.classList.remove("slideLeftNew");
				self.navigationCurrentMonth.classList.remove("slideRightNew");
				var nav = self.navigationCurrentMonth;

				while (nav.nextSibling && /curr/.test(nav.nextSibling.className)) {
					self.monthNav.removeChild(nav.nextSibling);
				}while (nav.previousSibling && /curr/.test(nav.previousSibling.className)) {
					self.monthNav.removeChild(nav.previousSibling);
				}self.oldCurMonth = null;
				break;
		}
	}

	/**
  * Set the calendar view to a particular date.
  * @param {Date} jumpDate the date to set the view to
  */
	function jumpToDate(jumpDate) {
		jumpDate = jumpDate ? self.parseDate(jumpDate) : self.latestSelectedDateObj || (self.config.minDate > self.now ? self.config.minDate : self.config.maxDate && self.config.maxDate < self.now ? self.config.maxDate : self.now);

		try {
			self.currentYear = jumpDate.getFullYear();
			self.currentMonth = jumpDate.getMonth();
		} catch (e) {
			/* istanbul ignore next */
			console.error(e.stack);
			/* istanbul ignore next */
			console.warn("Invalid date supplied: " + jumpDate);
		}

		self.redraw();
	}

	/**
  * The up/down arrow handler for time inputs
  * @param {Event} e the click event
  */
	function timeIncrement(e) {
		if (~e.target.className.indexOf("arrow")) incrementNumInput(e, e.target.classList.contains("arrowUp") ? 1 : -1);
	}

	/**
  * Increments/decrements the value of input associ-
  * ated with the up/down arrow by dispatching an
  * "increment" event on the input.
  *
  * @param {Event} e the click event
  * @param {Number} delta the diff (usually 1 or -1)
  * @param {Element} inputElem the input element
  */
	function incrementNumInput(e, delta, inputElem) {
		var input = inputElem || e.target.parentNode.childNodes[0];
		var event = createEvent("increment");
		event.delta = delta;
		input.dispatchEvent(event);
	}

	function createNumberInput(inputClassName) {
		var wrapper = createElement("div", "numInputWrapper"),
		    numInput = createElement("input", "numInput " + inputClassName),
		    arrowUp = createElement("span", "arrowUp"),
		    arrowDown = createElement("span", "arrowDown");

		numInput.type = "text";
		numInput.pattern = "\\d*";

		wrapper.appendChild(numInput);
		wrapper.appendChild(arrowUp);
		wrapper.appendChild(arrowDown);

		return wrapper;
	}

	function build() {
		var fragment = window.document.createDocumentFragment();
		self.calendarContainer = createElement("div", "flatpickr-calendar");
		self.calendarContainer.tabIndex = -1;

		if (!self.config.noCalendar) {
			fragment.appendChild(buildMonthNav());
			self.innerContainer = createElement("div", "flatpickr-innerContainer");

			if (self.config.weekNumbers) self.innerContainer.appendChild(buildWeeks());

			self.rContainer = createElement("div", "flatpickr-rContainer");
			self.rContainer.appendChild(buildWeekdays());

			if (!self.daysContainer) {
				self.daysContainer = createElement("div", "flatpickr-days");
				self.daysContainer.tabIndex = -1;
			}

			buildDays();
			self.rContainer.appendChild(self.daysContainer);

			self.innerContainer.appendChild(self.rContainer);
			fragment.appendChild(self.innerContainer);
		}

		if (self.config.enableTime) fragment.appendChild(buildTime());

		toggleClass(self.calendarContainer, "rangeMode", self.config.mode === "range");
		toggleClass(self.calendarContainer, "animate", self.config.animate);

		self.calendarContainer.appendChild(fragment);

		var customAppend = self.config.appendTo && self.config.appendTo.nodeType;

		if (self.config.inline || self.config.static) {
			self.calendarContainer.classList.add(self.config.inline ? "inline" : "static");

			if (self.config.inline && !customAppend) {
				return self.element.parentNode.insertBefore(self.calendarContainer, self._input.nextSibling);
			}

			if (self.config.static) {
				var wrapper = createElement("div", "flatpickr-wrapper");
				self.element.parentNode.insertBefore(wrapper, self.element);
				wrapper.appendChild(self.element);

				if (self.altInput) wrapper.appendChild(self.altInput);

				wrapper.appendChild(self.calendarContainer);
				return;
			}
		}

		(customAppend ? self.config.appendTo : window.document.body).appendChild(self.calendarContainer);
	}

	function createDay(className, date, dayNumber, i) {
		var dateIsEnabled = isEnabled(date, true),
		    dayElement = createElement("span", "flatpickr-day " + className, date.getDate());

		dayElement.dateObj = date;
		dayElement.$i = i;
		dayElement.setAttribute("aria-label", self.formatDate(date, self.config.ariaDateFormat));

		if (compareDates(date, self.now) === 0) {
			self.todayDateElem = dayElement;
			dayElement.classList.add("today");
		}

		if (dateIsEnabled) {
			dayElement.tabIndex = -1;
			if (isDateSelected(date)) {
				dayElement.classList.add("selected");
				self.selectedDateElem = dayElement;
				if (self.config.mode === "range") {
					toggleClass(dayElement, "startRange", compareDates(date, self.selectedDates[0]) === 0);

					toggleClass(dayElement, "endRange", compareDates(date, self.selectedDates[1]) === 0);
				}
			}
		} else {
			dayElement.classList.add("disabled");
			if (self.selectedDates[0] && date > self.minRangeDate && date < self.selectedDates[0]) self.minRangeDate = date;else if (self.selectedDates[0] && date < self.maxRangeDate && date > self.selectedDates[0]) self.maxRangeDate = date;
		}

		if (self.config.mode === "range") {
			if (isDateInRange(date) && !isDateSelected(date)) dayElement.classList.add("inRange");

			if (self.selectedDates.length === 1 && (date < self.minRangeDate || date > self.maxRangeDate)) dayElement.classList.add("notAllowed");
		}

		if (self.config.weekNumbers && className !== "prevMonthDay" && dayNumber % 7 === 1) {
			self.weekNumbers.insertAdjacentHTML("beforeend", "<span class='disabled flatpickr-day'>" + self.config.getWeek(date) + "</span>");
		}

		triggerEvent("DayCreate", dayElement);

		return dayElement;
	}

	function focusOnDay(currentIndex, offset) {
		var newIndex = currentIndex + offset || 0,
		    targetNode = currentIndex !== undefined ? self.days.childNodes[newIndex] : self.selectedDateElem || self.todayDateElem || self.days.childNodes[0],
		    focus = function focus() {
			targetNode = targetNode || self.days.childNodes[newIndex];
			targetNode.focus();

			if (self.config.mode === "range") onMouseOver(targetNode);
		};

		if (targetNode === undefined && offset !== 0) {
			if (offset > 0) {
				self.changeMonth(1);
				newIndex = newIndex % 42;
			} else if (offset < 0) {
				self.changeMonth(-1);
				newIndex += 42;
			}

			return afterDayAnim(focus);
		}

		focus();
	}

	function afterDayAnim(fn) {
		if (self.config.animate === true) return self._animationLoop.push(fn);
		fn();
	}

	function buildDays(delta) {
		var firstOfMonth = (new Date(self.currentYear, self.currentMonth, 1).getDay() - self.l10n.firstDayOfWeek + 7) % 7,
		    isRangeMode = self.config.mode === "range";

		self.prevMonthDays = self.utils.getDaysinMonth((self.currentMonth - 1 + 12) % 12);
		self.selectedDateElem = undefined;
		self.todayDateElem = undefined;

		var daysInMonth = self.utils.getDaysinMonth(),
		    days = window.document.createDocumentFragment();

		var dayNumber = self.prevMonthDays + 1 - firstOfMonth,
		    dayIndex = 0;

		if (self.config.weekNumbers && self.weekNumbers.firstChild) self.weekNumbers.textContent = "";

		if (isRangeMode) {
			// const dateLimits = self.config.enable.length || self.config.disable.length || self.config.mixDate || self.config.maxDate;
			self.minRangeDate = new Date(self.currentYear, self.currentMonth - 1, dayNumber);
			self.maxRangeDate = new Date(self.currentYear, self.currentMonth + 1, (42 - firstOfMonth) % daysInMonth);
		}

		// prepend days from the ending of previous month
		for (; dayNumber <= self.prevMonthDays; dayNumber++, dayIndex++) {
			days.appendChild(createDay("prevMonthDay", new Date(self.currentYear, self.currentMonth - 1, dayNumber), dayNumber, dayIndex));
		}

		// Start at 1 since there is no 0th day
		for (dayNumber = 1; dayNumber <= daysInMonth; dayNumber++, dayIndex++) {
			days.appendChild(createDay("", new Date(self.currentYear, self.currentMonth, dayNumber), dayNumber, dayIndex));
		}

		// append days from the next month
		for (var dayNum = daysInMonth + 1; dayNum <= 42 - firstOfMonth; dayNum++, dayIndex++) {
			days.appendChild(createDay("nextMonthDay", new Date(self.currentYear, self.currentMonth + 1, dayNum % daysInMonth), dayNum, dayIndex));
		}

		if (isRangeMode && self.selectedDates.length === 1 && days.childNodes[0]) {
			self._hidePrevMonthArrow = self._hidePrevMonthArrow || self.minRangeDate > days.childNodes[0].dateObj;

			self._hideNextMonthArrow = self._hideNextMonthArrow || self.maxRangeDate < new Date(self.currentYear, self.currentMonth + 1, 1);
		} else updateNavigationCurrentMonth();

		var dayContainer = createElement("div", "dayContainer");
		dayContainer.appendChild(days);

		if (!self.config.animate || delta === undefined) clearNode(self.daysContainer);else {
			while (self.daysContainer.childNodes.length > 1) {
				self.daysContainer.removeChild(self.daysContainer.firstChild);
			}
		}

		if (delta >= 0) self.daysContainer.appendChild(dayContainer);else self.daysContainer.insertBefore(dayContainer, self.daysContainer.firstChild);

		self.days = self.daysContainer.firstChild;
		return self.daysContainer;
	}

	function clearNode(node) {
		while (node.firstChild) {
			node.removeChild(node.firstChild);
		}
	}

	function buildMonthNav() {
		var monthNavFragment = window.document.createDocumentFragment();
		self.monthNav = createElement("div", "flatpickr-month");

		self.prevMonthNav = createElement("span", "flatpickr-prev-month");
		self.prevMonthNav.innerHTML = self.config.prevArrow;

		self.currentMonthElement = createElement("span", "cur-month");
		self.currentMonthElement.title = self.l10n.scrollTitle;

		var yearInput = createNumberInput("cur-year");
		self.currentYearElement = yearInput.childNodes[0];
		self.currentYearElement.title = self.l10n.scrollTitle;

		if (self.config.minDate) self.currentYearElement.min = self.config.minDate.getFullYear();

		if (self.config.maxDate) {
			self.currentYearElement.max = self.config.maxDate.getFullYear();

			self.currentYearElement.disabled = self.config.minDate && self.config.minDate.getFullYear() === self.config.maxDate.getFullYear();
		}

		self.nextMonthNav = createElement("span", "flatpickr-next-month");
		self.nextMonthNav.innerHTML = self.config.nextArrow;

		self.navigationCurrentMonth = createElement("span", "flatpickr-current-month");
		self.navigationCurrentMonth.appendChild(self.currentMonthElement);
		self.navigationCurrentMonth.appendChild(yearInput);

		monthNavFragment.appendChild(self.prevMonthNav);
		monthNavFragment.appendChild(self.navigationCurrentMonth);
		monthNavFragment.appendChild(self.nextMonthNav);
		self.monthNav.appendChild(monthNavFragment);

		Object.defineProperty(self, "_hidePrevMonthArrow", {
			get: function get() {
				return this.__hidePrevMonthArrow;
			},
			set: function set(bool) {
				if (this.__hidePrevMonthArrow !== bool) self.prevMonthNav.style.display = bool ? "none" : "block";
				this.__hidePrevMonthArrow = bool;
			}
		});

		Object.defineProperty(self, "_hideNextMonthArrow", {
			get: function get() {
				return this.__hideNextMonthArrow;
			},
			set: function set(bool) {
				if (this.__hideNextMonthArrow !== bool) self.nextMonthNav.style.display = bool ? "none" : "block";
				this.__hideNextMonthArrow = bool;
			}
		});

		updateNavigationCurrentMonth();

		return self.monthNav;
	}

	function buildTime() {
		self.calendarContainer.classList.add("hasTime");
		if (self.config.noCalendar) self.calendarContainer.classList.add("noCalendar");
		self.timeContainer = createElement("div", "flatpickr-time");
		self.timeContainer.tabIndex = -1;
		var separator = createElement("span", "flatpickr-time-separator", ":");

		var hourInput = createNumberInput("flatpickr-hour");
		self.hourElement = hourInput.childNodes[0];

		var minuteInput = createNumberInput("flatpickr-minute");
		self.minuteElement = minuteInput.childNodes[0];

		self.hourElement.tabIndex = self.minuteElement.tabIndex = -1;

		self.hourElement.value = self.pad(self.latestSelectedDateObj ? self.latestSelectedDateObj.getHours() : self.config.defaultHour % (self.time_24hr ? 24 : 12));

		self.minuteElement.value = self.pad(self.latestSelectedDateObj ? self.latestSelectedDateObj.getMinutes() : self.config.defaultMinute);

		self.hourElement.step = self.config.hourIncrement;
		self.minuteElement.step = self.config.minuteIncrement;

		self.hourElement.min = self.config.time_24hr ? 0 : 1;
		self.hourElement.max = self.config.time_24hr ? 23 : 12;

		self.minuteElement.min = 0;
		self.minuteElement.max = 59;

		self.hourElement.title = self.minuteElement.title = self.l10n.scrollTitle;

		self.timeContainer.appendChild(hourInput);
		self.timeContainer.appendChild(separator);
		self.timeContainer.appendChild(minuteInput);

		if (self.config.time_24hr) self.timeContainer.classList.add("time24hr");

		if (self.config.enableSeconds) {
			self.timeContainer.classList.add("hasSeconds");

			var secondInput = createNumberInput("flatpickr-second");
			self.secondElement = secondInput.childNodes[0];

			self.secondElement.value = self.pad(self.latestSelectedDateObj ? self.latestSelectedDateObj.getSeconds() : self.config.defaultSeconds);

			self.secondElement.step = self.minuteElement.step;
			self.secondElement.min = self.minuteElement.min;
			self.secondElement.max = self.minuteElement.max;

			self.timeContainer.appendChild(createElement("span", "flatpickr-time-separator", ":"));
			self.timeContainer.appendChild(secondInput);
		}

		if (!self.config.time_24hr) {
			// add self.amPM if appropriate
			self.amPM = createElement("span", "flatpickr-am-pm", ["AM", "PM"][(self.latestSelectedDateObj ? self.hourElement.value : self.config.defaultHour) > 11 | 0]);
			self.amPM.title = self.l10n.toggleTitle;
			self.amPM.tabIndex = -1;
			self.timeContainer.appendChild(self.amPM);
		}

		return self.timeContainer;
	}

	function buildWeekdays() {
		if (!self.weekdayContainer) self.weekdayContainer = createElement("div", "flatpickr-weekdays");

		var firstDayOfWeek = self.l10n.firstDayOfWeek;
		var weekdays = self.l10n.weekdays.shorthand.slice();

		if (firstDayOfWeek > 0 && firstDayOfWeek < weekdays.length) {
			weekdays = [].concat(weekdays.splice(firstDayOfWeek, weekdays.length), weekdays.splice(0, firstDayOfWeek));
		}

		self.weekdayContainer.innerHTML = "\n\t\t<span class=flatpickr-weekday>\n\t\t\t" + weekdays.join("</span><span class=flatpickr-weekday>") + "\n\t\t</span>\n\t\t";

		return self.weekdayContainer;
	}

	/* istanbul ignore next */
	function buildWeeks() {
		self.calendarContainer.classList.add("hasWeeks");
		self.weekWrapper = createElement("div", "flatpickr-weekwrapper");
		self.weekWrapper.appendChild(createElement("span", "flatpickr-weekday", self.l10n.weekAbbreviation));
		self.weekNumbers = createElement("div", "flatpickr-weeks");
		self.weekWrapper.appendChild(self.weekNumbers);

		return self.weekWrapper;
	}

	function changeMonth(value, is_offset, animate) {
		is_offset = is_offset === undefined || is_offset;
		var delta = is_offset ? value : value - self.currentMonth;
		var skipAnimations = !self.config.animate || animate === false;

		if (delta < 0 && self._hidePrevMonthArrow || delta > 0 && self._hideNextMonthArrow) return;

		self.currentMonth += delta;

		if (self.currentMonth < 0 || self.currentMonth > 11) {
			self.currentYear += self.currentMonth > 11 ? 1 : -1;
			self.currentMonth = (self.currentMonth + 12) % 12;

			triggerEvent("YearChange");
		}

		buildDays(!skipAnimations ? delta : undefined);

		if (skipAnimations) {
			triggerEvent("MonthChange");
			return updateNavigationCurrentMonth();
		}

		// remove possible remnants from clicking too fast
		var nav = self.navigationCurrentMonth;
		if (delta < 0) {
			while (nav.nextSibling && /curr/.test(nav.nextSibling.className)) {
				self.monthNav.removeChild(nav.nextSibling);
			}
		} else if (delta > 0) {
			while (nav.previousSibling && /curr/.test(nav.previousSibling.className)) {
				self.monthNav.removeChild(nav.previousSibling);
			}
		}

		self.oldCurMonth = self.navigationCurrentMonth;

		self.navigationCurrentMonth = self.monthNav.insertBefore(self.oldCurMonth.cloneNode(true), delta > 0 ? self.oldCurMonth.nextSibling : self.oldCurMonth);

		if (delta > 0) {
			self.daysContainer.firstChild.classList.add("slideLeft");
			self.daysContainer.lastChild.classList.add("slideLeftNew");

			self.oldCurMonth.classList.add("slideLeft");
			self.navigationCurrentMonth.classList.add("slideLeftNew");
		} else if (delta < 0) {
			self.daysContainer.firstChild.classList.add("slideRightNew");
			self.daysContainer.lastChild.classList.add("slideRight");

			self.oldCurMonth.classList.add("slideRight");
			self.navigationCurrentMonth.classList.add("slideRightNew");
		}

		self.currentMonthElement = self.navigationCurrentMonth.firstChild;
		self.currentYearElement = self.navigationCurrentMonth.lastChild.childNodes[0];

		updateNavigationCurrentMonth();
		self.oldCurMonth.firstChild.textContent = self.utils.monthToStr(self.currentMonth - delta);

		triggerEvent("MonthChange");

		if (document.activeElement && document.activeElement.$i) {
			var index = document.activeElement.$i;
			afterDayAnim(function () {
				focusOnDay(index, 0);
			});
		}
	}

	function clear(triggerChangeEvent) {
		self.input.value = "";

		if (self.altInput) self.altInput.value = "";

		if (self.mobileInput) self.mobileInput.value = "";

		self.selectedDates = [];
		self.latestSelectedDateObj = undefined;
		self.showTimeInput = false;

		self.redraw();

		if (triggerChangeEvent !== false)
			// triggerChangeEvent is true (default) or an Event
			triggerEvent("Change");
	}

	function close() {
		self.isOpen = false;

		if (!self.isMobile) {
			self.calendarContainer.classList.remove("open");
			self._input.classList.remove("active");
		}

		triggerEvent("Close");
	}

	function destroy() {
		if (self.config !== undefined) triggerEvent("Destroy");

		for (var i = self._handlers.length; i--;) {
			var h = self._handlers[i];
			h.element.removeEventListener(h.event, h.handler);
		}

		self._handlers = [];

		if (self.mobileInput) {
			if (self.mobileInput.parentNode) self.mobileInput.parentNode.removeChild(self.mobileInput);
			self.mobileInput = null;
		} else if (self.calendarContainer && self.calendarContainer.parentNode) self.calendarContainer.parentNode.removeChild(self.calendarContainer);

		if (self.altInput) {
			self.input.type = "text";
			if (self.altInput.parentNode) self.altInput.parentNode.removeChild(self.altInput);
			delete self.altInput;
		}

		if (self.input) {
			self.input.type = self.input._type;
			self.input.classList.remove("flatpickr-input");
			self.input.removeAttribute("readonly");
			self.input.value = "";
		}

		["_showTimeInput", "latestSelectedDateObj", "_hideNextMonthArrow", "_hidePrevMonthArrow", "__hideNextMonthArrow", "__hidePrevMonthArrow", "isMobile", "isOpen", "selectedDateElem", "minDateHasTime", "maxDateHasTime", "days", "daysContainer", "_input", "_positionElement", "innerContainer", "rContainer", "monthNav", "todayDateElem", "calendarContainer", "weekdayContainer", "prevMonthNav", "nextMonthNav", "currentMonthElement", "currentYearElement", "navigationCurrentMonth", "selectedDateElem", "config"].forEach(function (k) {
			try {
				delete self[k];
			} catch (e) {}
		});
	}

	function isCalendarElem(elem) {
		if (self.config.appendTo && self.config.appendTo.contains(elem)) return true;

		return self.calendarContainer.contains(elem);
	}

	function documentClick(e) {
		if (self.isOpen && !self.config.inline) {
			var isCalendarElement = isCalendarElem(e.target);
			var isInput = e.target === self.input || e.target === self.altInput || self.element.contains(e.target) ||
			// web components
			e.path && e.path.indexOf && (~e.path.indexOf(self.input) || ~e.path.indexOf(self.altInput));

			var lostFocus = e.type === "blur" ? isInput && e.relatedTarget && !isCalendarElem(e.relatedTarget) : !isInput && !isCalendarElement;

			if (lostFocus && self.config.ignoredFocusElements.indexOf(e.target) === -1) {
				self.close();

				if (self.config.mode === "range" && self.selectedDates.length === 1) {
					self.clear(false);
					self.redraw();
				}
			}
		}
	}

	function changeYear(newYear) {
		if (!newYear || self.currentYearElement.min && newYear < self.currentYearElement.min || self.currentYearElement.max && newYear > self.currentYearElement.max) return;

		var newYearNum = parseInt(newYear, 10),
		    isNewYear = self.currentYear !== newYearNum;

		self.currentYear = newYearNum || self.currentYear;

		if (self.config.maxDate && self.currentYear === self.config.maxDate.getFullYear()) {
			self.currentMonth = Math.min(self.config.maxDate.getMonth(), self.currentMonth);
		} else if (self.config.minDate && self.currentYear === self.config.minDate.getFullYear()) {
			self.currentMonth = Math.max(self.config.minDate.getMonth(), self.currentMonth);
		}

		if (isNewYear) {
			self.redraw();
			triggerEvent("YearChange");
		}
	}

	function isEnabled(date, timeless) {
		if (self.config.minDate && compareDates(date, self.config.minDate, timeless !== undefined ? timeless : !self.minDateHasTime) < 0 || self.config.maxDate && compareDates(date, self.config.maxDate, timeless !== undefined ? timeless : !self.maxDateHasTime) > 0) return false;

		if (!self.config.enable.length && !self.config.disable.length) return true;

		var dateToCheck = self.parseDate(date, null, true); // timeless

		var bool = self.config.enable.length > 0,
		    array = bool ? self.config.enable : self.config.disable;

		for (var i = 0, d; i < array.length; i++) {
			d = array[i];

			if (d instanceof Function && d(dateToCheck)) // disabled by function
				return bool;else if (d instanceof Date && d.getTime() === dateToCheck.getTime())
				// disabled by date
				return bool;else if (typeof d === "string" && self.parseDate(d, null, true).getTime() === dateToCheck.getTime())
				// disabled by date string
				return bool;else if ( // disabled by range
			(typeof d === "undefined" ? "undefined" : _typeof(d)) === "object" && d.from && d.to && dateToCheck >= d.from && dateToCheck <= d.to) return bool;
		}

		return !bool;
	}

	function onKeyDown(e) {
		var isInput = e.target === self._input;
		var calendarElem = isCalendarElem(e.target);
		var allowInput = self.config.allowInput;
		var allowKeydown = self.isOpen && (!allowInput || !isInput);
		var allowInlineKeydown = self.config.inline && isInput && !allowInput;

		if (e.key === "Enter" && allowInput && isInput) {
			self.setDate(self._input.value, true, e.target === self.altInput ? self.config.altFormat : self.config.dateFormat);
			return e.target.blur();
		} else if (calendarElem || allowKeydown || allowInlineKeydown) {
			var isTimeObj = self.timeContainer && self.timeContainer.contains(e.target);
			switch (e.key) {
				case "Enter":
					if (isTimeObj) updateValue();else selectDate(e);

					break;

				case "Escape":
					// escape
					e.preventDefault();
					self.close();
					break;

				case "Backspace":
				case "Delete":
					if (!self.config.allowInput) self.clear();
					break;

				case "ArrowLeft":
				case "ArrowRight":
					if (!isTimeObj) {
						e.preventDefault();

						if (self.daysContainer) {
							var _delta = e.key === "ArrowRight" ? 1 : -1;

							if (!e.ctrlKey) focusOnDay(e.target.$i, _delta);else changeMonth(_delta, true);
						} else if (self.config.enableTime && !isTimeObj) self.hourElement.focus();
					}

					break;

				case "ArrowUp":
				case "ArrowDown":
					e.preventDefault();
					var delta = e.key === "ArrowDown" ? 1 : -1;

					if (self.daysContainer) {
						if (e.ctrlKey) {
							changeYear(self.currentYear - delta);
							focusOnDay(e.target.$i, 0);
						} else if (!isTimeObj) focusOnDay(e.target.$i, delta * 7);
					} else if (self.config.enableTime) {
						if (!isTimeObj) self.hourElement.focus();
						updateTime(e);
						self.debouncedChange();
					}

					break;

				case "Tab":
					if (e.target === self.hourElement) {
						e.preventDefault();
						self.minuteElement.select();
					} else if (e.target === self.minuteElement && (self.secondElement || self.amPM)) {
						e.preventDefault();
						(self.secondElement || self.amPM).focus();
					} else if (e.target === self.secondElement) {
						e.preventDefault();
						self.amPM.focus();
					}

					break;

				case "a":
					if (e.target === self.amPM) {
						self.amPM.textContent = "AM";
						setHoursFromInputs();
						updateValue();
					}
					break;

				case "p":
					if (e.target === self.amPM) {
						self.amPM.textContent = "PM";
						setHoursFromInputs();
						updateValue();
					}
					break;

				default:
					break;

			}

			triggerEvent("KeyDown", e);
		}
	}

	function onMouseOver(elem) {
		if (self.selectedDates.length !== 1 || !elem.classList.contains("flatpickr-day")) return;

		var hoverDate = elem.dateObj,
		    initialDate = self.parseDate(self.selectedDates[0], null, true),
		    rangeStartDate = Math.min(hoverDate.getTime(), self.selectedDates[0].getTime()),
		    rangeEndDate = Math.max(hoverDate.getTime(), self.selectedDates[0].getTime()),
		    containsDisabled = false;

		for (var t = rangeStartDate; t < rangeEndDate; t += self.utils.duration.DAY) {
			if (!isEnabled(new Date(t))) {
				containsDisabled = true;
				break;
			}
		}

		var _loop = function _loop(timestamp, i) {
			var outOfRange = timestamp < self.minRangeDate.getTime() || timestamp > self.maxRangeDate.getTime(),
			    dayElem = self.days.childNodes[i];

			if (outOfRange) {
				self.days.childNodes[i].classList.add("notAllowed");
				["inRange", "startRange", "endRange"].forEach(function (c) {
					dayElem.classList.remove(c);
				});
				return "continue";
			} else if (containsDisabled && !outOfRange) return "continue";

			["startRange", "inRange", "endRange", "notAllowed"].forEach(function (c) {
				dayElem.classList.remove(c);
			});

			var minRangeDate = Math.max(self.minRangeDate.getTime(), rangeStartDate),
			    maxRangeDate = Math.min(self.maxRangeDate.getTime(), rangeEndDate);

			elem.classList.add(hoverDate < self.selectedDates[0] ? "startRange" : "endRange");

			if (initialDate < hoverDate && timestamp === initialDate.getTime()) dayElem.classList.add("startRange");else if (initialDate > hoverDate && timestamp === initialDate.getTime()) dayElem.classList.add("endRange");

			if (timestamp >= minRangeDate && timestamp <= maxRangeDate) dayElem.classList.add("inRange");
		};

		for (var timestamp = self.days.childNodes[0].dateObj.getTime(), i = 0; i < 42; i++, timestamp += self.utils.duration.DAY) {
			var _ret = _loop(timestamp, i);

			if (_ret === "continue") continue;
		}
	}

	function onResize() {
		if (self.isOpen && !self.config.static && !self.config.inline) positionCalendar();
	}

	function open(e, positionElement) {
		if (self.isMobile) {
			if (e) {
				e.preventDefault();
				e.target.blur();
			}

			setTimeout(function () {
				self.mobileInput.click();
			}, 0);

			triggerEvent("Open");
			return;
		}

		if (self.isOpen || self._input.disabled || self.config.inline) return;

		self.isOpen = true;
		self.calendarContainer.classList.add("open");
		positionCalendar(positionElement);
		self._input.classList.add("active");

		triggerEvent("Open");
	}

	function minMaxDateSetter(type) {
		return function (date) {
			var dateObj = self.config["_" + type + "Date"] = self.parseDate(date);

			var inverseDateObj = self.config["_" + (type === "min" ? "max" : "min") + "Date"];
			var isValidDate = date && dateObj instanceof Date;

			if (isValidDate) {
				self[type + "DateHasTime"] = dateObj.getHours() || dateObj.getMinutes() || dateObj.getSeconds();
			}

			if (self.selectedDates) {
				self.selectedDates = self.selectedDates.filter(function (d) {
					return isEnabled(d);
				});
				if (!self.selectedDates.length && type === "min") setHoursFromDate(dateObj);
				updateValue();
			}

			if (self.daysContainer) {
				redraw();

				if (isValidDate) self.currentYearElement[type] = dateObj.getFullYear();else self.currentYearElement.removeAttribute(type);

				self.currentYearElement.disabled = inverseDateObj && dateObj && inverseDateObj.getFullYear() === dateObj.getFullYear();
			}
		};
	}

	function parseConfig() {
		var boolOpts = ["wrap", "weekNumbers", "allowInput", "clickOpens", "time_24hr", "enableTime", "noCalendar", "altInput", "shorthandCurrentMonth", "inline", "static", "enableSeconds", "disableMobile"];

		var hooks = ["onChange", "onClose", "onDayCreate", "onDestroy", "onKeyDown", "onMonthChange", "onOpen", "onParseConfig", "onReady", "onValueUpdate", "onYearChange"];

		self.config = Object.create(flatpickr.defaultConfig);

		var userConfig = _extends({}, self.instanceConfig, JSON.parse(JSON.stringify(self.element.dataset || {})));

		self.config.parseDate = userConfig.parseDate;
		self.config.formatDate = userConfig.formatDate;

		Object.defineProperty(self.config, "enable", {
			get: function get() {
				return self.config._enable || [];
			},
			set: function set(dates) {
				return self.config._enable = parseDateRules(dates);
			}
		});

		Object.defineProperty(self.config, "disable", {
			get: function get() {
				return self.config._disable || [];
			},
			set: function set(dates) {
				return self.config._disable = parseDateRules(dates);
			}
		});

		_extends(self.config, userConfig);

		if (!userConfig.dateFormat && userConfig.enableTime) {
			self.config.dateFormat = self.config.noCalendar ? "H:i" + (self.config.enableSeconds ? ":S" : "") : flatpickr.defaultConfig.dateFormat + " H:i" + (self.config.enableSeconds ? ":S" : "");
		}

		if (userConfig.altInput && userConfig.enableTime && !userConfig.altFormat) {
			self.config.altFormat = self.config.noCalendar ? "h:i" + (self.config.enableSeconds ? ":S K" : " K") : flatpickr.defaultConfig.altFormat + (" h:i" + (self.config.enableSeconds ? ":S" : "") + " K");
		}

		Object.defineProperty(self.config, "minDate", {
			get: function get() {
				return this._minDate;
			},
			set: minMaxDateSetter("min")
		});

		Object.defineProperty(self.config, "maxDate", {
			get: function get() {
				return this._maxDate;
			},
			set: minMaxDateSetter("max")
		});

		self.config.minDate = userConfig.minDate;
		self.config.maxDate = userConfig.maxDate;

		for (var i = 0; i < boolOpts.length; i++) {
			self.config[boolOpts[i]] = self.config[boolOpts[i]] === true || self.config[boolOpts[i]] === "true";
		}for (var _i = hooks.length; _i--;) {
			if (self.config[hooks[_i]] !== undefined) {
				self.config[hooks[_i]] = arrayify(self.config[hooks[_i]] || []).map(bindToInstance);
			}
		}

		for (var _i2 = 0; _i2 < self.config.plugins.length; _i2++) {
			var pluginConf = self.config.plugins[_i2](self) || {};
			for (var key in pluginConf) {

				if (self.config[key] instanceof Array || ~hooks.indexOf(key)) {
					self.config[key] = arrayify(pluginConf[key]).map(bindToInstance).concat(self.config[key]);
				} else if (typeof userConfig[key] === "undefined") self.config[key] = pluginConf[key];
			}
		}

		triggerEvent("ParseConfig");
	}

	function setupLocale() {
		if (_typeof(self.config.locale) !== "object" && typeof flatpickr.l10ns[self.config.locale] === "undefined") console.warn("flatpickr: invalid locale " + self.config.locale);

		self.l10n = _extends(Object.create(flatpickr.l10ns.default), _typeof(self.config.locale) === "object" ? self.config.locale : self.config.locale !== "default" ? flatpickr.l10ns[self.config.locale] || {} : {});
	}

	function positionCalendar() {
		var positionElement = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : self._positionElement;

		if (self.calendarContainer === undefined) return;

		var calendarHeight = self.calendarContainer.offsetHeight,
		    calendarWidth = self.calendarContainer.offsetWidth,
		    configPos = self.config.position,
		    inputBounds = positionElement.getBoundingClientRect(),
		    distanceFromBottom = window.innerHeight - inputBounds.bottom,
		    showOnTop = configPos === "above" || configPos !== "below" && distanceFromBottom < calendarHeight && inputBounds.top > calendarHeight;

		var top = window.pageYOffset + inputBounds.top + (!showOnTop ? positionElement.offsetHeight + 2 : -calendarHeight - 2);

		toggleClass(self.calendarContainer, "arrowTop", !showOnTop);
		toggleClass(self.calendarContainer, "arrowBottom", showOnTop);

		if (self.config.inline) return;

		var left = window.pageXOffset + inputBounds.left;
		var right = window.document.body.offsetWidth - inputBounds.right;
		var rightMost = left + calendarWidth > window.document.body.offsetWidth;

		toggleClass(self.calendarContainer, "rightMost", rightMost);

		if (self.config.static) return;

		self.calendarContainer.style.top = top + "px";

		if (!rightMost) {
			self.calendarContainer.style.left = left + "px";
			self.calendarContainer.style.right = "auto";
		} else {
			self.calendarContainer.style.left = "auto";
			self.calendarContainer.style.right = right + "px";
		}
	}

	function redraw() {
		if (self.config.noCalendar || self.isMobile) return;

		buildWeekdays();
		updateNavigationCurrentMonth();
		buildDays();
	}

	function selectDate(e) {
		e.preventDefault();
		e.stopPropagation();

		if (!e.target.classList.contains("flatpickr-day") || e.target.classList.contains("disabled") || e.target.classList.contains("notAllowed")) return;

		var selectedDate = self.latestSelectedDateObj = new Date(e.target.dateObj.getTime());

		var shouldChangeMonth = selectedDate.getMonth() !== self.currentMonth && self.config.mode !== "range";

		self.selectedDateElem = e.target;

		if (self.config.mode === "single") self.selectedDates = [selectedDate];else if (self.config.mode === "multiple") {
			var selectedIndex = isDateSelected(selectedDate);
			if (selectedIndex) self.selectedDates.splice(selectedIndex, 1);else self.selectedDates.push(selectedDate);
		} else if (self.config.mode === "range") {
			if (self.selectedDates.length === 2) self.clear();

			self.selectedDates.push(selectedDate);

			// unless selecting same date twice, sort ascendingly
			if (compareDates(selectedDate, self.selectedDates[0], true) !== 0) self.selectedDates.sort(function (a, b) {
				return a.getTime() - b.getTime();
			});
		}

		setHoursFromInputs();

		if (shouldChangeMonth) {
			var isNewYear = self.currentYear !== selectedDate.getFullYear();
			self.currentYear = selectedDate.getFullYear();
			self.currentMonth = selectedDate.getMonth();

			if (isNewYear) triggerEvent("YearChange");

			triggerEvent("MonthChange");
		}

		buildDays();

		if (self.minDateHasTime && self.config.enableTime && compareDates(selectedDate, self.config.minDate) === 0) setHoursFromDate(self.config.minDate);

		updateValue();

		if (self.config.enableTime) setTimeout(function () {
			return self.showTimeInput = true;
		}, 50);

		if (self.config.mode === "range") {
			if (self.selectedDates.length === 1) {
				onMouseOver(e.target);

				self._hidePrevMonthArrow = self._hidePrevMonthArrow || self.minRangeDate > self.days.childNodes[0].dateObj;

				self._hideNextMonthArrow = self._hideNextMonthArrow || self.maxRangeDate < new Date(self.currentYear, self.currentMonth + 1, 1);
			} else updateNavigationCurrentMonth();
		}

		triggerEvent("Change");

		// maintain focus
		if (!shouldChangeMonth) focusOnDay(e.target.$i, 0);else afterDayAnim(function () {
			return self.selectedDateElem && self.selectedDateElem.focus();
		});

		if (self.config.enableTime) setTimeout(function () {
			return self.hourElement.select();
		}, 451);

		if (self.config.closeOnSelect) {
			var single = self.config.mode === "single" && !self.config.enableTime;
			var range = self.config.mode === "range" && self.selectedDates.length === 2 && !self.config.enableTime;

			if (single || range) self.close();
		}
	}

	function set(option, value) {
		if (option !== null && (typeof option === "undefined" ? "undefined" : _typeof(option)) === "object") _extends(self.config, option);else self.config[option] = value;

		self.redraw();
		jumpToDate();
	}

	function setSelectedDate(inputDate, format) {
		if (inputDate instanceof Array) self.selectedDates = inputDate.map(function (d) {
			return self.parseDate(d, format);
		});else if (inputDate instanceof Date || !isNaN(inputDate)) self.selectedDates = [self.parseDate(inputDate, format)];else if (inputDate && inputDate.substring) {
			switch (self.config.mode) {
				case "single":
					self.selectedDates = [self.parseDate(inputDate, format)];
					break;

				case "multiple":
					self.selectedDates = inputDate.split("; ").map(function (date) {
						return self.parseDate(date, format);
					});
					break;

				case "range":
					self.selectedDates = inputDate.split(self.l10n.rangeSeparator).map(function (date) {
						return self.parseDate(date, format);
					});

					break;

				default:
					break;
			}
		}

		self.selectedDates = self.selectedDates.filter(function (d) {
			return d instanceof Date && isEnabled(d, false);
		});

		self.selectedDates.sort(function (a, b) {
			return a.getTime() - b.getTime();
		});
	}

	function setDate(date, triggerChange, format) {
		if (date !== 0 && !date) return self.clear(triggerChange);

		setSelectedDate(date, format);

		self.showTimeInput = self.selectedDates.length > 0;
		self.latestSelectedDateObj = self.selectedDates[0];

		self.redraw();
		jumpToDate();

		setHoursFromDate();
		updateValue(triggerChange);

		if (triggerChange) triggerEvent("Change");
	}

	function parseDateRules(arr) {
		for (var i = arr.length; i--;) {
			if (typeof arr[i] === "string" || +arr[i]) arr[i] = self.parseDate(arr[i], null, true);else if (arr[i] && arr[i].from && arr[i].to) {
				arr[i].from = self.parseDate(arr[i].from);
				arr[i].to = self.parseDate(arr[i].to);
			}
		}

		return arr.filter(function (x) {
			return x;
		}); // remove falsy values
	}

	function setupDates() {
		self.selectedDates = [];
		self.now = new Date();

		var preloadedDate = self.config.defaultDate || self.input.value;
		if (preloadedDate) setSelectedDate(preloadedDate, self.config.dateFormat);

		var initialDate = self.selectedDates.length ? self.selectedDates[0] : self.config.minDate && self.config.minDate.getTime() > self.now ? self.config.minDate : self.config.maxDate && self.config.maxDate.getTime() < self.now ? self.config.maxDate : self.now;

		self.currentYear = initialDate.getFullYear();
		self.currentMonth = initialDate.getMonth();

		if (self.selectedDates.length) self.latestSelectedDateObj = self.selectedDates[0];

		self.minDateHasTime = self.config.minDate && (self.config.minDate.getHours() || self.config.minDate.getMinutes() || self.config.minDate.getSeconds());

		self.maxDateHasTime = self.config.maxDate && (self.config.maxDate.getHours() || self.config.maxDate.getMinutes() || self.config.maxDate.getSeconds());

		Object.defineProperty(self, "latestSelectedDateObj", {
			get: function get() {
				return self._selectedDateObj || self.selectedDates[self.selectedDates.length - 1];
			},
			set: function set(date) {
				self._selectedDateObj = date;
			}
		});

		if (!self.isMobile) {
			Object.defineProperty(self, "showTimeInput", {
				get: function get() {
					return self._showTimeInput;
				},
				set: function set(bool) {
					self._showTimeInput = bool;
					if (self.calendarContainer) toggleClass(self.calendarContainer, "showTimeInput", bool);
					positionCalendar();
				}
			});
		}
	}

	function setupHelperFunctions() {
		self.utils = {
			duration: {
				DAY: 86400000
			},
			getDaysinMonth: function getDaysinMonth(month, yr) {
				month = typeof month === "undefined" ? self.currentMonth : month;

				yr = typeof yr === "undefined" ? self.currentYear : yr;

				if (month === 1 && (yr % 4 === 0 && yr % 100 !== 0 || yr % 400 === 0)) return 29;

				return self.l10n.daysInMonth[month];
			},
			monthToStr: function monthToStr(monthNumber, shorthand) {
				shorthand = typeof shorthand === "undefined" ? self.config.shorthandCurrentMonth : shorthand;

				return self.l10n.months[(shorthand ? "short" : "long") + "hand"][monthNumber];
			}
		};
	}

	/* istanbul ignore next */
	function setupFormats() {
		self.formats = Object.create(FlatpickrInstance.prototype.formats);
		["D", "F", "J", "M", "W", "l"].forEach(function (f) {
			self.formats[f] = FlatpickrInstance.prototype.formats[f].bind(self);
		});

		self.revFormat.F = FlatpickrInstance.prototype.revFormat.F.bind(self);
		self.revFormat.M = FlatpickrInstance.prototype.revFormat.M.bind(self);
	}

	function setupInputs() {
		self.input = self.config.wrap ? self.element.querySelector("[data-input]") : self.element;

		/* istanbul ignore next */
		if (!self.input) return console.warn("Error: invalid input element specified", self.input);

		self.input._type = self.input.type;
		self.input.type = "text";

		self.input.classList.add("flatpickr-input");
		self._input = self.input;

		if (self.config.altInput) {
			// replicate self.element
			self.altInput = createElement(self.input.nodeName, self.input.className + " " + self.config.altInputClass);
			self._input = self.altInput;
			self.altInput.placeholder = self.input.placeholder;
			self.altInput.disabled = self.input.disabled;
			self.altInput.required = self.input.required;
			self.altInput.type = "text";
			self.input.type = "hidden";

			if (!self.config.static && self.input.parentNode) self.input.parentNode.insertBefore(self.altInput, self.input.nextSibling);
		}

		if (!self.config.allowInput) self._input.setAttribute("readonly", "readonly");

		self._positionElement = self.config.positionElement || self._input;
	}

	function setupMobile() {
		var inputType = self.config.enableTime ? self.config.noCalendar ? "time" : "datetime-local" : "date";

		self.mobileInput = createElement("input", self.input.className + " flatpickr-mobile");
		self.mobileInput.step = self.input.getAttribute("step") || "any";
		self.mobileInput.tabIndex = 1;
		self.mobileInput.type = inputType;
		self.mobileInput.disabled = self.input.disabled;
		self.mobileInput.placeholder = self.input.placeholder;

		self.mobileFormatStr = inputType === "datetime-local" ? "Y-m-d\\TH:i:S" : inputType === "date" ? "Y-m-d" : "H:i:S";

		if (self.selectedDates.length) {
			self.mobileInput.defaultValue = self.mobileInput.value = self.formatDate(self.selectedDates[0], self.mobileFormatStr);
		}

		if (self.config.minDate) self.mobileInput.min = self.formatDate(self.config.minDate, "Y-m-d");

		if (self.config.maxDate) self.mobileInput.max = self.formatDate(self.config.maxDate, "Y-m-d");

		self.input.type = "hidden";
		if (self.config.altInput) self.altInput.type = "hidden";

		try {
			self.input.parentNode.insertBefore(self.mobileInput, self.input.nextSibling);
		} catch (e) {
			//
		}

		self.mobileInput.addEventListener("change", function (e) {
			self.setDate(e.target.value, false, self.mobileFormatStr);
			triggerEvent("Change");
			triggerEvent("Close");
		});
	}

	function toggle() {
		if (self.isOpen) return self.close();
		self.open();
	}

	function triggerEvent(event, data) {
		var hooks = self.config["on" + event];

		if (hooks !== undefined && hooks.length > 0) {
			for (var i = 0; hooks[i] && i < hooks.length; i++) {
				hooks[i](self.selectedDates, self.input.value, self, data);
			}
		}

		if (event === "Change") {
			self.input.dispatchEvent(createEvent("change"));

			// many front-end frameworks bind to the input event
			self.input.dispatchEvent(createEvent("input"));
		}
	}

	/**
  * Creates an Event, normalized across browsers
  * @param {String} name the event name, e.g. "click"
  * @return {Event} the created event
  */
	function createEvent(name) {
		if (self._supportsEvents) return new Event(name, { bubbles: true });

		self._[name + "Event"] = document.createEvent("Event");
		self._[name + "Event"].initEvent(name, true, true);
		return self._[name + "Event"];
	}

	function isDateSelected(date) {
		for (var i = 0; i < self.selectedDates.length; i++) {
			if (compareDates(self.selectedDates[i], date) === 0) return "" + i;
		}

		return false;
	}

	function isDateInRange(date) {
		if (self.config.mode !== "range" || self.selectedDates.length < 2) return false;
		return compareDates(date, self.selectedDates[0]) >= 0 && compareDates(date, self.selectedDates[1]) <= 0;
	}

	function updateNavigationCurrentMonth() {
		if (self.config.noCalendar || self.isMobile || !self.monthNav) return;

		self.currentMonthElement.textContent = self.utils.monthToStr(self.currentMonth) + " ";
		self.currentYearElement.value = self.currentYear;

		self._hidePrevMonthArrow = self.config.minDate && (self.currentYear === self.config.minDate.getFullYear() ? self.currentMonth <= self.config.minDate.getMonth() : self.currentYear < self.config.minDate.getFullYear());

		self._hideNextMonthArrow = self.config.maxDate && (self.currentYear === self.config.maxDate.getFullYear() ? self.currentMonth + 1 > self.config.maxDate.getMonth() : self.currentYear > self.config.maxDate.getFullYear());
	}

	/**
  * Updates the values of inputs associated with the calendar
  * @return {void}
  */
	function updateValue(triggerChange) {
		if (!self.selectedDates.length) return self.clear(triggerChange);

		if (self.isMobile) {
			self.mobileInput.value = self.selectedDates.length ? self.formatDate(self.latestSelectedDateObj, self.mobileFormatStr) : "";
		}

		var joinChar = self.config.mode !== "range" ? "; " : self.l10n.rangeSeparator;

		self.input.value = self.selectedDates.map(function (dObj) {
			return self.formatDate(dObj, self.config.dateFormat);
		}).join(joinChar);

		if (self.config.altInput) {
			self.altInput.value = self.selectedDates.map(function (dObj) {
				return self.formatDate(dObj, self.config.altFormat);
			}).join(joinChar);
		}

		if (triggerChange !== false) triggerEvent("ValueUpdate");
	}

	function mouseDelta(e) {
		return Math.max(-1, Math.min(1, e.wheelDelta || -e.deltaY));
	}

	function onMonthNavScroll(e) {
		e.preventDefault();
		var isYear = self.currentYearElement.parentNode.contains(e.target);

		if (e.target === self.currentMonthElement || isYear) {

			var delta = mouseDelta(e);

			if (isYear) {
				changeYear(self.currentYear + delta);
				e.target.value = self.currentYear;
			} else self.changeMonth(delta, true, false);
		}
	}

	function onMonthNavClick(e) {
		var isPrevMonth = self.prevMonthNav.contains(e.target);
		var isNextMonth = self.nextMonthNav.contains(e.target);

		if (isPrevMonth || isNextMonth) changeMonth(isPrevMonth ? -1 : 1);else if (e.target === self.currentYearElement) {
			e.preventDefault();
			self.currentYearElement.select();
		} else if (e.target.className === "arrowUp") self.changeYear(self.currentYear + 1);else if (e.target.className === "arrowDown") self.changeYear(self.currentYear - 1);
	}

	/**
  * Creates an HTMLElement with given tag, class, and textual content
  * @param {String} tag the HTML tag
  * @param {String} className the new element's class name
  * @param {String} content The new element's text content
  * @return {HTMLElement} the created HTML element
  */
	function createElement(tag, className, content) {
		var e = window.document.createElement(tag);
		className = className || "";
		content = content || "";

		e.className = className;

		if (content !== undefined) e.textContent = content;

		return e;
	}

	function arrayify(obj) {
		if (obj instanceof Array) return obj;
		return [obj];
	}

	function toggleClass(elem, className, bool) {
		if (bool) return elem.classList.add(className);
		elem.classList.remove(className);
	}

	/* istanbul ignore next */
	function debounce(func, wait, immediate) {
		var timeout = void 0;
		return function () {
			var context = this,
			    args = arguments;
			clearTimeout(timeout);
			timeout = setTimeout(function () {
				timeout = null;
				if (!immediate) func.apply(context, args);
			}, wait);
			if (immediate && !timeout) func.apply(context, args);
		};
	}

	/**
  * Compute the difference in dates, measured in ms
  * @param {Date} date1
  * @param {Date} date2
  * @param {Boolean} timeless whether to reset times of both dates to 00:00
  * @return {Number} the difference in ms
  */
	function compareDates(date1, date2, timeless) {
		if (!(date1 instanceof Date) || !(date2 instanceof Date)) return false;

		if (timeless !== false) {
			return new Date(date1.getTime()).setHours(0, 0, 0, 0) - new Date(date2.getTime()).setHours(0, 0, 0, 0);
		}

		return date1.getTime() - date2.getTime();
	}

	function timeWrapper(e) {
		e.preventDefault();

		var isKeyDown = e.type === "keydown",
		    isWheel = e.type === "wheel",
		    isIncrement = e.type === "increment",
		    input = e.target;

		if (self.amPM && e.target === self.amPM) return e.target.textContent = ["AM", "PM"][e.target.textContent === "AM" | 0];

		var min = Number(input.min),
		    max = Number(input.max),
		    step = Number(input.step),
		    curValue = parseInt(input.value, 10),
		    delta = e.delta || (!isKeyDown ? Math.max(-1, Math.min(1, e.wheelDelta || -e.deltaY)) || 0 : e.which === 38 ? 1 : -1);

		var newValue = curValue + step * delta;

		if (typeof input.value !== "undefined" && input.value.length === 2) {
			var isHourElem = input === self.hourElement,
			    isMinuteElem = input === self.minuteElement;

			if (newValue < min) {
				newValue = max + newValue + !isHourElem + (isHourElem && !self.amPM);

				if (isMinuteElem) incrementNumInput(null, -1, self.hourElement);
			} else if (newValue > max) {
				newValue = input === self.hourElement ? newValue - max - !self.amPM : min;

				if (isMinuteElem) incrementNumInput(null, 1, self.hourElement);
			}

			if (self.amPM && isHourElem && (step === 1 ? newValue + curValue === 23 : Math.abs(newValue - curValue) > step)) self.amPM.textContent = self.amPM.textContent === "PM" ? "AM" : "PM";

			input.value = self.pad(newValue);
		}
	}

	init();
	return self;
}

FlatpickrInstance.prototype = {
	formats: {
		// get the date in UTC
		Z: function Z(date) {
			return date.toISOString();
		},

		// weekday name, short, e.g. Thu
		D: function D(date) {
			return this.l10n.weekdays.shorthand[this.formats.w(date)];
		},

		// full month name e.g. January
		F: function F(date) {
			return this.utils.monthToStr(this.formats.n(date) - 1, false);
		},

		// padded hour 1-12
		G: function G(date) {
			return FlatpickrInstance.prototype.pad(FlatpickrInstance.prototype.formats.h(date));
		},

		// hours with leading zero e.g. 03
		H: function H(date) {
			return FlatpickrInstance.prototype.pad(date.getHours());
		},

		// day (1-30) with ordinal suffix e.g. 1st, 2nd
		J: function J(date) {
			return date.getDate() + this.l10n.ordinal(date.getDate());
		},

		// AM/PM
		K: function K(date) {
			return date.getHours() > 11 ? "PM" : "AM";
		},

		// shorthand month e.g. Jan, Sep, Oct, etc
		M: function M(date) {
			return this.utils.monthToStr(date.getMonth(), true);
		},

		// seconds 00-59
		S: function S(date) {
			return FlatpickrInstance.prototype.pad(date.getSeconds());
		},

		// unix timestamp
		U: function U(date) {
			return date.getTime() / 1000;
		},

		W: function W(date) {
			return this.config.getWeek(date);
		},

		// full year e.g. 2016
		Y: function Y(date) {
			return date.getFullYear();
		},

		// day in month, padded (01-30)
		d: function d(date) {
			return FlatpickrInstance.prototype.pad(date.getDate());
		},

		// hour from 1-12 (am/pm)
		h: function h(date) {
			return date.getHours() % 12 ? date.getHours() % 12 : 12;
		},

		// minutes, padded with leading zero e.g. 09
		i: function i(date) {
			return FlatpickrInstance.prototype.pad(date.getMinutes());
		},

		// day in month (1-30)
		j: function j(date) {
			return date.getDate();
		},

		// weekday name, full, e.g. Thursday
		l: function l(date) {
			return this.l10n.weekdays.longhand[date.getDay()];
		},

		// padded month number (01-12)
		m: function m(date) {
			return FlatpickrInstance.prototype.pad(date.getMonth() + 1);
		},

		// the month number (1-12)
		n: function n(date) {
			return date.getMonth() + 1;
		},

		// seconds 0-59
		s: function s(date) {
			return date.getSeconds();
		},

		// number of the day of the week
		w: function w(date) {
			return date.getDay();
		},

		// last two digits of year e.g. 16 for 2016
		y: function y(date) {
			return String(date.getFullYear()).substring(2);
		}
	},

	/**
  * Formats a given Date object into a string based on supplied format
  * @param {Date} dateObj the date object
  * @param {String} frmt a string composed of formatting tokens e.g. "Y-m-d"
  * @return {String} The textual representation of the date e.g. 2017-02-03
  */
	formatDate: function formatDate(dateObj, frmt) {
		var _this = this;

		if (this.config !== undefined && this.config.formatDate !== undefined) return this.config.formatDate(dateObj, frmt);

		return frmt.split("").map(function (c, i, arr) {
			return _this.formats[c] && arr[i - 1] !== "\\" ? _this.formats[c](dateObj) : c !== "\\" ? c : "";
		}).join("");
	},

	revFormat: {
		D: function D() {},
		F: function F(dateObj, monthName) {
			dateObj.setMonth(this.l10n.months.longhand.indexOf(monthName));
		},
		G: function G(dateObj, hour) {
			dateObj.setHours(parseFloat(hour));
		},
		H: function H(dateObj, hour) {
			dateObj.setHours(parseFloat(hour));
		},
		J: function J(dateObj, day) {
			dateObj.setDate(parseFloat(day));
		},
		K: function K(dateObj, amPM) {
			var hours = dateObj.getHours();

			if (hours !== 12) dateObj.setHours(hours % 12 + 12 * /pm/i.test(amPM));
		},
		M: function M(dateObj, shortMonth) {
			dateObj.setMonth(this.l10n.months.shorthand.indexOf(shortMonth));
		},
		S: function S(dateObj, seconds) {
			dateObj.setSeconds(seconds);
		},
		U: function U(dateObj, unixSeconds) {
			return new Date(parseFloat(unixSeconds) * 1000);
		},

		W: function W(dateObj, weekNumber) {
			weekNumber = parseInt(weekNumber);
			return new Date(dateObj.getFullYear(), 0, 2 + (weekNumber - 1) * 7, 0, 0, 0, 0, 0);
		},
		Y: function Y(dateObj, year) {
			dateObj.setFullYear(year);
		},
		Z: function Z(dateObj, ISODate) {
			return new Date(ISODate);
		},

		d: function d(dateObj, day) {
			dateObj.setDate(parseFloat(day));
		},
		h: function h(dateObj, hour) {
			dateObj.setHours(parseFloat(hour));
		},
		i: function i(dateObj, minutes) {
			dateObj.setMinutes(parseFloat(minutes));
		},
		j: function j(dateObj, day) {
			dateObj.setDate(parseFloat(day));
		},
		l: function l() {},
		m: function m(dateObj, month) {
			dateObj.setMonth(parseFloat(month) - 1);
		},
		n: function n(dateObj, month) {
			dateObj.setMonth(parseFloat(month) - 1);
		},
		s: function s(dateObj, seconds) {
			dateObj.setSeconds(parseFloat(seconds));
		},
		w: function w() {},
		y: function y(dateObj, year) {
			dateObj.setFullYear(2000 + parseFloat(year));
		}
	},

	tokenRegex: {
		D: "(\\w+)",
		F: "(\\w+)",
		G: "(\\d\\d|\\d)",
		H: "(\\d\\d|\\d)",
		J: "(\\d\\d|\\d)\\w+",
		K: "(am|AM|Am|aM|pm|PM|Pm|pM)",
		M: "(\\w+)",
		S: "(\\d\\d|\\d)",
		U: "(.+)",
		W: "(\\d\\d|\\d)",
		Y: "(\\d{4})",
		Z: "(.+)",
		d: "(\\d\\d|\\d)",
		h: "(\\d\\d|\\d)",
		i: "(\\d\\d|\\d)",
		j: "(\\d\\d|\\d)",
		l: "(\\w+)",
		m: "(\\d\\d|\\d)",
		n: "(\\d\\d|\\d)",
		s: "(\\d\\d|\\d)",
		w: "(\\d\\d|\\d)",
		y: "(\\d{2})"
	},

	pad: function pad(number) {
		return ("0" + number).slice(-2);
	},

	/**
  * Parses a date(+time) string into a Date object
  * @param {String} date the date string, e.g. 2017-02-03 14:45
  * @param {String} givenFormat the date format, e.g. Y-m-d H:i
  * @param {Boolean} timeless whether to reset the time of Date object
  * @return {Date} the parsed Date object
  */
	parseDate: function parseDate(date, givenFormat, timeless) {
		var _this2 = this;

		if (date !== 0 && !date) return null;

		var date_orig = date;

		if (date instanceof Date) date = new Date(date.getTime()); // create a copy

		else if (date.toFixed !== undefined) // timestamp
				date = new Date(date);else {
				// date string
				var format = givenFormat || (this.config || flatpickr.defaultConfig).dateFormat;
				date = String(date).trim();

				if (date === "today") {
					date = new Date();
					timeless = true;
				} else if (/Z$/.test(date) || /GMT$/.test(date)) // datestrings w/ timezone
					date = new Date(date);else if (this.config && this.config.parseDate) date = this.config.parseDate(date, format);else {
					(function () {
						var parsedDate = !_this2.config || !_this2.config.noCalendar ? new Date(new Date().getFullYear(), 0, 1, 0, 0, 0, 0) : new Date(new Date().setHours(0, 0, 0, 0));

						var matched = void 0,
						    ops = [];

						for (var i = 0, matchIndex = 0, regexStr = ""; i < format.length; i++) {
							var token = format[i];
							var isBackSlash = token === "\\";
							var escaped = format[i - 1] === "\\" || isBackSlash;

							if (_this2.tokenRegex[token] && !escaped) {
								regexStr += _this2.tokenRegex[token];
								var match = new RegExp(regexStr).exec(date);
								if (match && (matched = true)) {
									ops[token !== "Y" ? "push" : "unshift"]({
										fn: _this2.revFormat[token],
										val: match[++matchIndex]
									});
								}
							} else if (!isBackSlash) regexStr += "."; // don't really care

							ops.forEach(function (_ref) {
								var fn = _ref.fn,
								    val = _ref.val;
								return parsedDate = fn(parsedDate, val) || parsedDate;
							});
						}

						date = matched ? parsedDate : null;
					})();
				}
			}

		/* istanbul ignore next */
		if (!(date instanceof Date)) {
			console.warn("flatpickr: invalid date " + date_orig);
			console.info(this.element);
			return null;
		}

		if (timeless === true) date.setHours(0, 0, 0, 0);

		return date;
	}
};

/* istanbul ignore next */
function _flatpickr(nodeList, config) {
	var nodes = Array.prototype.slice.call(nodeList); // static list
	var instances = [];
	for (var i = 0; i < nodes.length; i++) {
		try {
			if (nodes[i].getAttribute("data-fp-omit") !== null) continue;

			if (nodes[i]._flatpickr) {
				nodes[i]._flatpickr.destroy();
				nodes[i]._flatpickr = null;
			}

			nodes[i]._flatpickr = new FlatpickrInstance(nodes[i], config || {});
			instances.push(nodes[i]._flatpickr);
		} catch (e) {
			console.warn(e, e.stack);
		}
	}

	return instances.length === 1 ? instances[0] : instances;
}

/* istanbul ignore next */
if (typeof HTMLElement !== "undefined") {
	// browser env
	HTMLCollection.prototype.flatpickr = NodeList.prototype.flatpickr = function (config) {
		return _flatpickr(this, config);
	};

	HTMLElement.prototype.flatpickr = function (config) {
		return _flatpickr([this], config);
	};
}

/* istanbul ignore next */
function flatpickr(selector, config) {
	if (selector instanceof NodeList) return _flatpickr(selector, config);else if (!(selector instanceof HTMLElement)) return _flatpickr(window.document.querySelectorAll(selector), config);

	return _flatpickr([selector], config);
}

/* istanbul ignore next */
flatpickr.defaultConfig = FlatpickrInstance.defaultConfig = {
	mode: "single",

	position: "auto",

	animate: typeof window !== "undefined" && window.navigator.userAgent.indexOf("MSIE") === -1,

	// wrap: see https://chmln.github.io/flatpickr/examples/#flatpickr-external-elements
	wrap: false,

	// enables week numbers
	weekNumbers: false,

	// allow manual datetime input
	allowInput: false,

	/*
 	clicking on input opens the date(time)picker.
 	disable if you wish to open the calendar manually with .open()
 */
	clickOpens: true,

	/*
 	closes calendar after date selection,
 	unless 'mode' is 'multiple' or enableTime is true
 */
	closeOnSelect: true,

	// display time picker in 24 hour mode
	time_24hr: false,

	// enables the time picker functionality
	enableTime: false,

	// noCalendar: true will hide the calendar. use for a time picker along w/ enableTime
	noCalendar: false,

	// more date format chars at https://chmln.github.io/flatpickr/#dateformat
	dateFormat: "Y-m-d",

	// date format used in aria-label for days
	ariaDateFormat: "F j, Y",

	// altInput - see https://chmln.github.io/flatpickr/#altinput
	altInput: false,

	// the created altInput element will have this class.
	altInputClass: "form-control input",

	// same as dateFormat, but for altInput
	altFormat: "F j, Y", // defaults to e.g. June 10, 2016

	// defaultDate - either a datestring or a date object. used for datetimepicker"s initial value
	defaultDate: null,

	// the minimum date that user can pick (inclusive)
	minDate: null,

	// the maximum date that user can pick (inclusive)
	maxDate: null,

	// dateparser that transforms a given string to a date object
	parseDate: null,

	// dateformatter that transforms a given date object to a string, according to passed format
	formatDate: null,

	getWeek: function getWeek(givenDate) {
		var date = new Date(givenDate.getTime());
		var onejan = new Date(date.getFullYear(), 0, 1);
		return Math.ceil(((date - onejan) / 86400000 + onejan.getDay() + 1) / 7);
	},

	// see https://chmln.github.io/flatpickr/#disable
	enable: [],

	// see https://chmln.github.io/flatpickr/#disable
	disable: [],

	// display the short version of month names - e.g. Sep instead of September
	shorthandCurrentMonth: false,

	// displays calendar inline. see https://chmln.github.io/flatpickr/#inline-calendar
	inline: false,

	// position calendar inside wrapper and next to the input element
	// leave at false unless you know what you"re doing
	"static": false,

	// DOM node to append the calendar to in *static* mode
	appendTo: null,

	// code for previous/next icons. this is where you put your custom icon code e.g. fontawesome
	prevArrow: "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z' /></svg>",
	nextArrow: "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z' /></svg>",

	// enables seconds in the time picker
	enableSeconds: false,

	// step size used when scrolling/incrementing the hour element
	hourIncrement: 1,

	// step size used when scrolling/incrementing the minute element
	minuteIncrement: 5,

	// initial value in the hour element
	defaultHour: 12,

	// initial value in the minute element
	defaultMinute: 0,

	// initial value in the seconds element
	defaultSeconds: 0,

	// disable native mobile datetime input support
	disableMobile: false,

	// default locale
	locale: "default",

	plugins: [],

	ignoredFocusElements: [],

	// called every time calendar is closed
	onClose: undefined, // function (dateObj, dateStr) {}

	// onChange callback when user selects a date or time
	onChange: undefined, // function (dateObj, dateStr) {}

	// called for every day element
	onDayCreate: undefined,

	// called every time the month is changed
	onMonthChange: undefined,

	// called every time calendar is opened
	onOpen: undefined, // function (dateObj, dateStr) {}

	// called after the configuration has been parsed
	onParseConfig: undefined,

	// called after calendar is ready
	onReady: undefined, // function (dateObj, dateStr) {}

	// called after input value updated
	onValueUpdate: undefined,

	// called every time the year is changed
	onYearChange: undefined,

	onKeyDown: undefined,

	onDestroy: undefined
};

/* istanbul ignore next */
flatpickr.l10ns = {
	en: {
		weekdays: {
			shorthand: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
			longhand: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
		},
		months: {
			shorthand: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			longhand: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
		},
		daysInMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
		firstDayOfWeek: 0,
		ordinal: function ordinal(nth) {
			var s = nth % 100;
			if (s > 3 && s < 21) return "th";
			switch (s % 10) {
				case 1:
					return "st";
				case 2:
					return "nd";
				case 3:
					return "rd";
				default:
					return "th";
			}
		},
		rangeSeparator: " to ",
		weekAbbreviation: "Wk",
		scrollTitle: "Scroll to increment",
		toggleTitle: "Click to toggle"
	}
};

flatpickr.l10ns.default = Object.create(flatpickr.l10ns.en);
flatpickr.localize = function (l10n) {
	return _extends(flatpickr.l10ns.default, l10n || {});
};
flatpickr.setDefaults = function (config) {
	return _extends(flatpickr.defaultConfig, config || {});
};

/* istanbul ignore next */
if (typeof jQuery !== "undefined") {
	jQuery.fn.flatpickr = function (config) {
		return _flatpickr(this, config);
	};
}

Date.prototype.fp_incr = function (days) {
	return new Date(this.getFullYear(), this.getMonth(), this.getDate() + parseInt(days, 10));
};

if (true) module.exports = flatpickr;

/***/ }),

/***/ "./resources/assets/sass/app.scss":
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/assets/thunderlab/js/ajax/ajax.js":
/***/ (function(module, exports) {

/*
	=====================================================================
	Ajax
	=====================================================================
	Version		: 0.3
	Author 		: Budi
	Requirement : jQuery
	if using webpack import jQuery
*/

window.ajax = new function () {

    // debug?
    var debug = true;

    // internal declare
    var on_success;
    var on_error;
    var xhr;

    // interface ajax actions
    this.defineOnSuccess = function (syntax) {
        on_success = syntax;
    };
    this.defineOnError = function (syntax) {
        on_error = syntax;
    };

    // interface ajax function
    this.get = function (url, data, token) {
        console.log('yes');
        console.log(data);
        if (data) {
            console.log(1);
            send(url + "?" + jsonToQueryString(data), null, 'GET', token);
            console.log(jsonToQueryString(data));
        } else {
            console.log(2);
            send(url, null, 'GET', token);
        }
    };
    this.post = function (url, data, token) {
        send(url, data, 'POST', token);
    };
    this.cancel = function () {
        xhr.abort();
    };
    function jsonToQueryString(obj) {
        var str = [];
        for (var p in obj) {
            if (obj.hasOwnProperty(p)) {
                if (obj[p] || obj[p] == 0) {
                    if (Object.prototype.toString.call(obj[p]) === '[object Array]') {
                        Object.keys(obj[p]).forEach(function (key) {
                            str.push(encodeURIComponent(p) + "[" + key + "]=" + encodeURIComponent(obj[p][key]));
                        });
                    } else {
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    }
                }
            }
        }return str.join("&");
    }

    // core
    function validateResult(resp) {
        if (debug == true) {
            console.log(resp);
        }
        try {
            // set data
            if (resp.status == 1) {
                var model = {
                    status: resp.status,
                    data: resp.data,
                    error: null
                };
                on_success(model);
            } else {
                var model;
                if (Object.prototype.toString.call(resp.error.message) == '[object Array]' || Object.prototype.toString.call(resp.error.message) == '[object Object]') {
                    model = {
                        status: resp.status,
                        data: resp.data,
                        error: {
                            code: 204,
                            message: resp.error.message
                        }
                    };
                } else {
                    model = {
                        status: resp.status,
                        data: resp.data,
                        error: {
                            code: 500,
                            message: resp.error.message
                        }
                    };
                }

                on_error(model);
            }
        } catch (ex) {
            console.log('[ERROR] ajax.js \n Modul : post/get ajax \n Function : validateResult \n Resolution: make sure retrieved data format match agreed expected format. see on ajax.js!');
            console.log(ex);
            console.log(resp);
            return false;
        }
    }
    function throwError(resp) {
        if (debug == true) {
            console.log(resp);
        }

        // check no network error
        var no_net = null;
        try {
            if (resp.responseJSON.code == "ENETUNREACH" || resp.responseJSON.code == "EHOSTUNREACH") {
                resp.status = 0;
                resp.statusText = "Can't connect";
            }
        } catch (ex) {}

        try {
            var model = {
                status: 0,
                data: null,
                error: {
                    code: $.isNumeric(resp.statusCode) ? resp.statusCode : $.isNumeric(resp.status) ? resp.status : 0,
                    message: { 0: resp.responseJSON ? resp.responseJSON.message : resp.statusText }
                }
            };
            on_error(model);
        } catch (ex) {
            console.log('[ERROR] ajax.js \n Modul : post/get ajax \n Function : validateResult \n Resolution: make sure retrieved data format match agreed expected format. see on ajax.js!');
            console.log(ex);
            console.log(resp);
            return false;
        }
    }

    // ajax engine
    function send(url, data, type, token) {
        if (debug == true) {
            console.log(url);
            console.log(data);
            console.log(token);
        }

        xhr = $.ajax({
            url: url,
            type: type,
            data: data,
            timeout: 10000,
            accept: 'application/json',
            contentType: "application/json",
            processData: false,
            dataType: 'json',
            success: on_success,
            error: throwError,
            headers: { "Authorization": 'Bearer ' + token }
        });
    }
}();

// 	Documentation:

// 	I. define ajax event action

// 	1. defineOnSuccess
// 		definition: A function to be called if the request succeeds.
// 		data: ajax response
// 		usage: 
// 			ajax.defineOnSuccess(function(response){
// 				// your code here
// 				console.log(response);
// 			});

// 	2. defineOnError
// 		definition: A function to be called if the request fails.
// 		data: ajax response
// 		usage: 
// 			ajax.defineOnError(function(response){
// 				// your code here
// 				console.log(response);
// 			});

// 	3. defineOnComplete
// 		definition: A function to be called when the request finishes
// 					(after success and error are executed)
// 		data: ajax response
// 		usage: 
// 			ajax.defineOnComplete(function(response){
// 				// your code here
// 				console.log(response);
// 			});

// 	II. Ajax call
// 	1. Get
// 		definition: ajax call using GET method
// 		parameter: url
// 		usage:
// 			ajax.get(YOUR URL);

// 	2. Post
// 		definition: ajax call using POST method
// 		parameter: url, data
// 		usage:
// 			ajax.get(YOUR URL, YOUR DATA);


// */

// /*
//   	prototype using promise
// 	------------------------------------------------------

// 	var ajax = function(){

// 	  this.get = function(url){
// 	      return $.ajax({
// 	          url: url,
// 	          type: 'GET',
// 	          dataType: 'json'
// 	      });
// 	  }
// 	}

// 	var success = function( resp ) {
// 	  console.log( resp );
// 	};

// 	var err = function( req, status, err ) {
// 	  console.log( err );
// 	};


// 	var qs = new ajax();
// 	qs.get('http://localhost:3000/ajax/akta/get/123').then( success, err ).done(function(){alert('done');});  
// */

// // import jquery
// import $ from 'jquery';

// window.ajax = new function(){

// 	// internal declare
// 	var on_success;
// 	var on_error;
// 	var on_complete;

// 	// interface ajax actions
// 	this.defineOnSuccess = function(syntax){
// 		on_success = syntax;
// 	}
// 	this.defineOnError = function(syntax){
// 		on_error = syntax;
// 	}
// 	this.defineOnComplete = function(syntax){
// 		on_complete = syntax;
// 	}

// 	// interface ajax function
// 	this.get = function (url, data = null){
// 		send(url, data, 'GET');
// 	}
// 	this.post = function (url, data){
// 		send(url, data, 'POST');
// 	}  	

// 		// ajax engine
// 		function send(url, data, type){
// 			$.ajax({
// 				url: url,
// 				type: type,
// 				data: data,
// 				timeout: 5000,
// 				contentType: false,
// 				processData: false,
// 				dataType: 'json',
// 				success: on_success,
// 				error: on_error,
// 				complete: on_complete
// 			});
// 		}
// }

// // This the interface
// // window.thunder.ajax = new ajax();

/***/ }),

/***/ "./resources/assets/thunderlab/js/inputmask/jquery.inputmask.bundle.min.js":
/***/ (function(module, exports) {

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/*!
* jquery.inputmask.bundle.js
* https://github.com/RobinHerbots/Inputmask
* Copyright (c) 2010 - 2017 Robin Herbots
* Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
* Version: 3.3.7
*/

!function (a) {
  function b(d) {
    if (c[d]) return c[d].exports;var e = c[d] = { i: d, l: !1, exports: {} };return a[d].call(e.exports, e, e.exports, b), e.l = !0, e.exports;
  }var c = {};b.m = a, b.c = c, b.i = function (a) {
    return a;
  }, b.d = function (a, c, d) {
    b.o(a, c) || Object.defineProperty(a, c, { configurable: !1, enumerable: !0, get: d });
  }, b.n = function (a) {
    var c = a && a.__esModule ? function () {
      return a.default;
    } : function () {
      return a;
    };return b.d(c, "a", c), c;
  }, b.o = function (a, b) {
    return Object.prototype.hasOwnProperty.call(a, b);
  }, b.p = "", b(b.s = 10);
}([function (a, b, c) {
  "use strict";
  var d, e, f;"function" == typeof Symbol && Symbol.iterator;!function (g) {
    e = [c(2)], d = g, void 0 !== (f = "function" == typeof d ? d.apply(b, e) : d) && (a.exports = f);
  }(function (a) {
    return a;
  });
}, function (a, b, c) {
  "use strict";
  var d,
      e,
      f,
      g = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (a) {
    return typeof a === "undefined" ? "undefined" : _typeof(a);
  } : function (a) {
    return a && "function" == typeof Symbol && a.constructor === Symbol && a !== Symbol.prototype ? "symbol" : typeof a === "undefined" ? "undefined" : _typeof(a);
  };!function (g) {
    e = [c(0), c(12), c(11)], d = g, void 0 !== (f = "function" == typeof d ? d.apply(b, e) : d) && (a.exports = f);
  }(function (a, b, c, d) {
    function e(b, c, g) {
      if (!(this instanceof e)) return new e(b, c, g);this.el = d, this.events = {}, this.maskset = d, this.refreshValue = !1, !0 !== g && (a.isPlainObject(b) ? c = b : (c = c || {}, c.alias = b), this.opts = a.extend(!0, {}, this.defaults, c), this.noMasksCache = c && c.definitions !== d, this.userOptions = c || {}, this.isRTL = this.opts.numericInput, f(this.opts.alias, c, this.opts));
    }function f(b, c, g) {
      var h = e.prototype.aliases[b];return h ? (h.alias && f(h.alias, d, g), a.extend(!0, g, h), a.extend(!0, g, c), !0) : (null === g.mask && (g.mask = b), !1);
    }function h(b, c) {
      function f(b, f, g) {
        var h = !1;if (null !== b && "" !== b || (h = null !== g.regex, h ? (b = g.regex, b = b.replace(/^(\^)(.*)(\$)$/, "$2")) : (h = !0, b = ".*")), 1 === b.length && !1 === g.greedy && 0 !== g.repeat && (g.placeholder = ""), g.repeat > 0 || "*" === g.repeat || "+" === g.repeat) {
          var i = "*" === g.repeat ? 0 : "+" === g.repeat ? 1 : g.repeat;b = g.groupmarker.start + b + g.groupmarker.end + g.quantifiermarker.start + i + "," + g.repeat + g.quantifiermarker.end;
        }var j,
            k = h ? "regex_" + g.regex : g.numericInput ? b.split("").reverse().join("") : b;return e.prototype.masksCache[k] === d || !0 === c ? (j = { mask: b, maskToken: e.prototype.analyseMask(b, h, g), validPositions: {}, _buffer: d, buffer: d, tests: {}, metadata: f, maskLength: d }, !0 !== c && (e.prototype.masksCache[k] = j, j = a.extend(!0, {}, e.prototype.masksCache[k]))) : j = a.extend(!0, {}, e.prototype.masksCache[k]), j;
      }if (a.isFunction(b.mask) && (b.mask = b.mask(b)), a.isArray(b.mask)) {
        if (b.mask.length > 1) {
          b.keepStatic = null === b.keepStatic || b.keepStatic;var g = b.groupmarker.start;return a.each(b.numericInput ? b.mask.reverse() : b.mask, function (c, e) {
            g.length > 1 && (g += b.groupmarker.end + b.alternatormarker + b.groupmarker.start), e.mask === d || a.isFunction(e.mask) ? g += e : g += e.mask;
          }), g += b.groupmarker.end, f(g, b.mask, b);
        }b.mask = b.mask.pop();
      }return b.mask && b.mask.mask !== d && !a.isFunction(b.mask.mask) ? f(b.mask.mask, b.mask, b) : f(b.mask, b.mask, b);
    }function i(f, h, j) {
      function o(a, b, c) {
        b = b || 0;var e,
            f,
            g,
            h = [],
            i = 0,
            k = r();-1 === (V = Y !== d ? Y.maxLength : d) && (V = d);do {
          !0 === a && p().validPositions[i] ? (g = p().validPositions[i], f = g.match, e = g.locator.slice(), h.push(!0 === c ? g.input : !1 === c ? f.nativeDef : J(i, f))) : (g = u(i, e, i - 1), f = g.match, e = g.locator.slice(), (!1 === j.jitMasking || i < k || "number" == typeof j.jitMasking && isFinite(j.jitMasking) && j.jitMasking > i) && h.push(!1 === c ? f.nativeDef : J(i, f))), i++;
        } while ((V === d || i < V) && (null !== f.fn || "" !== f.def) || b > i);return "" === h[h.length - 1] && h.pop(), p().maskLength = i + 1, h;
      }function p() {
        return h;
      }function q(a) {
        var b = p();b.buffer = d, !0 !== a && (b.validPositions = {}, b.p = 0);
      }function r(a, b, c) {
        var e = -1,
            f = -1,
            g = c || p().validPositions;a === d && (a = -1);for (var h in g) {
          var i = parseInt(h);g[i] && (b || !0 !== g[i].generatedInput) && (i <= a && (e = i), i >= a && (f = i));
        }return -1 !== e && a - e > 1 || f < a ? e : f;
      }function s(b, c, e, f) {
        var g,
            h = b,
            i = a.extend(!0, {}, p().validPositions),
            k = !1;for (p().p = b, g = c - 1; g >= h; g--) {
          p().validPositions[g] !== d && (!0 !== e && (!p().validPositions[g].match.optionality && function (a) {
            var b = p().validPositions[a];if (b !== d && null === b.match.fn) {
              var c = p().validPositions[a - 1],
                  e = p().validPositions[a + 1];return c !== d && e !== d;
            }return !1;
          }(g) || !1 === j.canClearPosition(p(), g, r(), f, j)) || delete p().validPositions[g]);
        }for (q(!0), g = h + 1; g <= r();) {
          for (; p().validPositions[h] !== d;) {
            h++;
          }if (g < h && (g = h + 1), p().validPositions[g] === d && E(g)) g++;else {
            var l = u(g);!1 === k && i[h] && i[h].match.def === l.match.def ? (p().validPositions[h] = a.extend(!0, {}, i[h]), p().validPositions[h].input = l.input, delete p().validPositions[g], g++) : w(h, l.match.def) ? !1 !== D(h, l.input || J(g), !0) && (delete p().validPositions[g], g++, k = !0) : E(g) || (g++, h--), h++;
          }
        }q(!0);
      }function t(a, b) {
        for (var c, e = a, f = r(), g = p().validPositions[f] || x(0)[0], h = g.alternation !== d ? g.locator[g.alternation].toString().split(",") : [], i = 0; i < e.length && (c = e[i], !(c.match && (j.greedy && !0 !== c.match.optionalQuantifier || (!1 === c.match.optionality || !1 === c.match.newBlockMarker) && !0 !== c.match.optionalQuantifier) && (g.alternation === d || g.alternation !== c.alternation || c.locator[g.alternation] !== d && C(c.locator[g.alternation].toString().split(","), h))) || !0 === b && (null !== c.match.fn || /[0-9a-bA-Z]/.test(c.match.def))); i++) {}return c;
      }function u(a, b, c) {
        return p().validPositions[a] || t(x(a, b ? b.slice() : b, c));
      }function v(a) {
        return p().validPositions[a] ? p().validPositions[a] : x(a)[0];
      }function w(a, b) {
        for (var c = !1, d = x(a), e = 0; e < d.length; e++) {
          if (d[e].match && d[e].match.def === b) {
            c = !0;break;
          }
        }return c;
      }function x(b, c, e) {
        function f(c, e, g, i) {
          function l(g, i, q) {
            function r(b, c) {
              var d = 0 === a.inArray(b, c.matches);return d || a.each(c.matches, function (a, e) {
                if (!0 === e.isQuantifier && (d = r(b, c.matches[a - 1]))) return !1;
              }), d;
            }function s(b, c, e) {
              var f, g;if (p().validPositions[b - 1] && e && p().tests[b]) for (var h = p().validPositions[b - 1].locator, i = p().tests[b][0].locator, j = 0; j < e; j++) {
                if (h[j] !== i[j]) return h.slice(e + 1);
              }return (p().tests[b] || p().validPositions[b]) && a.each(p().tests[b] || [p().validPositions[b]], function (a, b) {
                var h = e !== d ? e : b.alternation,
                    i = b.locator[h] !== d ? b.locator[h].toString().indexOf(c) : -1;(g === d || i < g) && -1 !== i && (f = b, g = i);
              }), f ? f.locator.slice((e !== d ? e : f.alternation) + 1) : e !== d ? s(b, c) : d;
            }if (k > 1e4) throw "Inputmask: There is probably an error in your mask definition or in the code. Create an issue on github with an example of the mask you are using. " + p().mask;if (k === b && g.matches === d) return m.push({ match: g, locator: i.reverse(), cd: o }), !0;if (g.matches !== d) {
              if (g.isGroup && q !== g) {
                if (g = l(c.matches[a.inArray(g, c.matches) + 1], i)) return !0;
              } else if (g.isOptional) {
                var t = g;if (g = f(g, e, i, q)) {
                  if (h = m[m.length - 1].match, !r(h, t)) return !0;n = !0, k = b;
                }
              } else if (g.isAlternator) {
                var u,
                    v = g,
                    w = [],
                    x = m.slice(),
                    y = i.length,
                    z = e.length > 0 ? e.shift() : -1;if (-1 === z || "string" == typeof z) {
                  var A,
                      B = k,
                      C = e.slice(),
                      D = [];if ("string" == typeof z) D = z.split(",");else for (A = 0; A < v.matches.length; A++) {
                    D.push(A);
                  }for (var E = 0; E < D.length; E++) {
                    if (A = parseInt(D[E]), m = [], e = s(k, A, y) || C.slice(), !0 !== (g = l(v.matches[A] || c.matches[A], [A].concat(i), q) || g) && g !== d && D[D.length - 1] < v.matches.length) {
                      var F = a.inArray(g, c.matches) + 1;c.matches.length > F && (g = l(c.matches[F], [F].concat(i.slice(1, i.length)), q)) && (D.push(F.toString()), a.each(m, function (a, b) {
                        b.alternation = i.length - 1;
                      }));
                    }u = m.slice(), k = B, m = [];for (var G = 0; G < u.length; G++) {
                      var H = u[G],
                          I = !1;H.alternation = H.alternation || y;for (var J = 0; J < w.length; J++) {
                        var K = w[J];if ("string" != typeof z || -1 !== a.inArray(H.locator[H.alternation].toString(), D)) {
                          if (function (a, b) {
                            return a.match.nativeDef === b.match.nativeDef || a.match.def === b.match.nativeDef || a.match.nativeDef === b.match.def;
                          }(H, K)) {
                            I = !0, H.alternation === K.alternation && -1 === K.locator[K.alternation].toString().indexOf(H.locator[H.alternation]) && (K.locator[K.alternation] = K.locator[K.alternation] + "," + H.locator[H.alternation], K.alternation = H.alternation), H.match.nativeDef === K.match.def && (H.locator[H.alternation] = K.locator[K.alternation], w.splice(w.indexOf(K), 1, H));break;
                          }if (H.match.def === K.match.def) {
                            I = !1;break;
                          }if (function (a, c) {
                            return null === a.match.fn && null !== c.match.fn && c.match.fn.test(a.match.def, p(), b, !1, j, !1);
                          }(H, K) || function (a, c) {
                            return null !== a.match.fn && null !== c.match.fn && c.match.fn.test(a.match.def.replace(/[\[\]]/g, ""), p(), b, !1, j, !1);
                          }(H, K)) {
                            H.alternation == K.alternation && -1 === H.locator[H.alternation].toString().indexOf(K.locator[K.alternation].toString().split("")[0]) && (H.na = H.na || H.locator[H.alternation].toString(), -1 === H.na.indexOf(H.locator[H.alternation].toString().split("")[0]) && (H.na = H.na + "," + H.locator[K.alternation].toString().split("")[0]), I = !0, H.locator[H.alternation] = K.locator[K.alternation].toString().split("")[0] + "," + H.locator[H.alternation], w.splice(w.indexOf(K), 0, H));break;
                          }
                        }
                      }I || w.push(H);
                    }
                  }"string" == typeof z && (w = a.map(w, function (b, c) {
                    if (isFinite(c)) {
                      var e = b.alternation,
                          f = b.locator[e].toString().split(",");b.locator[e] = d, b.alternation = d;for (var g = 0; g < f.length; g++) {
                        -1 !== a.inArray(f[g], D) && (b.locator[e] !== d ? (b.locator[e] += ",", b.locator[e] += f[g]) : b.locator[e] = parseInt(f[g]), b.alternation = e);
                      }if (b.locator[e] !== d) return b;
                    }
                  })), m = x.concat(w), k = b, n = m.length > 0, g = w.length > 0, e = C.slice();
                } else g = l(v.matches[z] || c.matches[z], [z].concat(i), q);if (g) return !0;
              } else if (g.isQuantifier && q !== c.matches[a.inArray(g, c.matches) - 1]) for (var L = g, M = e.length > 0 ? e.shift() : 0; M < (isNaN(L.quantifier.max) ? M + 1 : L.quantifier.max) && k <= b; M++) {
                var N = c.matches[a.inArray(L, c.matches) - 1];if (g = l(N, [M].concat(i), N)) {
                  if (h = m[m.length - 1].match, h.optionalQuantifier = M > L.quantifier.min - 1, r(h, N)) {
                    if (M > L.quantifier.min - 1) {
                      n = !0, k = b;break;
                    }return !0;
                  }return !0;
                }
              } else if (g = f(g, e, i, q)) return !0;
            } else k++;
          }for (var q = e.length > 0 ? e.shift() : 0; q < c.matches.length; q++) {
            if (!0 !== c.matches[q].isQuantifier) {
              var r = l(c.matches[q], [q].concat(g), i);if (r && k === b) return r;if (k > b) break;
            }
          }
        }function g(a) {
          if (j.keepStatic && b > 0 && a.length > 1 + ("" === a[a.length - 1].match.def ? 1 : 0) && !0 !== a[0].match.optionality && !0 !== a[0].match.optionalQuantifier && null === a[0].match.fn && !/[0-9a-bA-Z]/.test(a[0].match.def)) {
            if (p().validPositions[b - 1] === d) return [t(a)];if (p().validPositions[b - 1].alternation === a[0].alternation) return [t(a)];if (p().validPositions[b - 1]) return [t(a)];
          }return a;
        }var h,
            i = p().maskToken,
            k = c ? e : 0,
            l = c ? c.slice() : [0],
            m = [],
            n = !1,
            o = c ? c.join("") : "";if (b > -1) {
          if (c === d) {
            for (var q, r = b - 1; (q = p().validPositions[r] || p().tests[r]) === d && r > -1;) {
              r--;
            }q !== d && r > -1 && (l = function (b) {
              var c = [];return a.isArray(b) || (b = [b]), b.length > 0 && (b[0].alternation === d ? (c = t(b.slice()).locator.slice(), 0 === c.length && (c = b[0].locator.slice())) : a.each(b, function (a, b) {
                if ("" !== b.def) if (0 === c.length) c = b.locator.slice();else for (var d = 0; d < c.length; d++) {
                  b.locator[d] && -1 === c[d].toString().indexOf(b.locator[d]) && (c[d] += "," + b.locator[d]);
                }
              })), c;
            }(q), o = l.join(""), k = r);
          }if (p().tests[b] && p().tests[b][0].cd === o) return g(p().tests[b]);for (var s = l.shift(); s < i.length; s++) {
            if (f(i[s], l, [s]) && k === b || k > b) break;
          }
        }return (0 === m.length || n) && m.push({ match: { fn: null, cardinality: 0, optionality: !0, casing: null, def: "", placeholder: "" }, locator: [], cd: o }), c !== d && p().tests[b] ? g(a.extend(!0, [], m)) : (p().tests[b] = a.extend(!0, [], m), g(p().tests[b]));
      }function y() {
        return p()._buffer === d && (p()._buffer = o(!1, 1), p().buffer === d && (p().buffer = p()._buffer.slice())), p()._buffer;
      }function z(a) {
        return p().buffer !== d && !0 !== a || (p().buffer = o(!0, r(), !0)), p().buffer;
      }function A(a, b, c) {
        var e, f;if (!0 === a) q(), a = 0, b = c.length;else for (e = a; e < b; e++) {
          delete p().validPositions[e];
        }for (f = a, e = a; e < b; e++) {
          if (q(!0), c[e] !== j.skipOptionalPartCharacter) {
            var g = D(f, c[e], !0, !0);!1 !== g && (q(!0), f = g.caret !== d ? g.caret : g.pos + 1);
          }
        }
      }function B(b, c, d) {
        switch (j.casing || c.casing) {case "upper":
            b = b.toUpperCase();break;case "lower":
            b = b.toLowerCase();break;case "title":
            var f = p().validPositions[d - 1];b = 0 === d || f && f.input === String.fromCharCode(e.keyCode.SPACE) ? b.toUpperCase() : b.toLowerCase();break;default:
            if (a.isFunction(j.casing)) {
              var g = Array.prototype.slice.call(arguments);g.push(p().validPositions), b = j.casing.apply(this, g);
            }}return b;
      }function C(b, c, e) {
        for (var f, g = j.greedy ? c : c.slice(0, 1), h = !1, i = e !== d ? e.split(",") : [], k = 0; k < i.length; k++) {
          -1 !== (f = b.indexOf(i[k])) && b.splice(f, 1);
        }for (var l = 0; l < b.length; l++) {
          if (-1 !== a.inArray(b[l], g)) {
            h = !0;break;
          }
        }return h;
      }function D(b, c, f, g, h) {
        function i(a) {
          var b = Z ? a.begin - a.end > 1 || a.begin - a.end == 1 : a.end - a.begin > 1 || a.end - a.begin == 1;return b && 0 === a.begin && a.end === p().maskLength ? "full" : b;
        }function k(c, e, f) {
          var h = !1;return a.each(x(c), function (k, m) {
            for (var n = m.match, o = e ? 1 : 0, t = "", u = n.cardinality; u > o; u--) {
              t += H(c - (u - 1));
            }if (e && (t += e), z(!0), !1 !== (h = null != n.fn ? n.fn.test(t, p(), c, f, j, i(b)) : (e === n.def || e === j.skipOptionalPartCharacter) && "" !== n.def && { c: J(c, n, !0) || n.def, pos: c })) {
              var v = h.c !== d ? h.c : e;v = v === j.skipOptionalPartCharacter && null === n.fn ? J(c, n, !0) || n.def : v;var w = c,
                  x = z();if (h.remove !== d && (a.isArray(h.remove) || (h.remove = [h.remove]), a.each(h.remove.sort(function (a, b) {
                return b - a;
              }), function (a, b) {
                s(b, b + 1, !0);
              })), h.insert !== d && (a.isArray(h.insert) || (h.insert = [h.insert]), a.each(h.insert.sort(function (a, b) {
                return a - b;
              }), function (a, b) {
                D(b.pos, b.c, !0, g);
              })), h.refreshFromBuffer) {
                var y = h.refreshFromBuffer;if (A(!0 === y ? y : y.start, y.end, x), h.pos === d && h.c === d) return h.pos = r(), !1;if ((w = h.pos !== d ? h.pos : c) !== c) return h = a.extend(h, D(w, v, !0, g)), !1;
              } else if (!0 !== h && h.pos !== d && h.pos !== c && (w = h.pos, A(c, w, z().slice()), w !== c)) return h = a.extend(h, D(w, v, !0)), !1;return (!0 === h || h.pos !== d || h.c !== d) && (k > 0 && q(!0), l(w, a.extend({}, m, { input: B(v, n, w) }), g, i(b)) || (h = !1), !1);
            }
          }), h;
        }function l(b, c, e, f) {
          if (f || j.insertMode && p().validPositions[b] !== d && e === d) {
            var g,
                h = a.extend(!0, {}, p().validPositions),
                i = r(d, !0);for (g = b; g <= i; g++) {
              delete p().validPositions[g];
            }p().validPositions[b] = a.extend(!0, {}, c);var k,
                l = !0,
                n = p().validPositions,
                o = !1,
                s = p().maskLength;for (g = k = b; g <= i; g++) {
              var t = h[g];if (t !== d) for (var u = k; u < p().maskLength && (null === t.match.fn && n[g] && (!0 === n[g].match.optionalQuantifier || !0 === n[g].match.optionality) || null != t.match.fn);) {
                if (u++, !1 === o && h[u] && h[u].match.def === t.match.def) p().validPositions[u] = a.extend(!0, {}, h[u]), p().validPositions[u].input = t.input, m(u), k = u, l = !0;else if (w(u, t.match.def)) {
                  var v = D(u, t.input, !0, !0);l = !1 !== v, k = v.caret || v.insert ? r() : u, o = !0;
                } else if (!(l = !0 === t.generatedInput) && u >= p().maskLength - 1) break;if (p().maskLength < s && (p().maskLength = s), l) break;
              }if (!l) break;
            }if (!l) return p().validPositions = a.extend(!0, {}, h), q(!0), !1;
          } else p().validPositions[b] = a.extend(!0, {}, c);return q(!0), !0;
        }function m(b) {
          for (var c = b - 1; c > -1 && !p().validPositions[c]; c--) {}var e, f;for (c++; c < b; c++) {
            p().validPositions[c] === d && (!1 === j.jitMasking || j.jitMasking > c) && (f = x(c, u(c - 1).locator, c - 1).slice(), "" === f[f.length - 1].match.def && f.pop(), (e = t(f)) && (e.match.def === j.radixPointDefinitionSymbol || !E(c, !0) || a.inArray(j.radixPoint, z()) < c && e.match.fn && e.match.fn.test(J(c), p(), c, !1, j)) && !1 !== (o = k(c, J(c, e.match, !0) || (null == e.match.fn ? e.match.def : "" !== J(c) ? J(c) : z()[c]), !0)) && (p().validPositions[o.pos || c].generatedInput = !0));
          }
        }f = !0 === f;var n = b;b.begin !== d && (n = Z && !i(b) ? b.end : b.begin);var o = !0,
            v = a.extend(!0, {}, p().validPositions);if (a.isFunction(j.preValidation) && !f && !0 !== g && (o = j.preValidation(z(), n, c, i(b), j)), !0 === o) {
          if (m(n), i(b) && (Q(d, e.keyCode.DELETE, b, !0), n = p().p), n < p().maskLength && (V === d || n < V) && (o = k(n, c, f), (!f || !0 === g) && !1 === o)) {
            var y = p().validPositions[n];if (!y || null !== y.match.fn || y.match.def !== c && c !== j.skipOptionalPartCharacter) {
              if ((j.insertMode || p().validPositions[F(n)] === d) && !E(n, !0)) for (var G = n + 1, I = F(n); G <= I; G++) {
                if (!1 !== (o = k(G, c, f))) {
                  !function (b, c) {
                    var e = p().validPositions[c];if (e) for (var f = e.locator, g = f.length, h = b; h < c; h++) {
                      if (p().validPositions[h] === d && !E(h, !0)) {
                        var i = x(h).slice(),
                            j = t(i, !0),
                            m = -1;"" === i[i.length - 1].match.def && i.pop(), a.each(i, function (a, b) {
                          for (var c = 0; c < g; c++) {
                            if (b.locator[c] === d || !C(b.locator[c].toString().split(","), f[c].toString().split(","), b.na)) {
                              var e = f[c],
                                  h = j.locator[c],
                                  i = b.locator[c];e - h > Math.abs(e - i) && (j = b);break;
                            }m < c && (m = c, j = b);
                          }
                        }), j = a.extend({}, j, { input: J(h, j.match, !0) || j.match.def }), j.generatedInput = !0, l(h, j, !0), p().validPositions[c] = d, k(c, e.input, !0);
                      }
                    }
                  }(n, o.pos !== d ? o.pos : G), n = G;break;
                }
              }
            } else o = { caret: F(n) };
          }!1 === o && j.keepStatic && !f && !0 !== h && (o = function (b, c, e) {
            var f,
                h,
                i,
                k,
                l,
                m,
                n,
                o,
                s = a.extend(!0, {}, p().validPositions),
                t = !1,
                u = r();for (k = p().validPositions[u]; u >= 0; u--) {
              if ((i = p().validPositions[u]) && i.alternation !== d) {
                if (f = u, h = p().validPositions[f].alternation, k.locator[i.alternation] !== i.locator[i.alternation]) break;k = i;
              }
            }if (h !== d) {
              o = parseInt(f);var v = k.locator[k.alternation || h] !== d ? k.locator[k.alternation || h] : n[0];v.length > 0 && (v = v.split(",")[0]);var w = p().validPositions[o],
                  y = p().validPositions[o - 1];a.each(x(o, y ? y.locator : d, o - 1), function (f, i) {
                n = i.locator[h] ? i.locator[h].toString().split(",") : [];for (var k = 0; k < n.length; k++) {
                  var u = [],
                      x = 0,
                      y = 0,
                      z = !1;if (v < n[k] && (i.na === d || -1 === a.inArray(n[k], i.na.split(",")) || -1 === a.inArray(v.toString(), n))) {
                    p().validPositions[o] = a.extend(!0, {}, i);var A = p().validPositions[o].locator;for (p().validPositions[o].locator[h] = parseInt(n[k]), null == i.match.fn ? (w.input !== i.match.def && (z = !0, !0 !== w.generatedInput && u.push(w.input)), y++, p().validPositions[o].generatedInput = !/[0-9a-bA-Z]/.test(i.match.def), p().validPositions[o].input = i.match.def) : p().validPositions[o].input = w.input, l = o + 1; l < r(d, !0) + 1; l++) {
                      m = p().validPositions[l], m && !0 !== m.generatedInput && /[0-9a-bA-Z]/.test(m.input) ? u.push(m.input) : l < b && x++, delete p().validPositions[l];
                    }for (z && u[0] === i.match.def && u.shift(), q(!0), t = !0; u.length > 0;) {
                      var B = u.shift();if (B !== j.skipOptionalPartCharacter && !(t = D(r(d, !0) + 1, B, !1, g, !0))) break;
                    }if (t) {
                      p().validPositions[o].locator = A;var C = r(b) + 1;for (l = o + 1; l < r() + 1; l++) {
                        ((m = p().validPositions[l]) === d || null == m.match.fn) && l < b + (y - x) && y++;
                      }b += y - x, t = D(b > C ? C : b, c, e, g, !0);
                    }if (t) return !1;q(), p().validPositions = a.extend(!0, {}, s);
                  }
                }
              });
            }return t;
          }(n, c, f)), !0 === o && (o = { pos: n });
        }if (a.isFunction(j.postValidation) && !1 !== o && !f && !0 !== g) {
          var K = j.postValidation(z(!0), o, j);if (K.refreshFromBuffer && K.buffer) {
            var L = K.refreshFromBuffer;A(!0 === L ? L : L.start, L.end, K.buffer);
          }o = !0 === K ? o : K;
        }return o && o.pos === d && (o.pos = n), !1 === o && (q(!0), p().validPositions = a.extend(!0, {}, v)), o;
      }function E(a, b) {
        var c = u(a).match;if ("" === c.def && (c = v(a).match), null != c.fn) return c.fn;if (!0 !== b && a > -1) {
          var d = x(a);return d.length > 1 + ("" === d[d.length - 1].match.def ? 1 : 0);
        }return !1;
      }function F(a, b) {
        var c = p().maskLength;if (a >= c) return c;var d = a;for (x(c + 1).length > 1 && (o(!0, c + 1, !0), c = p().maskLength); ++d < c && (!0 === b && (!0 !== v(d).match.newBlockMarker || !E(d)) || !0 !== b && !E(d));) {}return d;
      }function G(a, b) {
        var c,
            d = a;if (d <= 0) return 0;for (; --d > 0 && (!0 === b && !0 !== v(d).match.newBlockMarker || !0 !== b && !E(d) && (c = x(d), c.length < 2 || 2 === c.length && "" === c[1].match.def));) {}return d;
      }function H(a) {
        return p().validPositions[a] === d ? J(a) : p().validPositions[a].input;
      }function I(b, c, e, f, g) {
        if (f && a.isFunction(j.onBeforeWrite)) {
          var h = j.onBeforeWrite(f, c, e, j);if (h) {
            if (h.refreshFromBuffer) {
              var i = h.refreshFromBuffer;A(!0 === i ? i : i.start, i.end, h.buffer || c), c = z(!0);
            }e !== d && (e = h.caret !== d ? h.caret : e);
          }
        }b !== d && (b.inputmask._valueSet(c.join("")), e === d || f !== d && "blur" === f.type ? S(b, c, e) : n && "input" === f.type ? setTimeout(function () {
          M(b, e);
        }, 0) : M(b, e), !0 === g && (_ = !0, a(b).trigger("input")));
      }function J(b, c, e) {
        if (c = c || v(b).match, c.placeholder !== d || !0 === e) return a.isFunction(c.placeholder) ? c.placeholder(j) : c.placeholder;if (null === c.fn) {
          if (b > -1 && p().validPositions[b] === d) {
            var f,
                g = x(b),
                h = [];if (g.length > 1 + ("" === g[g.length - 1].match.def ? 1 : 0)) for (var i = 0; i < g.length; i++) {
              if (!0 !== g[i].match.optionality && !0 !== g[i].match.optionalQuantifier && (null === g[i].match.fn || f === d || !1 !== g[i].match.fn.test(f.match.def, p(), b, !0, j)) && (h.push(g[i]), null === g[i].match.fn && (f = g[i]), h.length > 1 && /[0-9a-bA-Z]/.test(h[0].match.def))) return j.placeholder.charAt(b % j.placeholder.length);
            }
          }return c.def;
        }return j.placeholder.charAt(b % j.placeholder.length);
      }function K(b, f, g, h, i) {
        function k(a, b) {
          return -1 !== y().slice(a, F(a)).join("").indexOf(b) && !E(a) && v(a).match.nativeDef === b.charAt(b.length - 1);
        }var l = h.slice(),
            m = "",
            n = 0,
            o = d;if (q(), p().p = F(-1), !g) if (!0 !== j.autoUnmask) {
          var s = y().slice(0, F(-1)).join(""),
              t = l.join("").match(new RegExp("^" + e.escapeRegex(s), "g"));t && t.length > 0 && (l.splice(0, t.length * s.length), n = F(n));
        } else n = F(n);if (a.each(l, function (c, e) {
          if (e !== d) {
            var f = new a.Event("_checkval");f.which = e.charCodeAt(0), m += e;var h = r(d, !0),
                i = p().validPositions[h],
                l = u(h + 1, i ? i.locator.slice() : d, h);if (!k(n, m) || g || j.autoUnmask) {
              var s = g ? c : null == l.match.fn && l.match.optionality && h + 1 < p().p ? h + 1 : p().p;o = da.keypressEvent.call(b, f, !0, !1, g, s), n = s + 1, m = "";
            } else o = da.keypressEvent.call(b, f, !0, !1, !0, h + 1);if (!1 !== o && !g && a.isFunction(j.onBeforeWrite)) {
              var t = o.forwardPosition;if (o = j.onBeforeWrite(f, z(), o.forwardPosition, j), o.forwardPosition = t, o && o.refreshFromBuffer) {
                var v = o.refreshFromBuffer;A(!0 === v ? v : v.start, v.end, o.buffer), q(!0), o.caret && (p().p = o.caret, o.forwardPosition = o.caret);
              }
            }
          }
        }), f) {
          var w = d;c.activeElement === b && o && (w = j.numericInput ? G(o.forwardPosition) : o.forwardPosition), I(b, z(), w, i || new a.Event("checkval"), i && "input" === i.type);
        }
      }function L(b) {
        if (b) {
          if (b.inputmask === d) return b.value;b.inputmask && b.inputmask.refreshValue && da.setValueEvent.call(b);
        }var c = [],
            e = p().validPositions;for (var f in e) {
          e[f].match && null != e[f].match.fn && c.push(e[f].input);
        }var g = 0 === c.length ? "" : (Z ? c.reverse() : c).join("");if (a.isFunction(j.onUnMask)) {
          var h = (Z ? z().slice().reverse() : z()).join("");g = j.onUnMask(h, g, j);
        }return g;
      }function M(a, e, f, g) {
        function h(a) {
          if (!0 !== g && Z && "number" == typeof a && (!j.greedy || "" !== j.placeholder)) {
            a = z().join("").length - a;
          }return a;
        }var i;if (e === d) return a.setSelectionRange ? (e = a.selectionStart, f = a.selectionEnd) : b.getSelection ? (i = b.getSelection().getRangeAt(0), i.commonAncestorContainer.parentNode !== a && i.commonAncestorContainer !== a || (e = i.startOffset, f = i.endOffset)) : c.selection && c.selection.createRange && (i = c.selection.createRange(), e = 0 - i.duplicate().moveStart("character", -a.inputmask._valueGet().length), f = e + i.text.length), { begin: h(e), end: h(f) };if (e.begin !== d && (f = e.end, e = e.begin), "number" == typeof e) {
          e = h(e), f = h(f), f = "number" == typeof f ? f : e;var l = parseInt(((a.ownerDocument.defaultView || b).getComputedStyle ? (a.ownerDocument.defaultView || b).getComputedStyle(a, null) : a.currentStyle).fontSize) * f;if (a.scrollLeft = l > a.scrollWidth ? l : 0, k || !1 !== j.insertMode || e !== f || f++, a.setSelectionRange) a.selectionStart = e, a.selectionEnd = f;else if (b.getSelection) {
            if (i = c.createRange(), a.firstChild === d || null === a.firstChild) {
              var m = c.createTextNode("");a.appendChild(m);
            }i.setStart(a.firstChild, e < a.inputmask._valueGet().length ? e : a.inputmask._valueGet().length), i.setEnd(a.firstChild, f < a.inputmask._valueGet().length ? f : a.inputmask._valueGet().length), i.collapse(!0);var n = b.getSelection();n.removeAllRanges(), n.addRange(i);
          } else a.createTextRange && (i = a.createTextRange(), i.collapse(!0), i.moveEnd("character", f), i.moveStart("character", e), i.select());S(a, d, { begin: e, end: f });
        }
      }function N(b) {
        var c,
            e,
            f = z(),
            g = f.length,
            h = r(),
            i = {},
            j = p().validPositions[h],
            k = j !== d ? j.locator.slice() : d;for (c = h + 1; c < f.length; c++) {
          e = u(c, k, c - 1), k = e.locator.slice(), i[c] = a.extend(!0, {}, e);
        }var l = j && j.alternation !== d ? j.locator[j.alternation] : d;for (c = g - 1; c > h && (e = i[c], (e.match.optionality || e.match.optionalQuantifier && e.match.newBlockMarker || l && (l !== i[c].locator[j.alternation] && null != e.match.fn || null === e.match.fn && e.locator[j.alternation] && C(e.locator[j.alternation].toString().split(","), l.toString().split(",")) && "" !== x(c)[0].def)) && f[c] === J(c, e.match)); c--) {
          g--;
        }return b ? { l: g, def: i[g] ? i[g].match : d } : g;
      }function O(a) {
        for (var b, c = N(), e = a.length, f = p().validPositions[r()]; c < e && !E(c, !0) && (b = f !== d ? u(c, f.locator.slice(""), f) : v(c)) && !0 !== b.match.optionality && (!0 !== b.match.optionalQuantifier && !0 !== b.match.newBlockMarker || c + 1 === e && "" === (f !== d ? u(c + 1, f.locator.slice(""), f) : v(c + 1)).match.def);) {
          c++;
        }for (; (b = p().validPositions[c - 1]) && b && b.match.optionality && b.input === j.skipOptionalPartCharacter;) {
          c--;
        }return a.splice(c), a;
      }function P(b) {
        if (a.isFunction(j.isComplete)) return j.isComplete(b, j);if ("*" === j.repeat) return d;var c = !1,
            e = N(!0),
            f = G(e.l);if (e.def === d || e.def.newBlockMarker || e.def.optionality || e.def.optionalQuantifier) {
          c = !0;for (var g = 0; g <= f; g++) {
            var h = u(g).match;if (null !== h.fn && p().validPositions[g] === d && !0 !== h.optionality && !0 !== h.optionalQuantifier || null === h.fn && b[g] !== J(g, h)) {
              c = !1;break;
            }
          }
        }return c;
      }function Q(b, c, f, g, h) {
        if ((j.numericInput || Z) && (c === e.keyCode.BACKSPACE ? c = e.keyCode.DELETE : c === e.keyCode.DELETE && (c = e.keyCode.BACKSPACE), Z)) {
          var i = f.end;f.end = f.begin, f.begin = i;
        }c === e.keyCode.BACKSPACE && (f.end - f.begin < 1 || !1 === j.insertMode) ? (f.begin = G(f.begin), p().validPositions[f.begin] !== d && p().validPositions[f.begin].input === j.groupSeparator && f.begin--) : c === e.keyCode.DELETE && f.begin === f.end && (f.end = E(f.end, !0) && p().validPositions[f.end] && p().validPositions[f.end].input !== j.radixPoint ? f.end + 1 : F(f.end) + 1, p().validPositions[f.begin] !== d && p().validPositions[f.begin].input === j.groupSeparator && f.end++), s(f.begin, f.end, !1, g), !0 !== g && function () {
          if (j.keepStatic) {
            for (var c = [], e = r(-1, !0), f = a.extend(!0, {}, p().validPositions), g = p().validPositions[e]; e >= 0; e--) {
              var h = p().validPositions[e];if (h) {
                if (!0 !== h.generatedInput && /[0-9a-bA-Z]/.test(h.input) && c.push(h.input), delete p().validPositions[e], h.alternation !== d && h.locator[h.alternation] !== g.locator[h.alternation]) break;g = h;
              }
            }if (e > -1) for (p().p = F(r(-1, !0)); c.length > 0;) {
              var i = new a.Event("keypress");i.which = c.pop().charCodeAt(0), da.keypressEvent.call(b, i, !0, !1, !1, p().p);
            } else p().validPositions = a.extend(!0, {}, f);
          }
        }();var k = r(f.begin, !0);if (k < f.begin) p().p = F(k);else if (!0 !== g && (p().p = f.begin, !0 !== h)) for (; p().p < k && p().validPositions[p().p] === d;) {
          p().p++;
        }
      }function R(d) {
        function e(a) {
          var b,
              e = c.createElement("span");for (var f in h) {
            isNaN(f) && -1 !== f.indexOf("font") && (e.style[f] = h[f]);
          }e.style.textTransform = h.textTransform, e.style.letterSpacing = h.letterSpacing, e.style.position = "absolute", e.style.height = "auto", e.style.width = "auto", e.style.visibility = "hidden", e.style.whiteSpace = "nowrap", c.body.appendChild(e);var g,
              i = d.inputmask._valueGet(),
              j = 0;for (b = 0, g = i.length; b <= g; b++) {
            if (e.innerHTML += i.charAt(b) || "_", e.offsetWidth >= a) {
              var k = a - j,
                  l = e.offsetWidth - a;e.innerHTML = i.charAt(b), k -= e.offsetWidth / 3, b = k < l ? b - 1 : b;break;
            }j = e.offsetWidth;
          }return c.body.removeChild(e), b;
        }function f() {
          W.style.position = "absolute", W.style.top = g.top + "px", W.style.left = g.left + "px", W.style.width = parseInt(d.offsetWidth) - parseInt(h.paddingLeft) - parseInt(h.paddingRight) - parseInt(h.borderLeftWidth) - parseInt(h.borderRightWidth) + "px", W.style.height = parseInt(d.offsetHeight) - parseInt(h.paddingTop) - parseInt(h.paddingBottom) - parseInt(h.borderTopWidth) - parseInt(h.borderBottomWidth) + "px", W.style.lineHeight = W.style.height, W.style.zIndex = isNaN(h.zIndex) ? -1 : h.zIndex - 1, W.style.webkitAppearance = "textfield", W.style.mozAppearance = "textfield", W.style.Appearance = "textfield";
        }var g = a(d).position(),
            h = (d.ownerDocument.defaultView || b).getComputedStyle(d, null);W = c.createElement("div"), c.body.appendChild(W);for (var i in h) {
          h.hasOwnProperty(i) && isNaN(i) && "cssText" !== i && -1 == i.indexOf("webkit") && (W.style[i] = h[i]);
        }d.style.backgroundColor = "transparent", d.style.color = "transparent", d.style.webkitAppearance = "caret", d.style.mozAppearance = "caret", d.style.Appearance = "caret", f(), a(b).on("resize", function (c) {
          g = a(d).position(), h = (d.ownerDocument.defaultView || b).getComputedStyle(d, null), f();
        }), a(d).on("click", function (a) {
          return M(d, e(a.clientX)), da.clickEvent.call(this, [a]);
        }), a(d).on("keydown", function (a) {
          a.shiftKey || !1 === j.insertMode || setTimeout(function () {
            S(d);
          }, 0);
        });
      }function S(a, b, e) {
        function f() {
          h || null !== k.fn && l.input !== d ? h && null !== k.fn && l.input !== d && (h = !1, g += "</span>") : (h = !0, g += "<span class='im-static''>");
        }if (W !== d) {
          b = b || z(), e === d ? e = M(a) : e.begin === d && (e = { begin: e, end: e });var g = "",
              h = !1;if ("" != b) {
            var i,
                k,
                l,
                m = 0,
                n = r();do {
              m === e.begin && c.activeElement === a && (g += "<span class='im-caret' style='border-right-width: 1px;border-right-style: solid;'></span>"), p().validPositions[m] ? (l = p().validPositions[m], k = l.match, i = l.locator.slice(), f(), g += l.input) : (l = u(m, i, m - 1), k = l.match, i = l.locator.slice(), (!1 === j.jitMasking || m < n || "number" == typeof j.jitMasking && isFinite(j.jitMasking) && j.jitMasking > m) && (f(), g += J(m, k))), m++;
            } while ((V === d || m < V) && (null !== k.fn || "" !== k.def) || n > m);
          }W.innerHTML = g;
        }
      }h = h || this.maskset, j = j || this.opts;var T,
          U,
          V,
          W,
          X,
          Y = this.el,
          Z = this.isRTL,
          $ = !1,
          _ = !1,
          aa = !1,
          ba = !1,
          ca = { on: function on(b, c, f) {
          var g = function g(b) {
            if (this.inputmask === d && "FORM" !== this.nodeName) {
              var c = a.data(this, "_inputmask_opts");c ? new e(c).mask(this) : ca.off(this);
            } else {
              if ("setvalue" === b.type || "FORM" === this.nodeName || !(this.disabled || this.readOnly && !("keydown" === b.type && b.ctrlKey && 67 === b.keyCode || !1 === j.tabThrough && b.keyCode === e.keyCode.TAB))) {
                switch (b.type) {case "input":
                    if (!0 === _) return _ = !1, b.preventDefault();break;case "keydown":
                    $ = !1, _ = !1;break;case "keypress":
                    if (!0 === $) return b.preventDefault();$ = !0;break;case "click":
                    if (l || m) {
                      var g = this,
                          h = arguments;return setTimeout(function () {
                        f.apply(g, h);
                      }, 0), !1;
                    }}var i = f.apply(this, arguments);return !1 === i && (b.preventDefault(), b.stopPropagation()), i;
              }b.preventDefault();
            }
          };b.inputmask.events[c] = b.inputmask.events[c] || [], b.inputmask.events[c].push(g), -1 !== a.inArray(c, ["submit", "reset"]) ? null != b.form && a(b.form).on(c, g) : a(b).on(c, g);
        }, off: function off(b, c) {
          if (b.inputmask && b.inputmask.events) {
            var d;c ? (d = [], d[c] = b.inputmask.events[c]) : d = b.inputmask.events, a.each(d, function (c, d) {
              for (; d.length > 0;) {
                var e = d.pop();-1 !== a.inArray(c, ["submit", "reset"]) ? null != b.form && a(b.form).off(c, e) : a(b).off(c, e);
              }delete b.inputmask.events[c];
            });
          }
        } },
          da = { keydownEvent: function keydownEvent(b) {
          var d = this,
              f = a(d),
              g = b.keyCode,
              h = M(d);if (g === e.keyCode.BACKSPACE || g === e.keyCode.DELETE || m && g === e.keyCode.BACKSPACE_SAFARI || b.ctrlKey && g === e.keyCode.X && !function (a) {
            var b = c.createElement("input"),
                d = "on" + a,
                e = d in b;return e || (b.setAttribute(d, "return;"), e = "function" == typeof b[d]), b = null, e;
          }("cut")) b.preventDefault(), Q(d, g, h), I(d, z(!0), p().p, b, d.inputmask._valueGet() !== z().join("")), d.inputmask._valueGet() === y().join("") ? f.trigger("cleared") : !0 === P(z()) && f.trigger("complete");else if (g === e.keyCode.END || g === e.keyCode.PAGE_DOWN) {
            b.preventDefault();var i = F(r());j.insertMode || i !== p().maskLength || b.shiftKey || i--, M(d, b.shiftKey ? h.begin : i, i, !0);
          } else g === e.keyCode.HOME && !b.shiftKey || g === e.keyCode.PAGE_UP ? (b.preventDefault(), M(d, 0, b.shiftKey ? h.begin : 0, !0)) : (j.undoOnEscape && g === e.keyCode.ESCAPE || 90 === g && b.ctrlKey) && !0 !== b.altKey ? (K(d, !0, !1, T.split("")), f.trigger("click")) : g !== e.keyCode.INSERT || b.shiftKey || b.ctrlKey ? !0 === j.tabThrough && g === e.keyCode.TAB ? (!0 === b.shiftKey ? (null === v(h.begin).match.fn && (h.begin = F(h.begin)), h.end = G(h.begin, !0), h.begin = G(h.end, !0)) : (h.begin = F(h.begin, !0), h.end = F(h.begin, !0), h.end < p().maskLength && h.end--), h.begin < p().maskLength && (b.preventDefault(), M(d, h.begin, h.end))) : b.shiftKey || !1 === j.insertMode && (g === e.keyCode.RIGHT ? setTimeout(function () {
            var a = M(d);M(d, a.begin);
          }, 0) : g === e.keyCode.LEFT && setTimeout(function () {
            var a = M(d);M(d, Z ? a.begin + 1 : a.begin - 1);
          }, 0)) : (j.insertMode = !j.insertMode, M(d, j.insertMode || h.begin !== p().maskLength ? h.begin : h.begin - 1));j.onKeyDown.call(this, b, z(), M(d).begin, j), aa = -1 !== a.inArray(g, j.ignorables);
        }, keypressEvent: function keypressEvent(b, c, f, g, h) {
          var i = this,
              k = a(i),
              l = b.which || b.charCode || b.keyCode;if (!(!0 === c || b.ctrlKey && b.altKey) && (b.ctrlKey || b.metaKey || aa)) return l === e.keyCode.ENTER && T !== z().join("") && (T = z().join(""), setTimeout(function () {
            k.trigger("change");
          }, 0)), !0;if (l) {
            46 === l && !1 === b.shiftKey && "" !== j.radixPoint && (l = j.radixPoint.charCodeAt(0));var m,
                n = c ? { begin: h, end: h } : M(i),
                o = String.fromCharCode(l);p().writeOutBuffer = !0;var r = D(n, o, g);if (!1 !== r && (q(!0), m = r.caret !== d ? r.caret : c ? r.pos + 1 : F(r.pos), p().p = m), !1 !== f && (setTimeout(function () {
              j.onKeyValidation.call(i, l, r, j);
            }, 0), p().writeOutBuffer && !1 !== r)) {
              var s = z();I(i, s, j.numericInput && r.caret === d ? G(m) : m, b, !0 !== c), !0 !== c && setTimeout(function () {
                !0 === P(s) && k.trigger("complete");
              }, 0);
            }if (b.preventDefault(), c) return !1 !== r && (r.forwardPosition = m), r;
          }
        }, pasteEvent: function pasteEvent(c) {
          var d,
              e = this,
              f = c.originalEvent || c,
              g = a(e),
              h = e.inputmask._valueGet(!0),
              i = M(e);Z && (d = i.end, i.end = i.begin, i.begin = d);var k = h.substr(0, i.begin),
              l = h.substr(i.end, h.length);if (k === (Z ? y().reverse() : y()).slice(0, i.begin).join("") && (k = ""), l === (Z ? y().reverse() : y()).slice(i.end).join("") && (l = ""), Z && (d = k, k = l, l = d), b.clipboardData && b.clipboardData.getData) h = k + b.clipboardData.getData("Text") + l;else {
            if (!f.clipboardData || !f.clipboardData.getData) return !0;h = k + f.clipboardData.getData("text/plain") + l;
          }var m = h;if (a.isFunction(j.onBeforePaste)) {
            if (!1 === (m = j.onBeforePaste(h, j))) return c.preventDefault();m || (m = h);
          }return K(e, !1, !1, Z ? m.split("").reverse() : m.toString().split("")), I(e, z(), F(r()), c, T !== z().join("")), !0 === P(z()) && g.trigger("complete"), c.preventDefault();
        }, inputFallBackEvent: function inputFallBackEvent(b) {
          var c = this,
              d = c.inputmask._valueGet();if (z().join("") !== d) {
            var f = M(c);if ("." === d.charAt(f.begin - 1) && "" !== j.radixPoint && (d = d.split(""), d[f.begin - 1] = j.radixPoint.charAt(0), d = d.join("")), d.charAt(f.begin - 1) === j.radixPoint && d.length > z().length) {
              var g = new a.Event("keypress");return g.which = j.radixPoint.charCodeAt(0), da.keypressEvent.call(c, g, !0, !0, !1, f.begin), !1;
            }if (d = d.replace(new RegExp("(" + e.escapeRegex(y().join("")) + ")*"), ""), l) {
              var h = d.replace(z().join(""), "");if (1 === h.length) {
                var g = new a.Event("keypress");return g.which = h.charCodeAt(0), da.keypressEvent.call(c, g, !0, !0, !1, p().validPositions[f.begin - 1] ? f.begin : f.begin - 1), !1;
              }
            }if (f.begin > d.length && (M(c, d.length), f = M(c)), z().length - d.length != 1 || d.charAt(f.begin) === z()[f.begin] || d.charAt(f.begin + 1) === z()[f.begin] || E(f.begin)) {
              var i = [],
                  k = y().join("");for (i.push(d.substr(0, f.begin)), i.push(d.substr(f.begin)); null === d.match(e.escapeRegex(k) + "$");) {
                k = k.slice(1);
              }d = d.replace(k, ""), a.isFunction(j.onBeforeMask) && (d = j.onBeforeMask(d, j) || d), K(c, !0, !1, d.split(""), b), function (a, b, c) {
                var d = M(a).begin,
                    f = a.inputmask._valueGet(),
                    g = f.indexOf(b),
                    h = d;if (0 === g && d !== b.length) d = b.length;else {
                  for (; null === f.match(e.escapeRegex(c) + "$");) {
                    c = c.substr(1);
                  }var i = f.indexOf(c);-1 !== i && "" !== c && d > i && i > g && (d = i);
                }E(d) || (d = F(d)), h !== d && (M(a, d), n && setTimeout(function () {
                  M(a, d);
                }, 0));
              }(c, i[0], i[1]), !0 === P(z()) && a(c).trigger("complete");
            } else b.keyCode = e.keyCode.BACKSPACE, da.keydownEvent.call(c, b);b.preventDefault();
          }
        }, setValueEvent: function setValueEvent(b) {
          this.inputmask.refreshValue = !1;var c = this,
              d = c.inputmask._valueGet(!0);a.isFunction(j.onBeforeMask) && (d = j.onBeforeMask(d, j) || d), d = d.split(""), K(c, !0, !1, Z ? d.reverse() : d), T = z().join(""), (j.clearMaskOnLostFocus || j.clearIncomplete) && c.inputmask._valueGet() === y().join("") && c.inputmask._valueSet("");
        }, focusEvent: function focusEvent(a) {
          var b = this,
              c = b.inputmask._valueGet();j.showMaskOnFocus && (!j.showMaskOnHover || j.showMaskOnHover && "" === c) && (b.inputmask._valueGet() !== z().join("") ? I(b, z(), F(r())) : !1 === ba && M(b, F(r()))), !0 === j.positionCaretOnTab && !1 === ba && (I(b, z(), M(b)), da.clickEvent.apply(b, [a, !0])), T = z().join("");
        }, mouseleaveEvent: function mouseleaveEvent(a) {
          var b = this;if (ba = !1, j.clearMaskOnLostFocus && c.activeElement !== b) {
            var d = z().slice(),
                e = b.inputmask._valueGet();e !== b.getAttribute("placeholder") && "" !== e && (-1 === r() && e === y().join("") ? d = [] : O(d), I(b, d));
          }
        }, clickEvent: function clickEvent(b, e) {
          function f(b) {
            if ("" !== j.radixPoint) {
              var c = p().validPositions;if (c[b] === d || c[b].input === J(b)) {
                if (b < F(-1)) return !0;var e = a.inArray(j.radixPoint, z());if (-1 !== e) {
                  for (var f in c) {
                    if (e < f && c[f].input !== J(f)) return !1;
                  }return !0;
                }
              }
            }return !1;
          }var g = this;setTimeout(function () {
            if (c.activeElement === g) {
              var a = M(g);if (e && (Z ? a.end = a.begin : a.begin = a.end), a.begin === a.end) switch (j.positionCaretOnClick) {case "none":
                  break;case "radixFocus":
                  if (f(a.begin)) {
                    var b = z().join("").indexOf(j.radixPoint);M(g, j.numericInput ? F(b) : b);break;
                  }default:
                  var h = a.begin,
                      i = r(h, !0),
                      k = F(i);if (h < k) M(g, E(h) || E(h - 1) ? h : F(h));else {
                    var l = J(k),
                        m = p().validPositions[i],
                        n = u(k, m ? m.match.locator : d, m);if ("" !== l && z()[k] !== l && !0 !== n.match.optionalQuantifier || !E(k) && n.match.def === l) {
                      var o = F(k);h >= o && (k = o);
                    }M(g, k);
                  }}
            }
          }, 0);
        }, dblclickEvent: function dblclickEvent(a) {
          var b = this;setTimeout(function () {
            M(b, 0, F(r()));
          }, 0);
        }, cutEvent: function cutEvent(d) {
          var f = this,
              g = a(f),
              h = M(f),
              i = d.originalEvent || d,
              j = b.clipboardData || i.clipboardData,
              k = Z ? z().slice(h.end, h.begin) : z().slice(h.begin, h.end);j.setData("text", Z ? k.reverse().join("") : k.join("")), c.execCommand && c.execCommand("copy"), Q(f, e.keyCode.DELETE, h), I(f, z(), p().p, d, T !== z().join("")), f.inputmask._valueGet() === y().join("") && g.trigger("cleared");
        }, blurEvent: function blurEvent(b) {
          var c = a(this),
              e = this;if (e.inputmask) {
            var f = e.inputmask._valueGet(),
                g = z().slice();"" !== f && (j.clearMaskOnLostFocus && (-1 === r() && f === y().join("") ? g = [] : O(g)), !1 === P(g) && (setTimeout(function () {
              c.trigger("incomplete");
            }, 0), j.clearIncomplete && (q(), g = j.clearMaskOnLostFocus ? [] : y().slice())), I(e, g, d, b)), T !== z().join("") && (T = g.join(""), c.trigger("change"));
          }
        }, mouseenterEvent: function mouseenterEvent(a) {
          var b = this;ba = !0, c.activeElement !== b && j.showMaskOnHover && b.inputmask._valueGet() !== z().join("") && I(b, z());
        }, submitEvent: function submitEvent(a) {
          T !== z().join("") && U.trigger("change"), j.clearMaskOnLostFocus && -1 === r() && Y.inputmask._valueGet && Y.inputmask._valueGet() === y().join("") && Y.inputmask._valueSet(""), j.removeMaskOnSubmit && (Y.inputmask._valueSet(Y.inputmask.unmaskedvalue(), !0), setTimeout(function () {
            I(Y, z());
          }, 0));
        }, resetEvent: function resetEvent(a) {
          Y.inputmask.refreshValue = !0, setTimeout(function () {
            U.trigger("setvalue");
          }, 0);
        } };if (f !== d) switch (f.action) {case "isComplete":
          return Y = f.el, P(z());case "unmaskedvalue":
          return Y !== d && f.value === d || (X = f.value, X = (a.isFunction(j.onBeforeMask) ? j.onBeforeMask(X, j) || X : X).split(""), K(d, !1, !1, Z ? X.reverse() : X), a.isFunction(j.onBeforeWrite) && j.onBeforeWrite(d, z(), 0, j)), L(Y);case "mask":
          !function (b) {
            ca.off(b);var e = function (b, e) {
              var f = b.getAttribute("type"),
                  h = "INPUT" === b.tagName && -1 !== a.inArray(f, e.supportsInputType) || b.isContentEditable || "TEXTAREA" === b.tagName;if (!h) if ("INPUT" === b.tagName) {
                var i = c.createElement("input");i.setAttribute("type", f), h = "text" === i.type, i = null;
              } else h = "partial";return !1 !== h && function (b) {
                function f() {
                  return this.inputmask ? this.inputmask.opts.autoUnmask ? this.inputmask.unmaskedvalue() : -1 !== r() || !0 !== e.nullable ? c.activeElement === this && e.clearMaskOnLostFocus ? (Z ? O(z().slice()).reverse() : O(z().slice())).join("") : i.call(this) : "" : i.call(this);
                }function h(b) {
                  j.call(this, b), this.inputmask && a(this).trigger("setvalue");
                }var i, j;if (!b.inputmask.__valueGet) {
                  if (!0 !== e.noValuePatching) {
                    if (Object.getOwnPropertyDescriptor) {
                      "function" != typeof Object.getPrototypeOf && (Object.getPrototypeOf = "object" === g("test".__proto__) ? function (a) {
                        return a.__proto__;
                      } : function (a) {
                        return a.constructor.prototype;
                      });var k = Object.getPrototypeOf ? Object.getOwnPropertyDescriptor(Object.getPrototypeOf(b), "value") : d;k && k.get && k.set ? (i = k.get, j = k.set, Object.defineProperty(b, "value", { get: f, set: h, configurable: !0 })) : "INPUT" !== b.tagName && (i = function i() {
                        return this.textContent;
                      }, j = function j(a) {
                        this.textContent = a;
                      }, Object.defineProperty(b, "value", { get: f, set: h, configurable: !0 }));
                    } else c.__lookupGetter__ && b.__lookupGetter__("value") && (i = b.__lookupGetter__("value"), j = b.__lookupSetter__("value"), b.__defineGetter__("value", f), b.__defineSetter__("value", h));b.inputmask.__valueGet = i, b.inputmask.__valueSet = j;
                  }b.inputmask._valueGet = function (a) {
                    return Z && !0 !== a ? i.call(this.el).split("").reverse().join("") : i.call(this.el);
                  }, b.inputmask._valueSet = function (a, b) {
                    j.call(this.el, null === a || a === d ? "" : !0 !== b && Z ? a.split("").reverse().join("") : a);
                  }, i === d && (i = function i() {
                    return this.value;
                  }, j = function j(a) {
                    this.value = a;
                  }, function (b) {
                    if (a.valHooks && (a.valHooks[b] === d || !0 !== a.valHooks[b].inputmaskpatch)) {
                      var c = a.valHooks[b] && a.valHooks[b].get ? a.valHooks[b].get : function (a) {
                        return a.value;
                      },
                          f = a.valHooks[b] && a.valHooks[b].set ? a.valHooks[b].set : function (a, b) {
                        return a.value = b, a;
                      };a.valHooks[b] = { get: function get(a) {
                          if (a.inputmask) {
                            if (a.inputmask.opts.autoUnmask) return a.inputmask.unmaskedvalue();var b = c(a);return -1 !== r(d, d, a.inputmask.maskset.validPositions) || !0 !== e.nullable ? b : "";
                          }return c(a);
                        }, set: function set(b, c) {
                          var d,
                              e = a(b);return d = f(b, c), b.inputmask && e.trigger("setvalue"), d;
                        }, inputmaskpatch: !0 };
                    }
                  }(b.type), function (b) {
                    ca.on(b, "mouseenter", function (b) {
                      var c = a(this);this.inputmask._valueGet() !== z().join("") && c.trigger("setvalue");
                    });
                  }(b));
                }
              }(b), h;
            }(b, j);if (!1 !== e && (Y = b, U = a(Y), !0 === j.colorMask && R(Y), n && (Y.hasOwnProperty("inputmode") && (Y.inputmode = j.inputmode, Y.setAttribute("inputmode", j.inputmode)), "rtfm" === j.androidHack && (!0 !== j.colorMask && R(Y), Y.type = "password")), !0 === e && (ca.on(Y, "submit", da.submitEvent), ca.on(Y, "reset", da.resetEvent), ca.on(Y, "mouseenter", da.mouseenterEvent), ca.on(Y, "blur", da.blurEvent), ca.on(Y, "focus", da.focusEvent), ca.on(Y, "mouseleave", da.mouseleaveEvent), !0 !== j.colorMask && ca.on(Y, "click", da.clickEvent), ca.on(Y, "dblclick", da.dblclickEvent), ca.on(Y, "paste", da.pasteEvent), ca.on(Y, "dragdrop", da.pasteEvent), ca.on(Y, "drop", da.pasteEvent), ca.on(Y, "cut", da.cutEvent), ca.on(Y, "complete", j.oncomplete), ca.on(Y, "incomplete", j.onincomplete), ca.on(Y, "cleared", j.oncleared), n || !0 === j.inputEventOnly || (ca.on(Y, "keydown", da.keydownEvent), ca.on(Y, "keypress", da.keypressEvent)), ca.on(Y, "compositionstart", a.noop), ca.on(Y, "compositionupdate", a.noop), ca.on(Y, "compositionend", a.noop), ca.on(Y, "keyup", a.noop), ca.on(Y, "input", da.inputFallBackEvent), ca.on(Y, "beforeinput", a.noop)), ca.on(Y, "setvalue", da.setValueEvent), T = y().join(""), "" !== Y.inputmask._valueGet(!0) || !1 === j.clearMaskOnLostFocus || c.activeElement === Y)) {
              var f = a.isFunction(j.onBeforeMask) ? j.onBeforeMask(Y.inputmask._valueGet(!0), j) || Y.inputmask._valueGet(!0) : Y.inputmask._valueGet(!0);"" !== f && K(Y, !0, !1, Z ? f.split("").reverse() : f.split(""));var h = z().slice();T = h.join(""), !1 === P(h) && j.clearIncomplete && q(), j.clearMaskOnLostFocus && c.activeElement !== Y && (-1 === r() ? h = [] : O(h)), I(Y, h), c.activeElement === Y && M(Y, F(r()));
            }
          }(Y);break;case "format":
          return X = (a.isFunction(j.onBeforeMask) ? j.onBeforeMask(f.value, j) || f.value : f.value).split(""), K(d, !0, !1, Z ? X.reverse() : X), f.metadata ? { value: Z ? z().slice().reverse().join("") : z().join(""), metadata: i.call(this, { action: "getmetadata" }, h, j) } : Z ? z().slice().reverse().join("") : z().join("");case "isValid":
          f.value ? (X = f.value.split(""), K(d, !0, !0, Z ? X.reverse() : X)) : f.value = z().join("");for (var ea = z(), fa = N(), ga = ea.length - 1; ga > fa && !E(ga); ga--) {}return ea.splice(fa, ga + 1 - fa), P(ea) && f.value === z().join("");case "getemptymask":
          return y().join("");case "remove":
          if (Y && Y.inputmask) {
            U = a(Y), Y.inputmask._valueSet(j.autoUnmask ? L(Y) : Y.inputmask._valueGet(!0)), ca.off(Y);Object.getOwnPropertyDescriptor && Object.getPrototypeOf ? Object.getOwnPropertyDescriptor(Object.getPrototypeOf(Y), "value") && Y.inputmask.__valueGet && Object.defineProperty(Y, "value", { get: Y.inputmask.__valueGet, set: Y.inputmask.__valueSet, configurable: !0 }) : c.__lookupGetter__ && Y.__lookupGetter__("value") && Y.inputmask.__valueGet && (Y.__defineGetter__("value", Y.inputmask.__valueGet), Y.__defineSetter__("value", Y.inputmask.__valueSet)), Y.inputmask = d;
          }return Y;case "getmetadata":
          if (a.isArray(h.metadata)) {
            var ha = o(!0, 0, !1).join("");return a.each(h.metadata, function (a, b) {
              if (b.mask === ha) return ha = b, !1;
            }), ha;
          }return h.metadata;}
    }var j = navigator.userAgent,
        k = /mobile/i.test(j),
        l = /iemobile/i.test(j),
        m = /iphone/i.test(j) && !l,
        n = /android/i.test(j) && !l;return e.prototype = { dataAttribute: "data-inputmask", defaults: { placeholder: "_", optionalmarker: { start: "[", end: "]" }, quantifiermarker: { start: "{", end: "}" }, groupmarker: { start: "(", end: ")" }, alternatormarker: "|", escapeChar: "\\", mask: null, regex: null, oncomplete: a.noop, onincomplete: a.noop, oncleared: a.noop, repeat: 0, greedy: !0, autoUnmask: !1, removeMaskOnSubmit: !1, clearMaskOnLostFocus: !0, insertMode: !0, clearIncomplete: !1, alias: null, onKeyDown: a.noop, onBeforeMask: null, onBeforePaste: function onBeforePaste(b, c) {
          return a.isFunction(c.onBeforeMask) ? c.onBeforeMask(b, c) : b;
        }, onBeforeWrite: null, onUnMask: null, showMaskOnFocus: !0, showMaskOnHover: !0, onKeyValidation: a.noop, skipOptionalPartCharacter: " ", numericInput: !1, rightAlign: !1, undoOnEscape: !0, radixPoint: "", radixPointDefinitionSymbol: d, groupSeparator: "", keepStatic: null, positionCaretOnTab: !0, tabThrough: !1, supportsInputType: ["text", "tel", "password"], ignorables: [8, 9, 13, 19, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 0, 229], isComplete: null, canClearPosition: a.noop, preValidation: null, postValidation: null, staticDefinitionSymbol: d, jitMasking: !1, nullable: !0, inputEventOnly: !1, noValuePatching: !1, positionCaretOnClick: "lvp", casing: null, inputmode: "verbatim", colorMask: !1, androidHack: !1 }, definitions: { 9: { validator: "[0-9]", cardinality: 1, definitionSymbol: "*" }, a: { validator: "[A-Za-z\u0410-\u044F\u0401\u0451\xC0-\xFF\xB5]", cardinality: 1, definitionSymbol: "*" }, "*": { validator: "[0-9A-Za-z\u0410-\u044F\u0401\u0451\xC0-\xFF\xB5]", cardinality: 1 } }, aliases: {}, masksCache: {}, mask: function mask(g) {
        function j(c, e, g, h) {
          function i(a, e) {
            null !== (e = e !== d ? e : c.getAttribute(h + "-" + a)) && ("string" == typeof e && (0 === a.indexOf("on") ? e = b[e] : "false" === e ? e = !1 : "true" === e && (e = !0)), g[a] = e);
          }("rtl" === c.dir || e.rightAlign) && (c.style.textAlign = "right"), ("rtl" === c.dir || e.numericInput) && (c.dir = "ltr", c.removeAttribute("dir"), e.isRTL = !0);var j,
              k,
              l,
              m,
              n = c.getAttribute(h);if (n && "" !== n && (n = n.replace(new RegExp("'", "g"), '"'), k = JSON.parse("{" + n + "}")), k) {
            l = d;for (m in k) {
              if ("alias" === m.toLowerCase()) {
                l = k[m];break;
              }
            }
          }i("alias", l), g.alias && f(g.alias, g, e);for (j in e) {
            if (k) {
              l = d;for (m in k) {
                if (m.toLowerCase() === j.toLowerCase()) {
                  l = k[m];break;
                }
              }
            }i(j, l);
          }return a.extend(!0, e, g), e;
        }var k = this;return "string" == typeof g && (g = c.getElementById(g) || c.querySelectorAll(g)), g = g.nodeName ? [g] : g, a.each(g, function (b, c) {
          var f = a.extend(!0, {}, k.opts);j(c, f, a.extend(!0, {}, k.userOptions), k.dataAttribute);var g = h(f, k.noMasksCache);g !== d && (c.inputmask !== d && c.inputmask.remove(), c.inputmask = new e(d, d, !0), c.inputmask.opts = f, c.inputmask.noMasksCache = k.noMasksCache, c.inputmask.userOptions = a.extend(!0, {}, k.userOptions), c.inputmask.isRTL = f.isRTL, c.inputmask.el = c, c.inputmask.maskset = g, a.data(c, "_inputmask_opts", f), i.call(c.inputmask, { action: "mask" }));
        }), g && g[0] ? g[0].inputmask || this : this;
      }, option: function option(b, c) {
        return "string" == typeof b ? this.opts[b] : "object" === (void 0 === b ? "undefined" : g(b)) ? (a.extend(this.userOptions, b), this.el && !0 !== c && this.mask(this.el), this) : void 0;
      }, unmaskedvalue: function unmaskedvalue(a) {
        return this.maskset = this.maskset || h(this.opts, this.noMasksCache), i.call(this, { action: "unmaskedvalue", value: a });
      }, remove: function remove() {
        return i.call(this, { action: "remove" });
      }, getemptymask: function getemptymask() {
        return this.maskset = this.maskset || h(this.opts, this.noMasksCache), i.call(this, { action: "getemptymask" });
      }, hasMaskedValue: function hasMaskedValue() {
        return !this.opts.autoUnmask;
      }, isComplete: function isComplete() {
        return this.maskset = this.maskset || h(this.opts, this.noMasksCache), i.call(this, { action: "isComplete" });
      }, getmetadata: function getmetadata() {
        return this.maskset = this.maskset || h(this.opts, this.noMasksCache), i.call(this, { action: "getmetadata" });
      }, isValid: function isValid(a) {
        return this.maskset = this.maskset || h(this.opts, this.noMasksCache), i.call(this, { action: "isValid", value: a });
      }, format: function format(a, b) {
        return this.maskset = this.maskset || h(this.opts, this.noMasksCache), i.call(this, { action: "format", value: a, metadata: b });
      }, analyseMask: function analyseMask(b, c, f) {
        function g(a, b, c, d) {
          this.matches = [], this.openGroup = a || !1, this.alternatorGroup = !1, this.isGroup = a || !1, this.isOptional = b || !1, this.isQuantifier = c || !1, this.isAlternator = d || !1, this.quantifier = { min: 1, max: 1 };
        }function h(b, g, h) {
          h = h !== d ? h : b.matches.length;var i = b.matches[h - 1];if (c) 0 === g.indexOf("[") || u ? b.matches.splice(h++, 0, { fn: new RegExp(g, f.casing ? "i" : ""), cardinality: 1, optionality: b.isOptional, newBlockMarker: i === d || i.def !== g, casing: null, def: g, placeholder: d, nativeDef: g }) : a.each(g.split(""), function (a, c) {
            i = b.matches[h - 1], b.matches.splice(h++, 0, { fn: null, cardinality: 0, optionality: b.isOptional, newBlockMarker: i === d || i.def !== c && null !== i.fn, casing: null, def: f.staticDefinitionSymbol || c, placeholder: f.staticDefinitionSymbol !== d ? c : d, nativeDef: c });
          }), u = !1;else {
            var j = (f.definitions ? f.definitions[g] : d) || e.prototype.definitions[g];if (j && !u) {
              for (var k = j.prevalidator, l = k ? k.length : 0, m = 1; m < j.cardinality; m++) {
                var n = l >= m ? k[m - 1] : [],
                    o = n.validator,
                    p = n.cardinality;b.matches.splice(h++, 0, { fn: o ? "string" == typeof o ? new RegExp(o, f.casing ? "i" : "") : new function () {
                    this.test = o;
                  }() : new RegExp("."), cardinality: p || 1, optionality: b.isOptional, newBlockMarker: i === d || i.def !== (j.definitionSymbol || g), casing: j.casing, def: j.definitionSymbol || g, placeholder: j.placeholder, nativeDef: g }), i = b.matches[h - 1];
              }b.matches.splice(h++, 0, { fn: j.validator ? "string" == typeof j.validator ? new RegExp(j.validator, f.casing ? "i" : "") : new function () {
                  this.test = j.validator;
                }() : new RegExp("."), cardinality: j.cardinality, optionality: b.isOptional, newBlockMarker: i === d || i.def !== (j.definitionSymbol || g), casing: j.casing, def: j.definitionSymbol || g, placeholder: j.placeholder, nativeDef: g });
            } else b.matches.splice(h++, 0, { fn: null, cardinality: 0, optionality: b.isOptional, newBlockMarker: i === d || i.def !== g && null !== i.fn, casing: null, def: f.staticDefinitionSymbol || g, placeholder: f.staticDefinitionSymbol !== d ? g : d, nativeDef: g }), u = !1;
          }
        }function i(b) {
          b && b.matches && a.each(b.matches, function (a, e) {
            var g = b.matches[a + 1];(g === d || g.matches === d || !1 === g.isQuantifier) && e && e.isGroup && (e.isGroup = !1, c || (h(e, f.groupmarker.start, 0), !0 !== e.openGroup && h(e, f.groupmarker.end))), i(e);
          });
        }function j() {
          if (w.length > 0) {
            if (o = w[w.length - 1], h(o, m), o.isAlternator) {
              p = w.pop();for (var a = 0; a < p.matches.length; a++) {
                p.matches[a].isGroup = !1;
              }w.length > 0 ? (o = w[w.length - 1], o.matches.push(p)) : v.matches.push(p);
            }
          } else h(v, m);
        }function k(a) {
          a.matches = a.matches.reverse();for (var b in a.matches) {
            if (a.matches.hasOwnProperty(b)) {
              var c = parseInt(b);if (a.matches[b].isQuantifier && a.matches[c + 1] && a.matches[c + 1].isGroup) {
                var e = a.matches[b];a.matches.splice(b, 1), a.matches.splice(c + 1, 0, e);
              }a.matches[b].matches !== d ? a.matches[b] = k(a.matches[b]) : a.matches[b] = function (a) {
                return a === f.optionalmarker.start ? a = f.optionalmarker.end : a === f.optionalmarker.end ? a = f.optionalmarker.start : a === f.groupmarker.start ? a = f.groupmarker.end : a === f.groupmarker.end && (a = f.groupmarker.start), a;
              }(a.matches[b]);
            }
          }return a;
        }var l,
            m,
            n,
            o,
            p,
            q,
            r,
            s = /(?:[?*+]|\{[0-9\+\*]+(?:,[0-9\+\*]*)?\})|[^.?*+^${[]()|\\]+|./g,
            t = /\[\^?]?(?:[^\\\]]+|\\[\S\s]?)*]?|\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9][0-9]*|x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|c[A-Za-z]|[\S\s]?)|\((?:\?[:=!]?)?|(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??|[^.?*+^${[()|\\]+|./g,
            u = !1,
            v = new g(),
            w = [],
            x = [];for (c && (f.optionalmarker.start = d, f.optionalmarker.end = d); l = c ? t.exec(b) : s.exec(b);) {
          if (m = l[0], c && !0 !== u) switch (m.charAt(0)) {case "?":
              m = "{0,1}";break;case "+":case "*":
              m = "{" + m + "}";}if (u) j();else switch (m.charAt(0)) {case f.escapeChar:
              u = !0, c && j();break;case f.optionalmarker.end:case f.groupmarker.end:
              if (n = w.pop(), n.openGroup = !1, n !== d) {
                if (w.length > 0) {
                  if (o = w[w.length - 1], o.matches.push(n), o.isAlternator) {
                    p = w.pop();for (var y = 0; y < p.matches.length; y++) {
                      p.matches[y].isGroup = !1, p.matches[y].alternatorGroup = !1;
                    }w.length > 0 ? (o = w[w.length - 1], o.matches.push(p)) : v.matches.push(p);
                  }
                } else v.matches.push(n);
              } else j();break;case f.optionalmarker.start:
              w.push(new g(!1, !0));break;case f.groupmarker.start:
              w.push(new g(!0));break;case f.quantifiermarker.start:
              var z = new g(!1, !1, !0);m = m.replace(/[{}]/g, "");var A = m.split(","),
                  B = isNaN(A[0]) ? A[0] : parseInt(A[0]),
                  C = 1 === A.length ? B : isNaN(A[1]) ? A[1] : parseInt(A[1]);if ("*" !== C && "+" !== C || (B = "*" === C ? 0 : 1), z.quantifier = { min: B, max: C }, w.length > 0) {
                var D = w[w.length - 1].matches;l = D.pop(), l.isGroup || (r = new g(!0), r.matches.push(l), l = r), D.push(l), D.push(z);
              } else l = v.matches.pop(), l.isGroup || (c && null === l.fn && "." === l.def && (l.fn = new RegExp(l.def, f.casing ? "i" : "")), r = new g(!0), r.matches.push(l), l = r), v.matches.push(l), v.matches.push(z);break;case f.alternatormarker:
              if (w.length > 0) {
                o = w[w.length - 1];var E = o.matches[o.matches.length - 1];q = o.openGroup && (E.matches === d || !1 === E.isGroup && !1 === E.isAlternator) ? w.pop() : o.matches.pop();
              } else q = v.matches.pop();if (q.isAlternator) w.push(q);else if (q.alternatorGroup ? (p = w.pop(), q.alternatorGroup = !1) : p = new g(!1, !1, !1, !0), p.matches.push(q), w.push(p), q.openGroup) {
                q.openGroup = !1;var F = new g(!0);F.alternatorGroup = !0, w.push(F);
              }break;default:
              j();}
        }for (; w.length > 0;) {
          n = w.pop(), v.matches.push(n);
        }return v.matches.length > 0 && (i(v), x.push(v)), (f.numericInput || f.isRTL) && k(x[0]), x;
      } }, e.extendDefaults = function (b) {
      a.extend(!0, e.prototype.defaults, b);
    }, e.extendDefinitions = function (b) {
      a.extend(!0, e.prototype.definitions, b);
    }, e.extendAliases = function (b) {
      a.extend(!0, e.prototype.aliases, b);
    }, e.format = function (a, b, c) {
      return e(b).format(a, c);
    }, e.unmask = function (a, b) {
      return e(b).unmaskedvalue(a);
    }, e.isValid = function (a, b) {
      return e(b).isValid(a);
    }, e.remove = function (b) {
      a.each(b, function (a, b) {
        b.inputmask && b.inputmask.remove();
      });
    }, e.escapeRegex = function (a) {
      var b = ["/", ".", "*", "+", "?", "|", "(", ")", "[", "]", "{", "}", "\\", "$", "^"];return a.replace(new RegExp("(\\" + b.join("|\\") + ")", "gim"), "\\$1");
    }, e.keyCode = { ALT: 18, BACKSPACE: 8, BACKSPACE_SAFARI: 127, CAPS_LOCK: 20, COMMA: 188, COMMAND: 91, COMMAND_LEFT: 91, COMMAND_RIGHT: 93, CONTROL: 17, DELETE: 46, DOWN: 40, END: 35, ENTER: 13, ESCAPE: 27, HOME: 36, INSERT: 45, LEFT: 37, MENU: 93, NUMPAD_ADD: 107, NUMPAD_DECIMAL: 110, NUMPAD_DIVIDE: 111, NUMPAD_ENTER: 108, NUMPAD_MULTIPLY: 106, NUMPAD_SUBTRACT: 109, PAGE_DOWN: 34, PAGE_UP: 33, PERIOD: 190, RIGHT: 39, SHIFT: 16, SPACE: 32, TAB: 9, UP: 38, WINDOWS: 91, X: 88 }, e;
  });
}, function (a, b) {
  a.exports = jQuery;
}, function (a, b, c) {
  "use strict";
  var d, e, f;"function" == typeof Symbol && Symbol.iterator;!function (g) {
    e = [c(0), c(1)], d = g, void 0 !== (f = "function" == typeof d ? d.apply(b, e) : d) && (a.exports = f);
  }(function (a, b) {
    function c(a) {
      return isNaN(a) || 29 === new Date(a, 2, 0).getDate();
    }return b.extendAliases({ "dd/mm/yyyy": { mask: "1/2/y", placeholder: "dd/mm/yyyy", regex: { val1pre: new RegExp("[0-3]"), val1: new RegExp("0[1-9]|[12][0-9]|3[01]"), val2pre: function val2pre(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[1-9]|[12][0-9]|3[01])" + c + "[01])");
          }, val2: function val2(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[1-9]|[12][0-9])" + c + "(0[1-9]|1[012]))|(30" + c + "(0[13-9]|1[012]))|(31" + c + "(0[13578]|1[02]))");
          } }, leapday: "29/02/", separator: "/", yearrange: { minyear: 1900, maxyear: 2099 }, isInYearRange: function isInYearRange(a, b, c) {
          if (isNaN(a)) return !1;var d = parseInt(a.concat(b.toString().slice(a.length))),
              e = parseInt(a.concat(c.toString().slice(a.length)));return !isNaN(d) && b <= d && d <= c || !isNaN(e) && b <= e && e <= c;
        }, determinebaseyear: function determinebaseyear(a, b, c) {
          var d = new Date().getFullYear();if (a > d) return a;if (b < d) {
            for (var e = b.toString().slice(0, 2), f = b.toString().slice(2, 4); b < e + c;) {
              e--;
            }var g = e + f;return a > g ? a : g;
          }if (a <= d && d <= b) {
            for (var h = d.toString().slice(0, 2); b < h + c;) {
              h--;
            }var i = h + c;return i < a ? a : i;
          }return d;
        }, onKeyDown: function onKeyDown(c, d, e, f) {
          var g = a(this);if (c.ctrlKey && c.keyCode === b.keyCode.RIGHT) {
            var h = new Date();g.val(h.getDate().toString() + (h.getMonth() + 1).toString() + h.getFullYear().toString()), g.trigger("setvalue");
          }
        }, getFrontValue: function getFrontValue(a, b, c) {
          for (var d = 0, e = 0, f = 0; f < a.length && "2" !== a.charAt(f); f++) {
            var g = c.definitions[a.charAt(f)];g ? (d += e, e = g.cardinality) : e++;
          }return b.join("").substr(d, e);
        }, postValidation: function postValidation(a, b, d) {
          var e,
              f,
              g = a.join("");return 0 === d.mask.indexOf("y") ? (f = g.substr(0, 4), e = g.substring(4, 10)) : (f = g.substring(6, 10), e = g.substr(0, 6)), b && (e !== d.leapday || c(f));
        }, definitions: { 1: { validator: function validator(a, b, c, d, e) {
              var f = e.regex.val1.test(a);return d || f || a.charAt(1) !== e.separator && -1 === "-./".indexOf(a.charAt(1)) || !(f = e.regex.val1.test("0" + a.charAt(0))) ? f : (b.buffer[c - 1] = "0", { refreshFromBuffer: { start: c - 1, end: c }, pos: c, c: a.charAt(0) });
            }, cardinality: 2, prevalidator: [{ validator: function validator(a, b, c, d, e) {
                var f = a;isNaN(b.buffer[c + 1]) || (f += b.buffer[c + 1]);var g = 1 === f.length ? e.regex.val1pre.test(f) : e.regex.val1.test(f);if (!d && !g) {
                  if (g = e.regex.val1.test(a + "0")) return b.buffer[c] = a, b.buffer[++c] = "0", { pos: c, c: "0" };if (g = e.regex.val1.test("0" + a)) return b.buffer[c] = "0", c++, { pos: c };
                }return g;
              }, cardinality: 1 }] }, 2: { validator: function validator(a, b, c, d, e) {
              var f = e.getFrontValue(b.mask, b.buffer, e);-1 !== f.indexOf(e.placeholder[0]) && (f = "01" + e.separator);var g = e.regex.val2(e.separator).test(f + a);return d || g || a.charAt(1) !== e.separator && -1 === "-./".indexOf(a.charAt(1)) || !(g = e.regex.val2(e.separator).test(f + "0" + a.charAt(0))) ? g : (b.buffer[c - 1] = "0", { refreshFromBuffer: { start: c - 1, end: c }, pos: c, c: a.charAt(0) });
            }, cardinality: 2, prevalidator: [{ validator: function validator(a, b, c, d, e) {
                isNaN(b.buffer[c + 1]) || (a += b.buffer[c + 1]);var f = e.getFrontValue(b.mask, b.buffer, e);-1 !== f.indexOf(e.placeholder[0]) && (f = "01" + e.separator);var g = 1 === a.length ? e.regex.val2pre(e.separator).test(f + a) : e.regex.val2(e.separator).test(f + a);return d || g || !(g = e.regex.val2(e.separator).test(f + "0" + a)) ? g : (b.buffer[c] = "0", c++, { pos: c });
              }, cardinality: 1 }] }, y: { validator: function validator(a, b, c, d, e) {
              return e.isInYearRange(a, e.yearrange.minyear, e.yearrange.maxyear);
            }, cardinality: 4, prevalidator: [{ validator: function validator(a, b, c, d, e) {
                var f = e.isInYearRange(a, e.yearrange.minyear, e.yearrange.maxyear);if (!d && !f) {
                  var g = e.determinebaseyear(e.yearrange.minyear, e.yearrange.maxyear, a + "0").toString().slice(0, 1);if (f = e.isInYearRange(g + a, e.yearrange.minyear, e.yearrange.maxyear)) return b.buffer[c++] = g.charAt(0), { pos: c };if (g = e.determinebaseyear(e.yearrange.minyear, e.yearrange.maxyear, a + "0").toString().slice(0, 2), f = e.isInYearRange(g + a, e.yearrange.minyear, e.yearrange.maxyear)) return b.buffer[c++] = g.charAt(0), b.buffer[c++] = g.charAt(1), { pos: c };
                }return f;
              }, cardinality: 1 }, { validator: function validator(a, b, c, d, e) {
                var f = e.isInYearRange(a, e.yearrange.minyear, e.yearrange.maxyear);if (!d && !f) {
                  var g = e.determinebaseyear(e.yearrange.minyear, e.yearrange.maxyear, a).toString().slice(0, 2);if (f = e.isInYearRange(a[0] + g[1] + a[1], e.yearrange.minyear, e.yearrange.maxyear)) return b.buffer[c++] = g.charAt(1), { pos: c };if (g = e.determinebaseyear(e.yearrange.minyear, e.yearrange.maxyear, a).toString().slice(0, 2), f = e.isInYearRange(g + a, e.yearrange.minyear, e.yearrange.maxyear)) return b.buffer[c - 1] = g.charAt(0), b.buffer[c++] = g.charAt(1), b.buffer[c++] = a.charAt(0), { refreshFromBuffer: { start: c - 3, end: c }, pos: c };
                }return f;
              }, cardinality: 2 }, { validator: function validator(a, b, c, d, e) {
                return e.isInYearRange(a, e.yearrange.minyear, e.yearrange.maxyear);
              }, cardinality: 3 }] } }, insertMode: !1, autoUnmask: !1 }, "mm/dd/yyyy": { placeholder: "mm/dd/yyyy", alias: "dd/mm/yyyy", regex: { val2pre: function val2pre(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[13-9]|1[012])" + c + "[0-3])|(02" + c + "[0-2])");
          }, val2: function val2(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[1-9]|1[012])" + c + "(0[1-9]|[12][0-9]))|((0[13-9]|1[012])" + c + "30)|((0[13578]|1[02])" + c + "31)");
          }, val1pre: new RegExp("[01]"), val1: new RegExp("0[1-9]|1[012]") }, leapday: "02/29/", onKeyDown: function onKeyDown(c, d, e, f) {
          var g = a(this);if (c.ctrlKey && c.keyCode === b.keyCode.RIGHT) {
            var h = new Date();g.val((h.getMonth() + 1).toString() + h.getDate().toString() + h.getFullYear().toString()), g.trigger("setvalue");
          }
        } }, "yyyy/mm/dd": { mask: "y/1/2", placeholder: "yyyy/mm/dd", alias: "mm/dd/yyyy", leapday: "/02/29", onKeyDown: function onKeyDown(c, d, e, f) {
          var g = a(this);if (c.ctrlKey && c.keyCode === b.keyCode.RIGHT) {
            var h = new Date();g.val(h.getFullYear().toString() + (h.getMonth() + 1).toString() + h.getDate().toString()), g.trigger("setvalue");
          }
        } }, "dd.mm.yyyy": { mask: "1.2.y", placeholder: "dd.mm.yyyy", leapday: "29.02.", separator: ".", alias: "dd/mm/yyyy" }, "dd-mm-yyyy": { mask: "1-2-y", placeholder: "dd-mm-yyyy", leapday: "29-02-", separator: "-", alias: "dd/mm/yyyy" }, "mm.dd.yyyy": { mask: "1.2.y", placeholder: "mm.dd.yyyy", leapday: "02.29.", separator: ".", alias: "mm/dd/yyyy" }, "mm-dd-yyyy": { mask: "1-2-y", placeholder: "mm-dd-yyyy", leapday: "02-29-", separator: "-", alias: "mm/dd/yyyy" }, "yyyy.mm.dd": { mask: "y.1.2", placeholder: "yyyy.mm.dd", leapday: ".02.29", separator: ".", alias: "yyyy/mm/dd" }, "yyyy-mm-dd": { mask: "y-1-2", placeholder: "yyyy-mm-dd", leapday: "-02-29", separator: "-", alias: "yyyy/mm/dd" }, datetime: { mask: "1/2/y h:s", placeholder: "dd/mm/yyyy hh:mm", alias: "dd/mm/yyyy", regex: { hrspre: new RegExp("[012]"), hrs24: new RegExp("2[0-4]|1[3-9]"), hrs: new RegExp("[01][0-9]|2[0-4]"), ampm: new RegExp("^[a|p|A|P][m|M]"), mspre: new RegExp("[0-5]"), ms: new RegExp("[0-5][0-9]") }, timeseparator: ":", hourFormat: "24", definitions: { h: { validator: function validator(a, b, c, d, e) {
              if ("24" === e.hourFormat && 24 === parseInt(a, 10)) return b.buffer[c - 1] = "0", b.buffer[c] = "0", { refreshFromBuffer: { start: c - 1, end: c }, c: "0" };var f = e.regex.hrs.test(a);if (!d && !f && (a.charAt(1) === e.timeseparator || -1 !== "-.:".indexOf(a.charAt(1))) && (f = e.regex.hrs.test("0" + a.charAt(0)))) return b.buffer[c - 1] = "0", b.buffer[c] = a.charAt(0), c++, { refreshFromBuffer: { start: c - 2, end: c }, pos: c, c: e.timeseparator };if (f && "24" !== e.hourFormat && e.regex.hrs24.test(a)) {
                var g = parseInt(a, 10);return 24 === g ? (b.buffer[c + 5] = "a", b.buffer[c + 6] = "m") : (b.buffer[c + 5] = "p", b.buffer[c + 6] = "m"), g -= 12, g < 10 ? (b.buffer[c] = g.toString(), b.buffer[c - 1] = "0") : (b.buffer[c] = g.toString().charAt(1), b.buffer[c - 1] = g.toString().charAt(0)), { refreshFromBuffer: { start: c - 1, end: c + 6 }, c: b.buffer[c] };
              }return f;
            }, cardinality: 2, prevalidator: [{ validator: function validator(a, b, c, d, e) {
                var f = e.regex.hrspre.test(a);return d || f || !(f = e.regex.hrs.test("0" + a)) ? f : (b.buffer[c] = "0", c++, { pos: c });
              }, cardinality: 1 }] }, s: { validator: "[0-5][0-9]", cardinality: 2, prevalidator: [{ validator: function validator(a, b, c, d, e) {
                var f = e.regex.mspre.test(a);return d || f || !(f = e.regex.ms.test("0" + a)) ? f : (b.buffer[c] = "0", c++, { pos: c });
              }, cardinality: 1 }] }, t: { validator: function validator(a, b, c, d, e) {
              return e.regex.ampm.test(a + "m");
            }, casing: "lower", cardinality: 1 } }, insertMode: !1, autoUnmask: !1 }, datetime12: { mask: "1/2/y h:s t\\m", placeholder: "dd/mm/yyyy hh:mm xm", alias: "datetime", hourFormat: "12" }, "mm/dd/yyyy hh:mm xm": { mask: "1/2/y h:s t\\m", placeholder: "mm/dd/yyyy hh:mm xm", alias: "datetime12", regex: { val2pre: function val2pre(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[13-9]|1[012])" + c + "[0-3])|(02" + c + "[0-2])");
          }, val2: function val2(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[1-9]|1[012])" + c + "(0[1-9]|[12][0-9]))|((0[13-9]|1[012])" + c + "30)|((0[13578]|1[02])" + c + "31)");
          }, val1pre: new RegExp("[01]"), val1: new RegExp("0[1-9]|1[012]") }, leapday: "02/29/", onKeyDown: function onKeyDown(c, d, e, f) {
          var g = a(this);if (c.ctrlKey && c.keyCode === b.keyCode.RIGHT) {
            var h = new Date();g.val((h.getMonth() + 1).toString() + h.getDate().toString() + h.getFullYear().toString()), g.trigger("setvalue");
          }
        } }, "hh:mm t": { mask: "h:s t\\m", placeholder: "hh:mm xm", alias: "datetime", hourFormat: "12" }, "h:s t": { mask: "h:s t\\m", placeholder: "hh:mm xm", alias: "datetime", hourFormat: "12" }, "hh:mm:ss": { mask: "h:s:s", placeholder: "hh:mm:ss", alias: "datetime", autoUnmask: !1 }, "hh:mm": { mask: "h:s", placeholder: "hh:mm", alias: "datetime", autoUnmask: !1 }, date: { alias: "dd/mm/yyyy" }, "mm/yyyy": { mask: "1/y", placeholder: "mm/yyyy", leapday: "donotuse", separator: "/", alias: "mm/dd/yyyy" }, shamsi: { regex: { val2pre: function val2pre(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[1-9]|1[012])" + c + "[0-3])");
          }, val2: function val2(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[1-9]|1[012])" + c + "(0[1-9]|[12][0-9]))|((0[1-9]|1[012])" + c + "30)|((0[1-6])" + c + "31)");
          }, val1pre: new RegExp("[01]"), val1: new RegExp("0[1-9]|1[012]") }, yearrange: { minyear: 1300, maxyear: 1499 }, mask: "y/1/2", leapday: "/12/30", placeholder: "yyyy/mm/dd", alias: "mm/dd/yyyy", clearIncomplete: !0 }, "yyyy-mm-dd hh:mm:ss": { mask: "y-1-2 h:s:s", placeholder: "yyyy-mm-dd hh:mm:ss", alias: "datetime", separator: "-", leapday: "-02-29", regex: { val2pre: function val2pre(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[13-9]|1[012])" + c + "[0-3])|(02" + c + "[0-2])");
          }, val2: function val2(a) {
            var c = b.escapeRegex.call(this, a);return new RegExp("((0[1-9]|1[012])" + c + "(0[1-9]|[12][0-9]))|((0[13-9]|1[012])" + c + "30)|((0[13578]|1[02])" + c + "31)");
          }, val1pre: new RegExp("[01]"), val1: new RegExp("0[1-9]|1[012]") }, onKeyDown: function onKeyDown(a, b, c, d) {} } }), b;
  });
}, function (a, b, c) {
  "use strict";
  var d, e, f;"function" == typeof Symbol && Symbol.iterator;!function (g) {
    e = [c(0), c(1)], d = g, void 0 !== (f = "function" == typeof d ? d.apply(b, e) : d) && (a.exports = f);
  }(function (a, b) {
    return b.extendDefinitions({ A: { validator: "[A-Za-z\u0410-\u044F\u0401\u0451\xC0-\xFF\xB5]", cardinality: 1, casing: "upper" }, "&": { validator: "[0-9A-Za-z\u0410-\u044F\u0401\u0451\xC0-\xFF\xB5]", cardinality: 1, casing: "upper" }, "#": { validator: "[0-9A-Fa-f]", cardinality: 1, casing: "upper" } }), b.extendAliases({ url: { definitions: { i: { validator: ".", cardinality: 1 } }, mask: "(\\http://)|(\\http\\s://)|(ftp://)|(ftp\\s://)i{+}", insertMode: !1, autoUnmask: !1, inputmode: "url" }, ip: { mask: "i[i[i]].i[i[i]].i[i[i]].i[i[i]]", definitions: { i: { validator: function validator(a, b, c, d, e) {
              return c - 1 > -1 && "." !== b.buffer[c - 1] ? (a = b.buffer[c - 1] + a, a = c - 2 > -1 && "." !== b.buffer[c - 2] ? b.buffer[c - 2] + a : "0" + a) : a = "00" + a, new RegExp("25[0-5]|2[0-4][0-9]|[01][0-9][0-9]").test(a);
            }, cardinality: 1 } }, onUnMask: function onUnMask(a, b, c) {
          return a;
        }, inputmode: "numeric" }, email: { mask: "*{1,64}[.*{1,64}][.*{1,64}][.*{1,63}]@-{1,63}.-{1,63}[.-{1,63}][.-{1,63}]", greedy: !1, onBeforePaste: function onBeforePaste(a, b) {
          return a = a.toLowerCase(), a.replace("mailto:", "");
        }, definitions: { "*": { validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~-]", cardinality: 1, casing: "lower" }, "-": { validator: "[0-9A-Za-z-]", cardinality: 1, casing: "lower" } }, onUnMask: function onUnMask(a, b, c) {
          return a;
        }, inputmode: "email" }, mac: { mask: "##:##:##:##:##:##" }, vin: { mask: "V{13}9{4}", definitions: { V: { validator: "[A-HJ-NPR-Za-hj-npr-z\\d]", cardinality: 1, casing: "upper" } }, clearIncomplete: !0, autoUnmask: !0 } }), b;
  });
}, function (a, b, c) {
  "use strict";
  var d, e, f;"function" == typeof Symbol && Symbol.iterator;!function (g) {
    e = [c(0), c(1)], d = g, void 0 !== (f = "function" == typeof d ? d.apply(b, e) : d) && (a.exports = f);
  }(function (a, b, c) {
    function d(a, c) {
      for (var d = "", e = 0; e < a.length; e++) {
        b.prototype.definitions[a.charAt(e)] || c.definitions[a.charAt(e)] || c.optionalmarker.start === a.charAt(e) || c.optionalmarker.end === a.charAt(e) || c.quantifiermarker.start === a.charAt(e) || c.quantifiermarker.end === a.charAt(e) || c.groupmarker.start === a.charAt(e) || c.groupmarker.end === a.charAt(e) || c.alternatormarker === a.charAt(e) ? d += "\\" + a.charAt(e) : d += a.charAt(e);
      }return d;
    }return b.extendAliases({ numeric: { mask: function mask(a) {
          if (0 !== a.repeat && isNaN(a.integerDigits) && (a.integerDigits = a.repeat), a.repeat = 0, a.groupSeparator === a.radixPoint && ("." === a.radixPoint ? a.groupSeparator = "," : "," === a.radixPoint ? a.groupSeparator = "." : a.groupSeparator = ""), " " === a.groupSeparator && (a.skipOptionalPartCharacter = c), a.autoGroup = a.autoGroup && "" !== a.groupSeparator, a.autoGroup && ("string" == typeof a.groupSize && isFinite(a.groupSize) && (a.groupSize = parseInt(a.groupSize)), isFinite(a.integerDigits))) {
            var b = Math.floor(a.integerDigits / a.groupSize),
                e = a.integerDigits % a.groupSize;a.integerDigits = parseInt(a.integerDigits) + (0 === e ? b - 1 : b), a.integerDigits < 1 && (a.integerDigits = "*");
          }a.placeholder.length > 1 && (a.placeholder = a.placeholder.charAt(0)), "radixFocus" === a.positionCaretOnClick && "" === a.placeholder && !1 === a.integerOptional && (a.positionCaretOnClick = "lvp"), a.definitions[";"] = a.definitions["~"], a.definitions[";"].definitionSymbol = "~", !0 === a.numericInput && (a.positionCaretOnClick = "radixFocus" === a.positionCaretOnClick ? "lvp" : a.positionCaretOnClick, a.digitsOptional = !1, isNaN(a.digits) && (a.digits = 2), a.decimalProtect = !1);var f = "[+]";if (f += d(a.prefix, a), !0 === a.integerOptional ? f += "~{1," + a.integerDigits + "}" : f += "~{" + a.integerDigits + "}", a.digits !== c) {
            a.radixPointDefinitionSymbol = a.decimalProtect ? ":" : a.radixPoint;var g = a.digits.toString().split(",");isFinite(g[0] && g[1] && isFinite(g[1])) ? f += a.radixPointDefinitionSymbol + ";{" + a.digits + "}" : (isNaN(a.digits) || parseInt(a.digits) > 0) && (a.digitsOptional ? f += "[" + a.radixPointDefinitionSymbol + ";{1," + a.digits + "}]" : f += a.radixPointDefinitionSymbol + ";{" + a.digits + "}");
          }return f += d(a.suffix, a), f += "[-]", a.greedy = !1, f;
        }, placeholder: "", greedy: !1, digits: "*", digitsOptional: !0, enforceDigitsOnBlur: !1, radixPoint: ".", positionCaretOnClick: "radixFocus", groupSize: 3, groupSeparator: "", autoGroup: !1, allowMinus: !0, negationSymbol: { front: "-", back: "" }, integerDigits: "+", integerOptional: !0, prefix: "", suffix: "", rightAlign: !0, decimalProtect: !0, min: null, max: null, step: 1, insertMode: !0, autoUnmask: !1, unmaskAsNumber: !1, inputmode: "numeric", preValidation: function preValidation(b, d, e, f, g) {
          if ("-" === e || e == g.negationSymbol.front) return !0 === g.allowMinus && (g.isNegative = g.isNegative === c || !g.isNegative, "" === b.join("") || { caret: d, dopost: !0 });if (!1 === f && e === g.radixPoint && g.digits !== c && (isNaN(g.digits) || parseInt(g.digits) > 0)) {
            var h = a.inArray(g.radixPoint, b);if (-1 !== h) return !0 === g.numericInput ? d === h : { caret: h + 1 };
          }return !0;
        }, postValidation: function postValidation(d, e, f) {
          var g = f.suffix.split(""),
              h = f.prefix.split("");if (e.pos == c && e.caret !== c && !0 !== e.dopost) return e;var i = e.caret != c ? e.caret : e.pos,
              j = d.slice();f.numericInput && (i = j.length - i - 1, j = j.reverse());var k = j[i];if (k === f.groupSeparator && (i += 1, k = j[i]), i == j.length - f.suffix.length - 1 && k === f.radixPoint) return e;k !== c && k !== f.radixPoint && k !== f.negationSymbol.front && k !== f.negationSymbol.back && (j[i] = "?", f.prefix.length > 0 && i >= (!1 === f.isNegative ? 1 : 0) && i < f.prefix.length - 1 + (!1 === f.isNegative ? 1 : 0) ? h[i - (!1 === f.isNegative ? 1 : 0)] = "?" : f.suffix.length > 0 && i >= j.length - f.suffix.length - (!1 === f.isNegative ? 1 : 0) && (g[i - (j.length - f.suffix.length - (!1 === f.isNegative ? 1 : 0))] = "?")), h = h.join(""), g = g.join("");var l = j.join("").replace(h, "");if (l = l.replace(g, ""), l = l.replace(new RegExp(b.escapeRegex(f.groupSeparator), "g"), ""), l = l.replace(new RegExp("[-" + b.escapeRegex(f.negationSymbol.front) + "]", "g"), ""), l = l.replace(new RegExp(b.escapeRegex(f.negationSymbol.back) + "$"), ""), isNaN(f.placeholder) && (l = l.replace(new RegExp(b.escapeRegex(f.placeholder), "g"), "")), l.length > 1 && 1 !== l.indexOf(f.radixPoint) && ("0" == k && (l = l.replace(/^\?/g, "")), l = l.replace(/^0/g, "")), l.charAt(0) === f.radixPoint && "" !== f.radixPoint && !0 !== f.numericInput && (l = "0" + l), "" !== l) {
            if (l = l.split(""), (!f.digitsOptional || f.enforceDigitsOnBlur && "blur" === e.event) && isFinite(f.digits)) {
              var m = a.inArray(f.radixPoint, l),
                  n = a.inArray(f.radixPoint, j);-1 === m && (l.push(f.radixPoint), m = l.length - 1);for (var o = 1; o <= f.digits; o++) {
                f.digitsOptional && (!f.enforceDigitsOnBlur || "blur" !== e.event) || l[m + o] !== c && l[m + o] !== f.placeholder.charAt(0) ? -1 !== n && j[n + o] !== c && (l[m + o] = l[m + o] || j[n + o]) : l[m + o] = e.placeholder || f.placeholder.charAt(0);
              }
            }!0 !== f.autoGroup || "" === f.groupSeparator || k === f.radixPoint && e.pos === c && !e.dopost ? l = l.join("") : (l = b(function (a, b) {
              var c = "";if (c += "(" + b.groupSeparator + "*{" + b.groupSize + "}){*}", "" !== b.radixPoint) {
                var d = a.join("").split(b.radixPoint);d[1] && (c += b.radixPoint + "*{" + d[1].match(/^\d*\??\d*/)[0].length + "}");
              }return c;
            }(l, f), { numericInput: !0, jitMasking: !0, definitions: { "*": { validator: "[0-9?]", cardinality: 1 } } }).format(l.join("")), l.charAt(0) === f.groupSeparator && l.substr(1));
          }if (f.isNegative && "blur" === e.event && (f.isNegative = "0" !== l), l = h + l, l += g, f.isNegative && (l = f.negationSymbol.front + l, l += f.negationSymbol.back), l = l.split(""), k !== c) if (k !== f.radixPoint && k !== f.negationSymbol.front && k !== f.negationSymbol.back) i = a.inArray("?", l), i > -1 ? l[i] = k : i = e.caret || 0;else if (k === f.radixPoint || k === f.negationSymbol.front || k === f.negationSymbol.back) {
            var p = a.inArray(k, l);-1 !== p && (i = p);
          }f.numericInput && (i = l.length - i - 1, l = l.reverse());var q = { caret: k === c || e.pos !== c ? i + (f.numericInput ? -1 : 1) : i, buffer: l, refreshFromBuffer: e.dopost || d.join("") !== l.join("") };return q.refreshFromBuffer ? q : e;
        }, onBeforeWrite: function onBeforeWrite(d, e, f, g) {
          if (d) switch (d.type) {case "keydown":
              return g.postValidation(e, { caret: f, dopost: !0 }, g);case "blur":case "checkval":
              var h;if (function (a) {
                a.parseMinMaxOptions === c && (null !== a.min && (a.min = a.min.toString().replace(new RegExp(b.escapeRegex(a.groupSeparator), "g"), ""), "," === a.radixPoint && (a.min = a.min.replace(a.radixPoint, ".")), a.min = isFinite(a.min) ? parseFloat(a.min) : NaN, isNaN(a.min) && (a.min = Number.MIN_VALUE)), null !== a.max && (a.max = a.max.toString().replace(new RegExp(b.escapeRegex(a.groupSeparator), "g"), ""), "," === a.radixPoint && (a.max = a.max.replace(a.radixPoint, ".")), a.max = isFinite(a.max) ? parseFloat(a.max) : NaN, isNaN(a.max) && (a.max = Number.MAX_VALUE)), a.parseMinMaxOptions = "done");
              }(g), null !== g.min || null !== g.max) {
                if (h = g.onUnMask(e.join(""), c, a.extend({}, g, { unmaskAsNumber: !0 })), null !== g.min && h < g.min) return g.isNegative = g.min < 0, g.postValidation(g.min.toString().replace(".", g.radixPoint).split(""), { caret: f, dopost: !0, placeholder: "0" }, g);if (null !== g.max && h > g.max) return g.isNegative = g.max < 0, g.postValidation(g.max.toString().replace(".", g.radixPoint).split(""), { caret: f, dopost: !0, placeholder: "0" }, g);
              }return g.postValidation(e, { caret: f, dopost: !0, placeholder: "0", event: "blur" }, g);case "_checkval":
              return { caret: f };}
        }, regex: { integerPart: function integerPart(a, c) {
            return c ? new RegExp("[" + b.escapeRegex(a.negationSymbol.front) + "+]?") : new RegExp("[" + b.escapeRegex(a.negationSymbol.front) + "+]?\\d+");
          }, integerNPart: function integerNPart(a) {
            return new RegExp("[\\d" + b.escapeRegex(a.groupSeparator) + b.escapeRegex(a.placeholder.charAt(0)) + "]+");
          } }, definitions: { "~": { validator: function validator(a, d, e, f, g, h) {
              var i = f ? new RegExp("[0-9" + b.escapeRegex(g.groupSeparator) + "]").test(a) : new RegExp("[0-9]").test(a);if (!0 === i) {
                if (!0 !== g.numericInput && d.validPositions[e] !== c && "~" === d.validPositions[e].match.def && !h) {
                  var j = d.buffer.join("");j = j.replace(new RegExp("[-" + b.escapeRegex(g.negationSymbol.front) + "]", "g"), ""), j = j.replace(new RegExp(b.escapeRegex(g.negationSymbol.back) + "$"), "");var k = j.split(g.radixPoint);k.length > 1 && (k[1] = k[1].replace(/0/g, g.placeholder.charAt(0))), "0" === k[0] && (k[0] = k[0].replace(/0/g, g.placeholder.charAt(0))), j = k[0] + g.radixPoint + k[1] || "";var l = d._buffer.join("");for (j === g.radixPoint && (j = l); null === j.match(b.escapeRegex(l) + "$");) {
                    l = l.slice(1);
                  }j = j.replace(l, ""), j = j.split(""), i = j[e] === c ? { pos: e, remove: e } : { pos: e };
                }
              } else f || a !== g.radixPoint || d.validPositions[e - 1] !== c || (d.buffer[e] = "0", i = { pos: e + 1 });return i;
            }, cardinality: 1 }, "+": { validator: function validator(a, b, c, d, e) {
              return e.allowMinus && ("-" === a || a === e.negationSymbol.front);
            }, cardinality: 1, placeholder: "" }, "-": { validator: function validator(a, b, c, d, e) {
              return e.allowMinus && a === e.negationSymbol.back;
            }, cardinality: 1, placeholder: "" }, ":": { validator: function validator(a, c, d, e, f) {
              var g = "[" + b.escapeRegex(f.radixPoint) + "]",
                  h = new RegExp(g).test(a);return h && c.validPositions[d] && c.validPositions[d].match.placeholder === f.radixPoint && (h = { caret: d + 1 }), h;
            }, cardinality: 1, placeholder: function placeholder(a) {
              return a.radixPoint;
            } } }, onUnMask: function onUnMask(a, c, d) {
          if ("" === c && !0 === d.nullable) return c;var e = a.replace(d.prefix, "");return e = e.replace(d.suffix, ""), e = e.replace(new RegExp(b.escapeRegex(d.groupSeparator), "g"), ""), "" !== d.placeholder.charAt(0) && (e = e.replace(new RegExp(d.placeholder.charAt(0), "g"), "0")), d.unmaskAsNumber ? ("" !== d.radixPoint && -1 !== e.indexOf(d.radixPoint) && (e = e.replace(b.escapeRegex.call(this, d.radixPoint), ".")), e = e.replace(new RegExp("^" + b.escapeRegex(d.negationSymbol.front)), "-"), e = e.replace(new RegExp(b.escapeRegex(d.negationSymbol.back) + "$"), ""), Number(e)) : e;
        }, isComplete: function isComplete(a, c) {
          var d = a.join("");if (a.slice().join("") !== d) return !1;var e = d.replace(c.prefix, "");return e = e.replace(c.suffix, ""), e = e.replace(new RegExp(b.escapeRegex(c.groupSeparator), "g"), ""), "," === c.radixPoint && (e = e.replace(b.escapeRegex(c.radixPoint), ".")), isFinite(e);
        }, onBeforeMask: function onBeforeMask(a, d) {
          if (d.isNegative = c, a = a.toString().charAt(a.length - 1) === d.radixPoint ? a.toString().substr(0, a.length - 1) : a.toString(), "" !== d.radixPoint && isFinite(a)) {
            var e = a.split("."),
                f = "" !== d.groupSeparator ? parseInt(d.groupSize) : 0;2 === e.length && (e[0].length > f || e[1].length > f || e[0].length <= f && e[1].length < f) && (a = a.replace(".", d.radixPoint));
          }var g = a.match(/,/g),
              h = a.match(/\./g);if (h && g ? h.length > g.length ? (a = a.replace(/\./g, ""), a = a.replace(",", d.radixPoint)) : g.length > h.length ? (a = a.replace(/,/g, ""), a = a.replace(".", d.radixPoint)) : a = a.indexOf(".") < a.indexOf(",") ? a.replace(/\./g, "") : a = a.replace(/,/g, "") : a = a.replace(new RegExp(b.escapeRegex(d.groupSeparator), "g"), ""), 0 === d.digits && (-1 !== a.indexOf(".") ? a = a.substring(0, a.indexOf(".")) : -1 !== a.indexOf(",") && (a = a.substring(0, a.indexOf(",")))), "" !== d.radixPoint && isFinite(d.digits) && -1 !== a.indexOf(d.radixPoint)) {
            var i = a.split(d.radixPoint),
                j = i[1].match(new RegExp("\\d*"))[0];if (parseInt(d.digits) < j.toString().length) {
              var k = Math.pow(10, parseInt(d.digits));a = a.replace(b.escapeRegex(d.radixPoint), "."), a = Math.round(parseFloat(a) * k) / k, a = a.toString().replace(".", d.radixPoint);
            }
          }return a;
        }, canClearPosition: function canClearPosition(a, b, c, d, e) {
          var f = a.validPositions[b],
              g = f.input !== e.radixPoint || null !== a.validPositions[b].match.fn && !1 === e.decimalProtect || f.input === e.radixPoint && a.validPositions[b + 1] && null === a.validPositions[b + 1].match.fn || isFinite(f.input) || b === c || f.input === e.groupSeparator || f.input === e.negationSymbol.front || f.input === e.negationSymbol.back;return !g || "+" != f.match.nativeDef && "-" != f.match.nativeDef || (e.isNegative = !1), g;
        }, onKeyDown: function onKeyDown(c, d, e, f) {
          var g = a(this);if (c.ctrlKey) switch (c.keyCode) {case b.keyCode.UP:
              g.val(parseFloat(this.inputmask.unmaskedvalue()) + parseInt(f.step)), g.trigger("setvalue");break;case b.keyCode.DOWN:
              g.val(parseFloat(this.inputmask.unmaskedvalue()) - parseInt(f.step)), g.trigger("setvalue");}
        } }, currency: { prefix: "$ ", groupSeparator: ",", alias: "numeric", placeholder: "0", autoGroup: !0, digits: 2, digitsOptional: !1, clearMaskOnLostFocus: !1 }, decimal: { alias: "numeric" }, integer: { alias: "numeric", digits: 0, radixPoint: "" }, percentage: { alias: "numeric", digits: 2, digitsOptional: !0, radixPoint: ".", placeholder: "0", autoGroup: !1, min: 0, max: 100, suffix: " %", allowMinus: !1 } }), b;
  });
}, function (a, b, c) {
  "use strict";
  var d, e, f;"function" == typeof Symbol && Symbol.iterator;!function (g) {
    e = [c(0), c(1)], d = g, void 0 !== (f = "function" == typeof d ? d.apply(b, e) : d) && (a.exports = f);
  }(function (a, b) {
    function c(a, b) {
      var c = (a.mask || a).replace(/#/g, "9").replace(/\)/, "9").replace(/[+()#-]/g, ""),
          d = (b.mask || b).replace(/#/g, "9").replace(/\)/, "9").replace(/[+()#-]/g, ""),
          e = (a.mask || a).split("#")[0],
          f = (b.mask || b).split("#")[0];return 0 === f.indexOf(e) ? -1 : 0 === e.indexOf(f) ? 1 : c.localeCompare(d);
    }var d = b.prototype.analyseMask;return b.prototype.analyseMask = function (b, c, e) {
      function f(a, c, d) {
        c = c || "", d = d || h, "" !== c && (d[c] = {});for (var e = "", g = d[c] || d, i = a.length - 1; i >= 0; i--) {
          b = a[i].mask || a[i], e = b.substr(0, 1), g[e] = g[e] || [], g[e].unshift(b.substr(1)), a.splice(i, 1);
        }for (var j in g) {
          g[j].length > 500 && f(g[j].slice(), j, g);
        }
      }function g(b) {
        var c = "",
            d = [];for (var f in b) {
          a.isArray(b[f]) ? 1 === b[f].length ? d.push(f + b[f]) : d.push(f + e.groupmarker.start + b[f].join(e.groupmarker.end + e.alternatormarker + e.groupmarker.start) + e.groupmarker.end) : d.push(f + g(b[f]));
        }return 1 === d.length ? c += d[0] : c += e.groupmarker.start + d.join(e.groupmarker.end + e.alternatormarker + e.groupmarker.start) + e.groupmarker.end, c;
      }var h = {};return e.phoneCodes && (e.phoneCodes && e.phoneCodes.length > 1e3 && (b = b.substr(1, b.length - 2), f(b.split(e.groupmarker.end + e.alternatormarker + e.groupmarker.start)), b = g(h)), b = b.replace(/9/g, "\\9")), d.call(this, b, c, e);
    }, b.extendAliases({ abstractphone: { groupmarker: { start: "<", end: ">" }, countrycode: "", phoneCodes: [], mask: function mask(a) {
          return a.definitions = { "#": b.prototype.definitions[9] }, a.phoneCodes.sort(c);
        }, keepStatic: !0, onBeforeMask: function onBeforeMask(a, b) {
          var c = a.replace(/^0{1,2}/, "").replace(/[\s]/g, "");return (c.indexOf(b.countrycode) > 1 || -1 === c.indexOf(b.countrycode)) && (c = "+" + b.countrycode + c), c;
        }, onUnMask: function onUnMask(a, b, c) {
          return a.replace(/[()#-]/g, "");
        }, inputmode: "tel" } }), b;
  });
}, function (a, b, c) {
  "use strict";
  var d, e, f;"function" == typeof Symbol && Symbol.iterator;!function (g) {
    e = [c(0), c(1)], d = g, void 0 !== (f = "function" == typeof d ? d.apply(b, e) : d) && (a.exports = f);
  }(function (a, b) {
    return b.extendAliases({ Regex: { mask: "r", greedy: !1, repeat: "*", regex: null, regexTokens: null, tokenizer: /\[\^?]?(?:[^\\\]]+|\\[\S\s]?)*]?|\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9][0-9]*|x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|c[A-Za-z]|[\S\s]?)|\((?:\?[:=!]?)?|(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??|[^.?*+^${[()|\\]+|./g, quantifierFilter: /[0-9]+[^,]/, isComplete: function isComplete(a, b) {
          return new RegExp(b.regex, b.casing ? "i" : "").test(a.join(""));
        }, definitions: { r: { validator: function validator(b, c, d, e, f) {
              function g(a, b) {
                this.matches = [], this.isGroup = a || !1, this.isQuantifier = b || !1, this.quantifier = { min: 1, max: 1 }, this.repeaterPart = void 0;
              }function h(b, c) {
                var d = !1;c && (l += "(", n++);for (var e = 0; e < b.matches.length; e++) {
                  var g = b.matches[e];if (!0 === g.isGroup) d = h(g, !0);else if (!0 === g.isQuantifier) {
                    var j = a.inArray(g, b.matches),
                        k = b.matches[j - 1],
                        m = l;if (isNaN(g.quantifier.max)) {
                      for (; g.repeaterPart && g.repeaterPart !== l && g.repeaterPart.length > l.length && !(d = h(k, !0));) {}d = d || h(k, !0), d && (g.repeaterPart = l), l = m + g.quantifier.max;
                    } else {
                      for (var o = 0, p = g.quantifier.max - 1; o < p && !(d = h(k, !0)); o++) {}l = m + "{" + g.quantifier.min + "," + g.quantifier.max + "}";
                    }
                  } else if (void 0 !== g.matches) for (var q = 0; q < g.length && !(d = h(g[q], c)); q++) {} else {
                    var r;if ("[" == g.charAt(0)) {
                      r = l, r += g;for (var s = 0; s < n; s++) {
                        r += ")";
                      }var t = new RegExp("^(" + r + ")$", f.casing ? "i" : "");d = t.test(i);
                    } else for (var u = 0, v = g.length; u < v; u++) {
                      if ("\\" !== g.charAt(u)) {
                        r = l, r += g.substr(0, u + 1), r = r.replace(/\|$/, "");for (var s = 0; s < n; s++) {
                          r += ")";
                        }var t = new RegExp("^(" + r + ")$", f.casing ? "i" : "");if (d = t.test(i)) break;
                      }
                    }l += g;
                  }if (d) break;
                }return c && (l += ")", n--), d;
              }var i,
                  j,
                  k = c.buffer.slice(),
                  l = "",
                  m = !1,
                  n = 0;null === f.regexTokens && function () {
                var a,
                    b,
                    c = new g(),
                    d = [];for (f.regexTokens = []; a = f.tokenizer.exec(f.regex);) {
                  switch (b = a[0], b.charAt(0)) {case "(":
                      d.push(new g(!0));break;case ")":
                      j = d.pop(), d.length > 0 ? d[d.length - 1].matches.push(j) : c.matches.push(j);break;case "{":case "+":case "*":
                      var e = new g(!1, !0);b = b.replace(/[{}]/g, "");var h = b.split(","),
                          i = isNaN(h[0]) ? h[0] : parseInt(h[0]),
                          k = 1 === h.length ? i : isNaN(h[1]) ? h[1] : parseInt(h[1]);if (e.quantifier = { min: i, max: k }, d.length > 0) {
                        var l = d[d.length - 1].matches;a = l.pop(), a.isGroup || (j = new g(!0), j.matches.push(a), a = j), l.push(a), l.push(e);
                      } else a = c.matches.pop(), a.isGroup || (j = new g(!0), j.matches.push(a), a = j), c.matches.push(a), c.matches.push(e);break;default:
                      d.length > 0 ? d[d.length - 1].matches.push(b) : c.matches.push(b);}
                }c.matches.length > 0 && f.regexTokens.push(c);
              }(), k.splice(d, 0, b), i = k.join("");for (var o = 0; o < f.regexTokens.length; o++) {
                var p = f.regexTokens[o];if (m = h(p, p.isGroup)) break;
              }return m;
            }, cardinality: 1 } } } }), b;
  });
}, function (a, b, c) {
  "use strict";
  var d,
      e,
      f,
      g = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (a) {
    return typeof a === "undefined" ? "undefined" : _typeof(a);
  } : function (a) {
    return a && "function" == typeof Symbol && a.constructor === Symbol && a !== Symbol.prototype ? "symbol" : typeof a === "undefined" ? "undefined" : _typeof(a);
  };!function (g) {
    e = [c(2), c(1)], d = g, void 0 !== (f = "function" == typeof d ? d.apply(b, e) : d) && (a.exports = f);
  }(function (a, b) {
    return void 0 === a.fn.inputmask && (a.fn.inputmask = function (c, d) {
      var e,
          f = this[0];if (void 0 === d && (d = {}), "string" == typeof c) switch (c) {case "unmaskedvalue":
          return f && f.inputmask ? f.inputmask.unmaskedvalue() : a(f).val();case "remove":
          return this.each(function () {
            this.inputmask && this.inputmask.remove();
          });case "getemptymask":
          return f && f.inputmask ? f.inputmask.getemptymask() : "";case "hasMaskedValue":
          return !(!f || !f.inputmask) && f.inputmask.hasMaskedValue();case "isComplete":
          return !f || !f.inputmask || f.inputmask.isComplete();case "getmetadata":
          return f && f.inputmask ? f.inputmask.getmetadata() : void 0;case "setvalue":
          a(f).val(d), f && void 0 === f.inputmask && a(f).triggerHandler("setvalue");break;case "option":
          if ("string" != typeof d) return this.each(function () {
            if (void 0 !== this.inputmask) return this.inputmask.option(d);
          });if (f && void 0 !== f.inputmask) return f.inputmask.option(d);break;default:
          return d.alias = c, e = new b(d), this.each(function () {
            e.mask(this);
          });} else {
        if ("object" == (void 0 === c ? "undefined" : g(c))) return e = new b(c), void 0 === c.mask && void 0 === c.alias ? this.each(function () {
          if (void 0 !== this.inputmask) return this.inputmask.option(c);e.mask(this);
        }) : this.each(function () {
          e.mask(this);
        });if (void 0 === c) return this.each(function () {
          e = new b(d), e.mask(this);
        });
      }
    }), a.fn.inputmask;
  });
}, function (a, b, c) {
  var d = c(13);"string" == typeof d && (d = [[a.i, d, ""]]);c(15)(d, {});d.locals && (a.exports = d.locals);
}, function (a, b, c) {
  "use strict";
  function d(a) {
    return a && a.__esModule ? a : { default: a };
  }c(9), c(3), c(4), c(5), c(6), c(7);var e = c(1),
      f = d(e),
      g = c(0),
      h = d(g),
      i = c(2),
      j = d(i);h.default === j.default && c(8), window.Inputmask = f.default;
}, function (a, b, c) {
  "use strict";
  var d;"function" == typeof Symbol && Symbol.iterator;void 0 !== (d = function () {
    return document;
  }.call(b, c, b, a)) && (a.exports = d);
}, function (a, b, c) {
  "use strict";
  var d;"function" == typeof Symbol && Symbol.iterator;void 0 !== (d = function () {
    return window;
  }.call(b, c, b, a)) && (a.exports = d);
}, function (a, b, c) {
  b = a.exports = c(14)(void 0), b.push([a.i, ".im-caret {\r\n\t-webkit-animation: 1s blink step-end infinite;\r\n\tanimation: 1s blink step-end infinite;\r\n}\r\n\r\n@keyframes blink {\r\n\tfrom, to {\r\n\t\tborder-right-color: black;\r\n\t}\r\n\t50% {\r\n\t\tborder-right-color: transparent;\r\n\t}\r\n}\r\n\r\n@-webkit-keyframes blink {\r\n\tfrom, to {\r\n\t\tborder-right-color: black;\r\n\t}\r\n\t50% {\r\n\t\tborder-right-color: transparent;\r\n\t}\r\n}\r\n\r\n.im-static {\r\n\tcolor: grey;\r\n}\r\n", ""]);
}, function (a, b) {
  function c(a, b) {
    var c = a[1] || "",
        e = a[3];if (!e) return c;if (b && "function" == typeof btoa) {
      var f = d(e),
          g = e.sources.map(function (a) {
        return "/*# sourceURL=" + e.sourceRoot + a + " */";
      });return [c].concat(g).concat([f]).join("\n");
    }return [c].join("\n");
  }function d(a) {
    return "/*# sourceMappingURL=data:application/json;charset=utf-8;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(a)))) + " */";
  }a.exports = function (a) {
    var b = [];return b.toString = function () {
      return this.map(function (b) {
        var d = c(b, a);return b[2] ? "@media " + b[2] + "{" + d + "}" : d;
      }).join("");
    }, b.i = function (a, c) {
      "string" == typeof a && (a = [[null, a, ""]]);for (var d = {}, e = 0; e < this.length; e++) {
        var f = this[e][0];"number" == typeof f && (d[f] = !0);
      }for (e = 0; e < a.length; e++) {
        var g = a[e];"number" == typeof g[0] && d[g[0]] || (c && !g[2] ? g[2] = c : c && (g[2] = "(" + g[2] + ") and (" + c + ")"), b.push(g));
      }
    }, b;
  };
}, function (a, b, c) {
  function d(a, b) {
    for (var c = 0; c < a.length; c++) {
      var d = a[c],
          e = o[d.id];if (e) {
        e.refs++;for (var f = 0; f < e.parts.length; f++) {
          e.parts[f](d.parts[f]);
        }for (; f < d.parts.length; f++) {
          e.parts.push(k(d.parts[f], b));
        }
      } else {
        for (var g = [], f = 0; f < d.parts.length; f++) {
          g.push(k(d.parts[f], b));
        }o[d.id] = { id: d.id, refs: 1, parts: g };
      }
    }
  }function e(a) {
    for (var b = [], c = {}, d = 0; d < a.length; d++) {
      var e = a[d],
          f = e[0],
          g = e[1],
          h = e[2],
          i = e[3],
          j = { css: g, media: h, sourceMap: i };c[f] ? c[f].parts.push(j) : b.push(c[f] = { id: f, parts: [j] });
    }return b;
  }function f(a, b) {
    var c = q(a.insertInto);if (!c) throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");var d = t[t.length - 1];if ("top" === a.insertAt) d ? d.nextSibling ? c.insertBefore(b, d.nextSibling) : c.appendChild(b) : c.insertBefore(b, c.firstChild), t.push(b);else {
      if ("bottom" !== a.insertAt) throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");c.appendChild(b);
    }
  }function g(a) {
    a.parentNode.removeChild(a);var b = t.indexOf(a);b >= 0 && t.splice(b, 1);
  }function h(a) {
    var b = document.createElement("style");return a.attrs.type = "text/css", j(b, a.attrs), f(a, b), b;
  }function i(a) {
    var b = document.createElement("link");return a.attrs.type = "text/css", a.attrs.rel = "stylesheet", j(b, a.attrs), f(a, b), b;
  }function j(a, b) {
    Object.keys(b).forEach(function (c) {
      a.setAttribute(c, b[c]);
    });
  }function k(a, b) {
    var c, d, e;if (b.singleton) {
      var f = s++;c = r || (r = h(b)), d = l.bind(null, c, f, !1), e = l.bind(null, c, f, !0);
    } else a.sourceMap && "function" == typeof URL && "function" == typeof URL.createObjectURL && "function" == typeof URL.revokeObjectURL && "function" == typeof Blob && "function" == typeof btoa ? (c = i(b), d = n.bind(null, c, b), e = function e() {
      g(c), c.href && URL.revokeObjectURL(c.href);
    }) : (c = h(b), d = m.bind(null, c), e = function e() {
      g(c);
    });return d(a), function (b) {
      if (b) {
        if (b.css === a.css && b.media === a.media && b.sourceMap === a.sourceMap) return;d(a = b);
      } else e();
    };
  }function l(a, b, c, d) {
    var e = c ? "" : d.css;if (a.styleSheet) a.styleSheet.cssText = v(b, e);else {
      var f = document.createTextNode(e),
          g = a.childNodes;g[b] && a.removeChild(g[b]), g.length ? a.insertBefore(f, g[b]) : a.appendChild(f);
    }
  }function m(a, b) {
    var c = b.css,
        d = b.media;if (d && a.setAttribute("media", d), a.styleSheet) a.styleSheet.cssText = c;else {
      for (; a.firstChild;) {
        a.removeChild(a.firstChild);
      }a.appendChild(document.createTextNode(c));
    }
  }function n(a, b, c) {
    var d = c.css,
        e = c.sourceMap,
        f = void 0 === b.convertToAbsoluteUrls && e;(b.convertToAbsoluteUrls || f) && (d = u(d)), e && (d += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(e)))) + " */");var g = new Blob([d], { type: "text/css" }),
        h = a.href;a.href = URL.createObjectURL(g), h && URL.revokeObjectURL(h);
  }var o = {},
      p = function (a) {
    var b;return function () {
      return void 0 === b && (b = a.apply(this, arguments)), b;
    };
  }(function () {
    return window && document && document.all && !window.atob;
  }),
      q = function (a) {
    var b = {};return function (c) {
      return void 0 === b[c] && (b[c] = a.call(this, c)), b[c];
    };
  }(function (a) {
    return document.querySelector(a);
  }),
      r = null,
      s = 0,
      t = [],
      u = c(16);a.exports = function (a, b) {
    if ("undefined" != typeof DEBUG && DEBUG && "object" != (typeof document === "undefined" ? "undefined" : _typeof(document))) throw new Error("The style-loader cannot be used in a non-browser environment");b = b || {}, b.attrs = "object" == _typeof(b.attrs) ? b.attrs : {}, void 0 === b.singleton && (b.singleton = p()), void 0 === b.insertInto && (b.insertInto = "head"), void 0 === b.insertAt && (b.insertAt = "bottom");var c = e(a);return d(c, b), function (a) {
      for (var f = [], g = 0; g < c.length; g++) {
        var h = c[g],
            i = o[h.id];i.refs--, f.push(i);
      }if (a) {
        d(e(a), b);
      }for (var g = 0; g < f.length; g++) {
        var i = f[g];if (0 === i.refs) {
          for (var j = 0; j < i.parts.length; j++) {
            i.parts[j]();
          }delete o[i.id];
        }
      }
    };
  };var v = function () {
    var a = [];return function (b, c) {
      return a[b] = c, a.filter(Boolean).join("\n");
    };
  }();
}, function (a, b) {
  a.exports = function (a) {
    var b = "undefined" != typeof window && window.location;if (!b) throw new Error("fixUrls requires window.location");if (!a || "string" != typeof a) return a;var c = b.protocol + "//" + b.host,
        d = c + b.pathname.replace(/\/[^\/]*$/, "/");return a.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi, function (a, b) {
      var e = b.trim().replace(/^"(.*)"$/, function (a, b) {
        return b;
      }).replace(/^'(.*)'$/, function (a, b) {
        return b;
      });if (/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/)/i.test(e)) return a;var f;return f = 0 === e.indexOf("//") ? e : 0 === e.indexOf("/") ? c + e : d + e.replace(/^\.\//, ""), "url(" + JSON.stringify(f) + ")";
    });
  };
}]);

/***/ }),

/***/ "./resources/assets/thunderlab/js/inputmask/module-inputmask.js":
/***/ (function(module, exports) {

window.formInputMask = {
	money: function money() {
		var money = new Inputmask({
			rightAlign: false,
			prefix: 'Rp ',
			alias: 'currency',
			placeholder: ' ',
			autoGroup: true,
			groupSeparator: '.',
			groupSize: 3,
			radixPoint: ',',
			digit: 1,
			digitsOptional: 1,
			clearMaskOnLostFocus: true
		});
		var selector = $('.mask-money');
		money.mask(selector);
	},
	moneyRight: function moneyRight() {
		var moneyRight = new Inputmask({
			rightAlign: true,
			prefix: 'Rp ',
			alias: 'currency',
			placeholder: ' ',
			autoGroup: true,
			groupSeparator: '.',
			groupSize: 3,
			radixPoint: ',',
			digit: 1,
			digitsOptional: 1,
			clearMaskOnLostFocus: true
		});
		var selector = $('.mask-money-right');
		moneyRight.mask(selector);
	},
	birthDay: function birthDay() {
		var today = new Date();
		var year = today.getFullYear();

		var birthDate = new Inputmask({
			alias: 'dd/mm/yyyy',
			yearrange: { minyear: 1700, maxyear: year - 10 }
		});
		var selector = $('.mask-birthdate');
		birthDate.mask(selector);
	},
	date: function date() {
		var selector = $('.mask-date'),
		    min,
		    max;

		if (selector.attr('mask-date-min')) {
			min = selector.attr('mask-date-min');
		} else {
			min = '01/01/1800';
		}

		if (selector.attr('mask-date-max')) {
			max = selector.attr('mask-date-max');
		} else {
			max = '31/12/9999';
		}

		var date = new Inputmask({
			alias: 'dd/mm/yyyy',
			min: min,
			max: max
		});

		date.mask(selector);
	},
	dateTime: function dateTime() {
		var selector = $('.mask-datetime'),
		    min,
		    max;

		if (selector.attr('mask-date-min')) {
			min = selector.attr('mask-date-min');
		} else {
			min = '01/01/1800 00:00';
		}

		if (selector.attr('mask-date-max')) {
			max = selector.attr('mask-date-max');
		} else {
			max = '31/12/9999 23:59';
		}
		var dateTime = new Inputmask('datetime', {
			alias: 'dd/mm/yyyy hh:mm',
			min: min,
			max: max
		});

		dateTime.mask(selector);
	},
	year: function year() {
		var year = new Inputmask({
			mask: "y",
			definitions: {
				y: {
					validator: "(19|20)\\d{2}",
					cardinality: 4,
					prevalidator: [{
						validator: "[12]",
						cardinality: 1
					}, {
						validator: "(19|20)",
						cardinality: 2
					}, {
						validator: "(19|20)\\d",
						cardinality: 3
					}]
				}
			}
		});
		var selector = $('.mask-year');
		year.mask(selector);
	},
	yearWithRange: function yearWithRange() {
		var selector = $('.mask-year-range');
		var rangeYear = typeof selector.attr('data-year-range') !== 'undefined' && selector.attr('data-year-range') != '' ? selector.attr('data-year-range') : 0;
		var minYear = typeof selector.attr('data-year-min') !== 'undefined' && selector.attr('data-year-min') != '' ? selector.attr('data-year-min') : 0;
		var date = new Date();
		var yearNow = date.getFullYear();
		var yearRange = new Inputmask("numeric", {
			mask: 9999,
			min: rangeYear !== 0 ? yearNow - rangeYear : minYear,
			max: yearNow
		});

		yearRange.mask(selector);
	},
	yearNumber: function yearNumber() {
		var yearNumber = new Inputmask({
			alias: '9999',
			placeholder: ''
		});
		var selector = $('.mask-year-number');
		yearNumber.mask(selector);
	},
	idKTP: function idKTP() {
		var idKTP = new Inputmask({
			alias: '99-99-99-999999-9999',
			placeholder: ''
		});
		var idKTPCustom = new Inputmask({
			alias: '35-99-999999-9999',
			placeholder: ''
		});
		var selector = $('.mask-id-card');
		var selector2 = $('.mask-id-card-default');
		idKTPCustom.mask(selector);
		idKTP.mask(selector2);
	},
	noTelp: function noTelp() {
		var noTelp = new Inputmask({
			mask: '9999 999 999 99',
			placeholder: ''
		});
		var selector = $('.mask-no-telp');
		var selector2 = $('.mask-no-handphone');
		noTelp.mask(selector);
		noTelp.mask(selector2);

		// if (selector.inputmask) noTelp.inputmask.unmaskedvalue();
		// if (selector2.inputmask) noTelp.inputmask.unmaskedvalue();
	},
	noSertifikat: function noSertifikat() {
		var noSertifikat = new Inputmask('99999');
		var selector = $('.mask-no-sertifikat');
		noSertifikat.mask(selector);
	},
	rtRw: function rtRw() {
		var rtRw = new Inputmask({
			mask: '999',
			placeholder: ''
		});
		var selector = $('.mask-rt-rw');
		rtRw.mask(selector);
	},
	kodepos: function kodepos() {
		var kodepos = new Inputmask('99999');
		var selector = $('.mask-kodepos');
		kodepos.mask(selector);
	},
	numberSmall: function numberSmall() {
		var numberSmall = new Inputmask('numeric', {
			rightAlign: false,
			mask: '9{1,2}'
		});
		var selector = $('.mask-number-small');
		numberSmall.mask(selector);
	},
	numberLong: function numberLong() {
		var number = new Inputmask('9{1,20}');

		var selector = $('.mask-number');
		number.mask(selector);
	},
	numberWithDelimiter: function numberWithDelimiter() {
		var numberWithDelimiter = new Inputmask({
			rightAlign: false,
			groupSeparator: ".",
			alias: "numeric",
			placeholder: "",
			autoGroup: 3,
			digit: 1,
			radixPoint: '',
			digitsOptional: !1,
			clearMaskOnLostFocus: !1
		});
		var selector = $('.mask-number-delimiter');
		numberWithDelimiter.mask(selector);
	},
	noPolisi: function noPolisi() {
		var selector = $('.mask-no-polisi');
		var noPol = new Inputmask({
			alias: 'a{1,2}-9{1,4}-a{1,3}',
			oncomplete: function oncomplete(e) {
				$(this).closest('.form-group').removeClass('has-error').find('.thunder-validation-msg').hide();
			},
			onincomplete: function onincomplete(e) {
				$(this).closest('.form-group').addClass('has-error').find('.thunder-validation-msg').show();
			},
			isValid: function isValid(e) {
				$(this).closest('.form-group').removeClass('has-error').find('.thunder-validation-msg').hide();
			}
		});
		noPol.mask(selector);

		// event change
		selector.on('change', function () {
			$(this).val($(this).val());
		});
	},
	noPolisiNoKode: function noPolisiNoKode() {
		var selector = $('.mask-no-polisi-no-kode');
		var noPol2 = new Inputmask({
			alias: '9{1,4}-a{1,3}',
			oncomplete: function oncomplete(e) {
				$(this).closest('.form-group').removeClass('has-error').find('.thunder-validation-msg').hide();
			},
			onincomplete: function onincomplete(e) {
				$(this).closest('.form-group').addClass('has-error').find('.thunder-validation-msg').show();
			},
			isValid: function isValid(e) {
				$(this).closest('.form-group').removeClass('has-error').find('.thunder-validation-msg').hide();
			}
		});
		noPol2.mask(selector);

		// event change
		selector.on('change', function () {
			$(this).val($(this).val());
		});
	},
	kodeWilayahNoPolisi: function kodeWilayahNoPolisi() {
		var kodeWilayah = new Inputmask('a{1,2}');
		var selector = $('.mask-kode-no-polisi');
		kodeWilayah.mask(selector);
	},
	fullNoPolisi: function fullNoPolisi() {
		var fullNoPolisi = new Inputmask({
			alias: 'a{1,2}-9{1,4}-a{1,3}'
		});
		var selector = $('.mask-full-no-polisi');
		fullNoPolisi.mask(selector);
	},
	vinNumber: function vinNumber() {
		var noPol = new Inputmask('a{1,3}-*{1,6}-*{1,8}');
		var selector = $('.mask-no-rangka');
		noPol.mask(selector);
	},
	machineNumber: function machineNumber() {
		var noMesin = new Inputmask({
			mask: '*{25}',
			placeholder: ''
		});
		var selector = $('.mask-no-mesin');
		noMesin.mask(selector);
	},
	email: function email() {
		var email = new Inputmask({
			regex: "^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+[A-Za-z]+$",
			placeholder: ""
		});
		var selector = $('.mask-email');
		email.mask(selector);
	},
	numberAndDecimal: function numberAndDecimal() {
		var selector = $('.mask-decimal');
		var decimalMin = typeof selector.attr('data-min-value') !== 'undefined' && selector.attr('data-min-value') != '' ? parseInt(selector.attr('data-min-value')) : 0;
		var decimalMax = typeof selector.attr('data-max-value') !== 'undefined' && selector.attr('data-max-value') != '' ? parseInt(selector.attr('data-max-value')) : 0;
		var decimal = new Inputmask('numeric', {
			allowMinus: false,
			rightAlign: false,
			min: decimalMin,
			max: decimalMax
		});
		decimal.mask(selector);
	},
	init: function init() {
		this.money();
		this.moneyRight();
		this.date();
		this.dateTime();
		this.birthDay();
		this.year();
		this.idKTP();
		this.noTelp();
		this.noSertifikat();
		this.rtRw();
		this.kodepos();
		this.numberSmall();
		this.numberLong();
		this.numberWithDelimiter();
		this.noPolisi();
		this.noPolisiNoKode();
		this.fullNoPolisi();
		this.vinNumber();
		this.machineNumber();
		this.kodeWilayahNoPolisi();
		this.yearWithRange();
		this.yearNumber();
		this.email();
		this.numberAndDecimal();
	}
};

/***/ }),

/***/ "./resources/assets/thunderlab/js/ux.js":
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("./resources/assets/thunderlab/js/ux/dynamicSelect.js");
__webpack_require__("./resources/assets/thunderlab/js/ux/noEnter.js");
__webpack_require__("./resources/assets/thunderlab/js/ux/number_format.js");

/**
 * Setfocus 
 */
$('form .set-focus').focus();

/**
 * Clickable row in table
 */
$('.table-click > tbody > tr').on('click', function () {
	if ($(this).attr('href')) {
		window.location = $(this).attr('href');
	}
});
// $('.table a, .table button').on('click', function(e){
// 	e.stopPropagation();
// });
/**
 * ============
 * MODULE MODAL
 * ============
 */
/** 
 * Modal form parsing
 */
$('.modal-form').on('shown.bs.modal', function (e) {
	try {
		var modalID = $(e.relatedTarget).attr('data-target');
		var actionModal = $(e.relatedTarget).attr('data-action');
		var contentModal = $(e.relatedTarget).attr('data-content');

		if (typeof actionModal !== 'undefined' && actionModal !== '') {
			$(modalID).find('form').attr('action', actionModal);
			$(modalID).find('#body-modal').html(contentModal);
			$(modalID).find('form .set-focus').focus();
		}
	} catch (e) {
		console.log(e);
	}
});

/***/ }),

/***/ "./resources/assets/thunderlab/js/ux/dynamicSelect.js":
/***/ (function(module, exports) {

window.dynamicSelect = new function () {
	this.load = function (target, dataSource, dataSelector, callback, isArray) {
		try {
			if (dataSource) {
				var selected = target.attr("data-selected");
				Object.keys(dataSource).forEach(function (key) {
					if (isArray) {
						var tmp = dataSource[key];
						target.append($("<option class='text-capitalize' " + (selected == tmp ? 'selected=selected' : '') + "></option>").attr("value", key).text(tmp));
					} else {
						var tmp = dataSelector && dataSelector == "key" ? key : dataSource[key];
						target.append($("<option class='text-capitalize' " + (selected == tmp ? 'selected=selected' : '') + "></option>").attr("value", dataSelector && dataSelector == "key" ? key : dataSource[key]).text(tmp));
					}
				});
			}
			if (callback) {
				callback();
			}
		} catch (ex) {
			console.log(ex);
		}
	};
	this.clear = function (target, callback) {
		selectClear(target);
		if (callback) {
			callback();
		}
	};
	this.disableNextOnEmpty = function (current, target) {
		if (current.val()) {
			target.removeAttr("disabled");
		} else {
			target.attr("disabled", true);
		}
	};
	var selectClear = function selectClear(target) {
		target.find('option').remove().end().append($("<option selected='' disabled=''></option>").attr("value", "null").text("Pilih")).trigger("change");
	};
}();

/***/ }),

/***/ "./resources/assets/thunderlab/js/ux/noEnter.js":
/***/ (function(module, exports) {

/**
 * Disable submit by pressing enter on form
 */
$('form.no-enter input[type=text], form.no-enter select, form.no-enter input[type=checkbox], form.no-enter input[type=radio]').on('keypress', function (e) {
	if (e.which == 13) {
		e.preventDefault();
	}
});

/***/ }),

/***/ "./resources/assets/thunderlab/js/ux/number_format.js":
/***/ (function(module, exports) {

window.numberFormat = new function () {
	this.set = function (number, decimals, dec_point, thousands_sep) {

		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');

		var n = !isFinite(+number) ? 0 : +number,
		    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		    sep = typeof thousands_sep === 'undefined' ? '.' : thousands_sep,
		    dec = typeof dec_point === 'undefined' ? ',' : dec_point,
		    s = '',
		    toFixedFix = function toFixedFix(n, prec) {
			var k = Math.pow(10, prec);
			return '' + (Math.round(n * k) / k).toFixed(prec);
		};

		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}

		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		return s.join(dec);
	};
}();

/***/ }),

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("./resources/assets/js/app.js");
__webpack_require__("./resources/assets/js/flatpickr.js");
module.exports = __webpack_require__("./resources/assets/sass/app.scss");


/***/ })

/******/ });