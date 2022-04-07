<!DOCTYPE HTML>
<html>

<head>
    <title>My Blog</title>
    <meta name="description" content="website description" />
    <meta name="keywords" content="website keywords, website keywords" />
    <meta http-equiv="content-type" content="text/html; charset=windows-1252" />
    <link rel="shortcut icon" href="#">
    <link rel="stylesheet" type="text/css" href="<?php echo U('style.css');?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo U('bootstrap/css/bootstrap-theme.min.css','component');?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo U('bootstrap/css/bootstrap.min.css','component');?>" />
    <script src="<?php echo U('jquery-2.1.1.min.js');?>"></script>
</head>

<body>
<div id="main">
    <div id="header">
        <div id="logo">
            <h1><a href="<?php echo U();?>">My Blog</a></h1>
            <div class="slogan">Machines was born to be guided!</div>
        </div>
        <div id="menubar">
            <ul id="menu">
                <?php echo $menu;?>
            </ul>
        </div>
    </div>

	<?php if (isset($content)) echo $content;?>

    <div id="footer">
        <p>Copyright &copy; Al- Imran Ahmed</p>
    </div>
</div>
<script src="<?php echo U('bootstrap/js/bootstrap.min.js','component');?>"></script>
</body>
</html>

