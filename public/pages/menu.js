$(document).ready(function () {
  const loadParent = () => {
    $.ajax({
      url: "/menu/data-parent",
      type: "POST",
      dataType: "JSON",
      beforeSend: function () {
        before_send("Loading Parent Menu");
        $('#modal-menu select[name="parent_id"]').html("");
      },
      success: function (response) {
        Swal.close();
        let html = "";
        html += `<option value="0">No Parent</option>`;
        for (item of response.data) {
          html += `<option value="${item.id}">${item.name}</option>`;
        }
        $('#modal-menu select[name="parent_id"]').html(html);
      },
      error: function (error) {
        Swal.close();
        show_error({
          html: error.responseText,
        });
      },
    });
  };

  $("button[id=btn-add]").on("click", function () {
    loadParent();
    $("#modal-menu").find(".modal-title").html("Add Menu");
    $("#modal-menu").modal("show");
  });

  $("#form-menu").submit(function (e) {
    e.preventDefault();

    const data = $(this).serialize();
    const id = $("#modal-menu").find('input[name="id"]').val();
    const url = id ? "/menu/update/" : "/menu/store";
    const post = () => {
      ajax_post({
        url: url,
        data: data,
        beforeSend: function () {
          before_send("Saving Menu");
        },
        success: function (response) {
          Swal.close();
          $("#modal-menu").modal("hide");
          show_success({
            title: "Success",
            html: response.message,
          });
          //reset field
          $("#modal-menu").find('input[name="id"]').val("");
          $("#modal-menu").find('input[name="name"]').val("");
          $("#modal-menu").find('input[name="url"]').val("");
          $("#modal-menu").find('input[name="icon"]').val("");
          $("#modal-menu").find('select[name="parent_id"]').val("");
          // $("#table-menu").DataTable().ajax.reload();
        },
        error: function (error) {
          Swal.close();
          //convert error to json and get message key
          const message = JSON.parse(error.responseText).message;
          show_error({
            html: message,
          });
        },
      });
    };

    //prompt
    prompt_swal(post, {
      title: "Save Menu",
      text: "Are you sure to save this menu?",
    });
  });

  $("#modal-menu").on("click", "button[id=btn-save]", function () {
    //check if form valid html5
    const form = $("#form-menu");
    // Trigger HTML5 validity.
    const reportValidity = form[0].reportValidity();
    // Then submit if form is OK.
    if (reportValidity) {
      form.submit();
    }
  });

  new NestedSort({
    data: [
      { id: 1, text: "Item 1", icon: "asd" },
      { id: 11, text: "Item 1-1", parent: 1 },
      { id: 2, text: "Item 2" },
      { id: 3, text: "Item 3" },
      { id: 111, text: "Item 1-1-1", parent: 11 },
      { id: 112, text: "Item 1-1-2", parent: 11 },
      { id: 31, text: "Item 3-1", parent: 3 },
    ],
    actions: {
      onDrop(data) {
        // receives the new list structure JSON after dropping an item
        console.log(data);
      },
    },
    el: "#nested-sort-wrap", // a wrapper for the dynamically generated list element
    listClassNames: ["ns-v5"], // an array of custom class names for the dynamically generated list element
    renderListItem: (el, { id }) => {
      if (id === 2) el.textContent += " (this is a custom rendered item)";
      return el;
    },
  });
});
