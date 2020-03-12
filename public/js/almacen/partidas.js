var partida_edit = [];
var close = document.getElementsByClassName("closebtn");
/*
 * Con esta función se carga el funcionamiento de las notificaciones
*/
 $("#loader").show();
 $(document).ready(function(){
    $("#loader").hide();
    for (var i = 0; i < close.length; i++) {
        close[i].onclick = function(){
            var div = this.parentElement;
            div.style.opacity = "0";
            //Despues de 600 milisegundos despues de activar el boton de cierre la notificacion desaparecerá
            setTimeout(function(){ div.style.display = "none"; }, 600);
            //Despues de 1 segundo la notificaicon desaparecerá del html
            setTimeout(function(){
                var alertContainer=document.getElementById("contenedor-alert");
                if(alertContainer!=null){
                    alertContainer.remove();
                }
            }, 1000);
        }
    }

    var forms = document.getElementsByClassName('partidaForm');
    
    for(var i = 0, length1 = forms.length; i < length1; i++){
        forms[i].id=`partidaForm${i}`;
    }

    // Con las siguientes funciones se asignan los ids correspondientes para cada elemento dentro de los paneles de artículos.
    // Se asigna el id al panel
    var paneles = document.getElementsByClassName('panel-menu');
    for (var i = paneles.length - 1; i >= 0; i--) {
        paneles[i].setAttribute("id", "Partida"+i);
        //Se asignan los ids corrspondientes a los elementos del panel
        var campos = paneles[i].getElementsByClassName('panel-body')[0].getElementsByTagName("input");
        for (var index = campos.length-1; index >= 0; index--) {
            campos[index].setAttribute("id", campos[index].id+i);
        }
        var listas = paneles[i].getElementsByClassName('panel-body')[0].getElementsByTagName("select");
        for(var index = listas.length-1; index >=0; index--){
            listas[index].setAttribute("id", listas[index].id+i);
        }
     }


    //Se asignan los ids a los botones de edición y eliminación.
    var btn_editar=document.getElementsByClassName("btn-edit");
    for (var i = 0; i < btn_editar.length; i++) {
        btn_editar[i].setAttribute("id", btn_editar[i].id+i);
        btn_editar[i].setAttribute("disabled", "true");
    }

    var btn_eliminar=document.getElementsByClassName("btn-delete");
    for (var i = 0; i < btn_eliminar.length; i++) {
        btn_eliminar[i].setAttribute("id", btn_eliminar[i].id+i);
        btn_eliminar[i].setAttribute("disabled", "true");
        btn_eliminar[i].setAttribute("data-target", "#eliminarPartida"+i);
    }

    var modales = document.getElementsByClassName("modal-eliminar");
    for (var i = 0; i < modales.length; i++) {
        modales[i].setAttribute("id", modales[i].id+i);
    }

    //Se asignan los ids y métodos para los botones de cada panel que no pertenecen al form de artículo.
    var paneles = document.getElementsByClassName("panel-collapse");
    if(paneles!=null){
        for (let x = 0; x < paneles.length; x++) {
            const panel = paneles[x];
            panel.setAttribute("id", "collapsePartida"+x);
        }
        var botones = document.getElementsByClassName("btn-left");
        // Se genera un array de contadores para llevar el control de las veces que se da click en cada boton
        var contadores = new Array(botones.length);
        // Se inicializan los contadores en 0
        for (var i = 0; i < contadores.length; i++) {
            contadores[i]=0;
        }
        for (let index = 1; index < botones.length; index++) {
            var panel_aux = document.getElementById("Partida"+index);
           // console.log(panel_aux);
            
            var boton_guardar_aux = panel_aux.getElementsByClassName('btn-submit')[0];
            
            boton_guardar_aux.addEventListener('click', function(event){
                var form_aux = document.getElementById(`partidaForm${index}`);
                console.log(form_aux);
                /*if(form_aux.checkValidity()){
                  console.log('Entrando');
                    event.preventDefault();
                    if(!validateEach(index)){
                        alert('Los datos ingresados son incorrectos');
                    }
                }*/
            });
            var form_aux = document.getElementById(`partidaForm${index}`);
            var boton_cancelar_aux = form_aux.getElementsByClassName('btn-cancel')[0];
            boton_cancelar_aux.addEventListener('click', function(){
                cancelar_edicion(index);
            });

            const boton = botones[index];
            /*Las siguientes tres funciones asignan los ids de los paneles que se van a desplegar al dar click
             * sobre el boton de despliegue que esta dentro del encabezado del panel.
             */
            boton.setAttribute("id", "verPartida"+index);
            boton.setAttribute("data-target", "#collapsePartida"+index);
            boton.setAttribute("aria-controls", "collapsePartida"+index);

            //Cada vez que se le de click al botón de despliegue...
            boton.addEventListener('click', function(){
                setEditando(index);
                var btn_editar_aux = document.getElementById('btn_editar'+index);
                var btn_eliminar_aux = document.getElementById('btn_eliminar'+index);
                cerrarPaneles(btn_editar_aux, btn_eliminar_aux);
                //Se agrega el metodo click al boton
                btn_editar_aux.addEventListener("click",function(){
                    var panel_aux = document.getElementById("Partida"+index);
                    var campos_aux = panel_aux.getElementsByClassName('panel-body')[0].getElementsByTagName("input");
                    for (var x = 0; x < campos_aux.length; x++) {
                        if(campos_aux[x].hasAttribute("disabled")){
                            campos_aux[x].removeAttribute("disabled");
                        }else{
                            campos_aux[x].setAttribute("disabled", "true");
                        }
                    }
                    var botones_aux = panel_aux.getElementsByClassName('panel-body')[0].getElementsByTagName("button");
                    for (var x = 0; x < botones_aux.length; x++) {
                        if(botones_aux[x].hasAttribute("disabled")){
                            botones_aux[x].removeAttribute("disabled");
                        }else{
                            botones_aux[x].setAttribute("disabled", "true");
                        }
                    }
                });
                // Se habilitan los botones de edición y el de eliminación
                if(btn_editar_aux.hasAttribute("disabled")){
                    btn_editar_aux.removeAttribute("disabled");
                }else{
                    btn_editar_aux.setAttribute('disabled', 'true');
                }
                 if(btn_eliminar_aux.hasAttribute("disabled")){
                    btn_eliminar_aux.removeAttribute("disabled");
                }else{
                    btn_eliminar_aux.setAttribute('disabled', 'true');
                }
            });
        }
    }
    var crearPartida = document.getElementById('btn-guardar');
    crearPartida.addEventListener("click",function(event){
        var newPartida = document.getElementById('newPartida');
        if(newPartida.checkValidity()){
           if(!validateNew()){
            console.log('linea 153');
              event.preventDefault();
              alert('Los datos ingresados son incorrectos');
           }
        }
    });
    var cancelar = document.getElementById('btn-cancelar');
    cancelar.addEventListener("click", function(){
        clearOrden();
    });

    $(document).keyup(function(event){
        if(event.keyCode==27){
            clearOrden();
        }
    });
});

