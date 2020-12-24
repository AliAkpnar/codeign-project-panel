<?php

  $alert = $this->session->userdata("alert");

  if($alert){
    if($alert["type"] === "success"){ ?>
        <script>
              iziToast.success({
              title: '<?php echo $alert["title"]; ?>',
              message: '<?php echo $alert["text"]; ?>',
              position: "bottomRight",
            });
        </script>
    <?php } elseif($alert["type"] === "error"){ ?>
      <script>
              iziToast.error({
              title: '<?php echo $alert["title"]; ?>',
              message: '<?php echo $alert["text"]; ?>',
              position: "bottomRight",
            });
        </script>
    <?php } else { ?>   
        <script>
              iziToast.warning({
              title: '<?php echo $alert["title"]; ?>',
              message: '<?php echo $alert["text"]; ?>',
              position: "bottomRight",
            });
        </script>
    <?php } 
  }
?>