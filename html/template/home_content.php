<?php

//JAN 22,2024

#prevent push on enable
if (isset($_POST['no_auto_push']) && $_POST['no_auto_push'] == 'false') {
  $_SESSION['no_auto_push'] = false;
}
if (isset($_POST['no_auto_push']) && $_POST['no_auto_push'] == 'true') {
  $_SESSION['no_auto_push'] = true;
}

if (!isset($_SESSION['no_auto_push'])) {
  $_SESSION['no_auto_push'] = false;
}


#force dns update

if (isset($_POST) && array_search("a518f6f0b589d6e7dba07266d3b27981",$_POST)==true) {

  $UPDATE_DATA = $_POST;
  $UPDATE_DATA_ID = $_POST[array_keys($UPDATE_DATA)[1]];
  $DNS_MAN::set_alert('info', "Success!", $INFO = " Subdomain <b>Update</b> Sent Successfully.  <small>It will take a minute for changes to take effect.</small>" );

  $DOMAIN_INFO = $DNS_MAN->get_domain_info(base64_decode($UPDATE_DATA_ID));

  #update pool
  $POOL_DATA = "'".$_SESSION['U_CLOUDFD_DOMAIN']."','".$DOMAIN_INFO['subdomain']."','".$DNS_MAN::get_user_tag()."','0'";
  $POOL_COL  = "domain,subdomain,user_tag,status";
  $DNS_MAN->create_entry('new_entry_pool',$POOL_COL,$POOL_DATA);


}

#save user details

if (isset($_POST['change_user_details'])) {
  $UPDATE_DATA = $_POST;
  $COUNTER = 2;
  if ($_POST['password'] =="") {
    unset($UPDATE_DATA['password']);
    unset($_POST['password']);
    $DATA_USERS = " name='".$_POST['name']."'";

  }else{
    $PASSWORD = base64_encode(md5($_POST['password']));
    unset($UPDATE_DATA['password']);
    unset($_POST['password']);
    $COUNTER = 1;
    $UPDATE_DATA['password'] = $PASSWORD ;
    $_POST['password'] = $PASSWORD ;
    $DATA_USERS = " name='".$_POST['name']."',password='".$_POST['password']."'";

  }

  $DATA_DOMAIN = " cloudfapi='".$_POST['cloudfapi']."',cloudfemail='".$_POST['cloudfemail']."'";

  $UPDATE_USERS = $DNS_MAN->update_db_data('users',"",$DATA_USERS,'id',base64_decode($_POST['change_user_details']),false);
  $UPDATE_DOMAINS = $DNS_MAN->update_db_data('domains',"",$DATA_DOMAIN,'user_tag',$_POST['user_tag'],false);

  if ($UPDATE_USERS == true && $UPDATE_DOMAINS == true ){
    $DNS_MAN::set_alert('success', "Completed!", $INFO = " Profile Info <b>Updated</b> Successfully." );

    $_SESSION['U_NAME'] = $_POST['name'];
    $_SESSION['U_CLOUDF_EMAIL'] = $_POST['cloudfemail'];
    $_SESSION['U_CLOUDAPI'] = $_POST['cloudfapi'];
    #for future update
    // $_SESSION['U_CLOUDFD_DOMAIN'] = $_POST['cloudfdomain'];

  }
}

#edit
if (isset($_POST['name']) && !isset($_POST['new_dns_entry']) && !isset($_POST['change_user_details']) ) {

  $UPDATE_DATA = $_POST;
  $UPDATE_DATA_ID = $_POST['identifier'];


  for ($i=0; $i < COUNT(array_keys($UPDATE_DATA)) ; $i++) {
    if (array_keys($UPDATE_DATA)[$i] != 'identifier' ){

      if ($i == COUNT(array_keys($UPDATE_DATA)) - 1) {
        $DATA = $DATA . "".array_keys($UPDATE_DATA)[$i]."='".str_replace(".".$_SESSION['U_CLOUDFD_DOMAIN'],"",$_POST[array_keys($UPDATE_DATA)[$i]])."'";
      }else{
        $DATA = $DATA . "".array_keys($UPDATE_DATA)[$i]."='".str_replace(".".$_SESSION['U_CLOUDFD_DOMAIN'],"",$_POST[array_keys($UPDATE_DATA)[$i]])."',";
      }

    }
  }

  $DNS_MAN->update_db_data('subdomains',false,$DATA,'id',base64_decode($_POST['identifier']));
  $DNS_MAN::set_alert('success', "Success!", $INFO = " Info <b>Updated</b> Successfully." );

}

