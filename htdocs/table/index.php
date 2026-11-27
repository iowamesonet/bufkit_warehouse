<html>
<title>
Point-Based Model Forecast Table Viewer
</title>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1">
<script src='jquery.min.js'></script>
<script src='bootstrap.min.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js"></script>
<script src='ol.js'></script>
<script src='FileSaver.min.js'></script>
<script src='https://www.spc.noaa.gov/exper/sref/srefplumes/scripts/biglist.js'></script>
<script src='https://www.spc.noaa.gov/exper/sref/srefplumes/scripts/wfolist.js'></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css">
<link rel='stylesheet' href='bootstrap.min.css' />
<link rel='stylesheet' href='ol.css' />

<style>

.data{
    width:200px;
}

</style>


<script type="text/javascript">

var tempLast;
var rhLast;
var windLast;
var modelLast;
var bufVarLast;
var siteLast;
var sidLast;
var viewOptionLast;
var a;
var mLast;

var srefVars = {
	'tf':'TMP',
	'td':'DWP',
	'rh':'2mRH%',
	'wspd':'10mWND',
	'buf_snow_sr_rate':'SNO'
};

var allVars = ['tf','td','rh','wspd','mom_wind_mean','mom_wind_max','buf_snow_sr_rate','buf_snow_maxt_rate','frz_rain_rate','sleet_rate','fire','winter'];
var allVarNames = ['Temperature (F)','Dewpoint (F)','Relative Humidity (%)','Wind Speed (mph)','Mean Momentum Transfer Wind Gust (mph)','Max Momentum Transfer Wind Gust (mph)','Hourly Snow Rate (11:1 Ratio)','Hourly Snow Rate (Max-T Method)','Hourly Freezing Rain Rate','Hourly Sleet Rate','Fire Weather','Winter Weather'];

var allSrefVars = ['tf','td','rh','wspd','buf_snow_sr_rate'];
var allSrefVarNames = ['Mean Temperature (F)','Mean Dewpoint (F)','Mean Relative Humidity (%)','Mean Wind Speed (mph)','Mean Hourly Snow Rate (SLR = 1000/[100 + 6T])'];

