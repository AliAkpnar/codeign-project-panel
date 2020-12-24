/* $(document).ready(function () {
  $(".remove-btn").click(function () {
    let $data_url = $(this).data("url");

    swal({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel",
    }).then(function (result) {
      if (result.value) {
        window.location.href = $data_url;
      }
    });
  });

  $(".isActive").change(function () {
    let $data = $(this).prop("checked");
    let $data_url = $(this).data("url");

    if (typeof $data !== "undefined" && typeof $data_url !== "undefined") {
      $.post($data_url, { data: $data }, function (response) {});
    }
  });

  $(".sortable").sortable();

  $(".sortable").on("sortupdate", function (event, ui) {
    let $data = $(this).sortable("serialize"); // serialize = url için uygun şekilde al
    let $data_url = $(this).data("url"); // content data-url den gelen - giden
    $.post($data_url, { data: $data }, function (response) {
      //alert(response); // 1. hangi url ye post yapılacak - 2. hangi bilgiler gönderilecek 3. callback func - response
    });
  });

  let uploadSection = Dropzone.forElement("#dropzone"); // dropzone-plugin

  uploadSection.on("complete", function (file) {
    //console.log(file);
    let $data_url = $("#dropzone").data("url");
    $.post($data_url, {}, function (response) {
      $(".image_list_container").html(response); // content/resimler
    }); //product/refresh_image_list/$item->id
  }); // son eklenen kayıtı listeye ekle---***
});
 */

$(document).ready(function () {
  $(".sortable").sortable();

  $(".content-container , .image_list_container").on(
    "click",
    ".remove-btn",
    function () {
      var $data_url = $(this).data("url");

      swal({
        title: "Emin misiniz?",
        text: "Bu işlemi geri alamayacaksınız!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Evet, Sil!",
        cancelButtonText: "Hayır",
      }).then(function (result) {
        if (result.value) {
          window.location.href = $data_url;
        }
      });
    }
  );

  $(".content-container , .image_list_container").on(
    "change",
    ".isActive",
    function () {
      var $data = $(this).prop("checked");
      var $data_url = $(this).data("url");

      if (typeof $data !== "undefined" && typeof $data_url !== "undefined") {
        $.post($data_url, { data: $data }, function (response) {});
      }
    }
  );

  $(".image_list_container").on("change", ".isCover", function () {
    var $data = $(this).prop("checked");
    var $data_url = $(this).data("url");

    if (typeof $data !== "undefined" && typeof $data_url !== "undefined") {
      $.post($data_url, { data: $data }, function (response) {
        $(".image_list_container").html(response);

        $("[data-switchery]").each(function () {
          var $this = $(this),
            color = $this.attr("data-color") || "#188ae2",
            jackColor = $this.attr("data-jackColor") || "#ffffff",
            size = $this.attr("data-size") || "default";

          new Switchery(this, {
            color: color,
            size: size,
            jackColor: jackColor,
          });
        });

        $(".sortable").sortable();
      });
    }
  });

  $(".content-container, .image_list_container ").on(
    "sortupdate",
    ".sortable",
    function (event, ui) {
      var $data = $(this).sortable("serialize");
      var $data_url = $(this).data("url");

      $.post($data_url, { data: $data }, function (response) {});
    }
  );

  var uploadSection = Dropzone.forElement("#dropzone");

  uploadSection.on("complete", function (file) {
    var $data_url = $("#dropzone").data("url");

    $.post($data_url, {}, function (response) {
      $(".image_list_container").html(response);

      $("[data-switchery]").each(function () {
        var $this = $(this),
          color = $this.attr("data-color") || "#188ae2",
          jackColor = $this.attr("data-jackColor") || "#ffffff",
          size = $this.attr("data-size") || "default";

        new Switchery(this, {
          color: color,
          size: size,
          jackColor: jackColor,
        });
      });

      $(".sortable").sortable();
    });
  });
});