#save
if (isset($_POST['new_dns_entry'])){

  $NEW_DATA = $_POST;
  $NEW_DATA_ID = $_POST['identifier'];
  $NEW_DATA['status'] = "0";
  $_POST['status'] = "0";
  if (!isset($_POST['proxy'])) {
    $NEW_DATA['proxy'] = "0";
    $_POST['proxy'] = "0";
  }

  for ($i=0; $i < COUNT(array_keys($NEW_DATA)) ; $i++) {
    if (array_keys($_POST)[$i] != 'new_dns_entry' ){
      $DATA[] = $_POST[array_keys($NEW_DATA)[$i]];
      $COLUMN[] =array_keys($NEW_DATA)[$i];
    }
  }

  $COLUMN_SET = implode(',', $COLUMN);

  for ($i=0; $i < COUNT($DATA) ; $i++) {
    if ($i == COUNT($DATA)-1) {
      $DATA_SET = $DATA_SET . "'".str_replace(".".$_SESSION['U_CLOUDFD_DOMAIN'],"",$DATA[$i])."'";
    }else{
      $DATA_SET = $DATA_SET . "'".str_replace(".".$_SESSION['U_CLOUDFD_DOMAIN'],"",$DATA[$i])."',";

    }
  }


  $DNS_MAN->create_entry('subdomains',$COLUMN_SET,$DATA_SET);
  $DNS_MAN::set_alert('success', "Success!", $INFO = " Subdomain <b>Created</b> Successfully." );


}

#remove

if (isset($_POST) && array_search("44baca8fbc2f0ae2e9c74ebadf93bf05",$_POST)==true) {
  $DEL_DATA = $_POST;
  $DATA_ID = $_POST[array_keys($DEL_DATA)[1]];

  $QUERY = " `subdomains` WHERE `id` ='".base64_decode($DATA_ID)."'";
  $QUERY_CLEAR_HISTORY = "update_tracker WHERE domain='".$_POST['subdomain']."'";

  $DNS_MAN->remove_entry($QUERY);
  $DNS_MAN->remove_entry($QUERY_CLEAR_HISTORY);

  $DNS_MAN::set_alert('warning', "Completed!", $INFO = " Subdomain <b>Removed</b> Successfully." );


}


#disable

if (isset($_POST) && array_search("f0cb3950e8ea148ff66ab09dfc69204e",$_POST)==true) {
  $DISABLE_DATA = $_POST;
  $DISABLE_DATA_ID = $_POST[array_keys($DISABLE_DATA)[1]];

  $DNS_MAN->update_db_data('subdomains','status','0','id',base64_decode($DISABLE_DATA_ID),true);

  $DNS_MAN::set_alert('info', "Completed!", $INFO = " Subdomain <b>Disabled</b> Successfully." );


}


#ENABLE

if (isset($_POST) && array_search("d97bf40cb6dd3a6e1414ac287f240c6b",$_POST)==true) {
  $ENABLE_DNS = $_POST;
  $ENABLE_DNS_ID = $_POST[array_keys($ENABLE_DNS)[1]];

  $DNS_MAN->update_db_data('subdomains','status','1','id',base64_decode($ENABLE_DNS_ID),true);
  $DNS_MAN::set_alert('success', "Success!", $INFO = " Subdomain <b>Enabled</b> Successfully." );

  $DOMAIN_INFO = $DNS_MAN->get_domain_info(base64_decode($ENABLE_DNS_ID));

  #update pool
  if (  $_SESSION['no_auto_push'] == false) {
    $POOL_DATA = "'".$_SESSION['U_CLOUDFD_DOMAIN']."','".$DOMAIN_INFO['subdomain']."','".$DNS_MAN::get_user_tag()."','0'";
    $POOL_COL  = "domain,subdomain,user_tag,status";
    $DNS_MAN->create_entry('new_entry_pool',$POOL_COL,$POOL_DATA);
  }

}


#list all dns
#without sorting list new to old
//$DNS_DATA = $DNS_MAN->gen_get_db_data('subdomains',true);

#with sorting list new to old
$DNS_DATA = $DNS_MAN->gen_get_db_data('subdomains',"id > 0 Order by id DESC");
?>

