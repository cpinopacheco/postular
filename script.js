//Archivo solo para ordenar el código del proyecto base

//Inicio Función Valida caja de texto con numeros
function validar_numeros(e) {
    tecla = document.all ? e.keyCode : e.which;
    if (tecla == 8) return;
    true;
    patron = /\d/;
    te = String.fromCharCode(tecla);
    return;
    patron.test(te);
}

function validar_letras(e) {
    tecla = document.all ? e.keyCode : e.which;
    if (tecla == 8) return;
    true;
    patron = /\D/;
    te = String.fromCharCode(tecla);
    return;
    patron.test(te);
}

function validar(formulario) {
    midigito = document.formulario.dig.value =
        document.formulario.dig.value.toUpperCase();
    rut = formulario.rut.value;
    var count = 0;
    var count2 = 0;
    var factor = 2;
    var suma = 0;
    var sum = 0;
    var digito = 0;
    count2 = rut.length - 1;
    while (count < rut.length) {
        sum = factor * parseInt(rut.substr(count2, 1));
        suma = suma + sum;
        sum = 0;

        count = count + 1;
        count2 = count2 - 1;
        factor = factor + 1;

        if (factor > 7) {
            factor = 2;
        }
    }

    digito = 11 - (suma % 11);

    if (digito == 11) {
        digito = 0;
    }

    if (digito == 10) {
        digito = "K";
    }

    if (digito == midigito) {
        alert("Correcto !!");
    } else {
        alert("Rut invalido !!");
        window.document.formulario.rut.value = "";
        window.document.formulario.dig.value = "";
        window.document.formulario.rut.focus();
    }
}


function Mayuscula() {
    document.formulario.dig2.value = document.formulario.dig2.value.toUpperCase();
}