function updateTable(){
	var site = document.getElementById('site').value;
	var bufVar = document.getElementById('bufVar').value;
	var viewOption = document.getElementById('viewOption').value;
	var condition = document.getElementById('condition').value;
	var threshold = document.getElementById('threshold').value;
	if(threshold != 'obs'){
		threshold = parseFloat(threshold);
	}
	console.log('threshold: ' + threshold);
	var rh;
	var temp;
	var wind;
	if(bufVar == 'fire'){
		rh = document.getElementById('rh').value;
		temp = document.getElementById('temp').value;
		wind = document.getElementById('wind').value;
	}
	if(viewOption == 'single'){
		var model = document.getElementById('model').value;
		if(model != modelLast || bufVar != bufVarLast || site != siteLast || viewOption != viewOptionLast || rh != rhLast || temp != tempLast || wind != windLast){
			a = getSingleData(model,bufVar,site);
		}
	}
	else{
		var model = '';
		if(model != modelLast || bufVar != bufVarLast || site != siteLast || viewOption != viewOptionLast || rh != rhLast || temp != tempLast || wind != windLast){
			a = getAllData(bufVar,site);
		}
	}
	var keys = a[0].sort();
	keys.reverse();
	var times = a[1].sort();
	var d = a[2];
	var o = a[3];
	var c = a[4];

	modelLast = model;
	bufVarLast = bufVar;
	siteLast = site;
	rhLast = rh;
	tempLast = temp;
	windLast = wind;
	sidLast = 'Site: ' + siteLast.toUpperCase();
	viewOptionLast = viewOption;

	var days = [];
	var spans = [];
	var i = -1;
	var table1 = '';
	var table2 = '';
	var table3 = '';
	var table4 = '';
	if(Object.keys(d).length == 0){
		table1 += 'Site ' + site + ' not found.';
		document.getElementById('sideTable').innerHTML = table1;
	        document.getElementById('mainTable').innerHTML = table2;
		map.updateSize();
		return;
	}
	table1 += '<table border="1" bgcolor="#FFFFFF">';
	table2 += '<table border="1">';
	table3 += '<table border="1">';
	table4 += '<table border="1">';
	if(Object.keys(o).length > 0){
		table1 += '<tr><td rowspan="3" colspan="1" bgcolor="#FFFFFF"></td>';
	}
	else{
		table1 += '<tr><td rowspan="2" colspan="1" bgcolor="#FFFFFF"></td>';
	}
	table2 += '<tr>';

	for(var t in times){
		i += 1;
		var da = new Date(times[t]);
                var day = da.getDate();
		if(i == 0){
			days.push(day);
		}
                else if(days.indexOf(day) == -1){
			days.push(day);
			spans.push(i);
			i = 0;
		}
	}
	spans.push(i+1);

	i = 0;
	days = [];
	for(var t=0;t<times.length;t++){
		var da = new Date(times[t]);
		var da2 = new Date(times[t+1]);
		var day = da.getDate();
		if(days.indexOf(day) == -1){
			days.push(day);
			//var s = getColSpan(da.getHours(),da2.getHours(),model);
			table2 += '<td bgcolor="#FFFFFF" style="font-weight:bold; font-size:22px; white-space: nowrap;" colspan="' + spans[i] + '" align="center">' + getDay(da.getDay()) + ' ' + (da.getMonth()+1) + '/' + day + '</td>';
			i += 1;
		}
	}

        table2 += '</tr><tr>';
        for(var t in times){
		da = new Date(times[t]);
		table2 += '<td bgcolor="#FFFFFF" style="font-weight:bold; font-size:22px;" align="center" onClick="labelPoint(\'' + pad(da.getHours()) + 'z\');">' + pad(da.getHours()) + 'z</td>';
	}
	table1 += '<td bgcolor="#FFFFFF" style="font-weight:bold; font-size:22px;" align="center">MM/DD</td></tr>';
	table1 += '<tr><td bgcolor="#FFFFFF" style="font-weight:bold; font-size:22px;" align="center">HHz</td></tr>';
	if(Object.keys(o).length > 0){
		table1 += '<tr><td bgcolor="#FFFFFF" style="font-weight:bold;" align="center">Obs</td></tr>';
	}
	table4 += '</table>';
	table2 += '</tr>';

	var vMax = -99999999;
	var vMin = 999999999;

	if(Object.keys(o).length > 0){
		table2 += '<tr>';
		for(var t in times){
    	    if(o.hasOwnProperty(times[t])){
    	        if(!isNaN(threshold) && threshold != 'obs'){
					if(condition == 'greaterThan' && parseFloat(o[times[t]]) > threshold){
						table2 += '<td align="center" bgcolor="' + color + '" onClick="labelPoint(' + o[times[t]] + ')">' + o[times[t]] + '</td>';
	                }
	                else if(condition == 'lessThan' && parseFloat(o[times[t]]) < threshold){
	                    table2 += '<td align="center" bgcolor="' + color + '" onClick="labelPoint(' + o[times[t]] + ')">' + o[times[t]] + '</td>';
	                }
	                else if(condition == 'equalTo' && parseFloat(o[times[t]]) == threshold){
	                    table2 += '<td align="center" bgcolor="' + color + '" onClick="labelPoint(' + o[times[t]] + ')">' + o[times[t]] + '</td>';
	                }
	                else{
	                    table2 += '<td align="center" onClick="labelPoint(' + o[times[t]] + ')">' + o[times[t]] + '</td>';
					}
				}
				else{
					table2 += '<td align="center" onClick="labelPoint(' + o[times[t]] + ')">' + o[times[t]] + '</td>';
	                	}
			}
	                else{
	                        table2 += '<td></td>';
	                }
	        }
	        table2 += '</tr>';
	}

	var days = [];
	for(var i in keys){
		if(viewOption == 'single'){
			init = keys[i];
			init2 = keys[i+1];	
		}
		else{
			init = keys[i].split('|')[0];
			try{
				init2 = keys[i+1].split('|')[1];
			}catch(err){init2 = init;}
		}
		table1 += '<tr>';
		table2 += '<tr>';
		console.log('init: ' + init);
		var da = new Date(init);
                var da2 = new Date(init2);
		var day = da.getDate();
		if(days.indexOf(day) == -1){
			days.push(day);
			if(viewOption == 'single'){
				var s = getRowSpan(da.getHours(),da2.getHours(),model);
			}
			else{
				var s = getRowSpanAll(day,keys,model);
			}
			table1 += '<td bgcolor="#FFFFFF" style="font-weight:bold;" rowspan="' + s + ' align="center"">' + (da.getMonth()+1) + '/' + day + '</td>';
		}
		if(viewOption == 'single'){
			table1 += '<td bgcolor="#FFFFFF" style="font-weight:bold;" align="center">' + pad(da.getHours()) + 'z</td>';
		}
		else{
			table1 += '<td bgcolor="#FFFFFF" style="font-weight:bold;" align="center">' + keys[i].split('|')[1].toUpperCase() + ' ' + pad(da.getHours()) + 'z</td>';
		}
		for(var t in times){
			if(d[keys[i]].hasOwnProperty(times[t])){
				var thresh = threshold;
                                if(threshold == 'obs'){
                                        thresh = o[times[t]];
                                }
				if(bufVar == 'winter'){
					table2 += '<td align="center" bgcolor="' + c[keys[i]][times[t]] + '" onClick="labelPoint(' + d[keys[i]][times[t]] + ')">' + d[keys[i]][times[t]] + '</td>';
				}
				else if(!isNaN(thresh)){
					console.log(d[keys[i]][times[t]] + ',' + thresh);
					if(condition == 'greaterThan' && parseFloat(d[keys[i]][times[t]]) > thresh){
						table2 += '<td align="center" bgcolor="' + color + '" onClick="labelPoint(' + d[keys[i]][times[t]] + ')">' + d[keys[i]][times[t]] + '</td>';
					}
					else if(condition == 'lessThan' && parseFloat(d[keys[i]][times[t]]) < thresh){
                                                table2 += '<td align="center" bgcolor="' + color + '" onClick="labelPoint(' + d[keys[i]][times[t]] + ')">' + d[keys[i]][times[t]] + '</td>';
                                        }
					else if(condition == 'equalTo' && parseFloat(d[keys[i]][times[t]]) == thresh){
                                                table2 += '<td align="center" bgcolor="' + color + '" onClick="labelPoint(' + d[keys[i]][times[t]] + ')">' + d[keys[i]][times[t]] + '</td>';
                                        }
					else{
						table2 += '<td align="center" onClick="labelPoint(' + d[keys[i]][times[t]] + ')">' + d[keys[i]][times[t]] + '</td>';
					}
				}
				else{
					table2 += '<td align="center" onClick="labelPoint(' + d[keys[i]][times[t]] + ')">' + d[keys[i]][times[t]] + '</td>';
				}
				if(parseFloat(d[keys[i]][times[t]]) > vMax){
					vMax = parseFloat(d[keys[i]][times[t]]);
				}
				if(parseFloat(d[keys[i]][times[t]]) < vMin){
					vMin = parseFloat(d[keys[i]][times[t]]);
				}
			}
			else{
				table2 += '<td></td>';
			}
		}
		table1 += '</tr>';
		table2 += '</tr>';
	}
	table1 += '</table>';
	table2 += '</table>';
	document.getElementById('sideTable').innerHTML = table1;
	document.getElementById('mainTable').innerHTML = table2;
	//document.getElementById('timeTable').innerHTML = table3;
        //document.getElementById('emptyTable').innerHTML = table4;
	console.log(vMin + ',' + vMax);
	var factor = 10;
	if(bufVar == 'frz_rain_rate' || bufVar == 'sleet_rate'){
		factor = 100;
	}
	document.getElementById('threshSlider').min = vMin*factor;
	document.getElementById('threshSlider').max = vMax*factor;
	map.updateSize();
}