<style media="screen">
/* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #334254;
}

input:focus + .slider {
  box-shadow: 0 0 1px #334254;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.toast-location{
  position: absolute;
  top: 0;
  right:0;
  /* zIndex: -100; */
  float: right;
  top: 5vw;
  right: 1vw;
}
</style>



<script type="text/javascript">
function dns_del_confirm(RN,VAL){
  if(VAL == 'DELETE') { $("#frm_delete_dns"+RN).submit(); }
  $('#delete_dns_modal').modal('show');
  $('#dns_process').text(VAL);
  $('#dns_delete_btn').attr('onclick','dns_del_confirm("'+RN+'","DELETE");');
}
function dns_disable_confirm(RN,VAL){
  if(VAL == 'DISABLE') { $("#frm_disable_dns"+RN).submit(); }
  $('#disable_dns_modal').modal('show');
  $('#dns_process_disable').text(VAL);
  $('#dns_disable_btn').attr('onclick','dns_disable_confirm("'+RN+'","DISABLE");');
}
function dns_enable_confirm(RN,VAL){
  if(VAL == 'ENABLE') { $("#frm_enable_dns"+RN).submit(); }
  $('#enable_dns_modal').modal('show');
  $('#dns_process_enable').text(VAL);
  $('#dns_enable_btn').attr('onclick','dns_enable_confirm("'+RN+'","ENABLE");');
}  // 2404d906cee8c7ecd1e1be9a4eb9afe3

function dns_update_confirm(RN,VAL){
  if(VAL == 'UPDATE') { $("#frm_update_dns"+RN).submit(); }
  $('#update_dns_modal').modal('show');
  $('#dns_process_update').text(VAL);
  $('#dns_update_btn').attr('onclick','dns_update_confirm("'+RN+'","UPDATE");');
}  // a518f6f0b589d6e7dba07266d3b27981


</script>


<form id="edit_form" class="hidden" action="/home" method="post">
</form>

<?php if ($_SESSION['ALERT_MSG'] == true): ?>
  <div id="alert_msg" class="alert alert-<?php echo $_SESSION['ALERT_TYPE']; ?> alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><?php echo $_SESSION['ALERT_TITLE']; ?></strong> <?php echo $_SESSION['ALERT_INFO']; ?>
  </div>
<?php endif; $_SESSION['ALERT_MSG'] = false;?>

