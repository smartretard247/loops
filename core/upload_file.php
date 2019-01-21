<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    $category = filter_input(INPUT_POST, 'CategoryName');
    
    require_once 'include.php';
    UploadFile($category);
    
    $_SESSION['ImgName'] = '';
      
    header("location:../?action=view_upload_file");