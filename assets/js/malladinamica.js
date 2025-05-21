function mallaDinamica(
  table, 
  data,
  cols = {},
  clases={
    title: "text-center fw-bold",
    encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
    bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
  },
  link = $appcfg_Dominio_Raiz + ":90/api_rep.php?ra=S&action=get-facturaPdf&nu=",
  CampoLink=1,   
  ) 
  {

  console.log('table'+table); 
  console.log('data'+data); 
    
  var $html = '';
  const entriesArray = Object.entries(data);
  const totalData = entriesArray.length - 1;
  var inicio = 0;
  for (let index = 0; index < entriesArray.length - 1; index++) {
    console.log('index ' . index);
    if (index == 0) {
      $html = `<div class="row">
      <div class="col-11"><h4 class="${clases.title}">${table.titulo}</h4></div>
      <div class="col-1"><span class="badge rounded-pill bg-primary float-end">Total:${totalData} </span></div>
      </div>`;
      $html += `<div class="row ${clases.encabezado}">`;
      let dataHead = Object.entries(entriesArray[entriesArray.length - 1][1]);
      var show = true;
      var limite = (dataHead.length / 2);
      console.log('dataHead'+dataHead);
      for (let index = 0; index < limite; index++) {
        if (show == true) {
          const campo = dataHead[index][1];
          const colClass = cols[index]?.col ? `col-${cols[index].col}` : 'col';
          $html += `<div class="${colClass} border-primary-subtle">${campo}</div>`;
          show == false
        } else {
          show == true;
        }
      }
      $html += '</div>';
    }

    $html += `<div class="row ${clases.bodyRow}">`;
    let dataBody = Object.entries(entriesArray[index][1]);
    console.log('dataBody '+ dataBody);
    show = true;
    limite = (dataBody.length / 2);
    console.log('limite '+ limite);
    inicio = index;
    for (let index = 0; index < limite; index++) {
        const campo = dataBody[index][1];
        const campox = dataBody[index][0];
        const colClass = cols[index]?.col ? `col-${cols[index].col}` : 'col';
        if (CampoLink != index) {
          $html += `<div id="${table.name + '_' + campo}" class="${colClass}">${campo}</div>`;
        } else {
          console.log(inicio,'inicio');
          console.log(index,'index');
          console.log(CampoLink,'CampoLink');
          //*Poner Link Aqui
          $html += `<div id="${table.name + '_' + campo}" class="${colClass}">
          <a href=${link}${campo} target="_blank" class="btn btn-primary btn-sm">${campo}</a>
          </div>`;
          console.log('LINK INSIDE');
        }
    }

    $html += `</div></br>`;

  }
  return $html;
}