<div class="table-responsive  overflow-y">
  <table class="table ">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Subdomain</th>
        <th>Description</th>
        <th>Proxy</th>
        <th>Status</th>
        <th>Action</th>
        <!-- <th>Status</th> -->

      </tr>
    </thead>
    <tbody>
      <tr >
        <form id="new_dns_entry" class="" action="/home" method="post">
          <input type="text" class="hidden" name="new_dns_entry" value="true">
          <td></td>
          <td> <input placeholder="Example" type="text" class="form-control" name="name" value=""> </td>
          <td> <input placeholder="Only Subdomain" type="text" class="form-control" name="subdomain" value=""> </td>
          <td> <input placeholder="Remark" type="text" class="form-control" name="description" value=""> </td>
          <td><input onclick="checkbox_val('','0');" class="form-control" type="checkbox" id="proxy" name="proxy" value="0"> <input type="text" class ="hidden" name="user_tag" value="<?php echo $DNS_MAN::get_user_tag(); ?>"> </td>
          <td> <button data-toggle="tooltip" data-placement="top" title="Create" class="btn btn-success btn-sm" type="submit" value="save"><i class="fas fa-plus-circle"></i></button> </td>
          <td></td>
        </form>
      </tr>

      <?php if ($DNS_DATA == NULL): ?>
        <tr>
          <td></td>
          <td><h3 class="text-success">You Have No Domains</h3></td>
          <td><h3 class="text-success">Time To Setup One</h3></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      <?php endif; ?>

      <?php if ($DNS_DATA != NULL): ?>

        <?php for ($i=0; $i < COUNT($DNS_DATA); $i++): ?>
          <tr class="data-hover  <?php if ($DNS_DATA[$i]['status'] == 0) { echo 'bg-warning-2';} ?>">
            <td><?php echo $i+1; ?></td>
            <div id="edit_items<?php echo$i; ?>" onclick="start_edit('<?php echo $i; ?>','<?php echo $DNS_DATA[$i]['proxy']; ?>');" >
              <td id="dns_name<?php echo $i; ?>" onclick="start_edit('<?php echo $i; ?>','<?php echo $DNS_DATA[$i]['proxy']; ?>');" ><?php echo $DNS_DATA[$i]['name'];  ?></td>
              <td id="dns_subdomain<?php echo $i; ?>" onclick="start_edit('<?php echo $i; ?>','<?php echo $DNS_DATA[$i]['proxy']; ?>');" ><?php echo $DNS_DATA[$i]['subdomain'].".".$_SESSION['U_CLOUDFD_DOMAIN'];  ?></td>
              <td id="dns_description<?php echo $i; ?>" onclick="start_edit('<?php echo $i; ?>','<?php echo $DNS_DATA[$i]['proxy']; ?>');" ><?php echo $DNS_DATA[$i]['description'];  ?></td>
              <td id="dns_proxy<?php echo $i; ?>" onclick="start_edit('<?php echo $i; ?>','<?php echo $DNS_DATA[$i]['proxy']; ?>');" ><?php $PROXY_STAT = ($DNS_DATA[$i]['proxy'] == '1')? '<i class="fas fa-cloud text-success"></i> Proxied'  : '<i class="fas fa-cloud text-muted"></i> Direct'; echo $PROXY_STAT;  ?></td>
            </div>
            <td><?php $STATUS_MSG =  ($DNS_DATA[$i]['status'] == '1')? '<i class="fas fa-play-circle text-success"></i> Enabled' : '<i class="fas fa-pause-circle text-warning"></i> Disabled'; echo $STATUS_MSG;  ?></td>
            <td>
              <ul class="list-inline ">
                <li class="list-inline-item">
                  <a data-toggle="tooltip" data-placement="top" title="Save" class="btn btn-success btn-sm" href="#" onclick="apply_changes('<?php echo $i; ?>','<?php echo base64_encode($DNS_DATA[$i]['id']); ?>');"><i class="fas fa-save"></i></a>
                </li>
                <li class="list-inline-item">
                  <?php if ($DNS_DATA[$i]['status'] == 1) { ?>
                    <form  id="frm_disable_dns<?php echo $i ?>" name="frm_disable_dns<?php echo $i ?>" action="<?php echo "/home"; ?>" method="post" >
                      <!-- <button title="disable" data-toggle="tooltip" data-placement="top" class="btn btn-sm"><i class="fas fa-hand-paper text-warning"></i></button> -->
                      <input type="text" name="REQUEST_P<?php echo $i ?>" value="<?php echo md5(base64_encode('DISABLE_DNS')); ?>" hidden>
                      <input type="text" name="ps_data_tag<?php echo $i ?>" value="<?php echo base64_encode($DNS_DATA[$i]['id']); ?>" hidden>
                    </form>
                    <button onclick="dns_disable_confirm('<?php echo $i ?>','<?php echo $DNS_DATA[$i]['name']; ?>');" title="Disable" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-warning"><i class="fas fa-hand-paper"></i></button>
                  <?php }
                  if ($DNS_DATA[$i]['status'] == 0) { ?>
                    <form  id="frm_enable_dns<?php echo $i ?>" name="frm_enable_dns<?php echo $i ?>" action="<?php echo "/home"; ?>" method="post" >
                      <!-- <button title="enable" data-toggle="tooltip" data-placement="top" class="btn btn-sm"><i class="fas fa-step-forward text-info"></i></button> -->
                      <input type="text" name="REQUEST_R<?php echo $i ?>" value="<?php echo md5(base64_encode('ENABLE_DNS')); ?>" hidden>
                      <input type="text" name="rsm_data_tag<?php echo $i ?>" value="<?php echo base64_encode($DNS_DATA[$i]['id']); ?>" hidden>
                    </form>
                    <button onclick="dns_enable_confirm('<?php echo $i ?>','<?php echo $DNS_DATA[$i]['name']; ?>');" title="Enable" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-info"><i class="fas fa-step-forward"></i></button>
                    <?php
                  }
                  ?>
                </li class="list-inline-item">
                <li class="list-inline-item"><form  id="frm_delete_dns<?php echo $i ?>" name="frm_delete_dns<?php echo $i ?>" action="<?php echo "/home"; ?>" method="post" >
                  <input type="text" name="REQUEST<?php echo $i ?>" value="<?php echo md5(base64_encode('DELETE_DNS')); ?>" hidden>
                  <input type="text" name="del_data_tag<?php echo $i ?>" value="<?php echo base64_encode($DNS_DATA[$i]['id']); ?>" hidden>
                  <input type="text" name="subdomain" value="<?php echo $DNS_DATA[$i]['subdomain']; ?>" hidden>
                  <!-- <button type="submit" class="btn btn-url btn-sm" title="Delete" data-toggle="tooltip" data-placement="right" ><i class="fas fa-times text-danger"></i></button> -->
                </form>
                <button data-toggle="tooltip" data-placement="top" title="Remove" onclick="dns_del_confirm('<?php echo $i ?>','<?php echo $DNS_DATA[$i]['name']; ?>');" class="btn btn-danger btn-sm" title="Remove" data-toggle="tooltip" data-placement="right" ><i class="fas fa-times"></i></button>
              </li>

            </ul>
            <ul class="list-inline ">
              <?php  if ($DNS_DATA[$i]['status'] == 1): ?>
                <li  class="list-inline-item">
                  <form  id="frm_update_dns<?php echo $i ?>" name="frm_update_dns<?php echo $i ?>" action="<?php echo "/home"; ?>" method="post" >
                    <!-- <button title="enable" data-toggle="tooltip" data-placement="top" class="btn btn-sm"><i class="fas fa-step-forward text-info"></i></button> -->
                    <input type="text" name="REQUEST_R<?php echo $i ?>" value="<?php echo md5(base64_encode('UPDATE_DNS')); ?>" hidden>
                    <input type="text" name="rsm_data_tag<?php echo $i ?>" value="<?php echo base64_encode($DNS_DATA[$i]['id']); ?>" hidden>
                  </form>
                  <button data-toggle="tooltip" data-placement="bottom" title="Update" onclick="dns_update_confirm('<?php echo $i ?>','<?php echo $DNS_DATA[$i]['name']; ?>');" type="button" name="button" class="btn btn-sm btn-info">
                    <i class="fas fa-cloud-upload-alt"></i>
                  </button>
                </li>
              <?php endif; ?>
              <li class="list-inline-item"> <button id="<?php echo $i; ?>" onclick="show_status(this.id,'graphic');" data-toggle="tooltip" data-placement="bottom" title="Status" type="button" class="btn btn-primary btn-sm" name="button"> <i class="fa-solid fa-clock-rotate-left"></i></button></li>
            </ul>


          </td>
        </tr>
      <?php endfor; ?>
    <?php endif; ?>
  </tbody>
