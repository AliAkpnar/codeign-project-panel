<div class="simple-page-wrap">
    <div class="simple-page-logo animated swing">
        <a href="index.html">
            <span><i class="fa fa-gg"></i></span>
            <span>Panel Control</span>
        </a>
    </div><!-- logo -->
    <div class="simple-page-form animated flipInY" id="login-form">
        <h4 class="form-title m-b-xl text-center">Panel Giriş</h4>
        <form action="<?php echo base_url("useroperation/permissionLogin") ?>" method="POST">
            <div class="form-group">
                <input id="sign-in-email" type="email" class="form-control" placeholder="Email" name="user_email">

                <?php if (isset($form_error)) { ?>
                    <small class="pull-right input-form-error"><?php echo form_error("user_email"); ?></small>
                <?php } ?>

            </div>

            <div class="form-group">
                <input id="sign-in-password" type="password" class="form-control" placeholder="Şifre" name="user_password">
                <?php if (isset($form_error)) { ?>
                    <small class="pull-right input-form-error"><?php echo form_error("user_password"); ?></small>
                <?php } ?>

            </div>

            <button type="submit" class="btn btn-primary">Giriş Yap</button>
        </form>
    </div><!-- #login-form -->

    <div class="simple-page-footer">
        <p><a href="sifremi-unuttum">Şifremi Unuttum</a></p>
    </div><!-- .simple-page-footer -->


</div><!-- .simple-page-wrap -->