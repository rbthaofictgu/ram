// Función para calcular el porcentaje de scroll restante
function calcularPorcentajeRestante() {
  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  const windowHeight = window.innerHeight || document.documentElement.clientHeight;
  const fullHeight = document.documentElement.scrollHeight;
  const scrollPercentage = (scrollTop / (fullHeight - windowHeight)) * 100;
  return scrollPercentage;
}
// Ejemplo de uso
window.addEventListener('scroll', () => {
  const porcentaje = calcularPorcentajeRestante();
  if (porcentaje> 15){
    document.getElementById('reading_progress_masterbar_id').style="display:block;"
  }
  document.getElementById('reading_progress_bar_id').style=`width: ${porcentaje.toFixed(2)}%;`
  //console.log(`Porcentaje de scroll ${porcentaje.toFixed(2)}% del total de la altura de la pagina].`);
});