function cerrarPaneles(btn_editar, btn_eliminar){
    var botones_edit = document.getElementsByClassName("btn-edit");
    var botones_delete = document.getElementsByClassName("btn-delete");
    for(var i = 0, length1 = botones_edit.length; i < length1; i++){
        if (botones_edit[i].id != btn_editar.id) {
            botones_edit[i].setAttribute("disabled","true");
        }
    }
    for(var i = 0, length1 = botones_delete.length; i < length1; i++){
        if (botones_delete[i].id != btn_eliminar.id) {
            botones_delete[i].setAttribute("disabled","true");
        }
    }
}

/*function validateEach(index){

    var partidaCta = $(`#partidaCta${index}`);
    var partidaScta = $(`#partidaScta${index}`);
    var partidaSscta = $(`#partidaSscta${index}`);
    var partidaNombre = $(`#partidaNombre${index}`);
    var partidaGrupo = $(`#partidaGrupo${index}`);
    var partidaCtaArm = $(`#partidaCtaArm${index}`);
    var partidaNombreArm = $(`#partidaNombreArm${index}`);

    if(isNaN(parseFloat(partidaCta.val().replace('.','')))){
        return false;
    }

    if(isNaN(parseFloat(partidaScta.val().replace('.','')))){
        return false;
    }

    if(isNaN(parseFloat(partidaSscta.val().replace('.','')))){
        return false;
    }

    if(!isNaN(parseFloat(partidaNombre.val()))){
        return false;
    }

    if(!isNaN(parseFloat(partidaGrupo.val()))){
        return false;
    }

    if(isNaN(parseFloat(partidaCtaArm.val().replace('.','')))){
        return false;
    }

    if(!isNaN(parseFloat(partidaNombreArm.val()))){
        return false;
    }

    return true;
}*/