function getAllData(v,s){
    var allTimes = [];
    var allKeys = [];
    var d = {};
	var c = {};
	var models = ['hrrr','rap','rrfs','gfs','gfsm'];
    var models2 = ['hrrr','rap','rrfs','gfs','gfs'];
	for(var j=0;j<4;j++){
		var m = models[j];
		var url = "../data/parser.php?model=" + m + "&site=" + s;
                var dataRequest = new XMLHttpRequest();
                dataRequest.open('GET', url, false);
                dataRequest.send();
                var lines = dataRequest.responseText.split("\n");
		if(lines[0].includes('Try again')){
                        console.log('site not available');
                        return [allKeys,allTimes,d,{},{}];
                }
                var vars = lines[0].split("\t");
                if(v == 'fire'){
			var idx1 = 60;
			var idx2 = 68;
			var idx3 = 58;
			var wThresh = parseFloat(document.getElementById('wind').value);
			var rThresh = parseFloat(document.getElementById('rh').value);
			var tThresh = parseFloat(document.getElementById('temp').value);
		}
		else if(v == 'winter'){
                        var idx1 = 63;
                        var idx2 = 71;
                        var idx3 = 72;
                        var idx4 = 69;
                }
		else{
			var idx = -1;
                	for(var i=0;i<vars.length;i++){
                	        if(vars[i] == v){
                	                idx = i;
                	                break;
                	        }
                	}
		}

                for(var i=1;i<lines.length;i++){
                        var vars = lines[i].split("\t");
                        if(i == 1){
				console.log(vars.length);
                                var init = vars[vars.length - 1] + '|' + models2[j];
                                d[init] = {};
				c[init] = {};
                                allKeys.push(init);
                        }
                        if(vars[1] == undefined){
                                continue;
                        }
			if(v == 'fire'){
				if(parseFloat(vars[idx1]) >= wThresh && parseFloat(vars[idx2]) < rThresh && parseFloat(vars[idx3]) >= tThresh){
					d[init][vars[1]] = 1;
				}
				else{
					d[init][vars[1]] = 0;
				}
			}
                        else if(v == 'winter'){
                                if(vars[idx1] > 0){
                                        d[init][vars[1]] = vars[idx1];
                                        c[init][vars[1]] = '#00cc00';
                                }
                                else{
                                        d[init][vars[1]] = '&nbsp;';
                                        c[init][vars[1]] = '#ffffff';
                                }
                                if(vars[idx2] > 0){
                                        d[init][vars[1]] = vars[idx2];
                                        c[init][vars[1]] = '#ff0000';
                                }
                                if(vars[idx3] > 0){
                                        d[init][vars[1]] = vars[idx3];
                                        c[init][vars[1]] = '#ff9900';
                                }
                                if(vars[idx4] > 0){
                                        d[init][vars[1]] = vars[idx4];
                                        c[init][vars[1]] = '#00ffff';
                                }
                        }
			else if(vars[idx] == 0){
                                d[init][vars[1]] = '&nbsp;';
                        }
			else{
	                        d[init][vars[1]] = vars[idx];
			}
                        if(allTimes.indexOf(vars[1]) == -1){
                                allTimes.push(vars[1]);
                        }
                }
        }

	var obs = getObs(s,v,allTimes);

        return [allKeys,allTimes,d,obs,c];
}

