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

        //

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

    public function update($id)
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
            redirect(base_url("product"));
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
        redirect(base_url("product"));
    }

    public function image_delete($id, $parent_id)
    {


        $file_name = $this->product_image_model->getById(
            array(
                "id"   => $id
            )
        );

        $delete = $this->product_image_model->delete(
            array(
                "id" => $id
            )
        );

        if ($delete) {

            unlink("uploads/{$this->viewFolder}/$file_name->img_url"); // yüklenenler dosyadan silinir

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
        redirect(base_url("product/image_form/$parent_id"));
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

    public function isCoverSetter($id, $parent_id)
    {

        if ($id && $parent_id) {

            $isCover = ($this->input->post("data") === "true") ? 1 : 0;

            // Kapak yapılmak istenen kayıt
            $this->product_image_model->update(
                array(
                    "id"         => $id,
                    "product_id" => $parent_id
                ),
                array(
                    "isCover"  => $isCover
                )
            );


            // Kapak yapılmayan diğer kayıtlar
            $this->product_image_model->update(
                array(
                    "id !="      => $id,
                    "product_id" => $parent_id
                ),
                array(
                    "isCover"  => 0
                )
            );

            $viewData = new stdClass();

            /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "resimler";

            $viewData->item_images = $this->product_image_model->get_all(
                array(
                    "product_id"    => $parent_id
                ),
                "rank ASC"
            );

            $render_html = $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/render_elements/image_list_v", $viewData, true);

            echo $render_html;
        }
    }

    public function imageIsActiveSetter($id)
    {

        if ($id) {

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->product_image_model->update(
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

    public function imageRankSetter()
    {
        $data = $this->input->post("data");

        parse_str($data, $order);   // gelen array i ayırma, ayrılanları order degiskenine aktarır

        $items = $order["ord"]; // "ord" diamik olarak listeleme contentin içinden gelecek

        foreach ($items as $rank => $id) {
            $this->product_image_model->update(
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

    public function image_form($id)
    {
        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "resimler";
        // veri çek db den
        $item = $this->news_model->getById(array(
            "id" => $id
        )); // dinamik başlık yazısı - resimler

        $item_images = $this->product_image_model->get_all(
            array(
                "product_id"  => $id
            ),
            "rank ASC"
        );

        $viewData->item = $item;
        $viewData->item_images = $item_images;
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function image_upload($id)
    {

        // $dosya_adi = convertToSeo($_FILES["file"] ["name"]); 
        $dosya_adi = convertToSeo(pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME)) . "." . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION); // uzantı ayarlama;

        $config["allowed_types"] = "jpg|jpeg|png";
        $config["upload_path"] = "uploads/$this->viewFolder";
        $config["file_name"] = $dosya_adi;


        $this->load->library("upload", $config);

        $upload = $this->upload->do_upload("file"); // 1 yada 0 degeri döndürür - success or not name=file dropzone daki default name



        if ($upload) {

            $uploaded_file = $this->upload->data("file_name");

            $this->product_image_model->add(
                array(
                    "img_url"      => $uploaded_file, // = dosya_adi
                    "rank"         => 0,
                    "isCover"      => 0,
                    "isActive"     => 1,
                    "createdAt"    => date("Y-m-d H:i:s"),
                    "product_id"   => $id
                ) // product_id = content dropzone $item->id
            );
        } // product/image_upload/$item->id
        else {
            echo "Failed";
        }
    }


    public function refresh_image_list($id)
    {

        $viewData = new stdClass();

        /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "image";

        $viewData->item_images = $this->product_image_model->get_all(
            array(
                "product_id"    => $id
            )
        );

        $render_html = $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/render_elements/image_list_v", $viewData, true);

        echo $render_html;
    }
}
