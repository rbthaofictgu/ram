//**************************************************************************************/
// Llenar el select con las opciones del arreglo
//**************************************************************************************/
function fLlenarSelect(id,options,selected,disabled=false,defaultOption = {text: 'SELECCIONE UNA OPCIÓN', value: '-1'},dwd_hijo=false,option_default_hijo=['<opcion value="-1">SELECCIONE UNA ALDEA</opcion>']) {
    var select = document.getElementById(id);
    // Inicializar el select vaciando su contenido
    select.innerHTML = '';
    // Creando las opciones del select
    let option = document.createElement("option");
    option.text = defaultOption.text;
    option.value = defaultOption.value;
    // Estableciendo el valor seleccionado si es igual al valor del arreglo
    if ('' == selected || null == selected) {
        option.selected = true;
    }
    select.add(option,0);          
    // Agergar la opción por defecto
    let i = 1;
    options.forEach(element => {
        var option = document.createElement("option");
        option.text = element.text;
        option.value = element.value;
        // Estableciendo el valor seleccionado si es igual al valor del arreglo
        if (option.value == selected) {
            option.selected = true;
        }
        select.add(option,i);
        i++;
    });
    //**************************************************************************************/
    select.disabled = disabled;
    //**************************************************************************************/
    // Limpiando y estableciendo las opciones por defecto en los select hijos
    //**************************************************************************************/
    if (dwd_hijo != false) {
        i=0;
        dwd_hijo.forEach(element => {
            document.getElementById(element).innerHTML = ''; 
            document.getElementById(element).innerHTML = option_default_hijo[i]; 
            i++;
        });
    }
    let divElement = document.getElementById(id+'div');
    if (divElement !== null) {    
        divElement.style = "display:block";
    }    
    let spinnerElement = document.getElementById(id + 'spinner');
    if (spinnerElement !== null) {    
        spinnerElement.style = "display:none";
    }
}

//**************************************************************************************/
//Cargando la información por default que debe usar el formulario
//**************************************************************************************/
function fCargarDwd(accion,filtro,dwd,selected,option_default={text: 'SELECCIONE UN OPCIÓN', value: '-1'},dwd_hijo=false,option_default_hijo=['<opcion value="-1">SELECCIONE UNA ALDEA</opcion>']) {
    var divElement = document.getElementById(dwd+'div');
    if (divElement !== null) {    
        divElement.style = "display:none;";
    }    
    var spinnerElement = document.getElementById(dwd + 'spinner');
    if (spinnerElement !== null) {    
        spinnerElement.style = "display:block;";
    }
    // URL del Punto de Acceso a la API
    const url = $appcfg_Dominio + "Api_Ram.php";
    //  Fetch options
    // const options = {
    //   method: 'POST',
    //   body: fd,
    //   headers: {
    //     'Content-Type': 'application/json',
    //   },
    // };
    let fd = new FormData(document.forms.form1);
    //Adjuntando el action al FormData
    fd.append("action", accion);
    fd.append("filtro", filtro);
    fd.append("echo", true);
    // Fetch options
    const options = {
        method: 'POST',
        body: fd,
    };
    // Hace la solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
        .then(response => response.json())
        .then(function (datos) {
        if (typeof datos != 'undefined') {
            if (datos != false) {
                fLlenarSelect(dwd,datos,selected,false,option_default,dwd_hijo,option_default_hijo);
            } else {
                fSweetAlertEventNormal('INFORMACIÓN', 'ALGO RARO PASO, INTENTELO DE NUEVO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA', 'error');
            }       
        } else {
            if (typeof datos.error != 'undefined') {
                fSweetAlertEventNormal(datos.errorhead, datos.error + '- ' + datos.errormsg , 'error');
            } else {
                console.log(datos);
                fSweetAlertEventNormal('INFORMACIÓN', 'ALGO RARO PASO, INTENTELO DE NUEVO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA', 'error');
            }
        }
        })
        .catch((error) => {
        console.log('error'+error);
        fSweetAlertEventNormal('CONEXÍON', 'ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA', 'warning');
    });
}