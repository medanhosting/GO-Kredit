window.formInputMask = {
	money: function () {
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
			clearMaskOnLostFocus: true,
		});
		var selector = $('.mask-money');
		money.mask(selector);
	},
	moneyRight: function () {
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
			clearMaskOnLostFocus: true,
		});
		var selector = $('.mask-money-right');
		moneyRight.mask(selector);
	},
	birthDay: function () {
		var today = new Date();
		var year = today.getFullYear();

		var birthDate = new Inputmask({
			alias: 'dd/mm/yyyy',
			yearrange: {minyear: 1700, maxyear: (year - 10)}
		});
		var selector = $('.mask-birthdate');
		birthDate.mask(selector);
	},
	date: function () {
		var selector = $('.mask-date'), min, max;

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
	dateTime: function () {
		var selector = $('.mask-datetime'), min, max;

		if (selector.attr('mask-datetime-min')) {
			min = selector.attr('mask-datetime-min');
		} else {
			min = '01/01/1800 00:00';
		}

		if (selector.attr('mask-datetime-max')) {
			max = selector.attr('mask-datetime-max');
		} else {
			max = '31/12/9999 23:59';
		}
		var dateTime = new Inputmask('datetime', {
			alias: 'dd/mm/yyyy hh:mm',
			min: min,
			max: max,
		});

		dateTime.mask(selector);
	},
	year: function () {
		var year = new Inputmask({
			mask: "y",
			definitions: {
				y: {
					validator: "(19|20)\\d{2}",
					cardinality: 4,
					prevalidator: [{
						validator: "[12]",
						cardinality: 1,
					}, {
						validator: "(19|20)",
						cardinality: 2,
					}, {
						validator: "(19|20)\\d",
						cardinality: 3,
					}],
				},
			},
		});
		var selector = $('.mask-year');
		year.mask(selector);
	},
	yearWithRange: function () {
		var selector = $('.mask-year-range');
		var rangeYear = ((typeof(selector.attr('data-year-range')) !== 'undefined') && (selector.attr('data-year-range') != '')) ? selector.attr('data-year-range') : 0 ;
		var minYear = ((typeof(selector.attr('data-year-min')) !== 'undefined') && (selector.attr('data-year-min') != '')) ? selector.attr('data-year-min') : 0 ;
		var date = new Date();
		var yearNow = date.getFullYear();
		var yearRange = new Inputmask("numeric", {
			mask: 9999,
			min: (rangeYear !== 0) ? (yearNow - rangeYear) : minYear,
			max: yearNow
		});
		
		yearRange.mask(selector);
	},
	idKTP: function () {
		var idKTP = new Inputmask('99-99-999999-9999');
		var selector = $('.mask-id-card');
		idKTP.mask(selector);
	},
	noTelp: function () {
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
	noSertifikat: function () {
		var noSertifikat = new Inputmask('99999');
		var selector = $('.mask-no-sertifikat');
		noSertifikat.mask(selector);
	},
	rtRw: function () {
		var rtRw = new Inputmask({
			mask: '999',
			placeholder: ''
		});
		var selector = $('.mask-rt-rw');
		rtRw.mask(selector);
	},
	kodepos: function () {
		var kodepos = new Inputmask('99999');
		var selector = $('.mask-kodepos');
		kodepos.mask(selector);
	},
	numberSmall: function () {
		var numberSmall = new Inputmask(
			'numeric', {
				rightAlign: false,
				mask: '9{1,2}',
		});
		var selector = $('.mask-number-small');
		numberSmall.mask(selector);
	},
	numberLong: function () {
		var number = new Inputmask('9{1,20}');

		var selector = $('.mask-number');
		number.mask(selector);
	},
	numberWithDelimiter: function () {
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
	noPolisi: function () {
		var selector = $('.mask-no-polisi');
		var noPol = new Inputmask({
			alias: 'a{1,2}-9{1,4}-a{1,3}',
			oncomplete: function(e) {
				$(this).closest('.form-group')
						.removeClass('has-error')
						.find('.thunder-validation-msg')
						.hide();
			},
			onincomplete: function(e) {
				$(this).closest('.form-group')
						.addClass('has-error')
						.find('.thunder-validation-msg')
						.show();
			},
			isValid: function(e) {
				$(this).closest('.form-group')
						.removeClass('has-error')
						.find('.thunder-validation-msg')
						.hide();	
			}
		});
		noPol.mask(selector);

		// event change
		selector.on('change', function() {
			$(this).val($(this).val())
		});
	},
	noPolisiNoKode: function () {
		var selector = $('.mask-no-polisi-no-kode');
		var noPol2 = new Inputmask({
			alias: '9{1,4}-a{1,3}',
			oncomplete: function(e) {
				$(this).closest('.form-group')
						.removeClass('has-error')
						.find('.thunder-validation-msg')
						.hide();
			},
			onincomplete: function(e) {
				$(this).closest('.form-group')
						.addClass('has-error')
						.find('.thunder-validation-msg')
						.show();
			},
			isValid: function(e) {
				$(this).closest('.form-group')
						.removeClass('has-error')
						.find('.thunder-validation-msg')
						.hide();	
			}
		});
		noPol2.mask(selector);

		// event change
		selector.on('change', function() {
			$(this).val($(this).val())
		});
	},
	kodeWilayahNoPolisi: function () {
		var kodeWilayah = new Inputmask('a{1,2}');
		var selector = $('.mask-kode-no-polisi');
		kodeWilayah.mask(selector);
	},
	vinNumber: function () {
	    var noPol = new Inputmask('a{1,3}-*{1,6}-*{1,8}');
	    var selector = $('.mask-no-rangka');
	    noPol.mask(selector);
	},
	machineNumber: function () {
		var noMesin = new Inputmask({
			mask: '*{25}',
			placeholder: '',
		});
		var selector = $('.mask-no-mesin');
		noMesin.mask(selector);
	},
	email: function () {
		var email = new Inputmask({
			regex: "^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+[A-Za-z]+$",
		    placeholder: ""
		});
		var selector = $('.mask-email');
		email.mask(selector);
	},
	numberAndDecimal: function () {
		var selector = $('.mask-decimal');
		var decimalMin = ((typeof(selector.attr('data-min-value')) !== 'undefined') && (selector.attr('data-min-value') != '')) ? parseInt(selector.attr('data-min-value')) : 0 ;
		var decimalMax = ((typeof(selector.attr('data-max-value')) !== 'undefined') && (selector.attr('data-max-value') != '')) ? parseInt(selector.attr('data-max-value')) : 0 ;
		var decimal = new Inputmask('numeric', {
			allowMinus: false,
			rightAlign: false,
			min: decimalMin,
			max: decimalMax
		});
		decimal.mask(selector);
	},
	init: function () {
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
		this.vinNumber();
		this.machineNumber();
		this.kodeWilayahNoPolisi();
		this.yearWithRange();
		this.email();
		this.numberAndDecimal();
	}
}