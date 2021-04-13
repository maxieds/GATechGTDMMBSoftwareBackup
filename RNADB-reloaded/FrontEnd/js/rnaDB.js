
/**
 * =================
 * 		SLIDERS
 * =================
 */
 
var minSliderValue = 0;
var maxSliderValue = 1;
 
function setSlider(ele, max) {
	ele = $(ele);
	if ((/[0-9]+/).test(ele.val())) {
		console.debug($("#" + ele.attr('sliderId')).slider);
		$("#"+ele.attr('sliderId')).slider('value', ele.val());
		ele.old = ele.value;
		minSliderValue = min;
		maxSliderValue = max;
	} else {
		// TODO: reset to old / valid value
	}
}

function sliderRange(ele, id, min, max, divider) {
	ele.html('<label for="min'+id+'">Min: </label><input type="text" id="min'+id+'" sliderId="slider'+id+'" '+
		'name="min'+id+'" style="width:70px;" value="'+(minSliderValue / divider)+'"" onblur="getSetSizeOut();" />'+
		'<span id="slider'+id+'" style="width:50%;display:inline-block;margin-left:20px;margin-right:20px;"></span>'+
		'<label for="max'+id+'">Max: </label><input type="text" id="max'+id+'" sliderId="slider'+id+'" '+
		'name="max'+id+'" style="width:70px;" value="'+(maxSliderValue / divider)+'"" onblur="getSetSizeOut();" />');
	var slider = $("#slider" + id).slider({
		range: true,
		min: min,
		max: max,
		values: [ min, max ],
		slide: function( event, ui ) {
			if (event && ui) {
				//console.debug(ui);
				$("#min" + id).val(ui.values[ 0 ] / divider);
				$("#max" + id).val(ui.values[ 1 ] / divider);
			}
		}
	});
	minSliderValue = min;
	maxSliderValue = max;
	$("#slider" + id).live('blur', getSetSizeOut);
	$('#min' + id).change(function() {
		var values = slider.slider( "option", "values" );
		values[0] = $("#min" + id).val() * divider;
		slider.slider( "option", "values", values );
		minSliderValue = values[0];
	});
	$('#max' + id).change(function() {
		var values = slider.slider( "option", "values" );
		values[1] = $("#max" + id).val() * divider;
		slider.slider( "option", "values", values  );
		maxSliderValue = values[1];
	});
}

/**
 * ====================
 * 		SEARCH
 * ====================
 */
