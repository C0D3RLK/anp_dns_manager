<?php
/*
* @author kanth raj 86kanth@gmail.com
* v1.0 27/ov/2023
*/
if (class_exists('GENERAL_CONFIGURATION')!=true) {
  require_once './config/general.php';
}

if (class_exists('DB_COMM')!=true) {
  require_once './config/db.php';
}


$DNS_MAN = new DB_COMM();
$DNS_MAN::GENERAL_VARIABLES();
if ($DNS_MAN->gen_check_db_data(false,"SELECT * FROM users") == true){

  #initiate installation
  #Create user table

  $DNS_MAN->redirect_route('login');

}else{
  // $GLOBALS['DB_CREATION_STATUS'] = ($DNS_MAN->initialise())?"Complete_":"Failed To Create, Check database/connection.";
  $GLOBALS['DB_CREATION_STATUS'] = "Complete_";
}


?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ANP-DNS Installation</title>
  <link href="./css/general.css" rel="stylesheet" id="bootstrap-css">
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
  <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@100;300;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/general.css">
  <style media="screen">
  /* * {
  margin: 0;
  padding: 0;
}

html {
height: 100%;
} */

/*Background color*/
#grad1 {
  /* background-color: : #9C27B0; */
  /* background-image: linear-gradient(120deg, #FF4081, #81D4FA); */
}

/*form styles*/
#msform {
  text-align: center;
  position: relative;
  margin-top: 20px;
}

#msform fieldset .form-card {
  background: white;
  border: 0 none;
  border-radius: 0px;
  box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.2);
  padding: 20px 40px 30px 40px;
  box-sizing: border-box;
  width: 94%;
  margin: 0 3% 20px 3%;

  /*stacking fieldsets above each other*/
  position: relative;
}

#msform fieldset {
  background: white;
  border: 0 none;
  border-radius: 0.5rem;
  box-sizing: border-box;
  width: 100%;
  margin: 0;
  padding-bottom: 20px;

  /*stacking fieldsets above each other*/
  position: relative;
}

/*Hide all except first fieldset*/
#msform fieldset:not(:first-of-type) {
  display: none;
}

#msform fieldset .form-card {
  text-align: left;
  color: #9E9E9E;
}

#msform input, #msform textarea {
  padding: 0px 8px 4px 8px;
  border: none;
  border-bottom: 1px solid #ccc;
  border-radius: 0px;
  margin-bottom: 25px;
  margin-top: 2px;
  width: 100%;
  box-sizing: border-box;
  font-family: montserrat;
  color: #2C3E50;
  font-size: 16px;
  letter-spacing: 1px;
}

#msform input:focus, #msform textarea:focus {
  -moz-box-shadow: none !important;
  -webkit-box-shadow: none !important;
  box-shadow: none !important;
  border: none;
  font-weight: bold;
  border-bottom: 2px solid skyblue;
  outline-width: 0;
}

/*Blue Buttons*/
#msform .action-button {
  width: 100px;
  background: #000 !important;
  font-weight: bold;
  color: white;
  border: 0 none;
  border-radius: 0px;
  cursor: pointer;
  padding: 10px 5px;
  margin: 10px 5px;
}

#msform .action-button:hover, #msform .action-button:focus {
  box-shadow: 0 0 0 2px white, 0 0 0 3px skyblue;
}

/*Previous Buttons*/
#msform .action-button-previous {
  width: 100px;
  background: #616161;
  font-weight: bold;
  color: white;
  border: 0 none;
  border-radius: 0px;
  cursor: pointer;
  padding: 10px 5px;
  margin: 10px 5px;
}

#msform .action-button-previous:hover, #msform .action-button-previous:focus {
  box-shadow: 0 0 0 2px white, 0 0 0 3px #616161;
}

/*Dropdown List Exp Date*/
select.list-dt {
  border: none;
  outline: 0;
  border-bottom: 1px solid #ccc;
  padding: 2px 5px 3px 5px;
  margin: 2px;
}

