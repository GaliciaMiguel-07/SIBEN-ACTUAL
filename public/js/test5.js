/**
* trim
* Elimina los espacios al inicio o fin de la cadena.
* Es parte del objeto String, se llama con cadena.trim()
*
* 2010-10-27
* Se eliminan además caracteres que no se esperan en el nombre:
* "*'/-+?¡¿#$%&()
*/
String.prototype.trim = function () {
  //return this.replace(/^\s+/, "").replace(/\s+$/, "").replace(/[^a-zA-Z0-9 ñÑáÁéÉíÍóÓúÚ]+/g, '');
  return this.replace(/^\s+/, "").replace(/\s+$/, "");
}

/**
* chequeo
* Recorre los elementos de un formulario (form) validando la información
* Voy a presuponer que el formulario tendrá una forma estándar, y que la información
* está dentro de una tabla, la cual estará dentro del formulario
* 
* @param string formulario
*/
function test (formulario) {
  var i     = 0; // el contador
  var cont  = 0; // contador para los checkbox seleccionanos

  // extraemos por grupos los datos del formulario, tenemos selects, inputs y radios
  var selects   = $$('#' + formulario + ' select');
  var texts     = $$('#' + formulario + ' input[type=text]');
  var pass      = $$('#' + formulario + ' input[type=password]');
  var checks    = $$('#' + formulario + ' input[type=checkbox]');
  var textareas = $$('#' + formulario + ' textarea');

  for (i = 0; i < texts.size(); i++) {
    if (!texts[i].readAttribute('oculto') && !texts[i].readAttribute('voluntario') && texts[i].up('div', 0).style.display != 'none') { // checamos que el div que lo contiene no esté oculto...
      if (texts[i].value.trim() == '') {
        //alert(texts[i].up('div', 0).id + ':' + texts[i].up('div', 0).style.display);
        alert('Por favor, escriba ' + texts[i].readAttribute('nombre'));
        return false;
      }
      if (texts[i].id.toLowerCase().match("curp")) {
        // corregimos las curp que pongan "o" en lugar de cero al final
        regex = '^[A-Z0-9]{16}[O][0-9]$';
        if (valida(texts[i].value, regex)) {
          var reg = /^([A-Z0-9]{16})[O]([0-9])$/i;
          var c = reg.exec(texts[i].value);
          texts[i].value = c[1] + '0' + c[2];
        }
        if (!validaCurp(texts[i].value)) {
            alert('Error en la ' + texts[i].readAttribute('nombre'));
            return false;
        }
      } else if (texts[i].id.toLowerCase().match("correo")) {
        if (!validaCorreo(texts[i].value)) {
            alert('Formato incorrecto para ' + texts[i].readAttribute('nombre'));
            return false;
        }
      }
    }
  }
  for (i = 0; i < selects.size(); i++) {
      if (!selects[i].readAttribute('oculto') && !selects[i].readAttribute('voluntario') && selects[i].up('div', 0).style.display != 'none') { // checamos que el div que lo contiene no esté oculto...
          if (selects[i][selects[i].selectedIndex].value == '') {
              alert('Por favor, elija ' + selects[i].readAttribute('nombre'));
              return false;
          }
      }
  }
  for (i = 0; i < pass.size(); i++) {
      if (!pass[i].readAttribute('oculto') && !pass[i].readAttribute('voluntario')) {
          if (pass[i].value.trim() == '') {
              alert('Por favor, escriba ' + pass[i].readAttribute('nombre'));
              return false;
          }
      }
  }
  for (i = 0; i < checks.size(); i++) {
      if (checks[i].checked) {
        cont++;
      }
  }
  if (checks[0]) {
    if (cont == 0) {
      alert ('Por favor, elija al menos una opción de ' + checks[0].readAttribute('nombre'));
      return false
    }
  }
  for (i = 0; i < textareas.size(); i++) {
      if (!textareas[i].readAttribute('oculto') && !textareas[i].readAttribute('voluntario')) {
          if (textareas[i].value.trim() == '') {
              alert('Por favor, escriba ' + textareas[i].readAttribute('nombre'));
              return false;
          }
      }
  }
  return true;
}

