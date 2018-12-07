<?php
date_default_timezone_set('America/Chicago');


//Start Here
$systemname='changeme'; //device name backed up - CHANGE 1st
$dirlocation='/mnt/backup/'; //local directory on rofnoc011a - /mnt/backup (\\nocnas01\nocbackup) - PICK THIS
//$dirlocation='/mnt/secbackup/'; //local directory on rofnoc011a - /mnt/secbackup (\\nocnas01\secbackup) - OR THIS
$comparedate=strtotime("24 hours ago"); //Change "days ago" - CHANGE 3rd - change day to be 1 day less then backup interval *example* days ago, weeks ago, hours ago, minutes ago, yesterday, last week


$dir = $dirlocation.$systemname;


directory_tree_expander($dir,$comparedate,$systemname);

function directory_tree_expander($address,$comparedate,&$systemname) {

@$dir = opendir($address);
$outofdate = 0;
$last_modified_newest = date("m-d-Y",$comparedate);
$locationaddress = $address;
$location = str_replace("/mnt/backup/","\\\\nocnas01\\nocbackup\\",$locationaddress); //USE THIS FOR NOCBACKUP FOLDER - this will swap the rofnoc011a location to network share location
//$location = str_replace("/mnt/secbackup/","\\\\nocnas01\\secbackup\\",$locationaddress); //USE THIS FOR SECBACKUP FOLDER

//Message can be anything inside before the ; just format how you want the email to look when sent. Varibles are pulled from higher up.
$message = "Hello,

Backup for ". $systemname ." is out of date.

Please check ". $location . " and verify the system is backing up correctly. (Username: nocnas01\\username)

Find ". $systemname ." in Password Manager Pro (PMP) and login into ". $systemname ." and verify backup is running.

Please create ticket for all issues with backups and assign to Tom or Kyle.

This is required for all Network maintained devices, please don't ignore this request.

Thanks,

NOC Oversight Operations Backup Support (NOOBS)

Should have been backed up on: " . $last_modified_newest;

  if(!$dir){ echo "Not a Directory"; }
        while($entry = readdir($dir)){
                if(is_dir("$address/$entry") && ($entry != ".." && $entry != "." && $entry != ".ssh")){
                        directory_tree_expander("$address/$entry",$comparedate);
                }
                 else   {

                  if($entry != ".." && $entry != "." && $entry != ".ssh" ) {

                    $fulldir=$address.'/'.$entry;
                    $last_modified = filemtime($fulldir);
                    $last_modified_str= date("Y-m-d h:i:s", $last_modified);

                       if($comparedate < $last_modified)  {
                          $outofdate++;
						  $last_modified_newest=$last_modified_str;
                       }

                 }

            }

      }


if ($outofdate < 1 )
{ mail('email1@email.com, email2@email.com, email3@email.com',"CALL TO ACTION: ".$systemname ." backup is out of date!", $message , null, "-fsendemail@email.com"); //sendemail has to be -f with no space before the send email.
}
echo $outofdate; echo "<BR>" . "Last Modified file: " . $last_modified_newest;
}
?>
