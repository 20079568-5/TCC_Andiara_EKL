/*
Swal.fire({
  icon: 'error',
  title: 'Oops...',
  text: 'Something went wrong!',
  footer: '<a href="">Why do I have this issue?</a>'
})
*/

function popup(type = "success", parameters = {}){
  Swal.fire({
    icon: type,
    title: parameters.title,
    text: parameters.text,
    html: true,
   
  });
}

function handle_form(parameters = {}){
  let form = $(`form[id='${parameters.form_id}']`);
  if ( !form )
    console.warn(`form not found, id: ${parameters.form_id}`);

  $(form).trigger(`reset`);
  /*
  $(form).data(`action`, parameters.action );
  $(form).data(`submit_url`, parameters.submit_url);  
  $(form).data(`api`, parameters.api );  
  $(form).data(`method`, parameters.method );
  */
  $(form).data(`form_parameters`, JSON.stringify(parameters));
}


      
function submit_form(parameters = {}){
  let form = $(`form[id='${parameters.form_id}']`);
  let formdata = new FormData($(form)[0]);
  let form_parameters = $(form).data(`form_parameters`);

  $.each(form_parameters, function(parameter, value){ // extra parameters before submit (used for api control and before/after)
    formdata.append(parameter, value);
  });
  
  console.log(formdata);
  let link = form_parameters.submit_url;
  $.ajax({
    type: form_parameters.method,
    url: link,
    data: formdata ,
    processData: false,
    contentType: false

  }).fail(function(data){
    let response = (data.responseJSON);
    console.log(data.responseJSON);
    popup("error", response.popup);
  }).done(function (data) {
    console.log(data);
    data.popup.reload = true;
    popup("success", data.popup);
    
  });
}