select.list-dt:focus {
  border-bottom: 2px solid skyblue;
}

/*The background card*/
.card {
  z-index: 0;
  border: none;
  border-radius: 0.5rem;
  position: relative;
  box-shadow: 0.1px 1px 0.7px grey;
}

/*FieldSet headings*/
.fs-title {
  font-size: 25px;
  color: #2C3E50;
  margin-bottom: 10px;
  font-weight: bold;
  text-align: left;
}

/*progressbar*/
#progressbar {
  margin-bottom: 30px;
  overflow: hidden;
  color: lightgrey;
}

#progressbar .active {
  color: #000000;
}

#progressbar li {
  list-style-type: none;
  font-size: 12px;
  width: 25%;
  float: left;
  position: relative;
}

/*Icons in the ProgressBar*/
#progressbar #account:before {
  font-family: FontAwesome;
  content: "\f023";
}

#progressbar #personal:before {
  font-family: FontAwesome;
  content: "\e07d";
}

#progressbar #payment:before {
  font-family: FontAwesome;
  content: "\f46d";
}

#progressbar #confirm:before {
  font-family: FontAwesome;
  content: "\f00c";
}

/*ProgressBar before any progress*/
#progressbar li:before {
  width: 50px;
  height: 50px;
  line-height: 45px;
  display: block;
  font-size: 18px;
  color: #ffffff;
  background: lightgray;
  border-radius: 50%;
  margin: 0 auto 10px auto;
  padding: 2px;
}

/*ProgressBar connectors*/
#progressbar li:after {
  content: '';
  width: 100%;
  height: 2px;
  background: lightgray;
  position: absolute;
  left: 0;
  top: 25px;
  z-index: -1;
}

/*Color number of the step and the connector before it*/
#progressbar li.active:before, #progressbar li.active:after {
  background: skyblue;
}

/*Imaged Radio Buttons*/
.radio-group {
  position: relative;
  margin-bottom: 25px;
}

.radio {
  display:inline-block;
  width: 204;
  height: 104;
  border-radius: 0;
  background: lightblue;
  box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.2);
  box-sizing: border-box;
  cursor:pointer;
  margin: 8px 2px;
}

.radio:hover {
  box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.3);
}

.radio.selected {
  box-shadow: 1px 1px 2px 2px rgba(0, 0, 0, 0.1);
}

