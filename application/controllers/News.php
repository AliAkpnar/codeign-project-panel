<?php


class News extends CI_Controller
{

    public $viewFolder = "";

    public function __construct()
    {
        parent::__construct();

        $this->viewFolder = "news_v"; // viewFolder = news_v

        /* Product modeli-entity yükle*/
        $this->load->model("news_model");
        $this->load->model("product_image_model");
    }

    public function index()
    {

        $viewData = new stdClass();
        /*Tablodan veri getir */
        $items = $this->news_model->get_all(
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

        $news_type = $this->input->post("news_type");

        if ($news_type == "image") {

            if ($_FILES["img_url"]["name"] == "") {

                $alert = array(
                    "text"  =>  "Select a view",
                    "title" => "Failure",
                    "type"  =>  "error"
                );


                $this->session->set_flashdata("alert", $alert); // Session a yazma işlemi - anlık

                redirect(base_url("news/new_form"));
            }
        } else if ($news_type == "video") {

            $this->form_validation->set_rules("video_url", "Video URL", "required|trim"); // news/content/input -> name = video_url

        }


        $this->form_validation->set_rules("title", "Başlık", "required|trim"); //content ---> name-placeholder-required ---->kuralları ayarla
        $this->form_validation->set_message(
            array(
                "required" => "<b>{field} zorunludur</b>"
            )
        );

        $validate = $this->form_validation->run(); // FormValidation calısır

        //******************* 

        if ($validate) {

            // upload

            if ($news_type == "image") {
                $dosya_adi = convertToSeo(pathinfo($_FILES["img_url"]["name"], PATHINFO_FILENAME)) . "." . pathinfo($_FILES["img_url"]["name"], PATHINFO_EXTENSION); // uzantı ayarlama;

                $config["allowed_types"] = "jpg|jpeg|png";
                $config["upload_path"] = "uploads/$this->viewFolder";
                $config["file_name"] = $dosya_adi;


                $this->load->library("upload", $config);

                $upload = $this->upload->do_upload("img_url"); // img_url isimli file upload edildi

                if ($upload) {
                    $uploaded_file = $this->upload->data("file_name");

                    $data =  array(
                        "title"           => $this->input->post("title"),
                        "description"     => $this->input->post("description"),
                        "url"             => convertToSeo($this->input->post("title")),
                        "news_type"       => $news_type,
                        "img_url"         => $uploaded_file,
                        "video_url"       => "#",
                        "rank"            => 0,
                        "isActive"        => 1,
                        "createdAt"       => date("Y-m-d H:i:s")
                    );
                } // product/image_upload/$item->id
                else {
                    $alert = array(
                        "text"  =>  "An Error Occurred",
                        "title" => "Failure",
                        "type"  =>  "error"
                    );

                    $this->session->set_flashdata("alert", $alert); // Session a yazma işlemi 

                    redirect(base_url("news/new_form"));
                }
            } elseif ($news_type == "video") {

                $data =  array(
                    "title"           => $this->input->post("title"),
                    "description"     => $this->input->post("description"),
                    "url"             => convertToSeo($this->input->post("title")),
                    "news_type"       => $news_type,
                    "img_url"         => "#",
                    "video_url"       => $this->input->post("video_url"),
                    "rank"            => 0,
                    "isActive"        => 1,
                    "createdAt"       => date("Y-m-d H:i:s")
                );
            }


            $insert = $this->news_model->add($data);
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

            redirect(base_url("news"));
        } else {
            $viewData = new stdClass();
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "ekleme";
            $viewData->form_error = true;
            $viewData->news_type = $news_type;
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function update($id)
    {

        $this->load->library("form_validation");

        // Kurallar 

        $news_type = $this->input->post("news_type");

        if ($news_type == "video") {

            $this->form_validation->set_rules("video_url", "Video URL", "required|trim");
        }

        $this->form_validation->set_rules("title", "Başlık", "required|trim");

        $this->form_validation->set_message(
            array(
                "required"  => "<b>{field}</b> alanı doldurulmalıdır"
            )
        );

        // Form Validation Calistir
        $validate = $this->form_validation->run();

        if ($validate) {

            if ($news_type == "image") {

                // Upload Süreci


                if ($_FILES["img_url"]["name"] !== "") {

                    $file_name = convertToSEO(pathinfo($_FILES["img_url"]["name"], PATHINFO_FILENAME)) . "." . pathinfo($_FILES["img_url"]["name"], PATHINFO_EXTENSION);

                    $config["allowed_types"] = "jpg|jpeg|png";
                    $config["upload_path"] = "uploads/$this->viewFolder/";
                    $config["file_name"] = $file_name;

                    $this->load->library("upload", $config);

                    $upload = $this->upload->do_upload("img_url");

                    if ($upload) {

                        $uploaded_file = $this->upload->data("file_name");

                        $data = array(
                            "title" => $this->input->post("title"),
                            "description" => $this->input->post("description"),
                            "url" => convertToSEO($this->input->post("title")),
                            "news_type" => $news_type,
                            "img_url" => $uploaded_file,
                            "video_url" => "#",
                        );
                    } else {

                        $alert = array(
                            "title" => "An Error Occured",
                            "text" => "Error",
                            "type" => "error"
                        );

                        $this->session->set_flashdata("alert", $alert);

                        redirect(base_url("news/update_form/$id"));

                        die();
                    }
                } else {

                    $data = array(
                        "title" => $this->input->post("title"),
                        "description" => $this->input->post("description"),
                        "url" => convertToSEO($this->input->post("title")),
                    );
                }
            } else if ($news_type == "video") {

                $data = array(
                    "title"         => $this->input->post("title"),
                    "description"   => $this->input->post("description"),
                    "url"           => convertToSEO($this->input->post("title")),
                    "news_type"     => $news_type,
                    "img_url"       => "#",
                    "video_url"     => $this->input->post("video_url")
                );
            }

            $update = $this->news_model->update(array("id" => $id), $data);


            if ($update) {

                $alert = array(
                    "title" => "Success",
                    "text" => "Updated",
                    "type"  => "success"
                );
            } else {

                $alert = array(
                    "title" => "Failed",
                    "text" => "An Error Occured",
                    "type"  => "error"
                );
            }

            // İşlemin Sonucunu Session'a yazma işlemi...
            $this->session->set_flashdata("alert", $alert);

            redirect(base_url("news"));
        } else {

            $viewData = new stdClass();

            /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "update";
            $viewData->form_error = true;
            $viewData->news_type = $news_type;

            /** Tablodan Verilerin Getirilmesi.. */
            $viewData->item = $this->news_model->get(
                array(
                    "id"    => $id,
                )
            );

            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function update_form($id)
    {
        $viewData = new stdClass();

        // veri getir db den tek kayıt
        $item = $this->news_model->getById(
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

    public function update_($id)
    {
        $this->load->library("form_validation"); // kendi özelliği library

        $this->form_validation->set_rules("title", "Başlık", "required|trim"); //content ---> name-placeholder-required ---->kuralları ayarla
        $this->form_validation->set_message(
            array(
                "required" => "<b>{field} zorunludur</b>"
            )
        );

        $validate = $this->form_validation->run(); // FormValidation calısır

        //

        if ($validate) {
            $update = $this->news_model->update(
                array(
                    "id" => $id
                ),
                array(
                    "title"           => $this->input->post("title"),
                    "description"     => $this->input->post("description"),
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
            redirect(base_url("news"));
        } else {


            $viewData = new stdClass();
            // veri getir db den tek kayıt
            $item = $this->news_model->getById(
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
        $delete = $this->news_model->delete(
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
        redirect(base_url("news"));
    }

    public function isActiveSetter($id)
    {

        if ($id) {

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->news_model->update(
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
            $this->news_model->update(
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
}
