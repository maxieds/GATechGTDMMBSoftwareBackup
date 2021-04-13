<?php
//// Search.php
//// Author: Maxie D. Schmidt (maxieds@gmail.com), modified from existing sources
//// Created: 2019.06.18
?>

<?php
     session_start();
?>

<html>
<head>
	<title>Georgia Institute of Technology RNA Database</title>
	<link type="text/css" href="css/smoothness/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
	<link type="text/css" href="css/smoothness/jquery-ui-tabs.css" rel="stylesheet" />
	<link type="text/css" href="css/main.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
	<script type="text/javascript" <?php 
	     	$dateTimeNow = date_create();
			$dstamp = date_timestamp_get($dateTimeNow);
         	echo "src='js/rnaDB.js?donotcache=", $dstamp, "'";
		 ?>></script>
</head>
<body onload="getSetSizeOut();">
	<div id="container">
		<!-- Tabs -->
		<div id="tabs">
		    <h1 class="pageHeader">
		    Georgia Institute of Technology <br/> RNA Database
		    </h1>
			<ul id="tabsNavUI" style="width: 50%;">
			    <li class="ui-tabs-selected"><a href="#ui-tabs-1">Search</a></li>
				<li class="liInline"><a href="Search.php" onclick="window.location.href='Search.php'">New Search</a></li>
				<li class="liInline"><a href="Papers.php" onclick="window.location.href='Papers.php'">Papers</a></li>
				<li class="liInline"><a href="Help.php" onclick="window.location.href='Help.php'">Help</a></li>
			</ul>
			<div id="tabs-1" style="height:auto;">
				<!-- Size Box -->
				<div class="sizeBox">
					<!-- Size Box for returned search header info -->
					<div id="sizeBox" style="text-align:center;">
					</div>
					<hr class="searchResults" />
					<!-- Confirm Box -->
					<div id="confirmBox">
					  <span class="searchResultsBoxText" style="color: #EEEEEC;"><b>&#9654;&nbsp;<u>Search Parameters Summary:</u></b></span>
					  <table class="searchParams">
					    <tr>
					       <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-search"></span></td>
						   <td><span id="confirmpaperHeader" style="color: #EEEEEC;"><b>Paper Keys:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="confirmpaper" style="color: #EEEEEC;"></span></td>
						</tr>
						<tr>
						   <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-search"></span></td>
						   <td><span style="color: #EEEEEC;"><b>Families:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="confirmFamily" style="color: #EEEEEC;"></span></td>
						</tr>
						<tr>
						   <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-search"></span></td>
						   <td><span style="color: #EEEEEC;"><b>Organism Key:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="confirmOrganism" style="color: #EEEEEC;">-</span></td>
						</tr>
						<tr>
						   <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-search"></span></td>
						   <td><span style="color: #EEEEEC;"><b>Accession Numbers:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="confirmaccession" style="color: #EEEEEC;"></span></td>
						</tr>
						<tr>
						   <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-search"></span></td>
						   <td><span style="color: #EEEEEC;"><b>Sequence Length:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="confirmLenMin" style="color: #EEEEEC;">0</span><span style="color: #EEEEEC;">-</span><span id="confirmLenMax" style="color: #EEEEEC;">7500</span></td>
						</tr>
						<tr>
						   <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-search"></span></td>
						   <td><span style="color: #EEEEEC;"><b>MFE Accuracy:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="confirmMfeAccMin" style="color: #EEEEEC;">0</span><span style="color: #EEEEEC;">-</span><span id="confirmMfeAccMax" style="color: #EEEEEC;">1</span></td>					
						</tr>
						<tr>
						   <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-search"></span></td>
						   <td><span style="color: #EEEEEC;"><b>Completeness:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="confirmcompleteness" style="color: #EEEEEC;">0</span><span style="color: #EEEEEC;">-</span><span id="confirmcompleteness" style="color: #EEEEEC;">1</span></td>
						</tr>
						<tr>
						   <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-search"></span></td>
						   <td><span style="color: #EEEEEC;"><b>GC content:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="confirmgccontent" style="color: #EEEEEC;">0</span><span style="color: #EEEEEC;">-</span><span style="color: #EEEEEC;" id="confirmgccontent">1</span></td>
						</tr>
						<tr>
						   <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-search"></span></td>
						   <td><span style="color: #EEEEEC;"><b>Ambiguous:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="confirmAmbiguous" style="color: #EEEEEC;">Allowed</span></td>
						</tr>
						<tr>
						   <td>&nbsp;&nbsp;</td>
					       <td><span class="ui-icon ui-icon-clock"></span></td>
						   <td><span style="color: #EEEEEC;"><b>Search time:</b></span></td>
						   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						   <td><span id="searchTimeDesc" style="color: #EEEEEC;"></span></td>
						</tr>
				      </table>
						
						<hr class="searchResults" />
						
						<span id="matchingOrgHeader" class="searchResultsBoxText" style="color: #EEEEEC;" ></span>
						<span id="displayResultsList" class="innerSearchResultsList"></span>
						
						<hr class="searchResults" />
						
					</div>
				</div>
				<!-- Content-->
				<div id="nav" style="float:left;padding:15px;width:750px;font-size:12pt;">
				    <h2>About RNADB: </h2>
					<p>
					<i>We currently index more than 
                      <b><?php
                           include_once "RNADBUtils.php";
                           //echo round(GetRNADBSequenceCount(), 2, PHP_ROUND_HALF_DOWN);
                           echo GetRNADBSequenceCount();
                         ?>
                      </b> 
                      sequences in RNADB.</i>
                    This database contains the curated RNA sequences used in
					papers produced by the 
					<a href="https://sites.google.com/site/christineheitsch/research"> 
						Heitsch Discrete Math and Molecular Biology Lab</a>.
					Most of these sequences originated from the
					<a href="http://www.rna.ccbb.utexas.edu/DAT/">
						Comparative RNA Website
					</a> 
					and from the 
					<a href="http://rfam.xfam.org">Rfam</a> database. 
                      Users can download the CT file data for the secondary structures of
					  these samples by searching with the criteria
                      below. 
                      Please visit the help and associated papers pages linked above 
                      for more information on this
                      database and the search criteria. For inquiries on the RNADB site, 
                      please contact <i><a href="mailto:gtdmmb@gatech.edu">gtdmmb@gatech.edu</a>.</i>
					</p>

					<hr/>

                        <h2>Search Parameters:</h2>
						<!-- Paper -->
						<p class="formHeader">&#9658;&nbsp;Select paper(s): <br/>
						<span style="font-size:10;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						(Select paper sources by key of the sequences in the search)</span>
					    </p>
						<p id="paper" class="formItem">
						    <?php
						         include_once "RNADBUtils.php";
						         $fullPapersList = getFullPapersList();
						         $paperCount = 0;
						         foreach($fullPapersList as $paperKey) {
						              $paperCount += 1;
						              echo "<input type=\"checkbox\" id=\"$paperKey\" value=\"$paperKey\" " . 
                                           "class=\"searchPaperKeyCBox\" checked " . 
                                           "onchange=\"getSetSizeOut();\" onclick=\"getSetSizeOut();\" />\n";
						              echo "<label for=\"$paperKey\">" . expandPaperKeyField($paperKey) . "</label>\n";
						              echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
						              if(($paperCount % 3) == 0) {
						                   echo "<br/>\n";
						              }
						         }
						    ?>
						</p>
						
						<div class="formHeader2">
						   By default the sequence search performed by RNADB uses an <b><i>OR</i></b>-based procedure with respect to the 
						   paper keys checked above. That is to say that if you have <i>PAPER1</i> and <i>PAPER2</i> both checked above, then 
						   we search the RNADB for sequences which are considered in <i>PAPER1</i> or <i>PAPER2</i> or <i>BOTH</i>. To select an 
						   analogous <b><i>AND</i></b>-based procedure (i.e., in both <i>PAPER1</i> and <i>PAPER2</i>, but not one xor the other), 
						   select the checkbox below: 
						</div>
						<p id="paperAndOr" class="formItem">
						   <input type="checkbox" id="paperKeyAnd" name="paperKeyAnd" value="paperKeyAnd" onchange="getSetSizeOut();" />
						   <label for="paperKeyAnd">Search for sequences in <i>*ALL*</i> selected papers</label>
						</p>

						<!-- RNA Category -->
						<p class="formHeader">&#9658;&nbsp;Select RNA Family:<br />
							<span style="font-size:10;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							(Select multiple or deselect by holding ctrl key while clicking)</span>
					    </p>
						<table style="padding-left: 15px;"> 
							<tr style="font-size: 11pt;">
								<th>Ribosomal: </th>
								<th>Riboswitch: </th>
								<th>Other: </th>
							</tr>
							<tr> 
							<td>
							<select id="ribosomalFamily" onchange="getSetSizeOut();" class="formItem" multiple=1 size = "5">
								<option id="5S" name="5S" ><label for="family5S">5S rRNA</label></option>
								<option id="16S" name="16S"><label for="family16S">16S rRNA</label></option>
								<option id="23S" name="23S" ><label for="family23S">23S rRNA</label></option>
								<option id="ribosomalAll" name="ribosomalAll" selected="selected"><label for="ribosomalAll">All of the above</label></option>
								<option id="ribosomalNone" name="ribosomalNone"><label for="ribosomalNone">None of the above</label></option>
							</select>
							</td>
							<td>
							<select id="riboswitchFamily" onchange="getSetSizeOut();" class="formItem" multiple=1 size="7">
								<option id="TPP" name="TPP" ><label for="familyTPP">TPP</label></option>
								<option id="THF" name="THF" ><label for="familyTHF">THF</label></option>
								<option id="FMN" name="FMN" ><label for="familyFMN" >FMN</label></option>
								<option id="ykok" name="ykok" ><label for="familyykok">ykoK</label></option>
								<option id="glms" name="glms" ><label for="familyglms">glmS</label></option>
								<option id="riboswitchAll" name="riboswitchAll" selected="selected"><label for="riboswitchAll">All of the above</label></option>
								<option id="riboswitchNone" name="riboswitchNone"><label for="riboswitchNone">None of the above</label></option>
							</select>
							</td>
							<td>
							<select id="otherFamily" onchange="getSetSizeOut();" class="formItem" multiple=1 size="6">
								<option id="tRNA" name="tRNA" ><label for="familytRNA">tRNA</label></option>
								<option id="IREScrip" name="IREScrip" ><label for="familyIREScrip">IRES cripavirus</label></option>
								<option id="IREShcv" name="IREShcv" ><label for="familyIREShcv" >IRES HCV</label></option>
								<option id="metazoaSRP" name="metazoaSRP" ><label for="familymetazoaSRP">Metazoa SRP</label></option>
								<option id="otherAll" name="otherAll" selected="selected"><label for="otherAll">All of the above</label></option>
								<option id="otherNone" name="otherNone"><label for="otherNone">None of the above</label></option>
							</select>
							</td>
							</tr>
					</table>
						
						<p class="formHeader">
							&#9658;&nbsp;Organism Name: <br />
							<span style="font-size:10;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							(For best results, enter just the species name like "Coli" for E. coli)</span>
						</p>
						<p class="formItem">
							<input type="text" id="organism" name="organism" onblur="getSetSizeOut();" onchange="getSetSizeOut();" value="" placeholder="Coli" />
						</p>

						<p class="formHeader">
							&#9658;&nbsp;Accession Number: <br />
							<span style="font-size:10;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							(Accession numbers for the sequences are standardized in the 
							<a href="https://www.ncbi.nlm.nih.gov/genbank/sequenceids/">NCBI databases</a>)</span>
						</p>
						<p class="formItem">
							<input type="text" id="accession" name="accession" onblur="getSetSizeOut();" onchange="getSetSizeOut();" placeholder="J01695" />
						</p>

						<!-- Sequence Length -->
						<p class="formHeader">&#9658;&nbsp;Sequence Length:</p>
						<p id="rangeSeqLen" class="formItem" onchange="getSetSizeOut();" placeholder="1542"></p>
						
						<!-- Prediction Accuracy -->
						<p class="formHeader">&#9658;&nbsp;MFE Prediction Accuracy (<i>f-measure</i>):</p>
						<p id="rangePredAcc" class="formItem" onchange="getSetSizeOut();" placeholder="0.423"></p>
						
						
						<!-- Completeness Ratio -->
						<p class="formHeader">&#9658;&nbsp;Completeness:</p>
						<p id="completeness" class="formItem" onchange="getSetSizeOut();" placeholder="0.906"></p>
						
						<!-- GC Content -->
						<p class="formHeader">&#9658;&nbsp;GC content:</p>
						<p id="gccontent" class="formItem" onchange="getSetSizeOut();" placeholder="0.544"></p>
						
						<!-- Ambiguous -->
						<p class="formHeader">&#9658;&nbsp;Allow ambiguous sequences: 
							<input type="radio" id="ambOn" name="ambiguous" checked="1" value="1" onclick="getSetSizeOut();" /><label for="ambOn">Allowed</label>
							<input type="radio" id="ambOff" name="ambiguous" value="0" onclick="getSetSizeOut();" /><label for="ambOff">Not Allowed</label><br/>
							<span style="font-size:10;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							(Whether the bases are completely specified in the sample, or whether the 
						    sequence sample can contain the nucleotide N)</span>
							<br/>
						</p>

                        <hr/>
				                <!-- Submit Box -->
				                <div class="submitBox" id="submitBox"  style="float:left;padding:10px;width:750px;">
					                   <button onclick="submitSearch();">Download Sequence Files&nbsp;&#xbb;&nbsp;</button>
					                   <!--<button onclick="resetSearchForm();">Reset All Form Parameters</button>-->
				                </div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#tabs').tabs();
			sliderRange($('#rangeSeqLen'),'SeqLen',0,7500, 1);
			sliderRange($('#completeness'),'completeness',0,1000, 1000);
			sliderRange($('#gccontent'),'gccontent',0,1000, 1000);
			sliderRange($('#rangePredAcc'),'PredAcc',0,1000, 1000);
			$( "#ambiguous" ).button();
			$( "#paper" ).button();
			$( "#paperAndOr" ).button();
			$( "#family" ).button();
			$( "#rnaFamily" ).buttonset();
			$( "#tabsNavUI" ).button();
		});
		$(window).load(function() {
			$('#tabs-1').append('<div id="bottomClearDiv" style="clear:both;" class="clear"></div>');
		});
	</script>
</body>
</html>