var currSizeId = 0;
function populateFormElement(jsonFormData) {
	jsonFormData.startTime = (new Date()).getTime();
	jsonFormData.paper = "";
	var paperListNeedsComma = false;
    var paperSearchKey = "";
    $(".searchPaperKeyCBox").each(function(cbIndex) {
        if($(this).is(':checked')) {
             if(paperListNeedsComma) {
                  jsonFormData.paper += ",";
             }
             paperSearchKey = $(this).attr("value");
             paperSearchKey.replace('\'', '');
             paperSearchKey.replace(' ', '');
             jsonFormData.paper += paperSearchKey.toLowerCase();
             paperListNeedsComma = true;
        }
    });
    //console.log(jsonFormData.paper);
    
	var Fam = "";
	if ($("#ribosomalAll").is(':selected')) 
		Fam = "5S,16S,23S,";
	else if(!$("#ribosomalNone").is(':selected')) {
		Fam = ($("#5S").is(':selected') ? "5S,":"")+
		($("#16S").is(':selected') ? "16S,":"")+
		($("#23S").is(':selected') ? "23S,":"");
    }
	if ($("#riboswitchAll").is(':selected')) 
		Fam = Fam + "TPP,THF,FMN,ykok,glms,";
	else if(!$("#riboswitchNone").is(':selected')) {
		Fam = Fam + ($("#TPP").is(':selected') ? "TPP,":"")+
		($("#THF").is(':selected') ? "THF,":"")+
		($("#FMN").is(':selected') ? "FMN,":"")+
		($("#ykok").is(':selected') ? "ykok,":"")+
		($("#glms").is(':selected') ? "glms,":"");
    }
	if ($("#otherAll").is(':selected')) 
		Fam = Fam + "tRNA,IRES_cripavirus,IRES_HCV,Metazoa_SRP";
	else if(!$("#otherNone").is(':selected')) {
		Fam = Fam + ($("#tRNA").is(':selected') ? "tRNA,":"") +
		($("#IREScripa").is(':selected') ? "IRES_cripavirus,":"") +
		($("#IREShcv").is(':selected') ? "IRES_HCV,":"") +
		($("#metazoaSRP").is(':selected') ? "Metazoa_SRP,":"");
    }
	
	if(Fam.length > 0) {
		Fam = Fam.substring(0, Fam.length - 1);
    }
    else {
        Fam = "_";
    }
    jsonFormData.family = Fam;
	jsonFormData.ambiguous = $("input:radio[name=ambiguous]:checked").val();
	jsonFormData.seqLength = $("#sliderSeqLen").slider( "option", "values" );
	jsonFormData.mfeAccuracy = $.extend(true, [], $("#sliderPredAcc").slider( "option", "values" ));
	jsonFormData.mfeAccuracy[0] = jsonFormData.mfeAccuracy[0] / 1000;
	jsonFormData.mfeAccuracy[1] = jsonFormData.mfeAccuracy[1] / 1000;
	jsonFormData.organism = $("#organism").attr("value");
	if(jsonFormData.organism.length == 0) {
		jsonFormData.organism = '_';
	}
	jsonFormData.accession = $("#accession").val();

	jsonFormData.gccontent = $.extend(true, [], $("#slidergccontent").slider( "option", "values" ));
	jsonFormData.gccontent[0] = jsonFormData.gccontent[0] / 1000;
	jsonFormData.gccontent[1] = jsonFormData.gccontent[1] / 1000;
	jsonFormData.completeness = $.extend(true, [], $("#slidercompleteness").slider( "option", "values" ));
	jsonFormData.completeness[0] = jsonFormData.completeness[0] / 1000;
	jsonFormData.completeness[1] = jsonFormData.completeness[1] / 1000;
	
	jsonFormData.paperKeyAnd = "false";
	if($("#paperKeyAnd").is(':checked')) { 
	     jsonFormData.paperKeyAnd = "true";
	}

}

function updateConfirmText(jsonFormData) {
	$("#confirmRid").html(jsonFormData.rid);
	
	var paperSet = new String(jsonFormData.paper);
	var papersList = paperSet.split(",");
	paperSet = "";
	for(var pi = 0; pi < papersList.length; pi++) {
	     paper = papersList[pi];
	     paper = paper.replace(/([a-zA-Z])([a-zA-Z]*)(\d+)/, "$1-$3");
	     paper = paper.toUpperCase();
	     if(paperSet != "") {
	          paperSet += ",";
	     }
	     paperSet += paper;
	}
	paperSet = (paperSet.length == 0 ? "*EMPTY SEARCH*" : paperSet);
	//console.log(paperSet);
	$("#confirmpaper").html(paperSet);
	
	var famList = jsonFormData.family;
	if(famList.length > 25) {
		famList = famList.substring(0, 22) + "...";
	}
	$("#confirmFamily").html(famList);
	$("#confirmAmbiguous").html(jsonFormData.ambiguous == "1" ? "Allowed" : "Not Allowed");
	$("#confirmLenMin").html(jsonFormData.seqLength[0]);
	$("#confirmLenMax").html(jsonFormData.seqLength[1]);
	$("#confirmMfeAccMin").html(jsonFormData.mfeAccuracy[0]);
	$("#confirmMfeAccMax").html(jsonFormData.mfeAccuracy[1]);
    
	var accDisplayName = jsonFormData.accession;
	if(accDisplayName.length == 0) { 
		accDisplayName = '*ALL ACC NO.*';
	}
	$("#confirmaccession").html(accDisplayName);
	var orgDisplayName = jsonFormData.organism;
	
	if(orgDisplayName == '_') {
		orgDisplayName = '*ALL NAME MATCHES*';
	}
	$("#confirmOrganism").html(orgDisplayName);

	$("#confirmGccontentMin").html(jsonFormData.gccontent[0]);
	$("#confirmGccontentMax").html(jsonFormData.gccontent[1]);
	$("#confirmCompletenessMin").html(jsonFormData.completeness[0]);
	$("#confirmCompletenessMax").html(jsonFormData.completeness[1]);

	$("#sizeBox").html("");
	$("<span><img src='images/loading_small.gif' /></span>").hide().appendTo("#sizeBox").fadeIn(2000);
}