/**
* testCurp
* Manda una alerta si la curp no cumple con el formato
*/
function testCurp (id) {
    if (!validaCurp($(id).value)) {
        alert('Error en la ' + $(id).readAttribute('nombre') + ', por favor verifíquela');
        return false;
    }
    return true;
}

function testEntidad (id,curp) {

   var entidad = curp.substring(11,13);

    if ($(id).value==entidad) {
        alert('Error en la ' + $(id).readAttribute('nombre') + ', por favor verifíquela');
        return false;
    }
    return true;
}

/**
* testMatricula
* Manda una alerta si la curp no cumple con el formato
*/
function testMat (id) {
    if (!$(id).readAttribute('voluntario') || $(id).value != ''){
      //if (!validaNumero($(id).value, )) {   // Ahora la matricula sera de 12 por ello el cambio
      if (!validaNumero($(id).value, 12)) {
        alert('Formato incorrecto para ' + $(id).readAttribute('nombre')+ 'Tiene que contener 12 digitos');
        $(id).value = '';
        return false;
      }
    }
    return true;
}

function testMatricula (id) {
    if (!$(id).readAttribute('voluntario') || $(id).value != ''){
      //if (!validaNumero($(id).value, )) {   // Ahora la matricula sera de 12 por ello el cambio
      if (!validaNumero($(id).value, 8)) {
        alert('Formato incorrecto para ' + $(id).readAttribute('nombre'));
        $(id).value = '';
        return false;
      }
    }
    return true;
}

function testCp (id) {
    if (!$(id).readAttribute('voluntario') || $(id).value != ''){
      if (!validaNumero($(id).value, 5)) {
        alert('Formato incorrecto para ' + $(id).readAttribute('nombre'));
        $(id).value = '';
        return false;
      }
    }
    return true;
}

/**
* testApellido
* Revisa si el apellido esta vacio y puede quedar asi
*/
function testApellido (id) {
    if ($(id).readAttribute('voluntario') || (id).value != ''){
        return true;
      }
    
}


/**
* testNumero
* Manda alerta si el valor no es número o es <= a cero
*/
function testNumero (id, max, min) {
  if (!max) {
    max = typeof(max) != 'undefined' ? max : 99999999; // un default que funcione con las matrículas
  }
  if (!min) {
    min = typeof(min) != 'undefined' ? min : 1; // el mínimo por defecto es uno
  }

  //if (!parseInt($(id).value) || parseInt($(id).value) < min) {
  if (parseInt($(id).value) < min) { // elimino la primera prueba para que el cero no regrese false
    alert($(id).readAttribute('nombre') + ' debe ser mayor o igual que ' + min);
    $(id).clear();
    return false;
  } else if (parseInt($(id).value) > max) { // agrego esto para
    alert($(id).readAttribute('nombre') + ' debe ser menor o igual que ' + max);
    $(id).clear();
    return false;
  }
  return true;
}

/**
* oculta
* Oculta los elementos del tipo indicado que estén dentro del
* contenedor y les agrega un atributo para distinguirlo.
*
* @param contenedor El id del div
* @param objeto El tipo de objeto a buscar en el div
*/
function oculta (contenedor, objeto) {
    switch (objeto) {
    case 'select':
        break;
    case 'text':
        objeto = 'input[type=text]';
        break;
    case 'radio':
        break;
    }

    var elementos = $$('#' + contenedor + ' ' + objeto);

    elementos.each(function(element){element.writeAttribute('oculto', true);element.hide();});
}

/**
* oculta2
* Oculta los elementos de la clase indicada
* y les agrega un atributo para distinguirlos.
*
* @param string clase
*/
function oculta2 (clase) {
    var selects   = $$('.' + clase + ' select');
    var inputs    = $$('.' + clase + ' input[type=text]');
    var elementos = $$('.' + clase);

    selects.each(function(element){element.writeAttribute('oculto', true);element.hide();});
    inputs.each(function(element){element.writeAttribute('oculto', true);element.hide();});
    elementos.each(function(element){element.writeAttribute('oculto', true);element.hide();});
}