function getSREFData(v,s){
        var allTimes = [];
        var allKeys = [];
        var d = {};
	var c = {};
	if(s.length == 3){
		var srefSite = s.toUpperCase();
	}
	else{
		var srefSite = s.substring(1,4).toUpperCase();
	}
	if(Object.keys(srefVars).indexOf(v) == -1){
		console.log('variable not found');
		return [allKeys,allTimes,d,{},{}];
	}
	else if(srefSites.indexOf(srefSite) == -1){
		console.log('site not found ' + srefSite);
		return [allKeys,allTimes,d,{},{}];
	}
        var iter = 5;

	for(var j=0;j<iter;j++){
		if(j == 0){
			var now = new Date();
		}
		else{
			now = new Date(now.getTime() - (3600 * 24 * 1000));
		}
		var da = String(now.getFullYear()) + String(pad(now.getMonth() + 1)) + String(pad(now.getDate()));
		var hours = ['21','15','09','03'];
		for(var k=0;k<hours.length;k++){
			var url = 'srefdata.php?s=' + srefSite + '&hr=' + hours[k] + '&v=' + srefVars[v] + '&d=' + da;
			console.log(url);
			var dataRequest = new XMLHttpRequest();
	                dataRequest.open('GET', url, false);
	                dataRequest.send();
	                var line = dataRequest.responseText;
			//console.log(line);
			try{
				var js = JSON.parse(line);
			}
			catch(err){
				console.log('Could not parse json, likely means data has not arrived, continuing...');
				continue;
			}
			console.log(js);
			var key = "MN" + hours[k];
			if(!js.hasOwnProperty(key)){
				console.log('Could not locate ' + key + ' in json object, continuing...');
				continue;
			}
			else{
				console.log('Found ' + key);
			}
			var tm = js[key]["data"][0][0] - (10800000);
			var dm = new Date(tm);
                        var tStamp = String(dm.getUTCFullYear()) + '-' + String(pad(dm.getUTCMonth() + 1)) + '-' + String(pad(dm.getUTCDate())) + ' ' + String(pad(dm.getUTCHours())) + ':' + String(pad(dm.getUTCMinutes())) + ':' + String(pad(dm.getUTCSeconds()));
	                var init = tStamp;
	                d[init] = {};
	                c[init] = {};
	                allKeys.push(init);
                        var initVal = init;
			console.log('Init: ' + init + ', ' + new Date(js[key]["data"][0][0]));
			for(var l=0;l<js[key]["data"].length;l++){
				var tm = js[key]["data"][l][0];
	                        var dm = new Date(tm);
				var tStamp = String(dm.getUTCFullYear()) + '-' + String(pad(dm.getUTCMonth() + 1)) + '-' + String(pad(dm.getUTCDate())) + ' ' + String(pad(dm.getUTCHours())) + ':' + String(pad(dm.getUTCMinutes())) + ':' + String(pad(dm.getUTCSeconds()));
                                var dv = parseFloat(js[key]["data"][l][1]);
				if(v == 'wspd'){
					dv = dv * 1.15078;
				}
				else if(v == 'buf_snow_sr_rate'){
					dv = dv / 3;
				}
				dv = dv.toFixed(2);
				if(dv == 0){
		                        d[init][tStamp] = '&nbsp;';
		                }
	                        else{
	                                d[init][tStamp] = dv;
	                        }
	                        if(allTimes.indexOf(tStamp) == -1){
	                                allTimes.push(tStamp);
				}
			}
			if(Object.keys(d).length == 15){
				break;
			}
		}
		if(Object.keys(d).length == 15){
                        break;
                }
	}

        var obs = getObs(s,v,allTimes);

        return [allKeys,allTimes,d,obs,c];
}

