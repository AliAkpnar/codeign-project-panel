$(document).ready(function () {
  $(".news_type_select").change(function () {
    //alert($(this).val());

    if ($(this).val() === "image") {
      //alert("image"); // image_upload_container
      $(".video_url_container").fadeOut();
      $(".image_upload_container").fadeIn(); // fadeIn ile ekranda gösterilecek
    } else if ($(this).val() === "video") {
      //alert("video"); // video_url_container
      $(".image_upload_container").fadeOut();
      $(".video_url_container").fadeIn();
    }
  });
});
