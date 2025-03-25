//*********************************************************************************************************************/
//** Final Function para Establecer los Codigos de los Tramites                                                       **/
//*********************************************************************************************************************/
//*********************************************************************************************************/
//* Inicio: Creando objeto de concesion desde Datos de Preformas
//*********************************************************************************************************/
function guardarConcesionSalvadaPreforma(Tramites, Unidades) {
    var index = 0;
    var Concesion = "";
    var Concesion_Encriptada;
    var Permiso_Explotacion_Encriptado;
    var esCarga;
    var esCertificado;
    var Placa = "";
    var Permiso_Explotacion = "";
    var ID_Formulario_Solicitud = "";
    var TramitesPreforma = Array();
    var index = 1;
    var Unidad1 = "";
    //*********************************************************************************************************/
    //* Inicio: Recorriendo arreglo de concesiones y tramites
    //*********************************************************************************************************/
    Tramites.forEach((row) => {
      //*********************************************************************************************************/
      //* La primera vez que entra llena la variable Concesion
      //*********************************************************************************************************/
      if (index == 1) {
        if (row["N_Permiso_Explotacion"] != "") {
          Concesion = row["N_Certificado"];
          Concesion_Encriptada = row["CertificadoEncriptado"];
          Permiso_Explotacion = row["N_Permiso_Explotacion"];
          Permiso_Explotacion_Encriptado = row["Permiso_Explotacion_Encriptado"];
        } else {
          Concesion = row["N_Certificado"];
          Concesion_Encriptada = row["PermisoEspecialEncriptado"];
          Permiso_Explotacion = "";
          Permiso_Explotacion_Encriptado = "";
        }
      }
  
      if (Concesion == row["N_Certificado"]) {
        esCarga = Boolean(row["esCarga"]);
        esCertificado = Boolean(row["esCertificado"]);
        Placa = row["ID_Placa"];
        var Cantidad_Vencimientos = 1;
        var Fecha_Expiracion_Nueva = "";
        var Fecha_Expiracion = "";
        if (
          row["ID_CHECK"] == "IHTTTRA-02_CLATRA-01_R_PE" &&
          row["Vencimientos"] != false
        ) {
          Fecha_Expiracion = row["Fecha_Expiracion_Explotacion"];
          Cantidad_Vencimientos =
            row["Vencimientos"]["renper-explotacion-cantidad"];
          Fecha_Expiracion_Nueva =
            row["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
        } else {
          if (
            (row["ID_CHECK"] == "IHTTTRA-02_CLATRA-02_R_CO" ||
              row["ID_CHECK"] == "IHTTTRA-02_CLATRA-02_R_PS") &&
            row["Vencimientos"] != false
          ) {
            Fecha_Expiracion = row["Fecha_Expiracion"];
            Cantidad_Vencimientos = row["Vencimientos"]["rencon-cantidad"];
            Fecha_Expiracion_Nueva =
              row["Vencimientos"]["Nueva_Fecha_Expiracion"];
          }
        }
  
        ID_Formulario_Solicitud = row["ID_Formulario_Solicitud"];
        TramitesPreforma.push({
          ID: row["ID"],
          ID_Compuesto: row["ID_CHECK"],
          Codigo: row["ID_Tramite"],
          descripcion: row["DESC_Tipo_Tramite"] + " " + row["DESC_Clase_Tramite"],
          ID_Tramite: row["ID_Tramite"],
          Monto: row["Monto"],
          Total_A_Pagar: parseFloat(
            parseFloat(row["Monto"]).toFixed(2) * Cantidad_Vencimientos
          ).toFixed(2),
          Cantidad_Vencimientos: Cantidad_Vencimientos,
          Fecha_Expiracion: Fecha_Expiracion,
          Fecha_Expiracion_Nueva: Fecha_Expiracion_Nueva,
          ID_Categoria: row["ID_Tipo_Categoria"],
          ID_Tipo_Servicio: row["ID_TIpo_Servicio"],
          ID_Modalidad: row["ID_Modalidad"],
          ID_Clase_Servico: row["ID_Clase_Servicio"],
        });
        //*************************************************************/
        //* Si trae Unidad 1
        //*************************************************************/
        Unidad1 = Unidades[Concesion]?.[1] ?? "";
        if (Tramites.length == index) {
          currentConcesionIndex = updateCollection(Concesion);
          concesionNumber[currentConcesionIndex] = {
            esCarga: esCarga,
            esCertificado: esCertificado,
            Concesion_Encriptada: Concesion_Encriptada,
            Concesion: Concesion,
            Permiso_Explotacion_Encriptado: Permiso_Explotacion_Encriptado,
            Permiso_Explotacion: Permiso_Explotacion,
            ID_Expediente: "",
            ID_Solicitud: "",
            ID_Formulario_Solicitud: ID_Formulario_Solicitud,
            CodigoAvisoCobro: "",
            ID_Resolucion: "",
            Placa: Placa,
            Unidad: Unidades[Concesion]?.[0] ?? "",
            Unidad1: Unidad1,
            Tramites: TramitesPreforma,
          };
          //***********************************************************************/
          //* Agregando concesion pura */
          //***********************************************************************/
          addElementToAutoComplete(Concesion, Concesion);
          //***********************************************************************/
          //* Agregando concesion con permiso de explotacion */
          //***********************************************************************/
          if (Permiso_Explotacion != "") {
            addElementToAutoComplete(
              Concesion,
              Permiso_Explotacion + " => " + Concesion
            );
          }
          //***********************************************************************/
          //* Agregando placa actual asociada a concesion */
          //***********************************************************************/
          if (Unidades[Concesion]?.[0]?.ID_Placa != null) {
            addElementToAutoComplete(
              Concesion,
              Unidades[Concesion][0].ID_Placa + " => " + Concesion
            );
          }
          //***********************************************************************/
          //* Agregando placa Anterior Asociada a concesion */
          //***********************************************************************/
          if (
            Unidades[Concesion]?.[0]?.ID_Placa_Antes_Replaqueo != null &&
            Unidades[Concesion]?.[0]?.ID_Placa != null &&
            Unidades[Concesion][0].ID_Placa_Antes_Replaqueo !==
            Unidades[Concesion][0].ID_Placa
          ) {
            addElementToAutoComplete(
              Concesion,
              Unidades[Concesion][0].ID_Placa_Antes_Replaqueo + " => " + Concesion
            );
          }
          if (Unidad1 != "" && Unidad1.ID_Placa != "undefined") {
            //***********************************************************************/
            //* Agregando placa actual asociada a concesion */
            //***********************************************************************/
            addElementToAutoComplete(
              Concesion,
              Unidad1.ID_Placa + " => " + Concesion
            );
            //***********************************************************************/
            //* Agregando placa Anterior Asociada a concesion */
            //***********************************************************************/
            if (
              Unidad1?.ID_Placa_Antes_Replaqueo != null &&
              Unidad1?.ID_Placa != null &&
              Unidad1.ID_Placa_Antes_Replaqueo !== Unidad1.ID_Placa
            ) {
              addElementToAutoComplete(
                Concesion,
                Unidad1.ID_Placa_Antes_Replaqueo + " => " + Concesion
              );
            }
            //***********************************************************************/
          }
        }
      } else {
        //*************************************************************/
        //* Si trae Unidad 1
        //*************************************************************/
        Unidad1 = Unidades[Concesion]?.[1] ?? "";
        //**********************************************************************************************************************/
        //*Agregando la concesión al arreglo de indice de concesiones y recuperando el indice de la concesion                  */
        //**********************************************************************************************************************/
        currentConcesionIndex = updateCollection(Concesion);
        concesionNumber[currentConcesionIndex] = {
          esCarga: esCarga,
          esCertificado: esCertificado,
          Concesion_Encriptada: Concesion_Encriptada,
          Concesion: Concesion,
          Permiso_Explotacion_Encriptado: Permiso_Explotacion_Encriptado,
          Permiso_Explotacion: Permiso_Explotacion,
          ID_Expediente: "",
          ID_Solicitud: "",
          ID_Formulario_Solicitud: ID_Formulario_Solicitud,
          CodigoAvisoCobro: "",
          ID_Resolucion: "",
          Placa: Placa,
          Unidad: Unidades[Concesion]?.[0] ?? "",
          Unidad1: Unidad1,
          Tramites: TramitesPreforma,
        };
        //***********************************************************************/
        //* Agregando concesion pura */
        //***********************************************************************/
        addElementToAutoComplete(Concesion, Concesion);
        //***********************************************************************/
        //* Agregando concesion con permiso de explotacion */
        //***********************************************************************/
        if (Permiso_Explotacion != "") {
          addElementToAutoComplete(
            Concesion,
            Permiso_Explotacion + " => " + Concesion
          );
        }
        //***********************************************************************/
        //* Agregando placa actual asociada a concesion */
        //***********************************************************************/
        if (Unidades[Concesion]?.[0]?.ID_Placa != null) {
          addElementToAutoComplete(
            Concesion,
            Unidades[Concesion][0].ID_Placa + " => " + Concesion
          );
        }
        //***********************************************************************/
        //* Agregando placa Anterior Asociada a concesion */
        //***********************************************************************/
        if (
          Unidades[Concesion]?.[0]?.ID_Placa_Antes_Replaqueo != null &&
          Unidades[Concesion]?.[0]?.ID_Placa != null &&
          Unidades[Concesion][0].ID_Placa_Antes_Replaqueo !==
          Unidades[Concesion][0].ID_Placa
        ) {
          addElementToAutoComplete(
            Concesion,
            Unidades[Concesion][0].ID_Placa_Antes_Replaqueo + " => " + Concesion
          );
        }
        if (Unidad1 != "" && Unidad1.ID_Placa != "undefined") {
          //***********************************************************************/
          //* Agregando placa actual asociada a concesion */
          //***********************************************************************/
          addElementToAutoComplete(
            Concesion,
            Unidad1.ID_Placa + " => " + Concesion
          );
          //***********************************************************************/
          //* Agregando placa Anterior Asociada a concesion */
          //***********************************************************************/
          if (
            Unidad1?.ID_Placa_Antes_Replaqueo != null &&
            Unidad1?.ID_Placa != null &&
            Unidad1.ID_Placa_Antes_Replaqueo !== Unidad1.ID_Placa
          ) {
            addElementToAutoComplete(
              Concesion,
              Unidad1.ID_Placa_Antes_Replaqueo + " => " + Concesion
            );
          }
          //***********************************************************************/
        }
  
        if (row["N_Permiso_Especial"] == "") {
          Concesion = row["N_Certificado"];
          Concesion_Encriptada = row["CertificadoEncriptado"];
          Permiso_Explotacion = row["Permiso_Explotacion"];
          Permiso_Explotacion_Encriptado = row["Permiso_Explotacion_Encriptado"];
        } else {
          Concesion = row["N_Permiso_Especial"];
          Concesion_Encriptada = row["PermisoEspecialEncriptado"];
          Permiso_Explotacion = "";
        }
  
        //**********************************************************************************************************************/
        //*Agregando la concesión al arreglo de indice de concesiones y recuperando el indice de la concesion                  */
        //**********************************************************************************************************************/
        /*         if (row['ID_Placa1'] != undefined && row['ID_Placa1'] != '' && row['ID_Placa1'] != null) {
            Placa = row['ID_Placa'] + '->' + row['ID_Placa1'];
          } else {
            Placa = row['ID_Placa'];
          } */
  
        Placa = row["ID_Placa"];
        let Cantidad_Vencimientos = 1;
        let Fecha_Expiracion_Nueva = "";
        let Fecha_Expiracion = "";
        if (
          row["ID_CHECK"] == "IHTTTRA-02_CLATRA-01_R_PE" &&
          row["Vencimientos"] != false
        ) {
          Fecha_Expiracion = row["Fecha_Expiracion_Explotacion"];
          Cantidad_Vencimientos =
            row["Vencimientos"]["renper-explotacion-cantidad"];
          Fecha_Expiracion_Nueva =
            row["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
        } else {
          if (
            (row["ID_CHECK"] == "IHTTTRA-02_CLATRA-02_R_CO" ||
              row["ID_CHECK"] == "IHTTTRA-02_CLATRA-02_R_PS") &&
            row["Vencimientos"] != false
          ) {
            Fecha_Expiracion = row["Fecha_Expiracion"];
            Cantidad_Vencimientos = row["Vencimientos"]["rencon-cantidad"];
            Fecha_Expiracion_Nueva =
              row["Vencimientos"]["Nueva_Fecha_Expiracion"];
          }
        }
  
        ID_Formulario_Solicitud = row["ID_Formulario_Solicitud"];
        TramitesPreforma = [];
        TramitesPreforma.push({
          ID: row["ID"],
          ID_Compuesto: row["ID_CHECK"],
          Codigo: row["ID_Tramite"],
          descripcion: row["DESC_Tipo_Tramite"] + " " + row["DESC_Clase_Tramite"],
          ID_Tramite: row["ID_Tramite"],
          Monto: row["Monto"],
          Total_A_Pagar: parseFloat(
            parseFloat(row["Monto"]).toFixed(2) * Cantidad_Vencimientos
          ).toFixed(2),
          Cantidad_Vencimientos: Cantidad_Vencimientos,
          Fecha_Expiracion: Fecha_Expiracion,
          Fecha_Expiracion_Nueva: Fecha_Expiracion_Nueva,
          ID_Categoria: row["ID_Tipo_Categoria"],
          ID_Tipo_Servicio: row["ID_TIpo_Servicio"],
          ID_Modalidad: row["ID_Modalidad"],
          ID_Clase_Servico: row["ID_Clase_Servicio"],
        });
        //*************************************************************/
        //* Si trae Unidad 1
        //*************************************************************/
        Unidad1 = Unidades[Concesion]?.[1] ?? "";
        if (Tramites.length == index) {
          currentConcesionIndex = updateCollection(Concesion);
          concesionNumber[currentConcesionIndex] = {
            esCarga: esCarga,
            esCertificado: esCertificado,
            Concesion_Encriptada: Concesion_Encriptada,
            Concesion: Concesion,
            Permiso_Explotacion_Encriptado: Permiso_Explotacion_Encriptado,
            Permiso_Explotacion: Permiso_Explotacion,
            ID_Expediente: "",
            ID_Solicitud: "",
            ID_Formulario_Solicitud: ID_Formulario_Solicitud,
            CodigoAvisoCobro: "",
            ID_Resolucion: "",
            Placa: Placa,
            Unidad: Unidades[Concesion]?.[0] ?? "",
            Unidad1: Unidad1,
            Tramites: TramitesPreforma,
          };
          //***********************************************************************/
          //* Agregando concesion pura */
          //***********************************************************************/
          addElementToAutoComplete(Concesion, Concesion);
          //***********************************************************************/
          //* Agregando concesion con permiso de explotacion */
          //***********************************************************************/
          if (Permiso_Explotacion != "") {
            addElementToAutoComplete(
              Concesion,
              Permiso_Explotacion + " => " + Concesion
            );
          }
          //***********************************************************************/
          //* Agregando placa actual asociada a concesion */
          //***********************************************************************/
          if (Unidades[Concesion]?.[0]?.ID_Placa != null) {
            addElementToAutoComplete(
              Concesion,
              Unidades[Concesion][0].ID_Placa + " => " + Concesion
            );
          }
          //***********************************************************************/
          //* Agregando placa Anterior Asociada a concesion */
          //***********************************************************************/
          if (
            Unidades[Concesion]?.[0]?.ID_Placa_Antes_Replaqueo != null &&
            Unidades[Concesion]?.[0]?.ID_Placa != null &&
            Unidades[Concesion][0].ID_Placa_Antes_Replaqueo !==
            Unidades[Concesion][0].ID_Placa
          ) {
            addElementToAutoComplete(
              Concesion,
              Unidades[Concesion][0].ID_Placa_Antes_Replaqueo + " => " + Concesion
            );
          }
          if (Unidad1 != "" && Unidad1.ID_Placa != "undefined") {
            //***********************************************************************/
            //* Agregando placa actual asociada a concesion */
            //***********************************************************************/
            addElementToAutoComplete(
              Concesion,
              Unidad1.ID_Placa + " => " + Concesion
            );
            //***********************************************************************/
            //* Agregando placa Anterior Asociada a concesion */
            //***********************************************************************/
            if (
              Unidad1?.ID_Placa_Antes_Replaqueo != null &&
              Unidad1?.ID_Placa != null &&
              Unidad1.ID_Placa_Antes_Replaqueo !== Unidad1.ID_Placa
            ) {
              addElementToAutoComplete(
                Concesion,
                Unidad1.ID_Placa_Antes_Replaqueo + " => " + Concesion
              );
            }
            //***********************************************************************/
          }
        }
      }
      index++;
    });
    //**********************************************************************************************************************/
    //* Llamando a funcion que habilita el AutoComplete                                                                    */
    //**********************************************************************************************************************/
    fAutoComplete();
  }

  //**************************************************************************************/
//* Cargando la información por default que debe usar el formulario
//**************************************************************************************/
function f_DataOmision() {
    //*****************************************************************************************/
    //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
    //*****************************************************************************************/
    loading(true, currentstep);
    //*****************************************************************************************/
    //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
    //*****************************************************************************************/
    var datos;
    var response;
    // Get the URL parameters from the current page
    const urlParams = new URLSearchParams(window.location.search);
    // Get a specific parameter by name
    const RAM = urlParams.get("RAM"); // Número de RAM
    if (RAM != null) {
      document.getElementById("RAM-ROTULO").innerHTML =
        "<strong>" + RAM + "</strong>";
      document.getElementById("RAM-ROTULO").style = "display:inline-block;";
      document.getElementById("RAM").value = RAM;
    } else {
      document.getElementById("RAM-ROTULO").style = "display:none;";
      document.getElementById("RAM").value = "";
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
    fd.append("action", "get-datosporomision");
    fd.append("RAM", RAM);
    // Fetch options
    const options = {
      method: "POST",
      body: fd,
    };
    // Hace la solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then((response) => response.json())
      .then(function (datos) {
        if (
          document
            .getElementById("Ciudad")
            .value.toUpperCase()
            .substring(0, 11) != "TEGUCIGALPA"
        ) {
          document.getElementById("cargadocs").style = "display:flex";
        }
        if (typeof datos[0] != "undefined") {
  
          if (typeof datos[2] != "undefined") {
  
            if (datos[2].length > 0) {
              fLlenarSelect("Departamentos", datos[2], -1, false, {
                text: "SELECCIONE UN DEPARTAMENTO",
                value: "-1",
              });
              fLlenarSelect("Municipios", [], -1, false, {
                text: "SELECCIONE UN MUNICIPIO",
                value: "-1",
              });
              fLlenarSelect("Aldeas", [], -1, false, {
                text: "SELECCIONE UNA ALDEA",
                value: "-1",
              });
            }
          }
  
          if (
            typeof datos[3] != "undefined" &&
            typeof datos[3][0] != "undefined"
          ) {
            //*Moviendo campos de base de datos a datos de pantalla Apoderado Legal
            document.getElementById("nomapoderado").value =
              datos[3][0]["Nombre_Apoderado_Legal"];
            document.getElementById("colapoderado").value =
              datos[3][0]["ID_Colegiacion"];
            document.getElementById("identidadapod").value =
              datos[3][0]["Ident_Apoderado_Legal"];
            document.getElementById("dirapoderado").value =
              datos[3][0]["Direccion_Apoderado_Legal"];
            document.getElementById("telapoderado").value =
              datos[3][0]["Telefono_Apoderado_Legal"];
            document.getElementById("emailapoderado").value =
              datos[3][0]["Email_Apoderado_Legal"];
            document.getElementById("ID_Apoderado").value = datos[3][0]["ID"];
  
            if (datos[4].length > 0) {
              fLlenarSelect("Departamentos", datos[11], datos[4][0]["ID_Departamento"], false, {
                text: "SELECCIONE UN DEPARTAMENTO",
                value: "-1",
              });
              fLlenarSelect("Municipios", datos[10], datos[4][0]["ID_Municipio"], false, {
                text: "SELECCIONE UN MUNICIPIO",
                value: "-1",
              });
              fLlenarSelect("Aldeas", datos[9], datos[4][0]["ID_Aldea"], false, {
                text: "SELECCIONE UNA ALDEA",
                value: "-1",
              });
            }
  
            //*******************************************************************************************************************/
            //* Si el elemento 12 existe quiero decir que ya esta convertido a Expediente la FLS
            //*******************************************************************************************************************/
            if (datos?.[12]) {
              document.getElementById("ID_Expediente").value = datos[12];
              document.getElementById("ID_Solicitud").value = datos[12];
            } else {
              document.getElementById("ID_Expediente").value = '';
              document.getElementById("ID_Solicitud").value = '';
            }
  
            fLlenarSelect(
              "entregadocs",
              datos[1],
              datos[4][0]["Entrega_Ubicacion"],
              false,
              {
                text: "SELECCIONE UN LUGAR DE ENTREGA",
                value: "-1",
              }
            );
  
            document.getElementById("tipopresentacion").value = datos[4][0]["Presentacion_Documentos"];
            //* Moviendo campos de base de datos a datos de pantalla Solicitante
            if (typeof datos[4] != "undefined") {
              document.getElementById("rtnsoli").value =
                datos[4][0]["RTN_Solicitante"];
              document.getElementById("nomsoli").value =
                datos[4][0]["Nombre_Solicitante"];
              document.getElementById("denominacionsoli").value =
                datos[4][0]["Denominacion_Social"];
              document.getElementById("domiciliosoli").value =
                datos[4][0]["Domicilo_Solicitante"];
              document.getElementById("idEstado").innerHTML = $appcfg_icono_de_importante + datos[4][0]["DESC_Estado"];
              document.getElementById("telsoli").value =
                datos[4][0]["Telefono_Solicitante"];
              document.getElementById("emailsoli").value =
                datos[4][0]["Email_Solicitante"];
              document.getElementById("tiposolicitante").value =
                datos[4][0]["ID_Tipo_Solicitante"];
              document.getElementById("Departamentos").value =
                datos[4][0]["ID_Departamento"];
              document.getElementById("ID_Solicitante").value = datos[4][0]["ID"];
              document.getElementById("ID_Estado_RAM").value =
                datos[4][0]["Estado_Formulario"];
  
            }
            //***************************************************************************/
            //* Armando Objeto de Concesiones Salvadas en Preforma
            //***************************************************************************/
            if (typeof datos[5] != "undefined") {
              guardarConcesionSalvadaPreforma(datos[5], datos[7]);
            }
            //***************************************************************************/
            //* Estableciento el Link del Expediente Cargado para Trabajarlo
            //***************************************************************************/
            if (typeof datos[8] != "undefined" && datos[8] != false) {
              document.getElementById("fileUploaded").style.display = "block";
              document
                .getElementById("fileUploadedLink")
                .setAttribute("href", $appcfg_Dominio + datos[8]);
            } else {
              document.getElementById("fileUploaded").style.display = "none";
            }
            //***************************************************************************/
            //* Marcar requicitos
            //***************************************************************************/
            fMarcarRequicitos();
          } else {
            if (datos[1].length > 0) {
              fLlenarSelect("entregadocs", datos[1], null, false, {
                text: "SELECCIONE UN LUGAR DE ENTREGA",
                value: "-1",
              });
            }
          }
        } else {
          if (typeof datos.error != "undefined") {
            fSweetAlertEventNormal(
              datos.errorhead,
              datos.error + "- " + datos.errormsg,
              "error"
            );
          } else {
            fSweetAlertEventNormal(
              "INFORMACIÓN",
              "ALGO RARO PASO, INTENTELO DE NUEVO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
              "error"
            );
          }
        }
        //*****************************************************************************************/
        //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        loading(false, currentstep);
        if (concesionNumber.length < 1) {
          document.getElementById("input-prefetch").style.display = "none";
          document.getElementById("toggle-icon").style.display = "none";
          document.getElementById("rightDiv").style.display = "none";
        } else {
          document.getElementById("input-prefetch").style.display = "block";
          document.getElementById("toggle-icon").style.display = "block";
          document.getElementById("rightDiv").style.display = "flex";
        }
        startCelebration();
        inicialitarTomSelect();
        //*****************************************************************************************/
        //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
      })
      .catch((error) => {
        //*****************************************************************************************/
        //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        loading(false, currentstep);
        //*****************************************************************************************/
        //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        console.log("error f_DataOmision() " + error);
        fSweetAlertEventNormal(
          "OPPS",
          "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
          "error"
        );
      });
  }
  