/*Fit image in bootstrap div*/
.fit-image{
  width: 100%;
  object-fit: cover;
}

}
</style>
</head>
<body class="main-bg" type="sun-down">
  <!-- MultiStep Form -->
  <div class="container-fluid" id="grad1">
    <div class="row justify-content-center mt-0">
      <div class="col-11 col-sm-9 col-md-7 col-lg-6 text-center p-0 mt-3 mb-2">
        <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
          <h2><strong>Welcome To</strong> <br> <small>ANP-DNS Manager</small> </h2>
          <p>Let's setup your account</p>
          <div class="row">
            <div class="col-md-12 mx-0">
              <form id="msform">
                <!-- progressbar -->
                <ul id="progressbar">
                  <li class="active" id="account"><strong>Account</strong></li>
                  <li id="personal"><strong>Domain</strong></li>
                  <li id="payment"><strong>Confirmation</strong></li>
                  <li id="confirm"><strong>Finish</strong></li>
                </ul>
                <!-- fieldsets -->
                <fieldset id="f1">
                  <div class="form-card">
                    <h2 class="fs-title">Account Information</h2>
                    <input type="email" id="email" name="email" placeholder="Email"/>
                    <input type="text" id="name" name="name" placeholder="Name"/>
                    <input type="password" id="pwd" name="pwd" placeholder="Password"/>
                    <input type="password" id="password" name="password" placeholder="Confirm Password"/>
                    <small id="err_msg_f1" class="hidden text-danger h5 text-center">Fill All The Missing Fields</small>
                  </div>
                  <input type="button" data="f1" name="next" class="next action-button" value="Next Step"/>
                </fieldset>
                <fieldset id="f2">
                  <div class="form-card">
                    <h2 class="fs-title">Cloudflare Information</h2>
                    <input type="text" id="cloudfemail" name="cloudfemail" placeholder="Your Cloudflare Email"/>
                    <input type="text" id="cloudfdomain" name="cloudfdomain" placeholder="Your Domain Name"/>
                    <input type="text" id="cloudfapi" name="cloudfapi" placeholder="API Key X-Auth-Key"/>
                    <!-- <input type="text" name="phno_2" placeholder="Alternate Contact No."/> -->
                    <small id="err_msg_f2" class="hidden text-danger h5 text-center">Fill All The Missing Fields</small></br>
                    <small>Your are solely responsible for your data protection. We do not collect any of your domain credentials.</small>
                  </div>
                  <input type="button" name="previous" class="previous action-button-previous" value="Previous"/>
                  <input type="button" data="f2" name="next" class="next action-button" value="Next Step"/>
                </fieldset>
                <fieldset>
                  <pre>
                    <ul id="particulars" class="list-unstyled">

                    </ul>
                  </pre>
                  <!-- <input type="button" name="previous" class="previous action-button-previous" value="Previous"/> -->
                  <?php if ($GLOBALS['DB_CREATION_STATUS'] =="Complete_"): ?>
                    <input type="button" data="f3" name="previous" class="previous action-button-previous" value="Previous"/>
                    <input type="button" data="f3" name="complete" class="next action-button" value="Proceed"/>
                  <?php else: ?>
                    <a type="button"  class="btn btn-url action-button" href="/" value="Retry"/>Retry</a>
                  <?php endif; ?>
                </fieldset>
                <!-- <fieldset>
                <div class="form-card">
                <h2 class="fs-title">Payment Information</h2>
                <div class="radio-group">
                <div class='radio' data-value="credit"><img src="https://i.imgur.com/XzOzVHZ.jpg" width="200px" height="100px"></div>
                <div class='radio' data-value="paypal"><img src="https://i.imgur.com/jXjwZlj.jpg" width="200px" height="100px"></div>
                <br>
              </div>
              <label class="pay">Card Holder Name*</label>
              <input type="text" name="holdername" placeholder=""/>
              <div class="row">
              <div class="col-9">
              <label class="pay">Card Number*</label>
              <input type="text" name="cardno" placeholder=""/>
            </div>
            <div class="col-3">
            <label class="pay">CVC*</label>
            <input type="password" name="cvcpwd" placeholder="***"/>
          </div>
        </div>
        <div class="row">
        <div class="col-3">
        <label class="pay">Expiry Date*</label>
      </div>
      <div class="col-9">
      <select class="list-dt" id="month" name="expmonth">
      <option selected>Month</option>
      <option>January</option>
      <option>February</option>
      <option>March</option>
      <option>April</option>
      <option>May</option>
      <option>June</option>
      <option>July</option>
      <option>August</option>
      <option>September</option>
      <option>October</option>
      <option>November</option>
      <option>December</option>
    </select>
    <select class="list-dt" id="year" name="expyear">
    <option selected>Year</option>
  </select>
</div>
</div>
</div>
<input type="button" name="previous" class="previous action-button-previous" value="Previous"/>
<input type="button" name="make_payment" class="next action-button" value="Confirm"/>
</fieldset> -->
<fieldset>
  <div class="form-card">
    <h2 class="fs-title text-center">Success !</h2>
    <br><br>
    <div class="row justify-content-center">
      <div class="col-3">
        <div class="h1 text-center text-success">
          <i class="fa-solid fa-circle-check"></i>
        </div>
      </div>
    </div>
    <br><br>
    <div class="row justify-content-center">
      <div class="col-7 text-center">
        <h5>The Installation Will Begin</h5>
        <br>
        <small id="success_msg" class="hidden text-success h5 text-center"></small></br>

      </div>
    </div>
  </div>
