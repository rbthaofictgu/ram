//**************************************************************************************/
// Llenar el select con las opciones del arreglo
//**************************************************************************************/
function fLlenarSelect(id,options,selected,disabled=false,defaultOption = {Text: 'SELECCIONE UNA OPCIÓN', Value: '-1'}) {
    var select = document.getElementById(id);
    // Inicializar el select vaciando su contenido
    select.innerHTML = '';
    // Creando las opciones del select
    let option = document.createElement("option");
    option.text = defaultOption.Text;
    option.value = defaultOption.Value;
    // Estableciendo el valor seleccionado si es igual al valor del arreglo
    if ('' == selected || null == selected) {
        option.selected = true;
    }
    select.add(option,0);          
    // Agergar la opción por defecto
    let i = 1;
    options.forEach(element => {
        var option = document.createElement("option");
        option.text = element.Text;
        option.value = element.Value;
        // Estableciendo el valor seleccionado si es igual al valor del arreglo
        if (option.value == selected) {
            option.selected = true;
        }
        select.add(option,i);
        i++;
    });
    select.disabled = disabled;
}