function getSingleData(m,v,s){
	if(m == 'sref'){
		return getSREFData(v,s);
	}
	var allTimes = [];
	var allKeys = [];
	var d = {};
	var c = {};
	var iter = 22;
	if(m == 'rrfs'){
		var iter = 15;
	}
	else if(m == 'nam4km'){
		var iter = 11;
	}
	else if(m == 'gfs'){
		var iter = 31;
	}
	for(var j=0;j<iter;j++){
		if(j == 0){
			var url = "../data/parser.php?model=" + m + "&site=" + s; 
		}
		else if(j == 1 && m == 'nam'){
			var url = "../data/parser.php?model=namm&site=" + s;
		}
                else if(j == 1 && m == 'gfs'){
                        var url = "../data/parser.php?model=gfsm&site=" + s;
                }
		else{
			//console.log(init);
			var now = new Date(initVal);
			if(m == 'rap' || m == 'hrrr'){
				var secs = now.valueOf() - (3600 * 1000);
			}
			else{
				var secs = now.valueOf() - (6 * 3600 * 1000);
			}
			var b = new Date(secs);
			console.log(b);
			var mod = m;
			if(b.getHours() == 6 || b.getHours() == 18){
				if(m == 'nam'){
					mod = 'namm';
				}
				else if(m == 'gfs'){
					mod = 'gfsm';
				}
			}
			var da = String(b.getFullYear()) + String(pad(b.getMonth() + 1)) + String(pad(b.getDate())) + String(pad(b.getHours()));
			var url = "../data/parser.php?model=" + mod + "&site=" + s + '&date=' + da;
		}
		var dataRequest = new XMLHttpRequest();
		dataRequest.open('GET', url, false);
		dataRequest.send();
		var lines = dataRequest.responseText.split("\n");
		if(lines[0].includes('Try again')){
			console.log('site not available');
			return [allKeys,allTimes,d,{},{}];
		}
		var vars = lines[0].split("\t");
		if(v == 'fire'){
                        var idx1 = 60;
                        var idx2 = 68;
                        var idx3 = 58;
                        var wThresh = parseFloat(document.getElementById('wind').value);
                        var rThresh = parseFloat(document.getElementById('rh').value);
                        var tThresh = parseFloat(document.getElementById('temp').value);
                }
		else if(v == 'winter'){
                        var idx1 = 63;
                        var idx2 = 71;
                        var idx3 = 72;
			var idx4 = 69;
		}
		else{
			var idx = -1;
			for(var i=0;i<vars.length;i++){
				if(vars[i] == v){
					idx = i;
					break;
				}
			}
		}
		for(var i=1;i<lines.length;i++){
			var vars = lines[i].split("\t");
			if(i == 1){
				var init = vars[vars.length-1];
				console.log(init);
				d[init] = {};
				c[init] = {};
				allKeys.push(init);
				if(m != 'rap' && m != 'hrrr' && m != 'rrfs' && j == 1){
					var now1 = new Date(initVal);
					var now2 = new Date(init);
					if(now1 > now2){
						initVal = init;
					}
				}
				else{
					var initVal = init;
				}
			}
			if(vars[1] == undefined){
				continue;
			}
			if(v == 'fire'){
                                if(parseFloat(vars[idx1]) >= wThresh && parseFloat(vars[idx2]) < rThresh && parseFloat(vars[idx3]) >= tThresh){
                                        d[init][vars[1]] = 1;
                                }
                                else{
                                        d[init][vars[1]] = 0;
                                }
                        }
			else if(v == 'winter'){
				if(vars[idx1] > 0){
					d[init][vars[1]] = vars[idx1];
					c[init][vars[1]] = '#00cc00';
				}
				else{
					d[init][vars[1]] = '&nbsp;';
					c[init][vars[1]] = '#ffffff';
				}
				if(vars[idx2] > 0){
					d[init][vars[1]] = vars[idx2];
					c[init][vars[1]] = '#ff0000';
				}
				if(vars[idx3] > 0){
                                        d[init][vars[1]] = vars[idx3];
					c[init][vars[1]] = '#ff9900';
                                }
				if(vars[idx4] > 0){
                                        d[init][vars[1]] = vars[idx4];
					c[init][vars[1]] = '#00ffff';
                                }
			}
                        else if(vars[idx] == 0){
                                d[init][vars[1]] = '&nbsp;';
                        }
                        else{
                                d[init][vars[1]] = vars[idx];
                        }
			if(allTimes.indexOf(vars[1]) == -1){
				allTimes.push(vars[1]);
			}
		}
	}

	var obs = getObs(s,v,allTimes);

	return [allKeys,allTimes,d,obs,c];
}

function getObs(s,v,t){
	var vs = {'tf':2,'td':3};
	var obs = {};
	var url = "checkSite.php?site=" + s;
	var dataRequest = new XMLHttpRequest();
        dataRequest.open('GET', url, false);
        dataRequest.send();
        var coords = dataRequest.responseText.split(",");
	if(coords[0] == 0 || Object.keys(vs).indexOf(v) == -1){
		return obs;
	}
	var idx = vs[v];
	var times = t.sort();
	var d = new Date(t[0]);
	url = 'https://mesonet.agron.iastate.edu/request/asos/csv.php?lat=' + coords[1] + '&lon=' + coords[0] + '&date=' + String(d.getFullYear()) + String(pad(d.getMonth() + 1)) + String(pad(d.getDate()));
	var dataRequest = new XMLHttpRequest();
        dataRequest.open('GET', url, false);
        dataRequest.send();
        var lines = dataRequest.responseText.split("\n");
	for(var i=1;i<lines.length-1;i++){
		var s = lines[i].split(',');
		var da = new Date(s[1].slice(0,19));
		//console.log(da + ',' + s[1].slice(0,19));
		if(da.getMinutes() < 45){
			continue;
		}
		var b = new Date(da.getTime() + 3600000);
		var obTime = String(b.getFullYear()) + '-' + String(pad(b.getMonth() + 1)) + '-' + String(pad(b.getDate())) + ' ' + String(pad(b.getHours())) + ':00:00';
		if(times.indexOf(obTime) != -1){
			obs[obTime] = s[idx];
		}
	}
        var d2 = new Date(t[t.length-1]);
        if(d.getFullYear() != d2.getFullYear()){
	        url = 'https://mesonet.agron.iastate.edu/request/asos/csv.php?lat=' + coords[1] + '&lon=' + coords[0] + '&date=' + String(d2.getFullYear()) + '0101';
	        var dataRequest = new XMLHttpRequest();
	        dataRequest.open('GET', url, false);
	        dataRequest.send();
	        var lines = dataRequest.responseText.split("\n");
	        for(var i=1;i<lines.length-1;i++){
	                var s = lines[i].split(',');
	                var da = new Date(s[1].slice(0,19));
	                //console.log(da + ',' + s[1].slice(0,19));
	                if(da.getMinutes() < 45){
	                        continue;
	                }
	                var b = new Date(da.getTime() + 3600000);
	                var obTime = String(b.getFullYear()) + '-' + String(pad(b.getMonth() + 1)) + '-' + String(pad(b.getDate())) + ' ' + String(pad(b.getHours())) + ':00:00';
	                if(times.indexOf(obTime) != -1){
	                        obs[obTime] = s[idx];
	                }
	        }
        }
	return obs;
}

