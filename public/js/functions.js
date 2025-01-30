$.ajaxSetup({
  headers: {
    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
  },
});

//add option to select function
const add_option = (
  selector,
  text = "",
  value = "",
  selected = false,
  data = {},
  disabled = false
) => {
  // console.log(selector, text, value, selected);
  $(selector).append(
    $("<option />")
      .val(value)
      .text(text)
      .prop("selected", selected)
      .prop("disabled", disabled)
      .data(data)
  );
};

const before_send = (text = "Getting your data...") => {
  Swal.fire({
    title: "Wait a moment",
    text: text,
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  $("input.is-invalid").removeClass("is-invalid");
  $("select.is-invalid").removeClass("is-invalid");
};

const show_field_invalid = (id_form, data) => {
  $.each(data, function (index, key) {
    $("#" + id_form + " #" + key).addClass("is-invalid");
  });
};

const show_success = (custom_option = {}) => {
  const option = {
    title: "Success",
    html: "Success",
    icon: "success",
    confirmButtonColor: "#206bc4",
    cancelButtonColor: "#d33",
    focusConfirm: true,
  };
  // console.log(option, 'success');
  Swal.fire({
    ...option,
    ...custom_option,
  });
};

const show_error = (custom_option = {}) => {
  const option = {
    title: "Attention",
    html: "Error",
    icon: "error",
    confirmButtonColor: "#206bc4",
    cancelButtonColor: "#d33",
    focusConfirm: true,
  };

  console.log(custom_option.html, "error");
  if (custom_option.html == "CSRF token mismatch.") {
    custom_option.didClose = () => {
      window.location.reload();
    };
  }
  console.log(custom_option);
  // console.log(option, 'error');
  Swal.fire({
    ...option,
    ...custom_option,
  });
};

const ajax_post = (custom_option = {}) => {
  const option = {
    type: "POST",
    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
    dataType: "JSON",
    beforeSend: function () {
      before_send("Sending your data...");
    },
    success: (result) => {},
    error: (xhr) => {
      Swal.close();
      const error = xhr.responseJSON;
      const message =
        error == undefined ? "Failed processing data" : error.message;
      show_error({
        html: message,
      });
    },
  };

  $.ajax({
    ...option,
    ...custom_option,
  });
};

const prompt_swal = (confirm = () => {}, custom_option = {}) => {
  const option = {
    title: "Are you sure",
    html: "You're want to save this data ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#206bc4",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, save it!",
    cancelButtonText: "No, cancel!",
    focusConfirm: true,
  };

  Swal.fire({
    ...option,
    ...custom_option,
  }).then((response) => {
    if (response.value) {
      confirm();
    }
  });

  $("button.swal2-confirm").focus();
};

const is_not_empty = (value) => {
  return value != null && typeof value != "undefined" && value != "";
};

function is_null(variable, defaultValue) {
  return variable !== null && variable !== undefined ? variable : defaultValue;
}

// Function to remove a field by name from FormData
function removeField(formData, fieldName) {
  // Create a new FormData object
  let newFormData = new FormData();

  // Copy all fields from the original FormData object to the new one, excluding the field to be removed
  for (let pair of formData.entries()) {
    let key = pair[0];
    let value = pair[1];

    if (key !== fieldName) {
      newFormData.append(key, value);
    }
  }

  return newFormData;
}

$(".modal").on("shown.bs.modal", function () {
  $(document).off("focusin.modal");
});

function calculatePercentage(value, total) {
  value = parseFloat(value);
  total = parseFloat(total);

  if (isNaN(value) || isNaN(total) || total === 0) {
    return "0.00"; // To handle invalid or zero total
  }
  const percentage = (value / total) * 100;
  return percentage.toFixed(2);
}

const money = (value) => {
  value = value.toString();
  const cleanValue = +value.replace(/\D+/g, "");
  const options = { style: "decimal" };
  return new Intl.NumberFormat("id-ID", options).format(cleanValue);
};

const reverseMoney = (value) => {
  //   value = value.replaceAll("Rp ", "");
  value = value.toString();
  value = value.replaceAll(".", "");
  value = value.replaceAll("_", "");
  value = value.replaceAll(",", ".");
  value = parseFloat(value);
  return value;
};

document.querySelectorAll("input.mask-money").forEach(($input) => {
  $input.addEventListener(
    "input",
    (e) => {
      e.target.value = money(e.target.value);
    },
    false
  );
});

const toUpperCaseTitle = (value) => {
  value = value.toString();
  value = value.replaceAll("_", " ");
  value = value.toUpperCase();
  return value;
};

function dataURItoBlob(dataURI) {
  const byteString = atob(dataURI.split(",")[1]);
  const ab = new ArrayBuffer(byteString.length);
  const ia = new Uint8Array(ab);
  for (let i = 0; i < byteString.length; i++) {
    ia[i] = byteString.charCodeAt(i);
  }
  return new Blob([ab], { type: "image/jpeg" }); // Change 'image/jpeg' to match the image type
}

const tomSelectInit = (selector) => {
  window.TomSelect &&
    new TomSelect((el = $(selector)), {
      copyClassesToDropdown: false,
      dropdownParent: "body",
      controlInput: "<input>",
      render: {
        item: function (data, escape) {
          //   console.log(data);
          if (data.customProperties) {
            return (
              '<div><span class="dropdown-item-indicator">' +
              data.customProperties +
              "</span>" +
              escape(data.text) +
              "</div>"
            );
          }
          return "<div>" + escape(data.text) + "</div>";
        },
        option: function (data, escape) {
          if (data.customProperties) {
            return (
              '<div><span class="dropdown-item-indicator">' +
              data.customProperties +
              "</span>" +
              escape(data.text) +
              "</div>"
            );
          }
          return "<div>" + escape(data.text) + "</div>";
        },
      },
    });
};
