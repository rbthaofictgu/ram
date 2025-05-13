function inicialitarTomSelect() {
    console.log('Antes de Inicializar');
    $('.select2bs4').select2({
        theme: 'bootstrap4',
        allowEmptyOption: true,
      });
    console.log('Despues de Inicializar');
}    