function getColSpan(h,n,m){
	var s = 4;
	if(h == 6){
		s = 3;
	}
	else if(h == 12){
		s = 2;
	}
	else if(h == 18){
		s = 1;
	}
	var f = 6;
	if(m == 'gfs' && (n-h) == 3){
		f = 2;
	}
	if(m == 'rap' && h != 0 || m == 'hrrr' && h != 0){
		f = 1;
		s = 24-h;
	}
	return (s*f);
}

function getRowSpanAll(day,keys){
        var s = 0;
	for(var i=0;i<keys.length;i++){
		var d = new Date(keys[i].split('|')[0]);
		if(d.getDate() != day){
			continue;
		}
		s += 1;
	}
        return s;
}

function getRowSpan(h,n,m){
        var s = 1;
	if(m == 'sref'){
		if(h == 9){
			s = 2;
		}
		else if(h == 15){
			s = 3;
		}
		else if(h == 21){
			s = 4;
		}
		return s;
	}
        if(h == 6){
                s = 2;
        }
        else if(h == 12){
                s = 3;
        }
        else if(h == 18){
                s = 4;
        }
	if(m == 'rap' && h != 0 || m == 'hrrr' && h != 0){
                s = h+1;
        }
        return s;
}

function getDay(n){
	if(n == 0){
		return 'Sun';
	}
	else if(n == 1){
		return 'Mon';
	}
        else if(n == 2){
                return 'Tue';
        }
        else if(n == 3){
                return 'Wed';
        }
        else if(n == 4){
                return 'Thu';
        }
        else if(n == 5){
                return 'Fri';
        }
        else if(n == 6){
                return 'Sat';
        }
}

function checkFire(){
        var bufVar = document.getElementById('bufVar').value;
        var innerHTML  = '';
        if(bufVar == 'fire'){
                innerHTML = '&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Wind</u>:</b>';
                innerHTML += '<select id="wind" onChange="">';
                innerHTML += '<option value="10">10 mph</option>';
                innerHTML += '<option value="15">15 mph</option>';
                innerHTML += '<option value="20">20 mph</option>';
                innerHTML += '<option value="25">25 mph</option>';
		innerHTML += '<option value="30">30 mph</option>';
                innerHTML += '</select>';
		innerHTML += '<b><u>RH</u>:</b>';
                innerHTML += '<select id="rh" onChange="">';
                innerHTML += '<option value="10">10%</option>';
                innerHTML += '<option value="15">15%</option>';
                innerHTML += '<option value="20">20%</option>';
                innerHTML += '<option value="25">25%</option>';
                innerHTML += '<option value="30">30%</option>';
                innerHTML += '<option value="35">35%</option>';
                innerHTML += '</select>';
                innerHTML += '<b><u>T</u>:</b>';
                innerHTML += '<select id="temp" onChange="">';
                innerHTML += '<option value="45">45 F</option>';
                innerHTML += '<option value="50">50 F</option>';
                innerHTML += '<option value="55">55 F</option>';
                innerHTML += '<option value="60">60 F</option>';
                innerHTML += '<option value="70">70 F</option>';
                innerHTML += '</select>';
        }
        document.getElementById('showFire').innerHTML = innerHTML;
}

function showModels(){
	var viewOption = document.getElementById('viewOption').value;
	var innerHTML  = '';
	if(viewOption == 'single'){
		innerHTML = '&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Model</u>:</b>';
		innerHTML += '<select id="model" onChange="showModelVars(); checkFire();">';
		innerHTML += '<option value="hrrr">HRRR</option>';
		innerHTML += '<option value="rap">RAP</option>';
		innerHTML += '<option value="rrfs">RRFS</option>';
		innerHTML += '<option value="gfs">GFS</option>';
		innerHTML += '</select>';
	}
	document.getElementById('showModels').innerHTML = innerHTML;
	showModelVars();
}

