/*
Swal.fire({
  icon: 'error',
  title: 'Oops...',
  text: 'Something went wrong!',
  footer: '<a href="">Why do I have this issue?</a>'
})
*/
function translate(string = "" ){
  let list = {
    "FormException": "Dados incorretos",
    "NotFoundException": "Não encontrado",
    "AuthException": "Permissão necessária",
    "Exception": "Exceção"
  }

  if ( list[string] == "" || list[string] == undefined )
    return string;

  return list[string];
}
function popup(type = "ok", parameters = {}){
  let config = {
    icon: type,
    title: translate(parameters.title),
    html: parameters.text,
  }

  if ( parameters.reload ){

    Swal.fire(config).then(function() {
      location.reload();
    });
  }else {
    Swal.fire(config);
  }
}