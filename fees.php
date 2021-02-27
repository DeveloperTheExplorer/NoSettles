<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>NoSettles</title>
  <meta name="viewport" content="initial-scale=1.0,width=device-width" />
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageTop1.css" />
  <link rel="stylesheet" href="style/pageTop3.css" media="only screen and (max-width: 700px)" />
  <link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageMiddle1.css" />
  <link rel="stylesheet" media="only screen and (max-width: 1250px)" href="style/pageMiddle2.css" />
  <link rel="stylesheet" media="only screen and (max-width: 700px)" href="style/pageMiddle3.css" />
  <link rel="icon" href="style/tabicon.png" type="image/x-icon" />
  <link rel="shortcut icon" href="style/tabicon.png" type="image/x-icon" />
  <style type="text/css">
#pageMiddle > h3{
	color: #0BB2FF;  
}
#pageMiddle > h2{
	color: #0BB2FF;  
}
#pageMiddle > h1{
	color: #0BB2FF;  
}
#pageMiddle > h4{
	color: #0BB2FF;  
}
.fees {
  color: #ddd;
  font-size: 40px;
}
</style>
  <script src="js/main.js"></script>
  <script src="js/Ajax.js"></script>
  <script src="js/autoScroll.js"></script>
  <script src="js/fade.js"></script>
  <script src="js/trBackground.js"></script>
  <script src="js/trDrop.js"></script>
  <script src="js/trSlide.js"></script>
</head>
<body style="background-color: #eee;">
<?php include_once("template_PageTop.php"); ?>

<div id="pageMiddle" style="margin: 0 auto;background-color: #fff;width: 60%;padding: 20px;margin-top: 20px;border-radius: 5px;border: 2px solid #ddd; text-align:center;">
<h1>Fees </h1>
<h2>NoSettles is proud to build a website with absolutely <b>no</b> fees of its own.</h2>
<h3>We currently have the lowest fees from any other crowdfunding website.</h3>
<h4>We are the only crowdfunding website that takes no fees from its users.</h4>
<div class="fees_p"><p class="fees">0% + 0¢</p> <img src="style/Logo.png" alt="NoSettles" style="height:100px;margin-top:-35px;" /> </div>
<p class="fees"> + </p>
<div class="fees_p"><p class="fees">2.9% + 30¢</p> <img src="style/Stripe.png" alt="Stripe" style="height: 70px;margin-top:-35px;" /> </div> 
<p class="fees"> = </p>
<div class="fees_p"><p class="fees">2.9% + 30¢</p> <p style="font-size: 50px;color: #4B92DC; margin-top:-20px;">Total</p></div>

<?php include_once("template_SignUp.php"); ?>
	     	
</div>
<div id="pageBottom" style="margin-top: 3%;">NoSettles Copyright &copy; 2015</div>
</body>
</html>