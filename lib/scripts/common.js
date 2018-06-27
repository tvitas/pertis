var xmlhttp;

function GetCookieVal (offset) {
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1) endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}

function GetCookie (name) {
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen)
	{
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg)	return GetCookieVal (j);
		i = document.cookie.indexOf(" ", i) + 1;
  	if (i == 0) break;
	}
	return null;
}

function writecookie(elmnt,name,value) {
	document.cookie=name + "=" + escape(value);
}

function deletecookie(name) {
	if (GetCookie(name)) document.cookie = name + "=0;"+"expires=Thu, 01-Jan-1970 00:00:01 GMT";
}

function ShowElement(elmnt) {
	document.getElementById(elmnt).style.visibility="visible";
}

function HideElement(elmnt) {
	document.getElementById(elmnt).style.visibility="hidden";
}

function ToogleElement(elmnt) {
	var state = document.getElementById(elmnt).style.display;
	if (state === 'none') {
		document.getElementById(elmnt).style.display = 'block';
	}
	if (state === 'block') {
		document.getElementById(elmnt).style.display = 'none';
	}
}

function calcRoute(waypoints, did) {
	var wpl = waypoints.length;
	var start = waypoints[0];
	var end = waypoints[wpl-1];
	var waypts = [];
    var directionsService = new google.maps.DirectionsService();
	for (var i = 0; i < wpl; i++) {
		waypts.push({location:waypoints[i]});
	}
	var request = {origin:start, destination:end, waypoints:waypts, travelMode: google.maps.TravelMode.DRIVING};
	directionsService.route(request, function(response, status) {
		var td = 0;
		if (status == google.maps.DirectionsStatus.OK) {
			var route = response.routes[0];
			for (var i = 0; i < route.legs.length; i++) {
				td += route.legs[i].distance.value/1000;
				//console.log(route.legs[i].distance.value+" "+td);
			}
			td = td.toFixed(2);
			document.getElementsByClassName(did)[0].innerHTML = td;
			document.getElementsByName(did)[0].value = td;
		} else {
			alert("Google DirectionsService negali suskaičiuoti maršruto atstumo.\n\nTikrinkite pagal žemėlapį, arba patikslinkite maršrutą.");
		}
	});
}

function getLink(trip, lid, did, map) {
	var trip = document.getElementById(trip).value;
	var	route = trip.split('-');
	var glink = route.join('/');
	if(!map) {
		calcRoute(route, did);
	}
	if(map) {
		document.getElementById(lid).href="http://maps.google.com/maps/dir/"+glink;
		document.getElementById(lid).target="_new";
	}
}

function hasClass(element, className) {
    return element.className && new RegExp("(^|\\s)" + className + "(\\s|$)").test(element.className);
}

function prep_sql (params) {
	var html = document.getElementById(params['tag_to_show']).innerHTML;
	var sql = document.getElementById(params['tag_to_glue']).value;
	var glue_value = params['glue_value'];
	var glue_text  = params['glue_text'];
	switch (params['option_type']) {
	case 1:  //which column
		sql  += "lower(" + glue_value + "::text) like '%";
		html += glue_text  + " = '";
		break;
	case 2: //value
		html += glue_value + "'";
		if ((glue_value.toLowerCase() == 'ne') || (glue_value.toLocaleLowerCase() == 'n')) {
			glue_value = 'f';
		}
		if ((glue_value.toLowerCase() == 'taip') || (glue_value.toLocaleLowerCase() == 't')) {
			glue_value = 't';
		}
		sql  += glue_value.toLowerCase() + "%'";
		break;
	case 3: //operator
		sql  += ' ' + glue_value + ' ';
		html += ' ' + glue_text  + ' <br />\n';
		break;
	}
	document.getElementById(params['tag_to_show']).innerHTML = html;
	document.getElementById(params['tag_to_glue']).value = sql;
}

function writedate() {
	var monthNames = new Array( "sausio","vasario","kovo","balandžio","gegužės","birželio","liepos","rugpjūčio","rugsėjo","spalio","lapkričio","gruodžio");
	var dayarray = new Array("sekmadienis","pirmadienis","antradienis","trečiadienis","ketvirtadienis","penktadienis","šeštadienis")
	var now = new Date();
	thisYear = now.getYear();
	var day = now.getDay();
	if(thisYear < 1900) {
		thisYear += 1900;
	}
	document.write(thisYear + " m. " + monthNames[now.getMonth()] + " " + now.getDate() + ", " + dayarray[day]);
}

function AjaxShow(str) {
	var paramstr = str.split(":");
//	console.log(paramstr);
	var url = paramstr[3];
	if ((paramstr[0]!="") && (paramstr[1]!="")) {
		writecookie(paramstr[0], paramstr[1], paramstr[2]);
	}
	if (paramstr[0]=='menu') {
		writecookie('','select_page','1');
		deletecookie('select_order');
		deletecookie('select_filter');
	}
	xmlhttp=AjaxGetXmlHttpObject()
	if (xmlhttp==null) {
		alert ("Browser does not support HTTP Request");
		return;
	}
	writecookie('','e_id', paramstr[4]);
	if (paramstr[5]!="") {
		writecookie('',paramstr[5],paramstr[6]);
	}
	xmlhttp.onreadystatechange=function() {
		var oid = GetCookie('e_id');
		if (xmlhttp.readyState==4) {
	   		document.getElementById(oid).innerHTML=xmlhttp.responseText;
	   		ShowElement(oid);
  		}
 	}
 	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

function AjaxGetXmlHttpObject() {
	if (window.XMLHttpRequest) {
  		//IE7+, Firefox, Chrome, Opera, Safari
  		return new XMLHttpRequest();
  	}
	if (window.ActiveXObject) {
  		//IE6, IE5
  		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	return null;
}

function ShowVal(vid, oid, val, txt) {
	var splitted = txt.split("-");
	var splitted_length = splitted.length;
	document.getElementById(vid).value = val;
	document.getElementById(oid).innerHTML = splitted[splitted_length-1];
}

function changetab(a) {
	var tabcount = document.getElementById("tabs-container").getElementsByTagName("li");
	for (var i=1; i<=tabcount.length; i++) {
		document.getElementById("tab-"+i).style.display = 'none';
		document.getElementById("tabs-li-"+i).setAttribute ('class', 'tabs-li');
	}
	document.getElementById("tab-"+a).style.display = 'block';
	document.getElementById("tabs-li-"+a).setAttribute ('class', 'tabs-li-active');
}

function hidetabs() {
	var tabcount = document.getElementById("tabs-container").getElementsByTagName("li");
	for (var i=1; i<=tabcount.length; i++) {
		document.getElementById("tab-"+i).style.display = 'none';
		document.getElementById("tabs-li-"+i).setAttribute ('class', 'tabs-li');
	}
}

function getProductById(id) {
    var product_id = document.getElementById(id).value;
    var xmlhttp = new XMLHttpRequest();
    var url = "api/api.php?sku=" + product_id;
    var products = [];

    xmlhttp.onreadystatechange = function() {

        if (this.readyState == 4 && this.status == 200) {
            var products = JSON.parse(this.responseText);
            if (products.hasOwnProperty('product_title')) {
                document.getElementById('order_recource_title').value = products['product_title'];
            } else {
                document.getElementById('order_recource_title').value = '';
            }

            if (products.hasOwnProperty('product_dim_title')) {
                document.getElementById('order_recource_dim_id').value = products['product_dim_title'];
            } else {
                document.getElementById('order_recource_dim_id').value = '';
            }
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}