</table>

</div>

<small>Note: Newly added domains needs to be enabled, for it to run.</small>


<form class="hidden" id="open_form">
</form>


<script type="text/javascript">

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

function apply_changes(RN,OTHR){
  FIELDS = ["name","subdomain","description","proxy"];
  INPUTS = ["name","subdomain","description","proxy"];

  for (var i = 0; i < FIELDS.length; i++) {

    B4 =  $("#"+FIELDS[i]+RN).val();

    if (B4 != undefined) {

      $('<input>').attr({
        type: 'text',
        hidden: 'true',
        class: "form-control",
        id: INPUTS[i],
        name: INPUTS[i],
        // class: 'form-control',
        value: B4
        // }).appendTo('#'+FIELDS[i]+RN);
      }).appendTo('#edit_form');



      $('<input>').attr({
        type: 'text',
        hidden: 'true',
        class: "form-control",
        id: 'identifier',
        name: 'identifier',
        // class: 'form-control',
        value: OTHR
        // }).appendTo('#'+FIELDS[i]+RN);
      }).appendTo('#edit_form');

      $('#edit_form').submit();
      // });
    }else{
      return false;
      break;
    }

  }

}


function  start_edit(RN,OTHR){

  FIELDS = ["dns_name","dns_subdomain","dns_description"];
  INPUTS = ["name","subdomain","description"];
  // CONSTRUCTFIELD = $('#edit_items'+RN);

  for (var i = 0; i < FIELDS.length; i++) {
    B4 =  $("#"+FIELDS[i]+RN).text().replace('.<?php echo $_SESSION['U_CLOUDFD_DOMAIN']; ?>','');
    REM_ELEMENT = $("#"+FIELDS[i]+RN).empty();
    REM_ELEMENT = $("#"+FIELDS[i]+RN).html("");

    $('<input>').attr({
      type: 'text',
      //hidden: 'true',
      class: "form-control",
      id: INPUTS[i]+RN,
      name: INPUTS[i]+RN,
      // class: 'form-control',
      value: B4
      // }).appendTo('#'+FIELDS[i]+RN);
    }).appendTo('#'+FIELDS[i]+RN);
  }


  // REM_ELEMENT = $("#proxy"+RN).empty();
  REM_ELEMENT = $("#dns_proxy"+RN).html("");

  SET_VAL = false;
  if (OTHR == '1') {
    SET_VAL = true;
  }

  $('<input>').attr({
    type: 'checkbox',
    //hidden: 'true',
    class: "form-control",
    id: "proxy"+RN,
    name: "proxy"+RN,
    // class: 'form-control',
    checked: SET_VAL,
    value: OTHR,
    onchange: "checkbox_val('"+RN+"','"+OTHR+"');"
    // }).appendTo('#'+FIELDS[i]+RN);
  }).appendTo('#dns_proxy'+RN);

  for (var i = 0; i < FIELDS.length; i++) {
    $("#"+FIELDS[i]+RN).removeAttr('onclick');
  }
  $("#dns_proxy"+RN).removeAttr('onclick');


}

