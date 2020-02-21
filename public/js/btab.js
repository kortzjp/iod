/**
 * btab.js
 * 1.0 Ver.
 */
var btab = function(input, table, case_insensitive) {
	var case_ins = case_insensitive === undefined ? false : case_insensitive ;
	var btab_search = function(val_input, obj_table, case_ins) {
		var regx_input = val_input.toString().trim() !== "" ? vals_input(val_input, case_ins) : false ;
		var tbody_filter = "" ;
		if (regx_input !== false && obj_table.values.html.toString().length > 0) {
			var values  = obj_table.values.tr, colspan = obj_table.values.tr[0].rows.length ;
			var tbody_not_found = "<tr><td colspan='" + colspan + "' style='color:red;text-align:center;'><b>Sin resultados</b></td></tr>" ;
			for (var i = 0; values.length > i; i++) {
				var rows = values[i].rows, found = false;
				for (var j = 0; rows.length > j; j++) {
					if (rows[j].innerText.search(regx_input) > -1) {
						found = true;
						break;
					}
				}
				if (found === true) tbody_filter += "<tr>" + values[i].html + "</tr>";
			}
			btab_html(obj_table.tbody_e, tbody_filter.toString().trim() === "" ? tbody_not_found : tbody_filter);
		} else {
			btab_html(obj_table.tbody_e, obj_table.values.html);
		}
	};
	var btab_html = function(tbody_e, html) {
		tbody_e.innerHTML = html;
	};
	var vals_table = function(tbody_table) {
		var values = [], table_elems = tbody_table.querySelectorAll('tr');
		for (var i = 0; table_elems.length > i; i++) {
			values.push({html:table_elems[i].innerHTML, rows:table_elems[i].querySelectorAll('td')});
		}
		return values;
	};
	var vals_input = function(val_input, case_ins) {
	  var vals = "", vals_array = [], val = "";
	  var reg_case = case_ins === true ? "i" : "" ;
		if (val_input.search(",") > -1) {
			vals_array = filter_array(val_input.split(","));
			for (var i = 0, l_v = vals_array.length; l_v > i; i++) {
				val = vals_array[i].toString().trim();
				if (val !== "") {
					vals += (i + 1 === l_v) ? regx_esc(val) : regx_esc(val) + "|" ;
				}
			}
			return new RegExp(vals.toString(), "g" + reg_case);
		} else {
			val = val_input.toString().trim();
			return new RegExp(val, "g" + reg_case);
		}
	};
	var regx_esc = function (value) {
		return value.replace(/\/|\$|\#|\.|\;|\:|\^|\$|\*|\?|\+|\=/g, "\\$&");
	};
	var filter_array = function(array) {
		var array_return = [];
		for (var i = 0; array.length > i; i++) {
			if (array[i].toString().trim() !== "") array_return.push(array[i]);
		}
		return array_return;
	};
	document.addEventListener('DOMContentLoaded', function() {
		var input_e = document.getElementById(input);
		var table_e = document.getElementById(table);
		var tbody_e = table_e.querySelector('tbody');
		var v_tbody_table = {'tr':vals_table(tbody_e), 'html':tbody_e.innerHTML};
		input_e.addEventListener('keyup', function(e) {
			btab_search(this.value, {tbody_e:tbody_e, values:v_tbody_table}, case_ins);
		});
	}, false);
};