function validateNew(){

    var partidaCta = $('#partidaCta');
    var partidaScta = $('#partidaScta');
    var partidaSscta = $('#partidaSscta');
    var partidaNombre = $('#partidaNombre');
    var partidaGrupo = $('#partidaGrupo');
    var partidaCtaArm = $('#partidaCtaArm');
    var partidaNombreArm = $('#partidaNombreArm');

    if(isNaN(parseFloat(partidaCta.val().replace('.','')))){
        return false;
    }

    if(isNaN(parseFloat(partidaScta.val().replace('.','')))){
        return false;
    }

    if(isNaN(parseFloat(partidaSscta.val().replace('.','')))){
        return false;
    }

    if(!isNaN(parseFloat(partidaNombre.val()))){
        return false;
    }

    if(!isNaN(parseFloat(partidaGrupo.val()))){
        return false;
    }

    if(isNaN(parseFloat(partidaCtaArm.val().replace('.','')))){
        return false;
    }

    if(!isNaN(parseFloat(partidaNombreArm.val()))){
        return false;
    }

    return true;
}

function clearOrden(){
    var partidaCta = $('#partidaCta');
    var partidaScta = $('#partidaScta');
    var partidaSscta = $('#partidaSscta');
    var partidaNombre = $('#partidaNombre');
    var partidaGrupo = $('#partidaGrupo');
    var partidaCtaArm = $('#partidaCtaArm');
    var partidaNombreArm = $('#partidaNombreArm');

    partidaCta.val('');
    partidaScta.val('');
    partidaSscta.val('');
    partidaNombre.val('');
    partidaGrupo.val('');
    partidaCtaArm.val('');
    partidaNombreArm.val('');
}

function cancelar_edicion(index){
    $(`#partidaCta${index}`).val(partida_edit[0]);
    $(`#partidaScta${index}`).val(partida_edit[1]);
    $(`#partidaSscta${index}`).val(partida_edit[2]);
    $(`#partidaNombre${index}`).val(partida_edit[3]);
    $(`#partidaGrupo${index}`).val(partida_edit[4]);
    $(`#partidaCtaArm${index}`).val(partida_edit[5]);
    $(`#partidaNombreArm${index}`).val(partida_edit[6]);

    const panel_aux = document.getElementById("Partida"+index);
    var campos_aux = panel_aux.getElementsByClassName('panel-body')[0].getElementsByTagName("input");
    for (var x = 0; x < campos_aux.length; x++) {
        campos_aux[x].setAttribute("disabled", "true");
    }
    var select_aux = panel_aux.getElementsByClassName('panel-body')[0].getElementsByTagName("select");
    for (var x = 0; x < select_aux.length; x++) {
        select_aux[x].setAttribute("disabled", "true");
    }
    var botones_aux = panel_aux.getElementsByClassName('panel-body')[0].getElementsByTagName("button");
    for (var x = 0; x < botones_aux.length; x++) {
        botones_aux[x].setAttribute("disabled", "true");
    }
}

function setEditando(index){
    partida_edit[0] = $(`#partidaCta${index}`).val();
    partida_edit[1] = $(`#partidaScta${index}`).val();
    partida_edit[2] = $(`#partidaSscta${index}`).val();
    partida_edit[3] = $(`#partidaNombre${index}`).val();
    partida_edit[4] = $(`#partidaGrupo${index}`).val();
    partida_edit[5] = $(`#partidaCtaArm${index}`).val();
    partida_edit[6] = $(`#partidaNombreArm${index}`).val();
}