function checkbox_val(RN,OTHR){

  ELEMENT = "proxy"+RN;

  SELECT_ELEMENT = $('#'+ELEMENT);
  ELEMENT_VAL = SELECT_ELEMENT.val();

  if (ELEMENT_VAL == '0') {
    SELECT_ELEMENT.val("1");
  }else{
    SELECT_ELEMENT.val("0");
  }
  // SELECT_ELEMENT.change();
  ELEMENT_VAL = SELECT_ELEMENT.val();


}


function edit_dns(RN,DID){

  CURRENT_TYPE_FIELD = $("#proxy"+RN).text();
  if (CURRENT_TYPE_FIELD == "BASH" || CURRENT_TYPE_FIELD == "#BASH" ) {NC_TYPE = "0";}
  if (CURRENT_TYPE_FIELD == "PHP" ) {NC_TYPE = "1";}
  if (CURRENT_TYPE_FIELD == "ECHO") {NC_TYPE = "2";}

  FIELDS = ["list_time","list_file","list_remark"];
  INPUTS = ["edt_time","edt_file","edt_remark"];
  var form_edit = $('<form>');
  form_edit.append(form_edit.attr({
    method: "post",
    action: "<?php echo './'.basename(__FILE__); ?>",
    id: "edt_form"+RN,
    name: "edt_form"+RN
    // display: 'none'
  })).appendTo("#list_item_head"+RN);

  for (var i = 0; i < FIELDS.length; i++) {
    B4 =  $("#"+FIELDS[i]+RN).text();

    $("#"+FIELDS[i]+RN).text("");

    $('<input>').attr({
      type: 'text',
      hidden: 'true',
      id: "frm_data_"+INPUTS[i]+RN,
      name: "frm_data_"+INPUTS[i]+RN,
      // class: 'form-control',
      value: B4
      // }).appendTo('#'+FIELDS[i]+RN);
    }).appendTo('#edt_form'+RN);

    $('<input>').attr({
      type: 'text',
      id: INPUTS[i]+RN,
      name: INPUTS[i]+RN,
      class: 'form-control',
      // onchange: '$("#frm_data_'+INPUTS[i]+'").val($("#'+INPUTS[i]+'").val())',
      onchange: '$("#frm_data_'+INPUTS[i]+RN+'").val($("#'+INPUTS[i]+RN+'").val());',
      value: B4
      // }).appendTo('#'+FIELDS[i]+RN);
    }).appendTo('#'+FIELDS[i]+RN);

  }

}


function show_status(RN,TYPE){

  $('#dns_history').modal('toggle');
  DATA = $('#dns_subdomain'+RN).text().replace('.<?php echo $_SESSION['U_CLOUDFD_DOMAIN']; ?>','');

  // $("#domain_history_contents").load('/dns_history');

  $.post("/api",
  {
    domain: DATA,
    request: 'domain_status',
    user_tag: "<?php echo $DNS_MAN::get_user_tag(); ?>",
    type: 'json',
    format: 'pretty',
    reply_header: false

  },
  function(data, status){
    $('#domain_history_contents').html(data);
  });

}


