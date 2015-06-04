  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-30422970-1', 'auto');
  ga('send', 'pageview');

</script>



  <?php
ini_set('memory_limit','16M');
/*

Notes for dmcd:

1.Script to connect to mySQL DB "crunchiot" and select table "iotcompanies" 
2.then sets up row of field names
3. uses cURL to pull JSON data from iot category of Crunchbase API: category_uuids=ed3a589dc9a73cbb9feb245f011e1d54
4. holds data in an array which is looped through with SQL which INSERTS into db table: `crunchiot`.`iotCompanies`
5. SQL order is echoed and success is printed (ideally)
*/

{   //Connect and Test MySQL and specific DB (return $dbSuccess = T/F)
        
      $hostname = "crunchiot.db.10718538.hostedresource.com";
      $username = "crunchiot";
      $password = "Crunchiot!1";      
      $databaseName = "crunchiot";


      $dbConnected = @mysql_connect($hostname, $username, $password);
      $dbSelected = @mysql_select_db($databaseName,$dbConnected);

      $dbSuccess = true;
      if ($dbConnected) {
        if (!$dbSelected) {
          echo "DB connection FAILED<br /><br />";
          $dbSuccess = false;
        }   
      } else {
        echo "MySQL connection FAILED<br /><br />";
        $dbSuccess = false;
      }
}  

if ($dbSuccess) {
  
  $crunchiot_SQLselect = "SELECT ";
  $crunchiot_SQLselect .= "MONTH(FROM_UNIXTIME(created)), YEAR(FROM_UNIXTIME(created)), COUNT(*) ";
  $crunchiot_SQLselect .= "FROM( ";
  $crunchiot_SQLselect .= "SELECT ";
  $crunchiot_SQLselect .= "a.hidden, a.created ";
  $crunchiot_SQLselect .= "FROM "; 
  $crunchiot_SQLselect .= "angeltestplus a ";  
  $crunchiot_SQLselect .= "WHERE ";
  $crunchiot_SQLselect .= "a.created > '1285710235' ";
  $crunchiot_SQLselect .= "AND ";
  $crunchiot_SQLselect .= "a.hidden = 'false' ";
  $crunchiot_SQLselect .= "UNION ALL ";
  $crunchiot_SQLselect .= "SELECT ";
  $crunchiot_SQLselect .= "i.hidden, i.created ";
  $crunchiot_SQLselect .= "FROM "; 
  $crunchiot_SQLselect .= "iotCompaniesByName i ";  
  $crunchiot_SQLselect .= "WHERE ";
  $crunchiot_SQLselect .= "i.created > '1285710235' ";
  $crunchiot_SQLselect .= "AND ";
  $crunchiot_SQLselect .= "i.hidden = 'false') AS combotable ";
  $crunchiot_SQLselect .= "GROUP BY ";
  $crunchiot_SQLselect .= "MONTH(FROM_UNIXTIME(created)), YEAR(FROM_UNIXTIME(created)) ";
  $crunchiot_SQLselect .= "ORDER BY ";
  $crunchiot_SQLselect .= "`created` DESC ";

  $crunchiot_SQLselect_Query = mysql_query($crunchiot_SQLselect);

  

$rows = array();
$json_response = array();
$json_response['cols'] = array(
                  array('label' => 'Date', 'type' => 'date'),
                  array('label' => 'Count', 'type' => 'number')
    );
    
$rows = array();
$indx = 1;  
  while ($row = mysql_fetch_assoc($crunchiot_SQLselect_Query, MYSQL_ASSOC)) {
    $row_array['Date']=$row['YEAR(FROM_UNIXTIME(created))'].','.($row['MONTH(FROM_UNIXTIME(created))']-1);
      $row_array['Count']=$row['COUNT(*)'];
        $temp = array();
        $temp[] = array('v' => "Date(".(string) $row_array['Date'].")");
        $temp[] = array('v' => (int) $row_array['Count']);
        $rows[] = array('c' => $temp);
        $indx++;
}
$json_response['rows'] = $rows;
$jsontable = json_encode($json_response);
//echo $jsontable;
}
?>
  <head>