/**
* muestra
* Muestra los elementos del tipo indicado dentro del
* contenedor indicado y les modifica (o agrega) el
* atributo correspondiente
*
* @param string contenedor
* @param string objeto
*/
function muestra (contenedor, objeto) {
    switch (objeto) {
    case 'select':
        break;
    case 'text':
        objeto = 'input[type=text]';
        break;
    case 'radio':
        break;
    }

    var elementos = $$('#' + contenedor + ' ' + objeto);

    elementos.each(function(e){e.writeAttribute('oculto', false);e.show();});
}

/**
* muestra2
* Muestra los elementos de la clase indicada
* y les modifica (o agrega) el atributo correspondiente
*
* @param string clase
*/
function muestra2 (clase) {
    var selects   = $$('.' + clase + ' select');
    var inputs    = $$('.' + clase + ' input[type=text]');
    var elementos = $$('.' + clase);

    selects.each(function(element){element.writeAttribute('oculto', false);element.show();});
    inputs.each(function(element){element.writeAttribute('oculto', false);element.show();});
    elementos.each(function(element){element.writeAttribute('oculto', false);element.show();});
}

/**
* valida
* Valida la cadena contra la expresión regular dada, regresando true o false
* dependiendo del éxito o fracaso de la evaluación
* 
* @param string cadena
* @param string regex
*/
function valida (cadena, regex) {
    var exp_reg = new RegExp(regex);

    return exp_reg.test(cadena);
}

/**
 * validaNoVacio
 * Verifica que la cadena sea no vacia, con expresiones regulares
 *
 * @param string cadena
 */
function validaNoVacio (cadena) {
  regex = '^.+$';

  return valida (cadena, regex);
}

/**
* validaTexto
* Verifica que el contenido de la cadena sea texto solamente
*
* @param string cadena
*/
function validaTexto (cadena) {
    regex = '^\\w+$';

    return valida (cadena, regex);
}

/**
* validanumero
* Verifica que la cadena dada sea un número de la longitud dada
*
* @param string cadena
* @param string longitud
*/
function validaNumero (cadena, longitud) {
    regex = '^\\d{' + longitud + '}$';

    return valida (cadena, regex);
}

/**
* validaCurp
* Verifica que la cadena dada sea conforme al esquema de la curp
*
* @param string cadena
*/
function validaCurp (cadena) {
    //regex = '^[A-Z]{4}\\d{6}[A-Z]{6}\\w\\d$';
    regex= '^[A-Z]{4}\\d{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9][0-9]$'; // usada en curp.emexico.gob.mx

    //return valida (cadena, regex);
    if (valida(cadena, regex)) { // si la curp tiene el formato correcto, vemos el dígito verificador
      var i = digitoCurp(cadena);
      var digito = cadena.substring(17, 18);
      if (!(parseInt(i) == parseInt(digito))) {
        return false;
      }
    } else {
      return false;
    }

    return true; // si sale del bloque anterior, todo está bien
}

/**
* validaCorreo
* Verifica que la cadena dada sea conforme al esquema del correo
*
* @param string cadena
*/
function validaCorreo (cadena) {
    //regex = '^.+\@.+\..+$';
    regex = "^[a-z0-9!#$%&'*+\\/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+\\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+(?:[a-z]{2,4}|museum|travel)$"
    //regex = '([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+';

    return valida (cadena, regex);
}

/**
* datosTitulacion
* Muestra u oculta los campos de los datos del modo de titulación.
*
* @param string elemento
*/
function datosTitulacion(elemento) {
    var titulacion  = $('titulacion');

    if ($(elemento).selectedIndex==1) {
        titulacion.show();
        muestra('titulacion2', 'select');
        muestra('titulacion2', 'text');
    } else {
        titulacion.hide();
        oculta('titulacion2', 'select');
        oculta('titulacion2', 'text');
    }
}


/**
* datosTitulacion2
* Muestra u oculta los campos de los datos del modo de titulación.
*
* @param string elemento
* @param string clase
* @param integer inicio
* @param integer fin
*/
function datosOcultos(elemento, clase, inicio, fin) {
    if ($(elemento)) {
        var indice = $(elemento).selectedIndex;
        if (indice >= inicio && indice <= fin) {
            muestra2(clase);
        } else {
            oculta2(clase);
        }
    }
}


