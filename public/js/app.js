var Mascara =  {
    setCPF: function (obj) {
        obj.maxLength = 14;
        obj.onkeypress = function (ev) {
            ev = window.event || ev;
            var keyCode = ev.keyCode || ev.which;

            if (keyCode === 9 || keyCode === 46 || keyCode === 8)
                return true;

            if (!Number.isNumber(String.fromCharCode(keyCode)))
                return false;

            var temp = obj.value;

            if (temp.length == 3){
                temp += '.';
                obj.value = temp;
                return;
            }

            if (temp.length == 7){
                temp += '.';
                obj.value = temp;
                return;
            }

            if (temp.length == 11){
                temp += '-';
                obj.value = temp;
                return;
            }
        };
    },

    setCelular: function (obj) {
        obj.maxLength = 15;
        obj.onkeypress = function (ev) {
            ev = window.event || ev;
            var keyCode = ev.keyCode || ev.which;

            if (keyCode === 9 || keyCode === 46 || keyCode === 8)
                return true;

            if (!Number.isNumber(String.fromCharCode(keyCode)))
                return false;

            var temp = obj.value;

            if (temp.length == 0){
                temp += '(';
                obj.value = temp;
                return true;
            }

            if (temp.length == 3){
                temp += ') ';
                obj.value = temp;
                return true;
            }

            if (temp.length == 4){
                temp += ' ';
                obj.value = temp;
                return true;
            }

            if (temp.length >= 6) {
                if (temp.substring(5, 6) == '9') {
                    if (temp.length >= 4) {
                        if (temp.substring(0, 4) == '(11)' || temp.substring(0, 4) == '(12)' || temp.substring(0, 4) == '(13)'
                                || temp.substring(0, 4) == '(14)' || temp.substring(0, 4) == '(15)' || temp.substring(0, 4) == '(16)'
                                || temp.substring(0, 4) == '(17)' || temp.substring(0, 4) == '(18)' || temp.substring(0, 4) == '(19)'
                                || temp.substring(0, 4) == '(21)' || temp.substring(0, 4) == '(22)' || temp.substring(0, 4) == '(24)'
                                || temp.substring(0, 4) == '(27)' || temp.substring(0, 4) == '(28)') {
                            if (temp.length == 10){
                                obj.maxLength = 15;
                                temp += '-';
                                obj.value = temp;
                                return true;
                            }
                        }else {
                            
                            if (temp.length == 9){
                                obj.maxLength = 14;
                                temp += '-';
                                obj.value = temp;
                                return true;
                            }
                        }
                    }
                } else {
                    if (temp.length == 9){
                        obj.maxLength = 14;
                        temp += '-';
                        obj.value = temp;
                        return true;
                    }
                }
            }
        };
    },

    setOnlyNumbers: function (obj) {
        obj.onkeypress = function (ev) {
            ev = window.event || ev;
            var keyCode = ev.keyCode || ev.which;

            if (keyCode === 9 || keyCode === 8)
                return true;

            if(keyCode == 44 || keyCode == 46){
                return false;
            }

            if (!Number.isNumber(String.fromCharCode(keyCode)))
                return false;
        };
    },    

};

Number.isNumber = function (text) {
    if (text.trim() === '' || isNaN(Number.getFloat(text).toString()) || Number.Filter(text, '.,') === '')
        return false;

    return true;
};

Number.Filter = function (text, others) {
    
    var numeros = "12334567890";

    if (others)
        numeros += others;

    var temp = '';

    for (var i = 0; i < text.length; i++)
        if (numeros.indexOf(text[i]) >= 0)
            temp += text[i];

    return temp;
};

Number.getFloat = function (texto) {
    
    if (Number.Filter(texto, '') === '')
        return 0.0;

    var temp = Number.Filter(texto, ",.");
    var found = false;
    var numero = '';

    for (var i = temp.length - 1; i >= 0; i--)
        if ((temp[i] === '.' || temp[i] === ',') && !found) {
            found = true;
            numero = '.' + numero;
        }
        else {
            numero = temp[i] + numero;
        }

    return parseFloat(numero);
};

Number.parseFloat = function (texto) {
    
    if (Number.Filter(texto, '') === '')
        return 0.0;

    texto = texto.toString().replace('.', '');
    texto = texto.replace();

    var temp = Number.Filter(texto, ",.");

    var found = false;
    var numero = '';

    for (var i = temp.length - 1; i >= 0; i--)
        if ((temp[i] === '.' || temp[i] === ',') && !found) {
            found = true;
            numero = '.' + numero;
        }
        else {
            numero = temp[i] + numero;
        }

    return parseFloat(numero);
};

Number.parseFloatNegativo = function (texto) {

    if (typeof texto === 'number')
        texto = texto.toString();

    if (Number.Filter(texto, '') === '')
        return 0.0;
    texto = texto.replaceAll(' ', '');
    for (var i = texto.length - 1; i >= 0; i--) {
        if ((texto[i] === '.' || texto[i] === ',')) {
            if (texto[i] === '.')
                texto = texto.replaceAll(',', '');
            else {
                texto = texto.replaceAll('.', '');
                texto = texto.replaceAll(',', '.');
            }
            break;
        }
    }
    var n = 0;
    try {
        n = parseFloat(Number.Filter(texto, "Ee+-,."));
    } catch (e) {
        console.error('Erro conversão de tipo');
    }
    ;
    return n;
}

Number.FormatarDinheiro = function (valor) {

    valor = valor.toFixed(2);
    var teste = valor.toString().split('.');

    if (teste.length === 1) {
        valor += '.00';
    }
    else {
        if (teste[1].length < 2) {
            valor += '0';
        }
    }

    valor = valor.toString().replace('.', '');

    var tmp = valor + '';
    tmp = tmp.replace(/([0-9]{2})$/g, ",$1");

    if (tmp.length > 6)
        tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");

    return tmp;
};


$(document).ready(function() {
    if($('#formatarCpf').length) {
        Mascara.setCPF(document.getElementById('formatarCpf'));
    }

    if($('#formatarTelefone').length) {
        Mascara.setCelular(document.getElementById('formatarTelefone'));
    }

    if($('#formatarMatricula').length) {
        Mascara.setOnlyNumbers(document.getElementById('formatarMatricula'));
    }

});