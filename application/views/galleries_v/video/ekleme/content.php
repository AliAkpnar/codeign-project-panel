<div class="row">
	<div class="col-md-12">
		<h4 class="m-b-lg">
			Yeni Video Ekle
		</h4>
	</div>
	<div class="col-md-12">
		<div class="widget">
			<div class="widget-body">
				<form action="<?php echo base_url("galleries/gallery_video_save/$gallery_id"); ?>" method="post">
					<!-- enctype="multipart/form-data - $FILES içine doldurmalı -->

					<div class="form-group">
						<label>Video URL</label>
						<input class="form-control" placeholder="Copy Video Link" name="url">
						<?php if (isset($form_error)) { ?>
							<small class="pull-right input-form-error"><?php echo form_error("url"); ?></small>
						<?php } ?>
					</div>
			</div>
			<button type="submit" class="btn btn-primary btn-md btn-outline"> Kaydet</button>
			<a href="<?php echo base_url("galleries/g_video_list/$gallery_id"); ?>" class="btn btn-md btn-danger btn-outline"> Iptal</a>
			</form>
		</div><!-- .widget-body -->
	</div><!-- .widget -->
</div>
</div>