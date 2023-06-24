
<?php
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $files = $_POST['files'];
        $folder = $_POST['folder'];
        $someArray = json_decode($files, true);
        //print_r($someArray);
        //if file is not index.php then delete it
        print_r($folder);
        foreach ($someArray as $file) {
            $path = "../archivos/$folder/$file";
            if ($file == "index.php") {
                echo ("$path cannot be deleted due to an error"); 
            }
            else if (!unlink($path)) { 
                echo ("$path cannot be deleted due to an error"); 
            } 
            else { 
                echo ("$path has been deleted"); 
            }
        }
        
    }
?>