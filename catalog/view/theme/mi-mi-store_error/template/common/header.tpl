﻿<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">

<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/mi-mi-store/stylesheet/stylesheet.css" />
<link rel="stylesheet" media="all" href="catalog/view/theme/mi-mi-store/stylesheet/mobile.css"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />
<script type="text/javascript" src="catalog/view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="catalog/view/javascript/common.js"></script>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/mi-mi-store/stylesheet/ie7.css" />
<![endif]-->
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/mi-mi-store/stylesheet/ie6.css" />
<script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix('#logo img');
</script>
<![endif]-->
<?php echo $google_analytics; ?>
<link href='http://fonts.googleapis.com/css?family=Arvo:400,700,400italic' rel='stylesheet' type='text/css'>
<body>

<!-- begin category menu //-->
<div id="menu-area">
<div id="menu-wrapper">
 <?php if ($categories) { ?>
 <div id="menu">
  <ul>
    <?php foreach ($categories as $category) { ?>
    <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
      <?php if ($category['children']) { ?>
      <div>
        <?php for ($i = 0; $i < count($category['children']);) { ?>
        <ul>
          <?php $j = $i + ceil(count($category['children']) / $category['column']); ?>
          <?php for (; $i < $j; $i++) { ?>
          <?php if (isset($category['children'][$i])) { ?>
          <li><a href="<?php echo $category['children'][$i]['href']; ?>"> - <?php echo $category['children'][$i]['name']; ?></a></li>
          <?php } ?>
          <?php } ?>
        </ul>
        <?php } ?>
      </div>
      <?php } ?>
    </li>
    <?php } ?>
  </ul>
 </div>
</div>
</div>
<!-- end category menu //-->

<!-- begin header //-->
<div id="header-area">
<div id="header-wrapper">
 <div id="header">
  <?php if ($logo) { ?>
  <div id="logo"><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></div>
  <?php } ?>
  <?php echo $language; ?>
  <?php echo $currency; ?>
  <?php echo $cart; ?>
  <div id="search">
    <div class="button-search"></div>
    <?php if ($filter_name) { ?>
    <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" />
    <?php } else { ?>
    <input type="text" name="filter_name" value="<?php echo $text_search; ?>" onclick="this.value = '';" onkeydown="this.style.color = '#666666';" />
    <?php } ?>
  </div>
  <div id="welcome">
    <?php if (!$logged) { ?>
    <?php echo $text_welcome; ?>
    <?php } else { ?>
    <?php echo $text_logged; ?>
    <?php } ?>
  </div>
  <div class="links"><a href="<?php echo $home; ?>"><span class="text-home"><?php echo $text_home; ?></span></a><a href="<?php echo $wishlist; ?>" id="wishlist-total"><?php echo $text_wishlist; ?></a><a href="<?php echo $account; ?>"><span class="text-account"><?php echo $text_account; ?></span></a><a href="<?php echo $shopping_cart; ?>"><span class="text-shopping-cart"><?php echo $text_shopping_cart; ?></span></a><a href="<?php echo $checkout; ?>"><span class="text-checkout"><?php echo $text_checkout; ?></span></a></div>
  <div class="links-mobile"><a href="<?php echo $home; ?>"><span class="text-home"><?php echo $text_home; ?></span></a><a href="<?php echo $wishlist; ?>" id="wishlist-total"><?php echo $text_wishlist; ?></a><a href="<?php echo $account; ?>"><span class="text-account"><?php echo $text_account; ?></span></a><a href="<?php echo $shopping_cart; ?>"><span class="text-shopping-cart"><?php echo $text_shopping_cart; ?></span></a><a href="<?php echo $checkout; ?>"><span class="text-checkout"><?php echo $text_checkout; ?></span></a></div>
 </div>
</div>
</div>
<!-- end header //-->

<!-- begin content container //-->
<div id="container-wrapper">
<div id="container">

<!-- begin category menu-mobile //-->
 <div id="menu-mobile">
  <div id="menu-mobile-nav">
  <ul>
    <?php foreach ($categories as $category) { ?>
    <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
    <?php } ?>
  </ul>
  </div>
 </div>
<!-- end category menu-mobile //-->

<?php } ?>
<div id="notification"></div>

<!-- begin category menu-mobile JQUERY //-->
<script type="text/javascript">
jQuery(document).ready(function($){

	/* prepend menu icon */
	$('#menu-mobile').prepend('<div id="menu-mobile-icon"><img src="catalog/view/theme/mi-mi-store/image/menu-mobile-icon.png"/></div>');
	
	/* toggle nav */
	$("#menu-mobile-icon").on("click", function(){
		$("#menu-mobile-nav").slideToggle();
		$(this).toggleClass("active");
	});

});
</script>
<!-- end category menu-mobile JQUERY //-->