//30 de mayo... muestra oculta el div de camino o carretera dependiendo de la seleccion
//Listo!

function datosOcultos2 (elemento, clase, clase2) {
    if ($(elemento)) {
        var indice = $(elemento).value;
        if (indice =='CARRETERA') {
            muestra2(clase);
        }
        else {
            oculta2(clase);
        }
        if (indice =='CAMINO') {
            muestra2(clase2);
        }
        else {
            oculta2(clase2);
        }

    }
}

function datosOcultos3 (elemento, clase) {
    if ($(elemento)) {
        var indice = $(elemento).value;
        if (indice =='0') {
            muestra2(clase);
        }
        else {
            oculta2(clase);
        }
        

    }
}


/**
* locacion
* Reajusta el atributo location de window
*
* @param string destino
*/
function locacion (destino) {
    window.location = destino;
}

/***
* digitoCurp
* Calcula el dígito verificador de la curp
*/
function digitoCurp (curp) {
    var subCurp      = curp.substring(0,17);
    var validChars   = "0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
    var intFactor    = new Array(17);
    var lngSuma      = 0.0;
    var lngDigito    = 0.0;

    for(var i = 0; i < 17; i++) {
      for(var j = 0; j < 37; j++) {
        if (subCurp.substring(i, i + 1) == validChars.substring(j, j + 1)) {
          intFactor[i]=j;
        }
      }
    }

    for (var k = 0; k < 17; k++) {
      lngSuma = lngSuma + ((intFactor[k]) * (18 - k));
    }

    lngDigito = (10 - (lngSuma % 10)) % 10;

    return lngDigito;
}
/* ******************************************************************************************* */

/**
* checkForm2
* Recorre los elementos de un formulario (form) validando la información
* Voy a presuponer que el formulario tendrá una forma estándar, y que la información
* está dentro de una tabla, la cual estará dentro del formulario
*
* @param string formulario
*/
function checkForm2 (formulario) {
  var i     = 0; // el contador
  var cont  = 0; // contador para los checkbox seleccionanos

  // extraemos por grupos los datos del formulario, tenemos selects, inputs y radios
  var selects   = $$('#' + formulario + ' select');
  var texts     = $$('#' + formulario + ' input[type=text]');
  var pass      = $$('#' + formulario + ' input[type=password]');
  var checks    = $$('#' + formulario + ' input[type=checkbox]');
  var textareas = $$('#' + formulario + ' textarea');

  /** textos **/
  for (i = 0; i < texts.size(); i++) {
    // checamos el valor del atributo display en cada caso (si es "none" no se checa)

    if (texts[i].up('tr').style.display != 'none') { // checamos que el tr que lo contiene no esté oculto (presuponemos una tabla)
      if (texts[i].value.trim() == '') {
        alert('Por favor, escriba ' + texts[i].readAttribute('nombre'));
        return false;
      }

      // si tenemos una curp, checamos el formato y corregimos hasta donde podamos
      if (texts[i].id.toLowerCase().match("curp")) {
        // corregimos las curp que pongan "0" en lugar de cero en el penúltimo caracter
        regex = '^[A-Z0-9]{16}[O][0-9]$';
        if (valida(texts[i].value, regex)) {
          var reg = /^([A-Z0-9]{16})[O]([0-9])$/i;
          var c = reg.exec(texts[i].value);
          texts[i].value = c[1] + '0' + c[2];
        }

        if (!validaCurp(texts[i].value)) {
          alert('Error en la ' + texts[i].readAttribute('nombre'));
          return false;
        }
      } else if (texts[i].id.toLowerCase().match("correo")) {
        if (!validaCorreo(texts[i].value)) {
          alert('Formato incorrecto para ' + texts[i].readAttribute('nombre'));
          return false;
        }
      }
    }
  }
  /** textos **/

  /** selects **/
  for (i = 0; i < selects.size(); i++) {
    if (selects[i].up('tr').style.display != 'none') {
      if (selects[i][selects[i].selectedIndex].value == '') {
        alert('Por favor, elija ' + selects[i].readAttribute('nombre'));
        return false;
      }
    }
  }
  /** selects **/

  /** password **/
  for (i = 0; i < pass.size(); i++) {
      if (!pass[i].readAttribute('oculto') && !pass[i].readAttribute('voluntario')) {
          if (pass[i].value.trim() == '') {
              alert('Por favor, escriba ' + pass[i].readAttribute('nombre'));
              return false;
          }
      }
  }
  /** password **/

  /** checkboxes **/
  for (i = 0; i < checks.size(); i++) {
      if (checks[i].checked) {
        cont++;
      }
  }
  if (checks[0]){
    if  (cont == 0) {
        alert ('Por favor, escriba ' + checks[0].readAttribute('nombre'));
        return false
    }
  }
  /** checkboxes **/

  /** textareas **/
  for (i = 0; i < textareas.size(); i++) {
    if (textareas[i].up('tr').style.display != 'none') {
      if (textareas[i].value.trim() == '') {
        alert('Por favor, escriba ' + textareas[i].readAttribute('nombre'));
        return false;
      }
    }
  }
  /** textareas **/

  return true;
}