function resetSearchForm() {
     var jsonFormData = {};
     populateFormElement(jsonFormData);
	 updateConfirmText(jsonFormData);
	 sliderRange($('#rangeSeqLen'),'SeqLen',0,7500, 1);
	 sliderRange($('#completeness'),'completeness',0,1000, 1000);
	 sliderRange($('#gccontent'),'gccontent',0,1000, 1000);
	 sliderRange($('#rangePredAcc'),'PredAcc',0,1000, 1000);
}

function getSetSizeOut() {
	currSizeId++;
	var jsonFormData = {};
	jsonFormData.sizeId = currSizeId;
	populateFormElement(jsonFormData);
	updateConfirmText(jsonFormData);
	$.ajax({
		type: 'POST',
		url: "RNADBApi.php?getSize",
		data: jsonFormData,
		success: getSetSizeIn
	});
}
function getSetSizeIn(data) {
	var obj = JSON.parse(data);
	console.log(obj);
	var startTime = obj.startTime;
	var endTime = (new Date()).getTime();
	var searchTime = (endTime - startTime) / 1000.0;
	$("#searchTimeDesc").html(searchTime.toString() + " seconds");
	$("#searchTimeDesc").css("color", "#EEEEEC");
	
	if (obj.setId == currSizeId) {
		$("#sizeBox").html("");
		var accurateSetSize = obj.setSize;
		$("<h2>RNADB Active Search Results:</h2>" + 
		  "<span><b><i>"+ accurateSetSize.toString() + " matching organisms found in RNADB." + 
		  "</u></i></span>").appendTo("#sizeBox").css("color", "#EEEEEC");
		if (obj.setSize > 0) {
			var ranges = obj.ranges;
			$("#matchingOrgHeader").html("<b>&#9658;&nbsp;<u>Organisms matching search criteria</u>:</b>");
			$("#macthingOrgHeader").css("color", "#EEEEEC");
			$("#displayResultsList").html(obj.seqsArray);

			// now set the rest of the parameters verbatim:
			$("#confirmRid").html(ranges.rid);
			$("#confirmRid").css("color", "#EEEEEC");
			$("#confirmFamily").html(ranges.family);
			$("#confirmFamily").css("color", "#EEEEEC");
			$("#confirmLenMin").html(ranges.seqLengthMin.toString().substring(0,5));
			$("#confirmLenMin").css("color", "#EEEEEC");
			$("#confirmLenMax").html(ranges.seqLengthMax.toString().substring(0,5));
			$("#confirmLenMax").css("color", "#EEEEEC");
			$("#confirmMfeAccMin").html(ranges.mfeAccuracyMin.toString().substring(0,5));
			$("#confirmMfeAccMin").css("color", "#EEEEEC");
			$("#confirmMfeAccMax").html(ranges.mfeAccuracyMax.toString().substring(0,5));
			$("#confirmMfeAccMax").css("color", "#EEEEEC");
			$("#confirmGccontent").html(ranges.gccontentMin.toString().substring(0,5));
			$("#confirmGccontent").css("color", "#EEEEEC");
			$("#confirmGccontent").html(ranges.gccontentMax.toString().substring(0,5));
			$("#confirmGccontent").css("color", "#EEEEEC");
			$("#confirmCompleteness").html(ranges.completenessMin.toString().substring(0,5));
			$("#confirmCompleteness").css("color", "#EEEEEC");
			$("#confirmCompleteness").html(ranges.completenessMax.toString().substring(0,5));
			$("#confirmCompleteness").css("color", "#EEEEEC");
			$("#confirmpaperHeader").css("color", "#EEEEEC");
			$("#confirmpaper").css("color", "#EEEEEC");
			
		}
		else {
			$("#matchingOrgHeader").html("");
			$("#displayResultsList").html("");	
		}
	}
}
function submitSearch() {
	var jsonFormData = {};
	populateFormElement(jsonFormData);
	$('<form id="searchForm" method="POST" action="DownloadResults.php"></form>').appendTo('body');
	$('<input>').attr({ type: 'hidden', name: 'paper', value: jsonFormData.paper }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'paperKeyAnd', value: jsonFormData.paperKeyAnd }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'family', value: jsonFormData.family }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'organism', value: jsonFormData.organism }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'accession', value: jsonFormData.accession }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'lenmin', value: jsonFormData.seqLength[0] }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'lenmax', value: jsonFormData.seqLength[1] }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'mfeaccmin', value: jsonFormData.mfeAccuracy[0] }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'mfeaccmax', value: jsonFormData.mfeAccuracy[1] }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'gccontentmin', value: jsonFormData.gccontent[0] }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'gccontentmax', value: jsonFormData.gccontent[1] }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'completenessmin', value: jsonFormData.completeness[0] }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'completenessmax', value: jsonFormData.completeness[1] }).appendTo('#searchForm');
	$('<input>').attr({ type: 'hidden', name: 'ambiguous', value: jsonFormData.ambiguous }).appendTo('#searchForm');
	return $("#searchForm").submit();
}