<link rel="stylesheet" href="frostedsutro.css"></link>
<link href='http://fonts.googleapis.com/css?family=Josefin+Sans:100' rel='stylesheet' type='text/css'>
    <title>AandV</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
     
     <link href='http://fonts.googleapis.com/css?family=Josefin+Sans:100' rel='stylesheet' type='text/css'>
      
      <!-- Google Chart reference script -->
       <script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>

    <script type="text/javascript">
      google.setOnLoadCallback(drawChart);

      function drawChart() {
var data = new google.visualization.DataTable(<?=$jsontable?>);

        var options = {
          curveType: 'function',
          legend: 'none',
          series: {
            0: { color: '#FF9900' }
                  },
          backgroundColor: { fill:'transparent' },
          hAxis: {
            textStyle: {color: 'grey'},
            baselineColor: 'transparent',
            gridlines: {color: 'transparent'}
                 },
          vAxis: {
            textStyle: {color: 'grey'},
            baselineColor: 'transparent',
            gridlines: {color: '#FFFFFF'}
                 },
          trendlines: {
      0: {
        color: '#00AEEF', 
        lineWidth: 10,
        opacity: 0.6,
        type: 'exponential'
         }
                      },
                          
                 };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script> 
  </head>
<body>
    <div id="wrapper">
<div id="titlecontainer">
    <img id="title" src="avlogoorange.png"></img>
  <img id="cballogo" src="cballight.png"></img>

  <div id="pagetitle">I o T  &nbsp D a s h b o a r d</div>
  <div id="attribution"> <a href="https://github.com/DavidMcDoughnut" target="_blank" id="githublink"> @davidmcdoughnut </a></div>
  </div>





<div class="frosted1">

  
  <div><h1 id="listTitle" style="opacity:1;"> List of New IoT Companies </h1></div>
    <div id='updatedtopleft'> 
      <?php

$date = new DateTime(NOW);
  $date->setTimezone(new DateTimeZone('America/Los_Angeles'));
echo "Updated: ".$date->format('m-d-Y, H:i:s') . " PT"."\n";

?>

     </div>
  <h3 id="listSubTitle"> Last 10 created, newest first, pulled from Crunchbase and AngelList databases at page load</h3>
    <div id="listleft">
<?php
/*

Notes for dmcd:

1.Script to connect to mySQL DB "crunchiot" and select table "iotcompanies" 
2.then sets up row of field names
3. uses cURL to pull JSON data from iot category of Crunchbase API: category_uuids=ed3a589dc9a73cbb9feb245f011e1d54
4. holds data in an array which is looped through with SQL which INSERTS into db table: `crunchiot`.`iotCompanies`
5. SQL order is echoed and success is printed (ideally)
*/

{   //Connect and Test MySQL and specific DB (return $dbSuccess = T/F)
        
      $hostname = "crunchiot.db.10718538.hostedresource.com";
      $username = "crunchiot";
      $password = "Crunchiot!1";      
      $databaseName = "crunchiot";


      $dbConnected = @mysql_connect($hostname, $username, $password);
      $dbSelected = @mysql_select_db($databaseName,$dbConnected);

      $dbSuccess = true;
      if ($dbConnected) {
        if (!$dbSelected) {
          echo "DB connection FAILED<br /><br />";
          $dbSuccess = false;
        }   
      } else {
        echo "MySQL connection FAILED<br /><br />";
        $dbSuccess = false;
      }
}  

if ($dbSuccess) {
  
  $crunchiot_SQLselect = "SELECT ";
  $crunchiot_SQLselect .= "name, created, hidden, db ";
  $crunchiot_SQLselect .= "FROM "; 
  $crunchiot_SQLselect .= "iotCompaniesByName ";  
  $crunchiot_SQLselect .= "WHERE ";
  $crunchiot_SQLselect .= "created > '1285710235' ";
  $crunchiot_SQLselect .= "UNION ALL ";
  $crunchiot_SQLselect .= "SELECT ";     
  $crunchiot_SQLselect .= "name, created, hidden, db ";
  $crunchiot_SQLselect .= "FROM angeltestplus ";
  $crunchiot_SQLselect .= "WHERE ";
  $crunchiot_SQLselect .= "hidden = 'false' "; 
  $crunchiot_SQLselect .= "AND ";
  $crunchiot_SQLselect .= "created > '1285710235' ";
  $crunchiot_SQLselect .= "ORDER BY ";
  $crunchiot_SQLselect .= "`created` DESC ";
  
  $crunchiot_SQLselect_Query = mysql_query($crunchiot_SQLselect);   


 
$indx = 1;  
  while ($row = mysql_fetch_array($crunchiot_SQLselect_Query, MYSQL_ASSOC)) {
      $Name = $row['name'];
      $Created = $row['created'];
      $Db = $row['db'];

      if ($indx <6){
      echo $indx." - ".$Name.": ".date("m-d-Y",$Created).", ".$Db."<br />";

      $indx++; }
      
  }
  
  mysql_free_result($crunchiot_SQLselect_Query);    
}

?>
</div>

<div id="listright">
 
<?php
/*

Notes for dmcd:

1.Script to connect to mySQL DB "crunchiot" and select table "iotcompanies" 
2.then sets up row of field names
3. uses cURL to pull JSON data from iot category of Crunchbase API: category_uuids=ed3a589dc9a73cbb9feb245f011e1d54
4. holds data in an array which is looped through with SQL which INSERTS into db table: `crunchiot`.`iotCompanies`
5. SQL order is echoed and success is printed (ideally)
*/

{   //Connect and Test MySQL and specific DB (return $dbSuccess = T/F)
        
      $hostname = "crunchiot.db.10718538.hostedresource.com";
      $username = "crunchiot";
      $password = "Crunchiot!1";      
      $databaseName = "crunchiot";


      $dbConnected = @mysql_connect($hostname, $username, $password);
      $dbSelected = @mysql_select_db($databaseName,$dbConnected);

      $dbSuccess = true;
      if ($dbConnected) {
        if (!$dbSelected) {
          echo "DB connection FAILED<br /><br />";
          $dbSuccess = false;
        }   
      } else {
        echo "MySQL connection FAILED<br /><br />";
        $dbSuccess = false;
      }
}  

if ($dbSuccess) {
  
  $crunchiot_SQLselect = "SELECT ";
  $crunchiot_SQLselect .= "name, created, hidden, db ";
  $crunchiot_SQLselect .= "FROM "; 
  $crunchiot_SQLselect .= "iotCompaniesByName ";  
  $crunchiot_SQLselect .= "WHERE ";
  $crunchiot_SQLselect .= "created > '1285710235' ";
  $crunchiot_SQLselect .= "UNION ALL ";
  $crunchiot_SQLselect .= "SELECT ";     
  $crunchiot_SQLselect .= "name, created, hidden, db ";
  $crunchiot_SQLselect .= "FROM angeltestplus ";
  $crunchiot_SQLselect .= "WHERE ";
  $crunchiot_SQLselect .= "hidden = 'false' "; 
  $crunchiot_SQLselect .= "AND ";
  $crunchiot_SQLselect .= "created > '1285710235' ";
  $crunchiot_SQLselect .= "ORDER BY ";
  $crunchiot_SQLselect .= "`created` DESC ";
  
  $crunchiot_SQLselect_Query = mysql_query($crunchiot_SQLselect);   

for($indx = 1; $row = mysql_fetch_array($crunchiot_SQLselect_Query, MYSQL_ASSOC); $indx++) {
      if($indx<6) { continue; }
      $Name = $row['name'];
      $Created = $row['created'];
      $Db = $row['db'];
      
     if($indx<11){
      echo $indx." - ".$Name.": ".date("m-d-Y",$Created).", ".$Db."<br />";

      }
      
  }
  mysql_free_result($crunchiot_SQLselect_Query);    
}

?>
</div>


</div>

<div class="frosted2">

<a href="https://ifttt.com/recipes/239435-new-iot-company-notification"
      target = "_blank" 
      class="embed_recipe embed_recipe-l_28"
      width="310px"
      id="iftttlink">
<div id='iftttcontainer'><img id='iftttbutton'
      src='iotiftttbutton.png'></img></div>
</a>

<div id="iftttdesccontainer">
<p id="iftttdesc">Get an iOS notification when a new IoT company is formed</p>
<!--<div id="bydmcd">by DavidMcDoughnut</div>-->
    </div>
      
    
    <script async type="text/javascript" ></script>

</div>

<div class="frosted3">
  <div id="trendcharttitle">IOT Mentions per Month</div>
      <div id='updatedbotleft'> 
      <?php

$date = new DateTime(NOW);
  $date->setTimezone(new DateTimeZone('America/Los_Angeles'));
echo "Updated: ".$date->format('m-d-Y, H:i:s') . " PT"."\n";

?>

     </div>
<div id="googtrend"><script type="text/javascript" src="//www.google.com/trends/embed.js?hl=en-US&q=internet+of+things&tz&content=1&cid=TIMESERIES_GRAPH_0&export=5&w=690&h=340" fill="#000000"></script></div>
 </div> 

<div class="frosted4">

<div id="curvecharttitle">IOT Startups Created per Month</div>
    <div id='updatedbotright'> 
      <?php

$date = new DateTime(NOW);
  $date->setTimezone(new DateTimeZone('America/Los_Angeles'));
echo "Updated: ".$date->format('m-d-Y, H:i:s') . " PT"."\n";

?>

     </div>

<div id="curve_chart"></div>
</div>
</div>
</body>
<!--<div id="iftttcontainer">
<a href="https://ifttt.com/view_embed_recipe/239435-new-iot-company-notification" 
target = "_blank" 
class="embed_recipe embed_recipe-l_28" 
id= "iftttbutton2">
  <img src='iotiftttbutton.png'></img>
</a>
<script async type="text/javascript" src= "//ifttt.com/assets/embed_recipe.js"></script>
</div>
