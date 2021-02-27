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
#pageMiddle > h3 {
	color: #0BB2FF;  
}
#pageMiddle > h1 {
	color: #0BB2FF;  
}
#signupform{
	margin-top:24px;	
}
#signupform > div {
	margin-top: 12px;	
}
#signupform > input,select {
	width: 200px;
	padding: 3px;
	background: #F3F9DD;
}
#signupbtn {}
#terms {
	border:#CCC 1px solid;
	background: #F5F5F5;
	padding: 12px;
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


<div id="pageMiddle" style="margin: 0 auto;background-color: #fff;width: 60%;padding: 20px;margin-top: 20px;border-radius: 5px;border: 2px solid #ddd;">
  <h1>About Nosettles</h1>
  <h3>What is NoSettles?</h3>
  <p>NoSettles is a non-profit website where anybody can sign in without any payments. The use of this site is to raise money for what people want. Anybody can do it. You can easily post or share what you want or how much money you need for something that you are going to buy and simply gain donations from other people all over the world. For example if you want to buy a high end laptop but your budget is not enough you can simply calculate the amount of money you need and post it with others.</p>
  <h3>Getting started</h3>
  <p>To get started simply just fill in the form at the <a href="index.php">Home Page</a>. It will most likely take about 5 minutes to set up. After filling your information an email will be sent to the one that you wrote in the filling form and the only thing you will need to do is to click the link in the mail to verify your email. After doing so find something that you really want and post it to get donations.</p>
  <h3>How can I trust NoSettles?</h3>
  <p>There is nothing to worry about when it comes to your privacy and your credit card information, simply because the company that takes care of it is the well-known, trustworthy <a href="http://www.stripe.com">Stripe</a>. If you are not familiar with Stripe, you can just click on the name and visit their website and ask any questions that you might have from them. And for your privacy, our company is trying its best to provide the best security possible and to protect all of your personal material. To help us protect your information, NEVER post or share anything important including your PASSWORD, CREDIT INFORMATION or any other PERSONAL MATERIAL with anybody and please DO NOT violate the rules of the website. If you have any remaining concerns, we will be more than happy to answer them. To contact or ask any questions <a href="contact.php">click here.</a></p>
  <h3>Tips and tricks</h3>
  <p>For tips and tricks on how to gain donations faster <a href="tips.php">click here.</a></p>
  <h3>Pricing and Fees</h3>
  <p>NoSettles is proud to present its fees to you.<a href="fees.php">click here.</a></p>
  <?php include_once("template_SignUp.php"); ?>
</div>
<div id="pageBottom" style="margin-top: 8%;">NoSettles Copyright &copy; 2015</div>
</body>
</html>