/**
 * ====================
 * 		SEARCH
 * ====================
 */
var offset = 0;
var setSize = 100;
var allDownloaded = 0;
var appendTime = 0;

function changeAllCheckboxes() {
	 $("INPUT[type='checkbox']").attr('checked', $('#cbAll').is(':checked'));
}

function getAllCheckboxes() {
    var selected = "";
	$("INPUT[type='checkbox']").each(function() {
	  var ele = $(this);
	  if(ele.attr('rnaId')) {
		  selected += ele.attr('rnaId') + ",";
	  }
	});
    if (selected.length > 0) {
		selected = selected.substring(0, selected.length - 1);
	}
    return selected;
}

function getSelectedCheckboxes() {
	var selected = "";
	$("INPUT[type='checkbox']").each(function() {
	  var ele = $(this);
	  if ((ele.attr('checked') == 'checked') && ele.attr('rnaId')) {
		  selected += ele.attr('rnaId') + ",";
	  }
	});
	if (selected.length > 0) {
		selected = selected.substring(0, selected.length - 1);
	}
    return selected;
}

function downloadOut(set) {
    const setString = new String(set);
	if(setString.valueOf() == new String('allMinimal').valueOf()) {
		allDownloaded = 1;
		var jsonData = {};
		jsonData.selected = getAllCheckboxes();
		$.ajax({
			type:     'POST',
			url:      "RNADBApi.php?downloadMinimal",
			data:     jsonData,
			success:  downloadIn
		});
	} 
	else if(setString.valueOf() == new String('allMaximal').valueOf()) {
		allDownloaded = 1;
		var jsonData = {};
		jsonData.selected = getAllCheckboxes();
		$.ajax({
			type:     'POST',
			url:      "RNADBApi.php?downloadMaximal",
			data:     jsonData,
			success:  downloadIn
		});
	} 
	else if(setString.valueOf() == new String('selectedMinimal').valueOf()) {
		var jsonData = {};
		jsonData.selected = getSelectedCheckboxes();
		$.ajax({
			type:      'POST',
			url:        "RNADBApi.php?downloadMinimal",
			data:       jsonData,
			success:    downloadIn
		});
	} else {
		var jsonData = {};
		jsonData.selected = getSelectedCheckboxes();
		$.ajax({
			type:      'POST',
			url:       "RNADBApi.php?downloadMaximal",
			data:      jsonData,
			success:   downloadIn
		});
	}
	$("#downloadLinksLoading").html("<img src='images/loading_small.gif' />");
}