/**
 * checkForm
 * Otro acercamiento, ahora checando los campos activos o inactivos
 */
function checkForm (formulario) {
  var i     = 0; // el contador
  var cont  = 0; // contador para los checkbox seleccionanos

  // extraemos por grupos los datos del formulario, tenemos selects, inputs y radios
  var selects   = $$('#' + formulario + ' select');
  var texts     = $$('#' + formulario + ' input[type=text]');
  var pass      = $$('#' + formulario + ' input[type=password]');
  var checks    = $$('#' + formulario + ' input[type=checkbox]');
  var textareas = $$('#' + formulario + ' textarea');

  for (i = 0; i < texts.size(); i++) {
    if (!texts[i].readAttribute('oculto') && !texts[i].readAttribute('voluntario') && !texts[i].disabled ) {
      if (texts[i].value.trim() == '') {
        //alert(texts[i].up('div', 0).id + ':' + texts[i].up('div', 0).style.display);
        alert('Por favor, escriba ' + texts[i].readAttribute('nombre'));
        return false;
      }
      if (texts[i].id.toLowerCase().match("curp")) {
        // corregimos las curp que pongan "o" en lugar de cero al final
        regex = '^[A-Z0-9]{16}[O][0-9]$';
        if (valida(texts[i].value, regex)) {
          var reg = /^([A-Z0-9]{16})[O]([0-9])$/i;
          var c = reg.exec(texts[i].value);
          texts[i].value = c[1] + '0' + c[2];
        }
        if (!validaCurp(texts[i].value)) {
            alert('Error en la ' + texts[i].readAttribute('nombre'));
            return false;
        }
      } else if (texts[i].id.toLowerCase().match("correo")) {
        if (!validaCorreo(texts[i].value)) {
            alert('Formato incorrecto para ' + texts[i].readAttribute('nombre'));
            return false;
        }
      }
    }
  }
  for (i = 0; i < selects.size(); i++) {
      if (!selects[i].readAttribute('oculto') && !selects[i].readAttribute('voluntario') && !selects[i].disabled) {
          if (selects[i][selects[i].selectedIndex].value == '') {
              alert('Por favor, elija ' + selects[i].readAttribute('nombre'));
              return false;
          }
      }
  }
  for (i = 0; i < pass.size(); i++) {
      if (!pass[i].readAttribute('oculto') && !pass[i].readAttribute('voluntario')) {
          if (pass[i].value.trim() == '') {
              alert('Por favor, escriba ' + pass[i].readAttribute('nombre'));
              return false;
          }
      }
  }
  for (i = 0; i < checks.size(); i++) {
      if (checks[i].checked) {
        cont++;
      }
  }
  if (checks[0]){
  if  (cont == 0) {
      alert ('Por favor, elija ' + checks[0].readAttribute('nombre'));
      return false
  }
  }
  for (i = 0; i < textareas.size(); i++) {
      if (!textareas[i].readAttribute('oculto') && !textareas[i].readAttribute('voluntario')) {
          if (textareas[i].value.trim() == '') {
              alert('Por favor, escriba ' + textareas[i].readAttribute('nombre'));
              return false;
          }
      }
  }
  return true;
}
