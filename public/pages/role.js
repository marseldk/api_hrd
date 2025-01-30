$(document).ready(function () {
  const loadRoles = () => {
    const countSelectNikFunc = $("#modal-role").find(
      'select[name="nik_func"] option'
    ).length;

    if (countSelectNikFunc == 0) {
      ajax_post({
        url: "/role/list",
        beforeSend: function () {
          before_send("Loading nik functional data....");
          $("#modal-role").find("select[name=nik_func]").html("");
        },
        success: function (result) {
          Swal.close();
          if (result.code == 200) {
            $.each(result.data, function (index, value) {
              $("#modal-role")
                .find("select[name=nik_func]")
                .append(
                  '<option value="' +
                    value.nik_func +
                    '">' +
                    value.init +
                    " - " +
                    value.nama_func +
                    "</option>"
                );
            });
            tomSelectInit("#modal-role select[name=nik_func]");
          }
        },
      });
    }
  };

  $("a[id=change-role]").click(function () {
    loadRoles();
    $("#modal-role").modal("show");
  });

  $("#form-role").on("submit", function (e) {
    e.preventDefault();

    const data = $(this).serialize();
    ajax_post({
      url: "/role/change",
      data: data,
      success: function (result) {
        if (result.code == 200) {
          Swal.fire({
            title: "Success",
            html: result.message,
            icon: "success",
            confirmButtonColor: "#206bc4",
            cancelButtonColor: "#d33",
            focusConfirm: true,
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.reload();
            }
          });
        } else {
          Swal.fire({
            title: "Attention",
            html: result.message,
            icon: "error",
            confirmButtonColor: "#206bc4",
            cancelButtonColor: "#d33",
            focusConfirm: true,
          });
        }
      },
    });
  });

  $("#modal-role").on("click", "button[id=btn-save]", function () {
    $("#form-role").submit();
  });
});
