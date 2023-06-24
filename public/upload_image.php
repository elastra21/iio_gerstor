<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
        $folder =  $_POST['path']; 
        // check if index document exist
        if(isset($_FILES['document'])){
                $folder =  $_POST['path']; 
                $document = $_FILES['document']['tmp_name'];
                $documentName = $_POST['pdfName']; 
                $path = "../archivos/$folder$documentName";
        }
        
        else {
                $document = $_FILES['image']['tmp_name'];
                $imageName = isset($_POST['imageName']) ? $_POST['imageName'] : date("d-m-Y") . "-" . time().".jpg";
                $path = "../archivos/$folder$imageName";
        } 
        
        move_uploaded_file($document, $path);
        echo "Successfully Uploaded";
}
else{
        echo "Error";
}
?>




