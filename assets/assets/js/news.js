$(document).ready(function () {
  $(".news_type_select").change(function () {
    //alert($(this).val());

    if ($(this).val() === "image") {
      //alert("image"); // image_upload_container
      $(".video_url_container").hide(); // or fadeOut
      $(".image_upload_container").fadeIn();
    } else if ($(this).val() === "video") {
      //alert("video"); // video_url_container
      $(".image_upload_container").hide();
      $(".video_url_container").fadeIn();
    }
  });
});
