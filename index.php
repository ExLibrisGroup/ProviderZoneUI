<html>
  <head>
    <title>Ex Libris Provider Zone UI</title>	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  </head>

  <script type="text/javascript">

function myFunction() {  
  var element = document.getElementById("myDropDownValue");
  if(document.getElementById("myModeDropdown").value =='Complete') { 
	element.style.display = 'none';
   }
	else {
	element.style.display = 'inline-block';
	}
}	

function myHarvestingDropdownFunction() {
  var element = document.getElementById("marcXmlInputFileDiv");
  var element2 = document.getElementById("kbartXmlInputFileDiv");
  if(document.getElementById("myHarvestingDropdown").value =='ftp') { 
	element.style.display = 'none';
	element2.style.display = 'none';
   }
	else {
	element.style.display = 'inline-block';
	element2.style.display = 'inline-block';
	}
}

function XMLTree(xmlString)
{
  indent = "\t"; //can be specified by second argument of the function
  var tabs = "";  //store the current indentation
  var result = xmlString.replace(
    /\s*<[^>\/]*>[^<>]*<\/[^>]*>|\s*<.+?>|\s*[^<]+/g , //pattern to match nodes (angled brackets or text)
    function(m,i)
    {
      m = m.replace(/^\s+|\s+$/g, "");  //trim the match just in case

      if(i<38)
       if (/^<[?]xml/.test(m))  return m+"\n";  //if the match is a header, ignore it

      if (/^<[/]/.test(m))  //if the match is a closing tag
       {
          tabs = tabs.replace(indent, "");  //remove one indent from the store
          m = tabs + m;  //add the tabs at the beginning of the match
       }
       else if (/<.*>.*<\/.*>|<.*[^>]\/>/.test(m))  //if the match contains an entire node
       {
        //leave the store as is or
        m = m.replace(/(<[^\/>]*)><[\/][^>]*>/g, "$1 />");  //join opening with closing tags of the same node to one entire node if no content is between them
        m = tabs + m; //add the tabs at the beginning of the match
       }
       else if (/<.*>/.test(m)) //if the match starts with an opening tag and does not contain an entire node
       {
        m = tabs + m;  //add the tabs at the beginning of the match
        tabs += indent;  //and add one indent to the store
       }
       else  //if the match contain a text node
       {
        m = tabs + m;  // add the tabs at the beginning of the match
       }

      //return m+"\n";
      return "\n"+m; //content has additional space(match) from header
    }//anonymous function
  );//replace

  return result;
}

function openNewTab(result)
{
var myWindow=window.open('');
					setTimeout(function () {
						myWindow.document.title = "Ex Libris Provider Zone Result";
					}, 100);					
				var xml =XMLTree(result); 				
				xml=xml.replace(/</g, '&lt;');
				xml=xml.replace(/>/g, '&gt;');								
				var tmp='<pre>';								
				xml=xml.concat('<\pre>');		
				xml=tmp.concat(xml);							
				myWindow.document.write(xml); 	
				myWindow.document.close();					
}


$(document).ready(function(){
	var i=1;
	$('#add').click(function(){
		i++;
		$('#dynamic_field').append('<tr id="row'+i+'"><td><input class="inputFiles" name="marcInputFiles[]" placeholder="Enter your HTTP link"  /></td><td><button type="button" name="remove" id="'+i+'" class="button-remove">x</button></td></tr>');
	});
	
	$(document).on('click', '.button-remove', function(){
		var button_id = $(this).attr("id"); 
		$('#row'+button_id+'').remove();
	});	
});	




$(document).ready(function(){
	var i=1;
	$('#addKbart').click(function(){
		i++;
		$('#dynamic_field_kbart').append('<tr id="rowKbart'+i+'"><td><input class="inputFiles" name="kbartInputFiles[]" placeholder="Enter your HTTP link"  required /></td><td><button type="button" name="remove" id="'+i+'" class="button-remove">x</button></td></tr>');
	});
	
	$(document).on('click', '.button-remove', function(){
		var button_id = $(this).attr("id"); 
		$('#rowKbart'+button_id+'').remove();
	});	
});	



</script>
   