function check_password(){

  if($('#password').val() == $('#pwd').val()){
    $('#password').css("border-color","green")
  }
  if($('#password').val() != $('#pwd').val()){
    $('#password').css("border-color","red")
  }

}

$("#alert_msg").fadeTo(2000, 500).slideUp(500, function(){
  $("#alert_msg").slideUp(500);
});


function auto_update_change(REQ){

  CURRENT_STATE = "<?php echo $_SESSION['no_auto_push']; ?>";
  SET_STATE = true;
  if (REQ == "CR") {
    if (CURRENT_STATE == "1"){ SET_STATE = false;}
    $('<input>').attr({
      type: 'text',
      hidden: 'true',
      class: "form-control",
      id: "no_auto_push",
      name: "no_auto_push",
      value: SET_STATE
    }).appendTo('#open_form');
    $('#open_form').attr('method','post');
    $('#open_form').attr('action','/home');
    $('#open_form').submit();

  }
}


$( document ).ready(function() {
  if ("<?php echo $_SESSION['no_auto_push']; ?>" == "1") {
    $('#auto_push_state_btn').prop('checked',true);
    $('#auto_push_alert').toast('show');
  }
});
</script>


<!-- DELETE Modal -->
<div id="delete_dns_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fas fa-exclamation-circle text-danger"></i> Remove DNS ?</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>Are you sure ? <br>This will remove this dns (<label id="dns_process"></label>), you cannot undo this action.</p>
      </div>
      <div class="modal-footer bg-dark">
        <!-- <button type="button" class="btn btn-success" data-dismiss="modal"><i class="far fa-window-close"></i></button> -->
        <button id="dns_delete_btn" type="button" onclick="" class="btn btn-danger" data-dismiss="modal" title="Remove" data-toggle="tooltip" data-placement="top"><i class="far fa-check-circle"></i></button>
      </div>
    </div>

  </div>
</div>

<!-- disable Modal -->
<div id="disable_dns_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fas fa-question-circle text-warning"></i> Disable DNS?</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>Are you sure ? <br>This will Disable this DNS <strong>(<label id="dns_process_disable"></label>)</strong>. You can Enable this DNS later.</p>
      </div>
      <div class="modal-footer bg-dark">
        <!-- <button type="button" class="btn btn-success" data-dismiss="modal"><i class="far fa-window-close"></i></button> -->
        <button id="dns_disable_btn" type="button" onclick="" class="btn btn-warning" data-dismiss="modal" title="Disable" data-toggle="tooltip" data-placement="top"><i class="far fa-check-circle"></i></button>
      </div>
    </div>

  </div>
</div>

<!-- enable Modal -->



<!-- The Modal -->
<div id="enable_dns_modal" class="modal fade" >
  <div class="modal-dialog ">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-play-circle text-success"></i> Enable DNS?</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <p>Are you sure ? <br>This will Enable this DNS <strong>(<label id="dns_process_enable"></label>)</strong>. You can Disable/Delete this DNS later.</p>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer bg-dark" >
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <button id="dns_enable_btn" type="button" onclick="" class="btn btn-success" data-dismiss="modal" title="Enable" data-toggle="tooltip" data-placement="top"><i class="far fa-check-circle"></i></button>

      </div>

    </div>
  </div>
</div>

<!-- dnsTAB SECTION ENDS -->



<!-- udpoate Modal -->



<!-- The Modal -->
<div id="update_dns_modal" class="modal fade" >
  <div class="modal-dialog ">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><i class="fas text-info fa-sync"></i> Update DNS?</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <p>Are you sure ? <br>This will Update this DNS <strong>(<label id="dns_process_update"></label>)</strong> Record.</p>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer bg-dark" >
        <small class="text-warning">We do not recommend a frequent update to avoid your API key from reaching usage limit. Do this onyl when necessary.</small>
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <button id="dns_update_btn" type="button" onclick="" class="btn btn-success" data-dismiss="modal" title="Update" data-toggle="tooltip" data-placement="top"><i class="far fa-check-circle"></i></button>

      </div>

    </div>
  </div>
</div>

<!-- dnsTAB SECTION ENDS -->


<!-- DNS History Modal Starts -->