</fieldset>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
<script type="text/javascript">
$(document).ready(function(){

  var current_fs, next_fs, previous_fs; //fieldsets
  var opacity;
  var counter  =0;
  $(".next").click(function(){

    current_fs = $(this).parent();
    next_fs = $(this).parent().next();


    section = $(this).attr('data');
    if (section == "f3"){
      current_f2 = $('#particulars').html()
      $('#particulars').empty();
      // $('#particulars').append(current_f2);
    }

    f1 = ["email","name","pwd","password"];
    if (section != "undefined" && section =="f1") {
      $('#particulars').empty();
      for (var i = 0; i < f1.length; i++) {
        if ($('#'+f1[i]).val() == "") {

          $('#'+f1[i]).addClass('input-error');
          $('#err_msg_'+section).removeClass('hidden');
          return false;
          break;
        }

        if (f1[i] != f1[2] && f1[i] != f1[3] ) {
          $('#particulars').append("<li>"+$('#'+f1[i]).attr('Placeholder')+": "+$('#'+f1[i]).val()+"</li>");
        }
      }
      if ($('#'+f1[2]).val() != $('#'+f1[3]).val() ) {
        $('#err_msg_'+section).empty();
        $('#err_msg_'+section).text('Passwords Are Not Matched');
        $('#err_msg_'+section).removeClass('hidden');
        return false;
      }
      current_f1 = $('#particulars').html()
    }

    f2 = ["cloudfemail","cloudfdomain","cloudfapi"];
    if (section != "undefined" && section =="f2") {

      $('#particulars').empty();
      $('#particulars').append(current_f1);

      for (var i = 0; i < f2.length; i++) {
        if ($('#'+f2[i]).val() == "") {

          $('#'+f2[i]).addClass('input-error');
          $('#err_msg_'+section).removeClass('hidden');
          return false;
          break;
        }

        $('#particulars').append("<li>"+$('#'+f2[i]).attr('Placeholder')+": "+$('#'+f2[i]).val()+"</li>");

      }


    }

    $('#err_msg_'+section).empty();
    $('#msform').attr('method',"post")
    $('#msform').attr('action',"./install-complete")
    //Add Class Active
    $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

    //show the next fieldset
    next_fs.show();
    //hide the current fieldset with style
    current_fs.animate({opacity: 0}, {
      step: function(now) {
        // for making fielset appear animation
        opacity = 1 - now;

        current_fs.css({
          'display': 'none',
          'position': 'relative'
        });
        next_fs.css({'opacity': opacity});
      },
      duration: 600
    });

    if (section != "undefined" && section =="f3") {

      var count = 5;

      $('#success_msg').removeClass('hidden');
      $('#success_msg').text('Please wait_('+count+")")

      var myTimer = setInterval(function(){
        if(count > 0){
          count = count - 1;
          $("span").text(count);
          $('#success_msg').text('Please wait_('+count+")")
        }
        else {
          clearInterval(myTimer);
          // alert("I'm done counting down!");
          $('#msform').submit();
        }
      },1000);


    }



  });

  $(".previous").click(function(){

    current_fs = $(this).parent();
    previous_fs = $(this).parent().prev();

    //Remove class active
    $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

    //show the previous fieldset
    previous_fs.show();

    //hide the current fieldset with style
    current_fs.animate({opacity: 0}, {
      step: function(now) {
        // for making fielset appear animation
        opacity = 1 - now;

        current_fs.css({
          'display': 'none',
          'position': 'relative'
        });
        previous_fs.css({'opacity': opacity});
      },
      duration: 600
    });
  });

  $('.radio-group .radio').click(function(){
    $(this).parent().find('.radio').removeClass('selected');
    $(this).addClass('selected');
  });

  $(".submit").click(function(){
    // return false;
    $('#msform').submit();
  })

});
</script>
</html>
