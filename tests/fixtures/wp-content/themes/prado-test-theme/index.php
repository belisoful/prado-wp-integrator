<?php
// Minimal stub WordPress theme index for tests.
language_attributes();
?>
<html>
<head profile="test">
<title><?php bloginfo('name'); ?></title>
<meta charset="<?php bloginfo('charset'); ?>" />
</head>
<body class="test-theme">
<div id="pre-form">PRE-FORM</div>
<?php get_header(); ?>
<wpcontent/>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
</body>
</html>
