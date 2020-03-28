

var dic = {};
var cookies = decodeURIComponent(document.cookie).split(';');
for(var i = 0; i < cookies.length; i++){
    var partes = cookies[i].split('=');
 	dic[partes[0].substring(1)] = partes[1].trim();
}

if(dic['__office_session'] == undefined){
	$("#loaderLogin").show();
	$("#loginModal").show();
}

$(document).ready(function(){
	if(dic['__office_session'] != undefined){
		$("#loaderLogin").hide();
		$("#loginModal").hide();
	}else{
		$("#loaderLogin").hide();
	}

	$("#visualizar").on('click', function(){
		var officeCode = document.getElementById('officeCode');
		if($('#officeCode').attr('type') == 'password'){
			officeCode.setAttribute('type', 'text');
			$('#ojo').removeClass('fa-eye');
			$('#ojo').addClass('fa-eye-slash');
		}else{
			officeCode.setAttribute('type', 'password');
			$('#ojo').removeClass('fa-eye-slash');
			$('#ojo').addClass('fa-eye');
		}

	});

	$("#btnLogin").on('click',function(event){
		event.preventDefault();
		var token = $('meta[name="csrf-token"]').attr('content');
		var codigo = $("#officeCode").val();
		var form = document.getElementById("oficina_login");
		if(codigo.trim()){
			var sha256 = Crypto.createHash("sha256");
			sha256.update(codigo);
			var result = sha256.digest("hex");
			$.ajax({
				url: "almacen/oficinas/login",
		        type: "POST",
		        dataType: "json",
		        data: {code:result, _token:token},
		        beforeSend: function(){
		            $("#loaderLogin").show();
		        },
		        success: function(resultdata){
		        	if(resultdata['code']==200){
		        		var now = new Date();
						var time = now.getTime();
						time += 1800 * 1000;
						now.setTime(time);
						document.cookie=`__office_session=${resultdata['officeCode']}; expires=${now.toUTCString()}; path=/`;
						window.location.reload();
		        	}else if(resultdata['code']==400){
		        		alert('Código inválido');
		        		$("#loaderLogin").hide();
		        	}else if(resultdata['code']==505){
		        		alert('Error de servidor o base de datos, porfavor contacte al departamento de tecnologías de la información.');
		        		$("#loaderLogin").hide();
		        	}
		        },
		        error: function(){
		        	alert('Error al conectarse con la base de datos\nPorfavor contecte al departamento de tecnologías de la información');
		        	$("#loaderLogin").hide();
		        },
		        timeout:5000
			});
		}else{
			var msg = '<label class="error" for="officeCode">Porfavor ingrese su codigo de oficina</label>';
          	$('#officeCode').addClass('inputTxtError').after(msg);
		}
	});
});

function clearLabels(){
	$('#officeCode').removeClass("inputTxtError");
	$('label.error').remove();
}