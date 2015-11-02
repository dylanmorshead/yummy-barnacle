<?php
include("config.php");
include("header.php");



error_reporting(0);
//get data
$button = $_GET['submit'];
$search = $_GET['search'];


$s = $_GET['s'];
if (!$s)
$s = 0;


$e = 10; // Just change to how many results you want per page


$next = $s + $e;
$prev = $s - $e;




 if (strlen($search)<=2)
  echo "Must be greater then 3 chars";
 else
 {
  echo "<br /><table><tr><td></td><td><form action='search.php' method='GET'><h2>$project - Search Videos</h2><br> <input type='text' onclick=value='' size='50' name='search' value='$search'> <input type='submit' name='submit' value='Search'></form></td></tr></table>";
  
  //connect to database
  mysql_connect("localhost","root","");
  mysql_select_db("videos");
   
   //explode out search term
   $search_exploded = explode(" ",$search);
   
   foreach($search_exploded as $search_each)
   {
   
        //construct query
    $x++;
    if ($x==1)
     $construct .= "keywords LIKE '%$search_each%'";
    else
     $construct .= " OR keywords LIKE '%$search_each%'";
   
   }
   
  //echo outconstruct
  $constructx = "SELECT * FROM videos WHERE $construct";
  
  $construct = "SELECT * FROM videos WHERE $construct LIMIT $s,$e";
  $run = mysql_query($constructx);
  
  $foundnum = mysql_num_rows($run);


  $run_two = mysql_query("$construct");
  
  if ($foundnum==0)
   echo "No results found for <b>$search</b>";
  else
  {
   echo "<table bgcolor='#0000FF' width='100%' height='1px'><br /></table><table bgcolor='#f0f7f9' width='100%' height='10px'><tr><td><div align='right'>Showing 1-10 of <b>$foundnum</b> results found for <b>$search.</b></div></td></tr></table><p>";
   
   while ($runrows = mysql_fetch_assoc($run_two))
   {
echo "<center>";
    //get data
   $title = $runrows['video'];
   $desc = $runrows['description'];
   $filename = $runrows['filename'];
   $uploaded = $runrows['username'];
		// fancy formating <3
		echo "<table border='1'>";
  		echo "<tr>";
    		echo "<th>Video Name - $title </th>";
  		echo "</tr>";
  		echo "<tr>";
   		echo '<td rowspan = "2">';
 		printf("<a href = './video.php?video=./videos/$filename'><video width = '500' controls> <source src = './videos/$filename'  type='
		video/mp4'>");
		//printf("Your browser does not support HTML5 video.");
		//printf("</video></a>");
 		 echo 'Your browser does not support the video tag.
		</video></a></td></div>';
		echo "</tr>";
		echo "<tr>";
		echo "</tr>";
		echo "<td>Uploaded By: " .   "<a href='./user.php?user=$uploaded'>$uploaded</a></td>";
		echo "<tr>";
		echo "<td>Description:" . "<br>". $desc . "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		echo "<br>";
		echo "</center>";
   }
?>

<table width='100%'>
<tr>
<td>
<div align="center">

<?php
if (!$s<=0)
 echo "<a href='search.php?search=$search&s=$prev'>Prev</a>";

$i =1; 
for ($x=0;$x<$foundnum;$x=$x+$e)
{


 echo " <a href='search.php?search=$search&s=$x'>$i</a> ";


$i++;


}

if ($s<$foundnum-$e)
  echo "<a href='search.php?search=$search&s=$next'>Next</a>";

	}
}  


?>
<center>
<br>
<br>
<?php
	include("footer.php");
?>
</center>
</table>
</div>
</td>
</tr>