<?php

   if(isset($_POST['submit']) ){
    $api_key = $_POST['api_key'];	
	$api_key = urlencode($api_key);
	$url = "https://api-eu.hosted.exlibrisgroup.com/almaws/v1/provider-zone/e-collections?apikey=$api_key";
	$collection_name=$_POST['collection_name'];		
	$mode=$_POST['myModeDropdown'];
	$action="";
	$useFtp="";
	
	if($mode == "Incremental") {
		$action=$_POST['myDropDownValue'];				
	 } else  {
       $action='add';
    }	

	$myHarvestingDropdown=$_POST['myHarvestingDropdown'];
	if($myHarvestingDropdown == "ftp") {
		$useFtp='<use_ftp>true</use_ftp>';				
	 } else  {
       $useFtp='<use_ftp>false</use_ftp>';
    }
	
	$data='<pz_parameters><collection_name>'.$collection_name.'</collection_name>'.$useFtp.'<kbart_title_list><mode>'.$mode.'</mode><actions><action>'.$action.'</action></actions><links>';
	
	
	$kbartNumber = count($_POST["kbartInputFiles"]);	
	if($kbartNumber > 0)
	{		
		for($i=0; $i<$kbartNumber; $i++)
		{
			if(trim($_POST['kbartInputFiles'][$i] != ''))
			{			
				$data.='<link>'.$_POST['kbartInputFiles'][$i].'</link>' ;				
			}
		}	
	}	
				
	$data.='</links></kbart_title_list><marc_file_list>';

	$number = count($_POST["marcInputFiles"]);	
	if($number > 0)
	{		
	$data.='<links>';
		for($i=0; $i<$number; $i++)
		{
			if(trim($_POST['marcInputFiles'][$i] != ''))
			{			
				$data.='<link>'.$_POST['marcInputFiles'][$i].'</link>' ;				
			}
		}			
	$data.='</links>';	
	}	
	$email=$_POST['email'];	
	$data.='</marc_file_list><email>'.$email.'</email></pz_parameters>';
	
	
	$marc_file_link=$data;	
	$marc_file_link=str_replace( '<', '&lt;', $marc_file_link );
	$marc_file_link=str_replace( '>', '&gt;', $marc_file_link );
	$marc_file_link='<pre>'.$marc_file_link.'</pre>';	
	
	$options = array(
			'http' => array(
			'header'  => "Content-type: application/xml\r\n" . "Content-Length: " . strlen($data) . "\r\n",
			'method'  => 'POST',
			'content' => $data
			)	
		);


	$context  = stream_context_create($options);	
	
	$result =@file_get_contents($url, false, $context);
		
	if ($result ===FALSE) { 
			?>
			<div  id="message" class="failMessage">
			<?php echo ("The job  failed. Please contact Exlibris Content Support.");	?>
				<div onclick="document.getElementById('message').style.display = 'none';"  class="pointer" >&#10006</div> 				
			</div> <?php				
					
			}
			else{	
			
			?>
			<div   id="message" class="successMessage">
			<?php echo ("Thank you for the update, you will receive a detailed email once the process completes.");?>
				<div onclick="document.getElementById('message').style.display = 'none';"  class="pointer" >&#10006</div> 
				<script type="text/javascript">openNewTab('<?php echo $result; ?>');</script>				
			</div> 
						
			
			<?php	
			
		} 		 
  }  
?>
<style>

body{
    font-family: Arial, Helvetica, sans-serif;  
	background: #f1f1f1 ;
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 14px;
    line-height: 1.5em;
	width: 80%;
    margin: 15px auto;
}


/* Add padding to containers */
.container {
  padding: 25px;
  background-color: white;
}

/* Full-width input fields */
input[type=text] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
}
input[type=text]:focus {
  background-color: #ddd;
  outline: none;
}

/* Overwrite default styles of hr */
hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}

/* Set a style for the submit button */
.registerbtn {
    background-color: #0066cc;
    color: white;
    padding: 16px 20px;
    border: none;
    cursor: pointer;
    width: 200px;
    margin: auto;
    opacity: 0.9;
    display: block;
}

.registerbtn:hover {
  opacity: 1;
}

.show {display: block;}

.left {
 float: left;
 padding: 0 150px 20px 0;
}

/* Full-width input fields */
select { 
  padding: 15px;
  margin: 5px 0 10px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
  margin-right: 100px;
  width: 235px;
}

select:focus {
  background-color: #ddd;
  outline: none;
}

.required:after{
  content:"*";
  font-weight:bold;
   color: red;
   padding-left: 3px;
}
.successMessage {
    background-color: white;
    border: green 1px solid;
    padding: 10px;
    width: 560px;
    margin: auto;
    border-radius: 5px;
}

