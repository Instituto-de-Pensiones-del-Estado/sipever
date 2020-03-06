var close = document.getElementsByClassName("closebtn");
var i;
/*
 * Con esta función se carga el funcionamiento de las notificaciones
*/
for (i = 0; i < close.length; i++) {
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

$("#loader").show();
$(document).ready(function(){
    $("#loader").hide();
    var checks = document.getElementsByClassName("checkReporte");
    for (var i = 0; i < checks.length; i++) {
        checks[i].checked = false;
        checks[i].addEventListener("click",function(event){
            seleccionarReporte(event.srcElement);
        });
    }
    var chckMes = document.getElementById("chckMes");
    chckMes.checked = false;
    chckMes.addEventListener("click", function(event){
        unSoloMes(event.srcElement);
    });

    var mesIniIncrement=document.getElementById('mesIniIncrement');
    var mesIniDecrement=document.getElementById('mesIniDecrement');
    var input = document.getElementById('inptMesInicio');
    mesIniIncrement.addEventListener("click",function(){
        if(parseInt(input.value) < 12){
            input.setAttribute("value", parseInt(input.value)+1);
        }
    });
    mesIniDecrement.addEventListener("click",function(){
        if(parseInt(input.value) > 1){
            input.setAttribute("value", parseInt(input.value)-1);
        }
    });

    var mesFinIncrement=document.getElementById('mesFinIncrement');
    var mesFinDecrement=document.getElementById('mesFinDecrement');
    var inputFin = document.getElementById('inptMesFin');
    mesFinIncrement.addEventListener("click",function(){
        if(parseInt(inputFin.value) < 12){
            inputFin.setAttribute("value", parseInt(inputFin.value)+1);
        }
    });
    mesFinDecrement.addEventListener("click",function(){
        if(parseInt(inputFin.value) > 1){
            inputFin.setAttribute("value", parseInt(inputFin.value)-1);
        }
    });
    var selectDepto = document.getElementById("selectDepto");
    var selectOficina = document.getElementById("selectOficina");
    if(selectDepto && selectOficina){
        selectDepto.style.display = 'none';
        selectOficina.style.display = 'none';
    }

    $('#genReporte').on("click",function(event){
        var form = document.getElementById("reportesForm");
        if(form.checkValidity()){
            $('#loader').show();
        }
    });
});

function seleccionarReporte(checkBox){
    var boxes = document.getElementsByClassName("checkReporte");
    for (var i = 0; i < boxes.length; i++) {
        if(boxes[i].id != checkBox.id){
            if(boxes[i].checked){
                boxes[i].checked =  !boxes[i].checked;
            }
            boxes[i].setAttribute("value", " ");
        }
    }
    checkBox.setAttribute("value","checked");
    var selectDepto = document.getElementById("selectDepto");
    var selectOficina = document.getElementById("selectOficina");
    /*if(checkBox.id == "reporteConsDepto" || checkBox.id == "consumosAreaArt"){
        if(checkBox.checked){
            selectDepto.style.display = 'block';
            selectOficina.style.display = 'block';
            selectDepto.setAttribute("required","");
            selectOficina.setAttribute("required", "");
            selectDepto.removeAttribute("disabled");
            selectOficina.removeAttribute("disabled");
        }else{
            selectDepto.setAttribute("disabled", "true");
            selectOficina.setAttribute("disabled", "true");
            selectDepto.removeAttribute("required");
            selectOficina.removeAttribute("required");
            selectDepto.style.display = 'none';
            selectOficina.style.display = 'none';
        }
    }else{
        selectDepto.style.display = 'none';
        selectOficina.style.display = 'none';
        selectDepto.value = "";
        selectOficina.value = "";
        for (var i = 1; i < selectOficina.options.length; i++) {
            selectOficina.options[i].remove();
        }
        selectDepto.removeAttribute("required");
        selectOficina.removeAttribute("required");
        selectDepto.setAttribute("disabled", "true");
        selectOficina.setAttribute("disabled", "true");
    }*/
}

function unSoloMes(checkBox){
    var lblYearInicio = document.getElementById("lblYearInicio");
    var lblMesInicio = document.getElementById("lblMesInicio");
    var lblYearFin = document.getElementById("lblYearFin");
    var lblMesFin = document.getElementById("lblMesFin");
    var inputInicio = document.getElementById('inptMesInicio');
    var inputFin = document.getElementById('inptMesFin');
    var mesFinIncrement=document.getElementById('mesFinIncrement');
    var mesFinDecrement=document.getElementById('mesFinDecrement');
    var groupMesFin = document.getElementById("groupMesFin");
    var selectYearFin = document.getElementById("selectYearFin");
    if(checkBox.checked){
        lblYearInicio.innerHTML = "Año";
        lblMesInicio.innerHTML = "Mes";
        lblYearFin.setAttribute("hidden", "true");
        lblMesFin.setAttribute("hidden", "true");
        inputFin.setAttribute("disabled", "true");
        mesFinIncrement.setAttribute("disabled", "true");
        mesFinDecrement.setAttribute("disabled", "true");
        groupMesFin.style.display = 'none';
        selectYearFin.removeAttribute("required");
        selectYearFin.setAttribute("disabled", "true");
        selectYearFin.style.display = 'none';
        checkBox.value=true;
    }else{
        lblYearInicio.innerHTML = "Año Inicio";
        lblMesInicio.innerHTML = "Mes Inicial";
        lblYearFin.removeAttribute("hidden");
        lblMesFin.removeAttribute("hidden");
        inputFin.removeAttribute("disabled");
        mesFinIncrement.removeAttribute("disabled");
        mesFinDecrement.removeAttribute("disabled");
        groupMesFin.setAttribute("hidden", "false");
        groupMesFin.style.display = 'table';
        selectYearFin.removeAttribute("disabled");
        selectYearFin.setAttribute("required", "");
        selectYearFin.style.display = 'table';
        checkBox.value=false;
    }

}