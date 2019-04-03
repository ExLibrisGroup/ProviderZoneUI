<html>
  <head>
    <title>Ex Libris Provider Zone UI</title>
  </head>

<?php

   if(isset($_POST['submit']) ){
    $api_key = $_POST['api_key'];
	//$url = 'http://AlmaSDK-exl_impl-institutionCode-CZ_INST:41_impl@aio98.impl.alma.dc04.hosted.exlibrisgroup.com:1801/almaws/v1/provider-zone/e-collections?api_restriction_profile='.$api_key;
	$url = 'https://api-eu.hosted.exlibrisgroup.com/almaws/v1/provider-zone/e-collections?apikey='.$api_key;
	$collection_name=$_POST['collection_name'];		
	$mode=$_POST['myModeDropdown'];
	$link=$_POST['kbartInputFile'];
	$action="";
	
	if($mode == "Incremental") {
		$action=$_POST['myDropDownValue'];				
	 } else  {
       $action='add';
    }		
		
	$marc_file_link=$_POST['marcInputFile'];
	$email=$_POST['email'];
	
	$data='<pz_parameters><collection_name>'.$collection_name.'</collection_name><kbart_title_list><mode>'.$mode.'</mode><link>'.$link.'</link><actions><action>'.$action.'</action></actions></kbart_title_list><marc_file_link>'.$marc_file_link.'</marc_file_link><email>'.$email.'</email></pz_parameters>';
	
	$options = array(
			'http' => array(
			'header'  => "Content-type: application/xml\r\n" . "Content-Length: " . strlen($data) . "\r\n",
			'method'  => 'POST',
			'content' => $data
			)	
		);


	$context  = stream_context_create($options);	
	$result = file_get_contents($url, false, $context);
	
			
			if ($result == FALSE) { 
			?>
			<div width="400" id="message" class="failMessage">
			<?php echo ("The job  failed. Please contact Exlibris Content Support.");	?>
				<div onclick="document.getElementById('message').style.display = 'none';"  class="pointer" >&#10006</div> 				
			</div> <?php				
					
			}
			else{
			?>
			<div  width="560" id="message" class="successMessage">
			<?php echo ("Thank you for the update, you will receive a detailed email once the process completes.");?>
				<div onclick="document.getElementById('message').style.display = 'none';"  class="pointer" >&#10006</div> 			
			</div> <?php
			//var_dump($result);
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
input[type=text], input[type=password] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
}
input[type=text]:focus, input[type=password]:focus {
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
  width: 200px;
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
    width: 200px;
    margin: auto;
    border-radius: 5px;
}

.failMessage {
    background-color: white;
    border: red 1px solid;
    padding: 10px;
    width: 200px;
    margin: auto;
    border-radius: 5px;
}

.pointer{
 cursor: pointer;
 display:inline-block;
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
	
	 <label><b>MARC XML Input File</b></label><br>
	<label>HTTP link to MARC records describing the titles that are part of 'Collection Name'.</label> 
    <input autocomplete="off" id="marcInputFile" type="text" name="marcInputFile" >			
	
	<label  class="required"><b>KBART Input File</b></label><br>
	<label>HTTP link to KBART format file which include relevant titles pertaining to the 'Collection Name'.</label> 
    <input autocomplete="off" id="kbartInputFile" type="text"  name="kbartInputFile" ><br><br>

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
    <input autocomplete="on" id="email" type="text"   name="email"   >  <br><br>
	
	<input class="registerbtn" type="submit" name="submit" value="Submit" />
	
  </div>
   
</form>
<script>

function myFunction() {
  
  var element = document.getElementById("myDropDownValue");
  if(document.getElementById("myModeDropdown").value =='Complete') { 
	element.style.display = 'none';
   }
	else {
	element.style.display = 'inline-block';
	}
}


</script>


</html>

