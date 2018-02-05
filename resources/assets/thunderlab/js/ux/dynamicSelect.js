window.dynamicSelect = new function () {
	this.load = function (target, dataSource, dataSelector, callback, isArray) {
		try {
			if (dataSource) {
				var selected = target.attr("data-selected");
				Object.keys(dataSource).forEach(function (key) {
					if (isArray) {
						var tmp = dataSource[key];
						target.append($("<option class='text-capitalize' " + (selected == tmp ? 'selected=selected' : '') + "></option>")
							.attr("value", key)
							.text(tmp));
					} else {
						var tmp = dataSelector && dataSelector == "key" ? key : dataSource[key];
						target.append($("<option class='text-capitalize' " + (selected == tmp ? 'selected=selected' : '') + "></option>")
							.attr("value", dataSelector && dataSelector == "key" ? key : dataSource[key])
							.text(tmp));
					}
				});
			}
			if (callback) { callback(); }
		} catch (ex) {
			console.log(ex);
		}
	}
	this.clear = function (target, callback) {
		selectClear(target);
		if (callback) { callback(); }
	}
	this.disableNextOnEmpty = function (current, target) {
		if (current.val()) {
			target.removeAttr("disabled");
		} else {
			target.attr("disabled", true);
		}
	}
	var selectClear = function (target) {
		target.find('option')
			.remove()
			.end()
			.append($("<option selected='' disabled=''></option>")
				.attr("value", "null")
				.text("Pilih"))
			.trigger("change");
	}
}