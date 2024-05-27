 /* ES: Crearás una variable global en el archivo JS para almacenar el valor de la tabla de datos a construir. Esto te será de gran utilidad cuando necesites actualizar la información de la tabla sin tener que recargar la página. */
 /* EN: You will create a global variable in the JS file to store the value of the data table to be built. This will be very useful when you need to update the table information without reloading the page. */
var dataTable;

/* ES: Define esta variable que contendrá la base del servidor a donde se enviarán las peticiones */
/* EN: Define this variable that will hold the server base where requests will be sent to */
var baseUrl = window.location.protocol + "//" + window.location.host

/* ES: Al Cargar el archivo */
/* EN: Upon loading the file */
$(document).ready(function () {

  /* ES: Aquí le asignaremos a la variable una instancia de dataTable, garantiza que sea el mismo ID del tag table */
  /* EN: Here, we will assign an instance of dataTable to the variable, ensure it matches the ID of the table tag */
  dataTable = $("#datatable").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    pagingType: "full_numbers",
    ajax: {
      /* ES: Asegúrate de que la ruta (web.php) sea de tipo GET */
      /* EN: Ensure that the route (web.php) is of type GET */
      url: baseUrl + "/modulo/datatable",
      dataType: "JSON",
      type: "GET",
    },
    columns: [
      /* ES: El nombre debe ser el mismo que el del arreglo asociativo devuelto desde el BackEnd */
      /* EN: The name must be the same as that of the associative array returned from the BackEnd */
      { data: "identification" },
      { data: "employee" },
      { data: "novelty_type" },
      { data: "description" },
      { data: "calendar_days" },
      { data: "business_days" },
      { data: "initial_date" },
      { data: "final_date" },
      { data: "action",
        render: function (data, type, row, meta) {
          let btnEdit = "";
          if (data.editar) {
            btnEdit = `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                        <i class="fa flaticon-edit-1"></i>
                                    </button>`;
          }
          return `<div class='btn-group'>${btnEdit}</div>`;
        },
        orderable: false,
      },
    ],
    /* ES: Recuerda que puedes aplicar la configuración de idioma de tu región. */
    /* EN: Remember that you can apply the language configuration of your region. */
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json",
    },
  });
});