function downloadIn(data) {
	$("#downloadLinksLoading").html("");
	if (!appendTime) {
		$("#downloadLinks").html("");
		appendTime = 1;
	}
	try {
	     var obj = JSON.parse(data);
	} catch(e) {
	     console.log("ERROR parsing JSON: " + e.message + "\n" + data.toString());
	     $("<p style='color:red;'><b>Download Error:</b> " + "UNSPECIFIED" + "</p>")
			.appendTo("#downloadLinks")
			.fadeIn(2000);
	     return;
	}
	
	console.log(typeof obj.link);
	console.log(obj.link);
	if(obj.link) {
	    var linkStr = String(obj.link);
		if (allDownloaded == 1)
			allDownloaded = linkStr;
	    var zipFileBaseName = linkStr.substr(linkStr.lastIndexOf('/') + 1);
		$("<p><b>&#9654;&nbsp;Download Zipped File:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"font-size:12;\"><a href='" + linkStr + "'>" + zipFileBaseName + "</a></span></p>")
			.appendTo("#downloadLinks")
			.fadeIn(2000);
	} 
	else {
		$("<p style='color:red;'><b>Download Error:</b> " + obj.error + "</p>")
			.appendTo("#downloadLinks")
			.fadeIn(2000);
	}
}

function searchOut(newOffset) {
	searchParams.offset = newOffset;
	$.ajax({
		type: 'POST',
		url: "RNADBApi.php?search",
		data: searchParams,
		success:searchIn
	});
	$("#setSelectionLinks").html("<img src='images/loading_small.gif' />");
}

function searchIn(data) {
	var obj = JSON.parse(data);
	if (obj) {
		offset = obj.offset;
		initTable();
		var ele = $("#rnaTable");
		for (var i in obj.rows) {
			var arr = obj.rows[i];
			ele.append('<tr><td><input type="checkbox" rnaId=' + arr.rid +
					' /></td><td>' + arr.family + '</td>' +
					'<td>' + arr.organism+'</td>' +
					'<td>' + arr.accession+'</td>' +
					'<td>' + arr.seqLength+'</td>' +
					'<td>' + arr.mfeAcc+'</td>' +
					'<td>' + arr.gccontent+'</td>' +
					'<td>' + arr.completeness+'</td>' +
					'<td>' + (arr.ambiguous ? "Yes" : "No") + '</td>' +
					'<td>' + arr.notes + '</td></tr>'
			);
		} 
	}
	populateSetSelectionLinks();
}

function populateSetSelectionLinks() {
	populateSetSelectionLinks2($("#setSelectionLinks1"));
	populateSetSelectionLinks2($("#setSelectionLinks2"));
}

function populateSetSelectionLinks2(ele) {
	ele.html("");
	var i = offset > 0 ? offset - setSize : offset;
	if (i != 0) {
		var str = "<span>...&nbsp;&nbsp;::&nbsp;&nbsp;</span>";
		ele.append($(str));
	}
	var count = 0;
	for(; (i + setSize) < maxSliderValue && count < 6; i += setSize, count++) {
		var str = "<span>";
		if (i != offset) {
			str = "<a href='#' onclick='searchOut(" + i + ");return false;'>";
		}
		str += i + "-" + (i + setSize - 1);
		if (i != offset) {
			str += "</a>";
		}
		str += "&nbsp;&nbsp;::&nbsp;&nbsp;</span>";
		ele.append($(str));
	}
	if (count == 6) {
		var str = "<span>...&nbsp;&nbsp;::&nbsp;&nbsp;</span>";
		ele.append($(str));
	}
	i = Math.floor(maxSliderValue / setSize) * setSize;
	var str = "<span>";
	if (i != offset) {
		str = "<a href='#' onclick='searchOut(" + i + ");return false;'>";
    }
	str += i + "-" + maxSliderValue;
	if (i != offset) {
		str += "</a>";
	}
	str += "</span>";
	ele.append($(str));
}

function initTable() {
	$("#rnaTable").html('<tr><th><input id="cbAll" type="checkbox" onclick="changeAllCheckboxes();" /></th><th>RID</th>'+
	      '<th>Family</th><th>Organism</th><th>Accession</th><th>Seq. Length</th><th>MFE Acc.</th><th>MFE Acc. (GTFold)</th><th>GC Content</th>'+
          '<th>Completeness</th><th>Ambiguous</th><th>Notes</th></tr>');
}