.failMessage {
    background-color: white;
    border: red 1px solid;
    padding: 10px;
    width: 400px;
    margin: auto;
    border-radius: 5px;
}

.pointer{
 cursor: pointer;
 display:inline-block;
}

.button-remove{
    color: #fff;
    background-color: #f90404db;
    border-color: #735356;
}

.button-success {
    color: #fff;
    background-color: #0066cc;
    border-color: #4d90d4;}
	


.inputFiles{
  padding: 15px;
  display: inline-block;
  border: none;
  background: #f1f1f1;
   width: 500px;
}	
	
</style>

<form method="post">

  <div class="container">
  <img src="exlibris.png"  class="left"><br><br>
    <h1>Ex Libris Provider Zone </h1> 
    <p>
	Using this form permitted providers can take ownership of their existing Community Zone collections (based on a list provided by Ex Libris) and manage them by adding/updating/deleting portfolios that are part of the collections.<br>
	
	    Additionally it is possible to  add new collections and portfolios, and optionally bibliographic records.</p>
    <hr>
	
	<label class="required"><b>API key</b></label><br>	
	<label>Unique identifier which allows the use of this form, contact Ex Libris in order to obtain an API key.</label> 
    <input autocomplete="on" id="api_key" type="text" name="api_key" required  ><br><br>
	
	
    <label  class="required"><b>Collection Name</b></label><br>
	<label>Enter the collection name you want to update or add.</label> 
    <input autocomplete="on" id="collection_name" type="text"  name="collection_name"  required ><br><br>
	
	<label  class="required" ><b>Harvesting Method</b></label><br>	
	<label>FTP=contact Ex Libris with the FTP account details.<br>
	HTTP=KBART or MARC file path is mandatory.</label> <br>	
	<select  name="myHarvestingDropdown"  onchange="myHarvestingDropdownFunction()" id="myHarvestingDropdown">
			<option value="" disabled selected>Select the Harvesting Method</option>
			<option value="ftp">FTP</option>
			<option value="http">HTTP</option>	
	</select><br>
	
	<div id="marcXmlInputFileDiv">
		<label><b>MARC XML Input File</b></label><br>
		<label>HTTP link to MARC records describing the titles that are part of 'Collection Name'.</label><br>
		<div class="table-responsive">	
		<table  id="dynamic_field">
			<tr>
			<td><input class="inputFiles" name="marcInputFiles[]" placeholder="Enter your HTTP link" /></td>
			<td><button type="button" name="add" id="add" class="button-success">+</button></td>
			</tr>
		</table>
		</div> 		
	</div>	
	<br><br>

	<div id="kbartXmlInputFileDiv">	
		<label  class="required" ><b>KBART Input File</b></label><br>
		<label>HTTP link to KBART format file which include relevant titles pertaining to the 'Collection Name'.</label><br>
		<div class="table-responsive">	
		<table  id="dynamic_field_kbart">
			<tr>
			<td><input class="inputFiles" name="kbartInputFiles[]" placeholder="Enter your HTTP link" required /></td>
			<td><button type="button" name="addKbart" id="addKbart" class="button-success">+</button></td>
			</tr>
		</table>
		</div> 
	</div>	
	<br><br>


	<div class="tooltip">
	<label  class="required" ><b>Mode</b></label><br>	
	<label>Complete=input file containing all titles that are part of the collection.<br>
	Incremental=file containing a subset of titles that should be added/updated/deleted according to 'Action' below.</label> <br>	
	<select   name="myModeDropdown"  onchange="myFunction()" id="myModeDropdown"   required >	
			 <option value="" disabled selected>Select the Mode</option>
			<option value="Complete">Complete</option>
			<option value="Incremental">Incremental</option>	
		</select>
		<!--<label  class="required" ><b>Mode action</b></label><br>-->
		<select  id="myDropDownValue"  name="myDropDownValue"  style="display:none" >
			 <option value="" disabled selected>Select the Mode Action</option>
			<option value="add">add</option>
			<option value="update">update</option>
			<option value="delete">delete</option>			
		</select>
	</div>	
	
	<br><br>
   
    <label  class="required"><b>Email</b></label><br>	
	<label>You will receive a report of Ex Libris Provider Zone process results.<br>
    <input autocomplete="on" id="email" type="text"   name="email"  required >  <br><br>
	
	<input class="registerbtn" type="submit" name="submit" value="Submit" />
	
  </div>   
</form>
</html>