function showModelVars(){
	var m = document.getElementById('model').value;
	if(mLast != 'sref' && m != 'sref' && mLast != undefined){
		return;
	}
	var innerHTML = '<select id="bufVar" onChange="checkFire();">';
        if(m == 'sref'){
		for(var i=0;i<allSrefVars.length;i++){
	                innerHTML += '<option value="' + allSrefVars[i] + '">' + allSrefVarNames[i] + '</option>';
		}
        }
	else{
		for(var i=0;i<allVars.length;i++){
                        innerHTML += '<option value="' + allVars[i] + '">' + allVarNames[i] + '</option>';
                }

	}
	innerHTML += '</select>';
        document.getElementById('showModelVars').innerHTML = innerHTML;
	mLast = m;
}

function labelPoint(t){
	var f = select.getFeatures();
	try{
		var s = f.a[0].N.sid;
	}
	catch(e){
		var s = sidLast;	
		//select.getFeatures().push(f2)
	}
	var vs = vectorSource.getFeatures();
	for(var i=0;i<vs.length;i++){
		if(vs[i].N.sid == s){
			vs[i].N.disc = String(t);
			break;
		}
	}
	vectorSource.refresh();
	select.getFeatures().clear();
	map.updateSize();
}

function saveMap(){
	map.once('postcompose', function(event) {
          var canvas = event.context.canvas;
          if (navigator.msSaveBlob) {
            navigator.msSaveBlob(canvas.msToBlob(), 'map.png');
          } else {
            canvas.toBlob(function(blob) {
              saveAs(blob, 'map.png');
            });
          }
        });
        map.renderSync();
}

function pad(val){
	if(val < 10){
		return '0' + val;
	}
	else{
		return val;
	}
}

srefSites = [];
for(var i=0;i<BigList.length;i++){
    srefSites.push(BigList[i][2]);
}
for(var i=0;i<WFOList.length;i++){
    srefSites.push(WFOList[i][2]);
}
console.log(srefSites);

</script>
<style>

td { 
    padding: 3px;
}

.slidecontainer {
    width: 100px;
    display:inline-block;
}

.slider {
    -webkit-appearance: none;
    appearance: none;
    width: 100px;
    height: 15px;
    background: #d3d3d3;
    outline: none;
    opacity: 0.7;
    -webkit-transition: .2s;
    transition: opacity .2s;
}

.slider:hover {
    opacity: 1;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 15px;
    height: 20px;
    background: #000000;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    width: 15px;
    height: 20px;
    background: #000000;
    cursor: pointer;
}

</style>
</head>
<body onLoad="showModels();">
<div style="position: fixed; top: 0; left: 0; z-index:3; width:100%; border-style: none;">
<table cellpadding="0" cellspacing="0" border="1" bgcolor="#FFFFFF" width="100%">
<tr>
<td bgcolor="#FFFFFF">

<b><u>Site</u>:</b>
<input type="text" id="site" size="4" maxlength="4" value="oun" onSubmit="$('#load').click();" />
&nbsp;&nbsp;&nbsp;&nbsp;
<b><u>Comparison</u>:</b>
<select id="viewOption" onChange="showModels();">
<option value="single">Single Model</option>
<option value="latest">Latest Models</option>
</select>
<span id="showModels"></span>
&nbsp;&nbsp;&nbsp;&nbsp;
<b><u>Variable</u>:</b>
<span id="showModelVars"></span>
<?php

$ratio = 11;

$vars_available = array('stn','date','pmsl','pres','sktc','stc1','snfl','wtns','p01m','c01m','stc2','lcld','mcld','hcld','snra','uwnd','vwnd','r01m','bfgr','t2ms','q2ms','wxts','wxtp','wxtz','wxtr','ustm','vstm','hlcy','sllh','wsym','cdbp','vsbk','td2m','evap','p03m','c03m','swem','s03m','show','lift','swet','kinx','lclp','pwat','totl','cape','lclt','cins','eqlv','lfct','brch','buf_snow_sr','buf_snow_maxt','snra_constant','snra_maxt','maxt','mom_wind_mean','mom_wind_max','tf','td','wspd','wdir','hiwc','qpf','qpf_accum','wagl','frz_rain','sleet','rh','buf_snow_sr_rate','buf_snow_maxt_rate');


$titles = array('stn','date','Mean Sea Level Pressure','Surface Pressure','sktc','stc1','snfl','wtns','1-Hour QPF','c01m','stc2','lcld','mcld','hcld','snra','U-Wind','V_Wind','r01m','bfgr','Temperature','q2ms','wxts','wxtp','Freezing Rain Category','wxtr','ustm','vstm','0-3 km Helicity','sllh','wsym','cdbp','vsbk','Dewpoint','evap','3-Hour QPF','c03m','swem','s03m','show','lift','swet','kinx','lclp','pwat','totl','CAPE','lclt','cins','eqlv','lfct','brch','Snowfall','Snowfall','Constant Snow Ratio','Max-T in Profile Snow Ratio','Max Temp in Profile','Wind Gust','Wind Gust','Temperature','Dewpoint','Wind Speed','Wind Direction','Apparent Temperature','Precip','Precip Accumulation','... m AGL Wind Speed','Freezing Rain Accumulation','Sleet Accumulation','Relative Humidity','Snow Rate ('.$ratio.':1 Ratio)','Snow Rate (Max-T Method)');

$vars_available = array('tf','td','rh','wspd','mom_wind_mean','mom_wind_max','buf_snow_sr_rate','buf_snow_maxt_rate','frz_rain_rate','sleet_rate','fire','winter');

