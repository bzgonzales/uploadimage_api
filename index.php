<?php 
include 'config.php';
?>

<!DOCTYPE html> 
<!-- Defines types of documents : Html 5.O  -->
<!DOCTYPE html>
<!-- Defines types of documents : Html 5.O -->
<html lang="en">
    <!-- DEfines languages of content : English -->
    <head>
    <!-- Information about website and creator -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Defines the compatibility of version with browser -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- for make website responsive -->
    <meta name="author" content="Mr.X">
    <meta name="Linkedin profile" content="WWW.linkedin.com/Mr.X_123" >
    <!-- To give information about author or owner -->
    <meta name="description " 
    content="A better place to learn computer science">
    <!-- to explain about website in few words -->
    <title></title>
    <!-- Name of website or content to display -->
</head>
<body>
    <!-- Main content of website -->
    <div>
        <h1>Image Upload</h1>
        <form method="POST" action="" >
            <input type="file" name="imagefile" value="">
            <input type="text" name="title" value="">
            <select name="albums">
                <option value=""></option>
            </select>
            <textarea name="description"></textarea>
            <button type="submit">Submit</button>
        </form>
    </div>

</body>


</html>