<!-- The Modal -->
<div class="modal fade" id="dns_history">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa-solid fa-clock-rotate-left text-info"></i> Domain Update Records</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <pre>
          <code id="server_response">
            <div id="domain_history_contents"></div>
          </code>
        </pre>
        <small>Domain's Last 3 Activity.</small>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer bg-dark">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

</div>
<!-- DNS History Modal Ends -->



<!-- User Details Modal Starts -->

<!-- The Modal -->
<div class="modal fade" id="user_info">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa-solid fa-user text-success"></i> User Info</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

        <form id="user_info_form" action="/home" method="post">

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Name" id="name" name="name" value="<?php echo $_SESSION['U_NAME']; ?>">
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            </div>
            <input type="password" class="form-control" placeholder="Password" id="pwd"  >
            <input onkeyup="check_password();" type="password" class="form-control" placeholder="Password Confirmation" id="password" name="password" >
          </div>

          <small>Cloudflare Details</small>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">@</span>
            </div>
            <input type="text" class="form-control" placeholder="Cloudflare Email" id="cloudfemail" name="cloudfemail" value="<?php echo $_SESSION['U_CLOUDF_EMAIL']; ?>">
          </div>

          <!-- <small>Cloudflare Email</small> -->
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa-solid fa-globe"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Domain" id="cloudfdomain" value="<?php echo $_SESSION['U_CLOUDFD_DOMAIN']; ?>" disabled>
            <input type="text" class="hidden" name="user_tag" value="<?php echo $DNS_MAN::get_user_tag(); ?>">
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
            </div>
            <input type="password" class="form-control" placeholder="Cloudflare API X-Auth-Key" id="cloudfapi" name="cloudfapi" value="<?php echo $_SESSION['U_CLOUDAPI']; ?>">
          </div>
          <input type="text" class="hidden" name="change_user_details" value="<?php echo base64_encode($_SESSION['U_ID']); ?>">

          <small>Leave empty if not changing password.</small><br>

        </form>
        <hr>
        <label class="text-primary" for=""><strong>No Auto Update</strong></label><br>
        <!-- Rounded switch -->
        <label   class="switch">
          <input onchange="auto_update_change('CR');" id="auto_push_state_btn"  type="checkbox">
          <span class="slider round"></span>
        </label>
        <label class="text-bolder" for=""> <small class="text-info">Enable domains without auto-updates.</small> </label>

      </div>
      <!-- Modal footer -->
      <div class="modal-footer bg-dark">
        <button form="user_info_form" type="submit" class="btn btn-primary">Save</button>
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
      </div>

    </div>
  </div>
</div>

</div>
<!-- User Details Modal Ends -->



<!-- PIP History modal Starts -->

<!-- The Modal -->
<div class="modal fade" id="ip_records">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><i class="fas fa-history text-primary"></i> IP Records</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <pre>
          <code id="ipdata">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>From</th>
                  <th>To</th>
                  <th>Updated</th>
                </tr>
              </thead>
              <tbody>

                <?php #You can add like a filter to the value of "10" to retrieve more
                $PIP_DATA =  json_decode(json_encode($DNS_MAN->gen_get_db_data('ip_history',"id > 0 Order by id DESC LIMIT 10")),true);
                for ($i=0; $i < COUNT($PIP_DATA) ; $i++) : ?>

                <tr>
                  <td><?php echo $i+1; ?></td>
                  <td><?php echo $PIP_DATA[$i]['current']; ?></td>
                  <td><?php echo $PIP_DATA[$i]['previous']; ?></td>
                  <td><?php echo $PIP_DATA[$i]['change_stamp']; ?></td>
                </tr>

              <?php endfor; ?>
            </tbody>
          </table>
        </code>
      </pre>
      <small>Last 10 Records.</small>
    </div>
    <!-- Modal footer -->
    <div class="modal-footer bg-dark">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>

  </div>
</div>
</div>

</div>
<!-- PIP History modal Ends -->


<div id="auto_push_alert" class="toast toast-location" data-autohide="false">
  <div class="toast-header">
    <strong class="mr-auto text-info">Alert</strong>
    <!-- <small class="text-muted">5 mins ago</small> -->
    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
  </div>
  <div class="toast-body">
    <label class="text-danger">Auto-update disabled;</label> <br>Domains won't refresh automatically when enable. Require manual update or auto-refresh on IP change.  </div>
  </div>
