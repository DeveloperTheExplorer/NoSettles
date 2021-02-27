<?php
  $signup_temp= '
    <div id="link-login"> 
      <div id="link-login-in" >
        <h2>You deserve better.</h2>
        <p style="margin-top: -15px;">Start your own fundraiser. Raise money and live the way you want to.</p>
        <a href="https://nosettles.com/" id="link-signup">I Am Ready</a>
      </div>
    </div>';
?>
<style type="text/css">
#link-login {
    width: 85%;
    height: 150px;
    margin: 0 auto;
    border: 4px dashed #0092F4;
}
#link-login-in {
    background-color: #0DB3FF;
	text-align: center;
    height: 92%;
    width: 98%;
    padding: 5px;
    margin-right: auto;
    margin-left: auto;
    margin-top: 2px;
    color: #fff;
}
#link-signup {
    display: inline-block;
    font-size: 26px;
    color: #fff;
    text-decoration: none;
    background-color: #09F;
    background-image: linear-gradient(rgba(0,0,0,0),rgba(0, 0, 0, 0.3));
    border-radius: 5px;
    border: 2px solid #1778C4;
    cursor: pointer;
    margin-top: -12px;
    padding: 13px;
    padding-right: 65px;
    padding-left: 65px;
    transition: all 0.5s ease;
}
#link-signup:hover {
    background-color: #27BEFF;
}
</style>
<?php echo $signup_temp; ?>
