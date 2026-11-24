/**
 * sendForm
 */
function sendForm (idForm, url, success, failure, loading) {
  if (test(idForm)) { // primero checamos el formulario
    var parametros = Form.serializeElements($(idForm).getElements());
    new Ajax.Request(url, {
      method: "post",
      asynchronous:true,
      evalScripts:true,
      parameters:parametros,
      onSuccess:success,
      onFailure:failure,
      onLoading:loading
    });
    return true;
  } else {
    return false;
  }
}

/**
 * consultaAjax
 * crea una consulta ajax usando los dos parámetros (obligatorios)
 */
function consultaAjax (url, parametros, accion, ocupado) {
  var pars = encodeURI(parametros);
  var myAjax = new Ajax.Updater(
    "overlay",
    url,
    {
      method: "post",
      parameters: pars,
      evalScripts: true,
      onComplete: accion,
      onLoading: ocupado,
      onFailure: ocultaOcupado
    }
  );
}

/**
 * consultaAjax2
 * crea una consulta ajax usando los dos parámetros (obligatorios)
 */
function consultaAjax2 (idUpdated, url, parametros, accion, ocupado) {
  var pars = encodeURI(parametros);
  var myAjax = new Ajax.Updater(
    idUpdated,
    url,
    {
      method: "post",
      parameters: pars,
      evalScripts: true,
      onComplete: accion,
      onLoading: ocupado,
      onFailure: ocultaOcupado
    }
  );
}

/**
 * muestraOverlay
 * Actualiza el contenido y muestra la capa overlay
 * Trabajando con un primer intento de "busy screen", oculto del elemento con id "busy", que será el div que cubra a la pantalla
 */
function muestraOverlay (consulta) {
  $('overlay').show();
  $('overoverlay').show();

  if ($('ocupado')) {
    $('ocupado').hide(); // como ya se terminó la carga, se oculta el "busy"
  }
}

/**
 * muestraOverlay2
 * Como el muestraOverlay, pero omitiendo lo del innerHTML
 */
function muestraOverlay2 () {
  $('overlay').show();
  $('overoverlay').show();

  if ($('ocupado')) {
    $('ocupado').hide(); // como ya se terminó la carga, se oculta el "busy"
  }
}

function ocultaOverlay (overlay) {
  $('overlay').hide();
  $('overoverlay').hide();
}

/**
 * muestraOcupado y ocultaOcupado
 * Las usamos al mostrar los formularios dentro del overlay para
 * cubrir la pantalla indicando la carga y evitando que presionen
 * botones durante esta
 */
function muestraOcupado () {
  $('ocupado').show();
}
function ocultaOcupado () {
  $('ocupado').hide();
}

function DialogOverlay (contenido, contenedor) {

  // se asignan los valores por defecto
  if (typeof contenedor == 'undefined') contenedor = document.body;
  if (null == (this.contenedor = $(contenedor))) throw ("el contenedor es inválido");

  // asignamos variables
  this.contenido = contenido;
  this.overlay = new Element('div', {'class':'overlay'}).hide();
  this.dialogo = new Element('div', {'class':'dialogo'}).hide();

  // ocultamos el overlay cuando recibe un click. Ignoramos los chicks en el dialogo
  Event.observe(this.overlay, 'click', this.hide.bindAsEventListener(this));
  Event.observe(this.dialogo, 'click', function(event) {Event.stop(event)});

  // insertamos los elementos en el DOM
  this.dialogo.insert(this.contenido);
  this.contenedor.insert(this.overlay);
  this.contenedor.insert(this.dialogo);

  // el contenido podría estar oculto si se monta en la página
  contenido.show();
  this.dialogo.hide();
}

DialogOverlay.prototype.show = function () {
  this.overlay.show();
  this.dialogo.show();
  return this;
}

DialogOverlay.prototype.hide = function () {
  this.dialogo.hide();
  this.overlay.hide();
  return this;
}

/**
 * enviar
 * Serializa el formulario indicado y lo envía por POST a la URL dada.
 * Usada por el botón "Guardar" en actualizar_alumno.blade.php.
 */
function enviar(idForm, url) {
  var form = $(idForm);
  if (!form) {
    alert('Formulario no encontrado.');
    return;
  }
  muestraOcupado();
  var parametros = Form.serializeElements(form.getElements());
  new Ajax.Request(url, {
    method: 'post',
    parameters: parametros,
    evalScripts: true,
    onSuccess: function(transport) {
      ocultaOcupado();
      alert('Datos actualizados correctamente.');
      ocultaOverlay();
    },
    onFailure: function(transport) {
      ocultaOcupado();
      alert(transport.responseText || 'Error al guardar los datos.');
    }
  });
}

