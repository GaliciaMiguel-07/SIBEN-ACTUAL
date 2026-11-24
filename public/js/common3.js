/**
* alto
* Detiene al evento indicado
*/
function alto (evento) {
    Event.stop(evento);
}

/**
* eco
* Alerta del código de la tecla presionada
*/
function eco (evento) {
    alert(evento.keyCode + ' ' + String.fromCharCode(evento.keyCode));
}

/**
* str_replace
*
* This function returns a string or an array with all occurrences of
* [search] in [subject] replaced with the given [replace] value.
* If you don't need fancy replacing rules (like regular expressions), you should always use this function.
*
* @param string search
* @param string replace
* @param string string
*/
function str_replace (search, replace, subject) {
    var result = "";
    var oldi = 0;
    for (i = subject.indexOf (search); i > -1; i = subject.indexOf (search, i)) {
      result += subject.substring (oldi, i);
      result += replace;
      i += search.length;
      oldi = i;
    }
    return result + subject.substring (oldi, subject.length);
}

/**
* curp2datos
*/
function curp2datos (curp, idEdad, idGenero, idRenapo) {
    var fecha = new Date();

    var anio = curp.substring(4,6);
    var mes  = curp.substring(6,8);
    var dia  = curp.substring(8,10);
    //var anio_dif = 108 - anio;
    //var mes_dif = 8 - mes;
    //var dia_dif = 20 - dia;
    //if (fecha.getYear()<1000) {
      //anio = '19' + anio;
    //}

    if(parseInt(anio) < 40){
      anio = '20' + anio;
    }else{
      anio = '19' + anio;
    }								

    var anio_actual = parseInt(fecha.getFullYear());
    var anio_dif = anio_actual - parseInt(anio);
    var mes_dif = (fecha.getMonth() + 1) - mes;
    var dia_dif = fecha.getDate() - dia;

    if (dia_dif < 0) {
        mes_dif--;
    }
    if (mes_dif < 0) {
        anio_dif--;
    }
    var edad = anio_dif;
    $(idEdad).value = edad;
    //document.getElementById('AlumnoFechaNacimiento').value
    for (i=0; i<$(idGenero).options.length; i++) {
        if ($(idGenero).options[i].value == curp.substring(10,11).toUpperCase()) {
            $(idGenero).selectedIndex=i;
        }
    }
    /*
    for (i=0; i<$(idRenapo).options.length; i++) {
        if ($(idRenapo).options[i].value == curp.substring(11,13)) {
            $(idRenapo).selectedIndex=i;
        }
    }
*/
    // todo porque los pendejos navegadores dan getYear como se les da la gana
    /*if (parseInt(anio)<1000) {
      anio = '19' + anio;
    }
    for (i=0; i<$('AlumnoYear').options.length; i++) {
        if ($('AlumnoYear').options[i].value == parseInt(anio)) {
            $('AlumnoYear').selectedIndex=i;
        }
    }
    for (i=0; i<$('AlumnoMes').options.length; i++) {
        if ($('AlumnoMes').options[i].value == curp.substring(6,8)) {
            $('AlumnoMes').selectedIndex=i;
        }
    }
    for (i=0; i<$('AlumnoDia').options.length; i++) {
        if ($('AlumnoDia').options[i].value == curp.substring(8,10)) {
            $('AlumnoDia').selectedIndex=i;
        }
    }*/
}

/**
* curp2datos2
* Variación de la función para el formulario de personal
*/
function curp2datos2 (curp, formId) {
    var fecha = new Date();

    var anio = curp.substring(4,6);
    var mes  = curp.substring(6,8);
    var dia  = curp.substring(8,10);
    //var anio_dif = 108 - anio;
    //var mes_dif = 8 - mes;
    //var dia_dif = 20 - dia;
    //alert(fecha.getYear());
    if (fecha.getYear()>1000) {
      anio = '19' + anio;
    }
    var anio_dif = fecha.getYear() - anio;
    var mes_dif = (fecha.getMonth() + 1) - mes;
    var dia_dif = fecha.getDate() - dia;

    if (dia_dif < 0) {
        mes_dif--;
    }
    if (mes_dif < 0) {
        anio_dif--;
    }
    var edad = anio_dif;
    $('PersonalEdad').value = edad;    
    for (i=0; i<$('PersonalGenero').options.length; i++) {
        if ($('PersonalGenero').options[i].value == curp.substring(10,11).toUpperCase()) {
            $('PersonalGenero').selectedIndex=i;
        }
    }
}

