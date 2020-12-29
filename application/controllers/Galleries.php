<?php


class Galleries extends CI_Controller
{

    public $viewFolder = "";

    public function __construct()
    {
        parent::__construct();

        $this->viewFolder = "galleries_v"; // viewFolder = product_v


        if (!get_active_user()) {
            redirect(base_url("login"));
        }

        /* Product modeli-entity yükle*/
        $this->load->model("gallery_model");
        $this->load->model("image_model");
        $this->load->model("video_model");
        $this->load->model("file_model");
    }

    public function index()
    {

        $viewData = new stdClass();
        /*Tablodan veri getir */
        $items = $this->gallery_model->get_all(
            array(),
            "rank ASC"
        );

        /* View e gönderilecek Degisklenlerin set edilmesi */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "listeleme";
        $viewData->items = $items;

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData); // dinamiklestirildi
    }

    public function new_form()
    {

        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "ekleme";
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function save()
    {
        $this->load->library("form_validation"); // kendi özelliği library

        $this->form_validation->set_rules("title", "Galeri Adı", "required|trim"); //content ---> name-placeholder-required ---->kuralları ayarla
        $this->form_validation->set_message(
            array(
                "required" => "<b>{field} zorunludur</b>"
            )
        );

        $validate = $this->form_validation->run(); // FormValidation calısır

        //

        if ($validate) {

            $gallery_type = $this->input->post("gallery_type");
            $path = "uploads/$this->viewFolder/";
            $folder_name = "";

            if ($gallery_type == "image") {
                $folder_name = convertToSeo($this->input->post("title"));
                $path = "$path/images/$folder_name";
            } else if ($gallery_type == "file") {
                $folder_name = convertToSeo($this->input->post("title"));
                $path = "$path/files/$folder_name";
            }



            if ($gallery_type != "video") {
                if (!mkdir($path, 0755)) {

                    $alert = array(
                        "text"  =>  "Error",
                        "title" => "Failure",
                        "type"  =>  "error"
                    );

                    $this->session->set_flashdata("alert", $alert); // Session a yazma işlemi 

                    redirect(base_url("galleries"));
                }
            }

            $insert = $this->gallery_model->add(
                array(
                    "title"           => $this->input->post("title"),
                    "gallery_type"     => $this->input->post("gallery_type"),
                    "url"             => convertToSeo($this->input->post("title")),
                    "folder_name"     => $folder_name,
                    "rank"            => 0,
                    "isActive"        => 1,
                    "createdAt"       => date("Y-m-d H:i:s")
                )
            );
            // Alert eklenecek
            if ($insert) {
                $alert = array(
                    "text"  =>  "Added!!!",
                    "title" => "Success",
                    "type"  =>  "success"
                );
            } else {

                $alert = array(
                    "text"  =>  "Error",
                    "title" => "Failure",
                    "type"  =>  "error"
                );
            }

            $this->session->set_flashdata("alert", $alert); // Session a yazma işlemi 

            redirect(base_url("galleries"));
        } else {
            $viewData = new stdClass();
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "ekleme";
            $viewData->form_error = true;
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function update_form($id)
    {
        $viewData = new stdClass();

        // veri getir db den tek kayıt
        $item = $this->gallery_model->getById(
            array(
                "id" => $id,
                // "isActive" => 1
            )
        );


        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "guncelleme";
        $viewData->item = $item; // view e, cektigim verileri item objesi ile gönderdim.
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update($id, $gallery_type, $oldFolderName = "")
    {
        $this->load->library("form_validation"); // kendi özelliği library

        $this->form_validation->set_rules("title", "Galeri Adı", "required|trim"); //content ---> name-placeholder-required ---->kuralları ayarla
        $this->form_validation->set_message(
            array(
                "required" => "<b>{field} zorunludur</b>"
            )
        );

        $validate = $this->form_validation->run(); // FormValidation calısır

        //

        if ($validate) {

            $path = "uploads/$this->viewFolder/";
            $folder_name = "";

            if ($gallery_type == "image") {
                $folder_name = convertToSeo($this->input->post("title"));
                $path = "$path/images";
            } else if ($gallery_type == "file") {
                $folder_name = convertToSeo($this->input->post("title"));
                $path = "$path/files";
            }



            if ($gallery_type != "video") {
                if (!rename("$path/$oldFolderName", "$path/$folder_name")) {

                    $alert = array(
                        "text"  =>  "Error",
                        "title" => "Failure",
                        "type"  =>  "error"
                    );

                    $this->session->set_flashdata("alert", $alert); // Session a yazma işlemi 

                    redirect(base_url("galleries"));
                    die();
                }
            }




            $update = $this->gallery_model->update(
                array(
                    "id" => $id
                ),
                array(
                    "title"           => $this->input->post("title"),
                    "folder_name"     => $folder_name,
                    "url"             => convertToSeo($this->input->post("title")),
                ) // id ile bulunan bulunan veriyi getir ve 2.arraydeki ile degisştir. 
            );
            // Alert eklenecek
            if ($update) {

                $alert = array(
                    "text"  =>  "Updated!!!",
                    "title" => "Success",
                    "type"  =>  "warning"
                );
            } else {

                $alert = array(
                    "text"  =>  "Error",
                    "title" => "Failure",
                    "type"  =>  "error"
                );
            }

            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("galleries"));
            die();
        } else {


            $viewData = new stdClass();
            // veri getir db den tek kayıt
            $item = $this->gallery_model->getById(
                array(
                    "id" => $id
                    // "isActive" => 1
                )
            );


            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "guncelleme";
            $viewData->form_error = true;
            $viewData->item = $item; // guncelleme-content de item olmalı
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function delete($id)
    {


        $gallery = $this->gallery_model->getById(
            array(
                "id"   => $id
            )
        );

        if ($gallery) {

            if ($gallery->gallery_type != "video") {

                if ($gallery->gallery_type == "image") {
                    $path = "uploads/$this->viewFolder/images/$gallery->folder_name";
                } elseif ($gallery->gallery_type == "file") {
                    $path = "uploads/$this->viewFolder/files/$gallery->folder_name";
                }

                $delete_folder = rmdir($path);

                if (!$delete_folder) {

                    $alert = array(
                        "text"  =>  "Error",
                        "title" => "Failure",
                        "type"  =>  "error"
                    );

                    $this->session->set_flashdata("alert", $alert);
                    redirect(base_url("galleries"));
                }
            }

            $delete = $this->gallery_model->delete(
                array(
                    "id" => $id
                )
            );
            if ($delete) {

                $alert = array(
                    "text"  =>  "Deleted!!!",
                    "title" => "Success",
                    "type"  =>  "error"
                );
            } else {

                $alert = array(
                    "text"  =>  "Error",
                    "title" => "Failure",
                    "type"  =>  "error"
                );
            }
            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("galleries"));
        }
    }

    public function fileDelete($id, $parent_id, $gallery_type)
    {

        $modelName = ($gallery_type == "image") ? "image_model" : "file_model";

        $file_name = $this->$modelName->getById(
            array(
                "id"   => $id
            )
        );

        $delete = $this->$modelName->delete(
            array(
                "id" => $id
            )
        );

        if ($delete) {

            unlink($file_name->url); // yüklenenler sunucudan-kaynak dosyadan silinir

            $alert = array(
                "text"  =>  "Deleted!!!",
                "title" => "Success",
                "type"  =>  "error"
            );
        } else {
            $alert = array(
                "text"  =>  "Error",
                "title" => "Failure",
                "type"  =>  "error"
            );
        }
        $this->session->set_flashdata("alert", $alert);
        redirect(base_url("galleries/upload_form/$parent_id"));
    }


    public function isActiveSetter($id)
    {

        if ($id) {

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->gallery_model->update(
                array(
                    "id"    => $id
                ),
                array(
                    "isActive"  => $isActive
                )
            );
        }
    }


    public function fileIsActiveSetter($id, $gallery_type)
    {

        if ($id && $gallery_type) {

            $modelName = ($gallery_type == "image") ? "image_model" : "file_model";

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->$modelName->update(
                array(
                    "id"    => $id
                ),
                array(
                    "isActive"  => $isActive
                )
            );
        }
    }


    public function rankSetter()
    {
        $data = $this->input->post("data");

        parse_str($data, $order);   // gelen array i ayırma, ayrılanları order degiskenine aktarır

        $items = $order["ord"]; // "ord" diamik olarak listeleme contentin içinden gelecek

        foreach ($items as $rank => $id) {
            $this->gallery_model->update(
                array(
                    "id" => $id,
                    "rank !=" => $rank // sıralama degismemisse aynı kalacak
                ),
                array(
                    "rank" => $rank
                )
            );
        }
    }

    public function fileRankSetter($gallery_type)
    {
        $data = $this->input->post("data");

        parse_str($data, $order);   // gelen array i ayırma, ayrılanları order degiskenine aktarır

        $items = $order["ord"]; // "ord" diamik olarak listeleme contentin içinden gelecek

        $modelName = ($gallery_type == "image") ? "image_model" : "file_model";

        foreach ($items as $rank => $id) {
            $this->$modelName->update(
                array(
                    "id" => $id,
                    "rank !=" => $rank // sıralama degismemisse aynı kalacak
                ),
                array(
                    "rank" => $rank
                )
            );
        }
    }

    public function upload_form($id)
    {
        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "resimler";
        // veri çek db den
        $item = $this->gallery_model->getById(array(
            "id" => $id
        ));

        if ($item->gallery_type == "image") {

            $items = $this->image_model->get_all(
                array(
                    "gallery_id"  => $id
                ),
                "rank ASC"
            );
        } elseif ($item->gallery_type == "file") {

            $items = $this->file_model->get_all(
                array(
                    "gallery_id"  => $id
                ),
                "rank ASC"
            );
        } else {

            $items = $this->video_model->get_all(
                array(
                    "gallery_id"  => $id
                ),
                "rank ASC"
            );
        }


        $viewData->gallery_type = $item->gallery_type;

        $viewData->item = $item;
        $viewData->items = $items;
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function file_upload($gallery_id, $gallery_type, $gallery_folderName)
    {

        // $dosya_adi = convertToSeo($_FILES["file"] ["name"]); 
        $dosya_adi = convertToSeo(pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME)) . "." . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION); // uzantı ayarlama;

        $config["allowed_types"] = "*";
        $config["upload_path"] = ($gallery_type == "image") ? "uploads/$this->viewFolder/images/$gallery_folderName" :  "uploads/$this->viewFolder/files/$gallery_folderName";
        $config["file_name"] = $dosya_adi;


        $this->load->library("upload", $config);

        $upload = $this->upload->do_upload("file"); // 1 yada 0 degeri döndürür - success or not name=file dropzone daki default name



        if ($upload) {

            $uploaded_file = $this->upload->data("file_name");

            $model_name = ($gallery_type == "image") ? "image_model" : "file_model";

            $this->$model_name->add(
                array(
                    "url"          => "{$config["upload_path"]}/$uploaded_file", // = dosya_adi
                    "rank"         => 0,
                    "isActive"     => 1,
                    "createdAt"    => date("Y-m-d H:i:s"),
                    "gallery_id"   => $gallery_id
                ) // product_id = content dropzone $item->id
            );
        } // product/image_upload/$item->id
        else {
            echo "Failed";
        }
    }


    public function refresh_file_list($gallery_id, $gallery_type)
    {

        $viewData = new stdClass();

        /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "image";

        $modelName = ($gallery_type == "image") ? "image_model" : "file_model";

        $viewData->items = $this->$modelName->get_all(
            array(
                "gallery_id"    => $gallery_id
            )
        );

        $viewData->gallery_type = $gallery_type;

        $render_html = $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/render_elements/file_list_v", $viewData, true);

        echo $render_html;
    }



    public function g_video_list($id)
    {

        $viewData = new stdClass();

        $gallery = $this->gallery_model->getById(
            array(
                "id"  => $id
            )
        );
        /*Tablodan veri getir */
        $items = $this->video_model->get_all(
            array(
                "gallery_id" => $id
            ),
            "rank ASC"
        );

        /* View e gönderilecek Degisklenlerin set edilmesi */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "video/listeleme";
        $viewData->items = $items;
        $viewData->gallery = $gallery;

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData); // dinamiklestirildi
    }





    public function new_gallery_video_form($id)
    {

        $viewData = new stdClass();

        $viewData->gallery_id = $id;

        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "video/ekleme";
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }


    public function gallery_video_save($id)
    {
        $this->load->library("form_validation"); // kendi özelliği library

        $this->form_validation->set_rules("url", "Video URL", "required|trim"); //content ---> name-placeholder-required ---->kuralları ayarla
        $this->form_validation->set_message(
            array(
                "required" => "<b>{field} zorunludur</b>"
            )
        );

        $validate = $this->form_validation->run(); // FormValidation calısır

        //

        if ($validate) {


            $insert = $this->video_model->add(
                array(
                    "url"             => $this->input->post("url"),
                    "gallery_id"     => $id,
                    "rank"            => 0,
                    "isActive"        => 1,
                    "createdAt"       => date("Y-m-d H:i:s")
                )
            );
            // Alert eklenecek
            if ($insert) {
                $alert = array(
                    "text"  =>  "Added!!!",
                    "title" => "Success",
                    "type"  =>  "success"
                );
            } else {

                $alert = array(
                    "text"  =>  "Error",
                    "title" => "Failure",
                    "type"  =>  "error"
                );
            }

            $this->session->set_flashdata("alert", $alert); // Session a yazma işlemi 

            redirect(base_url("galleries/g_video_list/$id"));
        } else {
            $viewData = new stdClass();
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "video/ekleme";
            $viewData->gallery_id = $id;
            $viewData->form_error = true;
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function update_gallery_video_form($id)
    {
        $viewData = new stdClass();

        // veri getir db den tek kayıt
        $item = $this->video_model->getById(
            array(
                "id" => $id,
                // "isActive" => 1
            )
        );


        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "video/guncelleme";
        $viewData->item = $item; // view e, cektigim verileri item objesi ile gönderdim.
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }



    public function gallery_video_update($id, $gallery_id)
    {
        $this->load->library("form_validation"); // kendi özelliği library

        $this->form_validation->set_rules("url", "Video URL", "required|trim"); //content ---> name-placeholder-required ---->kuralları ayarla
        $this->form_validation->set_message(
            array(
                "required" => "<b>{field} zorunludur</b>"
            )
        );

        $validate = $this->form_validation->run(); // FormValidation calısır

        //

        if ($validate) {


            $update = $this->video_model->update(
                array(
                    "id"   => $id
                ),
                array(
                    "url"  => $this->input->post("url"),
                ) // id ile bulunan bulunan veriyi getir ve 2.arraydeki ile degisştir. 
            );
            // Alert eklenecek
            if ($update) {

                $alert = array(
                    "text"  =>  "Updated!!!",
                    "title" => "Success",
                    "type"  =>  "warning"
                );
            } else {

                $alert = array(
                    "text"  =>  "Error",
                    "title" => "Failure",
                    "type"  =>  "error"
                );
            }

            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("galleries/g_video_list/$gallery_id"));
        } else {


            $viewData = new stdClass();
            // veri getir db den tek kayıt
            $item = $this->video_model->getById(
                array(
                    "id" => $id
                    // "isActive" => 1
                )
            );


            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "video/guncelleme";
            $viewData->form_error = true;
            $viewData->item = $item; // guncelleme-content de item olmalı
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }



    public function rankGalleryVideoSetter()
    {
        $data = $this->input->post("data");

        parse_str($data, $order);   // gelen array i ayırma, ayrılanları order degiskenine aktarır

        $items = $order["ord"]; // "ord" diamik olarak listeleme contentin içinden gelecek

        foreach ($items as $rank => $id) {
            $this->video_model->update(
                array(
                    "id" => $id,
                    "rank !=" => $rank // sıralama degismemisse aynı kalacak
                ),
                array(
                    "rank" => $rank
                )
            );
        }
    }



    public function isGalleryVideoActiveSetter($id)
    {

        if ($id) {

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->video_model->update(
                array(
                    "id"    => $id
                ),
                array(
                    "isActive"  => $isActive
                )
            );
        }
    }


    public function galleryVideoDelete($id, $gallery_id)
    {

        $delete = $this->video_model->delete(
            array(
                "id" => $id
            )
        );
        if ($delete) {

            $alert = array(
                "text"  =>  "Deleted!!!",
                "title" => "Success",
                "type"  =>  "error"
            );
        } else {

            $alert = array(
                "text"  =>  "Error",
                "title" => "Failure",
                "type"  =>  "error"
            );
        }
        $this->session->set_flashdata("alert", $alert);
        redirect(base_url("galleries/g_video_list/$gallery_id"));
    }
}