$titles = array('Temperature (F)','Dewpoint (F)','Relative Humidity (%)','Wind Speed (mph)','Mean Momentum Transfer Wind Gust (mph)','Max Momentum Transfer Wind Gust (mph)','Hourly Snow Rate ('.$ratio.':1 Ratio)','Hourly Snow Rate (Max-T Method)','Hourly Freezing Rain Rate','Hourly Sleet Rate','Fire Weather','Winter Weather');

//for($i=0;$i<count($titles);$i++){
//	echo '<option value="'.$vars_available[$i].'">'.$titles[$i].'</option>\n';
//}

?>
<span id="showFire"></span>
&nbsp;&nbsp;&nbsp;&nbsp;
<button type="button" class="btn btn-default" id="load">Load</button>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<b><u>Color</u>:</b>
<input type='text' id="custom" />
<select id="condition" onChange="$('#load').click();">
<option value="greaterThan">></option>
<option value="lessThan"><</option>
<option value="equalTo">=</option>
</select>
<input type='text' id="threshold" size="4" maxlength="4" onChange="$('#load').click();" style="display:inline;" />

<span class="slidecontainer">
  <input type="range" min="1" max="100" value="50" class="slider" id="threshSlider" style="width:100px;" width="100" />
</span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<button type="button" class="btn btn-default" id="save">Download Map</button>

</td>
</tr>
</table>
</div>
<div id="sideTableDiv" style="position: fixed; top: 442; left: 0; z-index:2; border-style: none;">
<span id="sideTable"></span>
</div>
<div id="mainTableDiv" style="position: absolute; top: 442; left:114; width:100%; z-index:1; border-style: none;">
<span id="mainTable"></span>
</div>

<div id="map" class="map" style="position: fixed; top: 42; left: 0; height:400px; width:100%; z-index:4; border-style: solid; border-width: 1px;"></div>

<script>
var color = '';
$("#custom").spectrum({
    allowEmpty:true,
    change: function(c) {
        color = c.toHexString();
	updateTable();
    }
});

var slider = document.getElementById("threshSlider");
var output = document.getElementById('threshold');

slider.oninput = function() {
	var factor = 10;
	if(bufVarLast == 'frz_rain_rate' || bufVarLast == 'sleet_rate'){
		factor = 100;
	}
        output.value = this.value/factor;
	updateTable();
}

$(document).ready(function(){
    $("#load").click(function(){
		$(this).removeClass("btn-default").addClass("btn-danger");
        	$(this).button("loading");
		setTimeout(function () {
			updateTable();
			$(".btn").removeClass("btn-danger").addClass("btn-default");
			$(".btn").button("reset");
		},1000);
	});
    $("#save").click(function(){
		$(this).button("loading");
		setTimeout(function () {
	               saveMap();
	                $(".btn").button("reset");
	        },1000);
    });  
});

//document.getElementsByClassName('btn btn-default')[0].click();

$(window).scroll(function(event) {
   $("#sideTableDiv").css("margin-top", 0-$(document).scrollTop());
});

$("#mainTableDiv").css("margin-top", 0-$(document).scrollTop());

function style(feature, resolution) {
        return new ol.style.Style({
          image: new ol.style.Circle({
            fill: new ol.style.Fill({
              color: 'rgba(255,255,0,0.4)'
            }),
            radius: 5,
            stroke: new ol.style.Stroke({
              color: '#000',
              width: 1
            })
          }),
          text: new ol.style.Text({
              text: feature.get('disc'),
              offsetY: -15,
	      offsetX: 0,
              font: 'bold 18px Verdana'
	  })
        });
}

function styleSelect(feature, resolution) {
        return new ol.style.Style({
          image: new ol.style.Circle({
            fill: new ol.style.Fill({
              color: 'rgba(255,0,0,1)'
            }),
            radius: 5,
            stroke: new ol.style.Stroke({
              color: '#000',
              width: 1
            })
	  })
        });
}

var url = '../data/global_network.php';
var dataRequest = new XMLHttpRequest();
dataRequest.open('GET', url, false);
dataRequest.send();
var points = JSON.parse(dataRequest.responseText);

var vectorSource = new ol.source.Vector({
        features: (new ol.format.GeoJSON()).readFeatures(points)
});

var vectorLayer = new ol.layer.Vector({
        source: vectorSource,
        style: style
      });

var raster = new ol.layer.Tile({
        source: new ol.source.OSM()
});

var map = new ol.Map({
        layers: [raster,vectorLayer],
        target: 'map',
        view: new ol.View({
		projection: 'EPSG:4326',
        	center: [-98,40],
        	zoom: 5
	})
});

var select = new ol.interaction.Select({
	style: styleSelect
});

select.on('select', function(e) {
	console.log(e.selected[0]);
	try{
		var s = e.selected[0].N.sid.split(' ')[1].toLowerCase();
	}
	catch(e2){
		return;
	}
	document.getElementById('site').value = s;
	$("#load").click();
});

map.addInteraction(select);
map.updateSize();

</script>

</body>
</html>