/**
 * agregaTr
 * Agrega un renglón a la tabla con identificador id_tabla
 */
function agregaTr(id_tabla, forma_contratacion, tiempo_contratacion) {
  var forma = $(forma_contratacion)[$(forma_contratacion).selectedIndex].text;
  var tiempo = $(tiempo_contratacion)[$(tiempo_contratacion).selectedIndex].text;
  var texto = '<tr><td>' + forma +'</td><td>' + tiempo +'</td></tr>';
  $(id_tabla).down('tbody').insert(texto);
}

/**
 * submitform
 * Lanzael evento submit del formulario id
 * si la validación de este es correcta
 */
function submitform (id) {
  if (test(id)) {
    $(id).submit();
    return true;
  } else {
    return false;
  }
}

/**
 * ocultables
 * Si el valor en idCambio está dentro de la lista de valores,
 * (cadena separada por comas), idOcultable se muestra, en otro
 * caso se oculta
 */
function ocultables (idCambio, idOcultable, valores) {
  var arreglo = valores.split(',');
  var valor = $(idCambio).getValue();
  var indice = arreglo.indexOf(valor);

  // si el valor está dentro de la lista, se muestra
  if (indice >= 0) {
    $(idOcultable).show();
  } else { // si no está, se oculta
    $(idOcultable).hide();
  }
}

/**
 * activables
 */
function activables (elemento, clase, valores) {
  var arreglo = valores.split(',');
  if ($(elemento)) {
    var valor = $(elemento).getValue();
    var indice = arreglo.indexOf(valor);

    if (indice >= 0) {
      activa(clase);
    } else {
      desactiva(clase);
    }
  }
}

/**
 * activar
 * Se activan los elementos con la clase indicada (se esperan sólo elementos de formulario)
 */
function activa (clase) {
  var selects   = $$('.' + clase + ' select');
  var inputs    = $$('.' + clase + ' input[type=text]');
  //var elementos = $$('.' + clase);

  selects.each(function(element){element.writeAttribute('oculto', false);element.enable();});
  inputs.each(function(element){element.writeAttribute('oculto', false);element.enable();});
  //elementos.each(function(element){element.writeAttribute('oculto', false);element.show();});
}

/**
 * desactivar
 * Se desactivan los elementos con la clase indicada (se esperan sólo elementos de formulario)
 */
function desactiva (clase) {
  var selects   = $$('.' + clase + ' select');
  var inputs    = $$('.' + clase + ' input[type=text]');
  //var elementos = $$('.' + clase);

  selects.each(function(element){element.writeAttribute('oculto', false);element.disable();});
  inputs.each(function(element){element.writeAttribute('oculto', false);element.disable();});
  //elementos.each(function(element){element.writeAttribute('oculto', false);element.show();});
}

/**
 * enviaAjax
 * Función para hacer envío de un formulario a través de una consulta ajax
 */
function enviaAjax (id, url, accion) {
  if (test(id)) {
    var parametros = Form.serializeElements($(id).getElements());
    new Ajax.Request(
      url,
      {
        method: 'post',
        asynchronous:true,
        parameters: parametros,
        onSuccess:accion,
        onFailure:function(respuesta) {
          alert(respuesta.responseText);
        }
      }
    );
  } else {
    return false;
  }
}

/**
 * scroll
 * Primer intento de función para mover el scroll
 */
function scroll (idElemento, cantidad) {
  $(idElemento).scrollTop = $(idElemento).scrollTop + (parseInt(cantidad));
}
function scrollUp (idElemento) {
  scroll(idElemento, -20);
}
function scrollDown (idElemento) {
  scroll(idElemento, 20)
}

/**
 * holdClick
 * Se realiza la acción, cada ocasión más rápido, si se mantiene presionado el "botón"
 */
function holdClick (idButton, action, start, speedup) {
  var t;

  var repeat = function () {
    action();
    t = setTimeout(repeat, start);
    start = start / speedup;
  }

  $(idButton).mousedown = function () {
    repeat();
  }

  $(idButton).mouseup = function () {
    clearTimeout(t);
  }
}

/**
 * toggleMenu
 * Se muestran u ocultan las opciones de un menú css
 */
function toggleMenu (idUL) {
  if ($(idUL).style.display == 'none') {
    $(idUL).show();
  } else {
    $(idUL).hide();
  }
}
