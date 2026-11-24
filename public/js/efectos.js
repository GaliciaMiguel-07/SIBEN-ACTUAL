/**
* objColor
* Cambia el color del fondo y el color de texto del objeto
*
* @param string backColor
* @param string textColor
* @param string obj
*/
function objColor (backColor, textColor, obj) {
    var estilo = obj.style;

    estilo.backgroundColor = backColor;
    estilo.color = textColor;
}

/**
* stickEvent
* Agrega a cada opción del menú un manejador para los eventos mouseover y mouseout
*
* @param string id
*/
function stickEvent (id, elemento, fondo1, texto1) {
    var elementos = $$('#' + id + ' ' + elemento);

    /* obtenemos los colores originales para usarlos al momento del evento mouseout */
    //var fondo2 = this.style.backgroundColor;
    //var texto2 = this.style.color;

    for (var i = 0; i < elementos.size(); i++) {
      fondo2 = elementos[i].style.backgroundColor;
      texto2 = elementos[i].style.color;
      elementos[i].observe('mouseover', elementColors.bindAsEventListener(this, fondo1, texto1));
      elementos[i].observe('mouseout', elementColors.bindAsEventListener(this, fondo2, texto2));
    }
}


function MouseOverLi(elem, clase){

  /*
  $(elem).setStyle({
   'float': 'left', // notice how float is surrounded by single quotes
   opacity: 0.5,
   backgroundColor: '#900',
   fontSize: '12px'
  });
  */
  $(elem).addClassName(clase);
}


function MouseOutLi(elem, clase){
  $(elem).removeClassName(clase);
}

/**
* elementColors
* Cambia el color de fondo y de texto del objeto sobre el que está el ratón
*
* @param string evento
*/
function elementColors (evento) {
    var datos = $A(arguments);
    datos.shift();
    var fondo = datos.shift();
    var texto = datos.shift();

    var elemento = Event.element(evento);
    objColor(fondo, texto, elemento);
}

/**
* out
* Cambia el color de fondo y de texto del objeto que es abandonado por el ratón
*
* @param evento El evento
*/
function out (evento) {
    var datos = $A(arguments);
    datos.shift();
    var fondo = datos.shift();
    var texto = datos.shift();

    var elemento = Event.element(evento);
    objColor(fondo, texto, elemento);
}

/**
* muestraOculta
* Esta función está destinada para la sección de
* agregar asignatura a los docentes, para elegir
* entre materia con plan o materia sin plan
*
* @param elemento1 Elemento a mostrar
* @param elemento2 Elemento a ocultar
*/
function muestraOculta(elemento1, elemento2) {
    if ($(elemento1)) {
        $(elemento1).show();
    }
    if ($(elemento2)) {
        $(elemento2).hide();
    }
}

/**
 * vaciaSelect
 * Esta función está pensada para el formulario de
 * inicio del cuestionario para los alumnos.
 * La idea es que, de acuerdo al orden en el que
 * están colocados, los combos de abajo se vacíen
 * al cambiar el actual. Se usan ids numéricos en las
 * envolturas de cada select
 *
 * @param id El id de la envoltura que contiene al select (se presupone numérico)
 */
function vaciaSelect (id) {
  for (var i = (id + 2); i <= 8; i++) { // hay que vaciar el resto
    var select = $$('#' + i + ' select');
    select[0].update("<option selected='selected' value=''>Seleccione</option>\n");
  }
}
