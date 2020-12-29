<?php

class Users extends CI_Controller
{
    public $viewFolder = "";

    public function __construct()
    {

        parent::__construct();

        $this->viewFolder = "users_v";


        if (!get_active_user()) {
            redirect(base_url("login"));
        }

        $this->load->model("user_model");
    }

    public function index()
    {

        $viewData = new stdClass();

        /** Tablodan Verilerin Getirilmesi.. */
        $items = $this->user_model->get_all(
            array()
        );

        /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "listeleme";
        $viewData->items = $items;

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function new_form()
    {

        $viewData = new stdClass();

        /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "ekleme";

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function save()
    {

        $this->load->library("form_validation");

        // Kurallar yaz

        $this->form_validation->set_rules("user_name", "Kullanıcı Adı", "required|trim|is_unique[users.user_name]");
        $this->form_validation->set_rules("full_name", "Ad Soyad", "required|trim");
        $this->form_validation->set_rules("email", "E-mail", "required|trim|valid_email|is_unique[users.email]");
        $this->form_validation->set_rules("password", "Şifre", "required|trim|min_length[6]|max_length[10]");
        $this->form_validation->set_rules("re_password", "Şifre Tekrar", "required|trim|min_length[6]|max_length[10]|matches[password]");

        $this->form_validation->set_message(
            array(
                "required"  => "<b>{field}</b> alanı doldurulmalıdır",
                "valid_email" => "Geçerli e-mail adresi girin",
                "is_unique" => "<b>{field}</b> alanı kayıtlıdır",
                "matches"  => "Şifreler eşleşmedi.. Tekrar Girin",
                "min_length" => "En az 6 karakter girilmeli",
                "max_length" => "En fazla 10 karakter girilmeli"
            )
        );

        // Form Validation Calistir
        $validate = $this->form_validation->run();

        if ($validate) {



            $insert = $this->user_model->add(
                array(
                    "user_name"     => $this->input->post("user_name"),
                    "full_name"     => $this->input->post("full_name"),
                    "email"         => $this->input->post("email"),
                    "password"      => md5($this->input->post("password")),
                    "isActive"      => 1,
                    "createdAt"     => date("Y-m-d H:i:s")
                )
            );


            if ($insert) {

                $alert = array(
                    "title" => "Succesful",
                    "text" => "Added",
                    "type"  => "success"
                );
            } else {

                $alert = array(
                    "title" => "An Error Occured",
                    "text" => "Error",
                    "type"  => "error"
                );
            }


            $this->session->set_flashdata("alert", $alert);

            redirect(base_url("users"));

            die();
        } else {

            $viewData = new stdClass();

            /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "ekleme";
            $viewData->form_error = true;

            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function update_form($id)
    {

        $viewData = new stdClass();

        /** Tablodan Verilerin Getirilmesi.. */
        $item = $this->user_model->get(
            array(
                "id"    => $id,
            )
        );

        /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "guncelleme";
        $viewData->item = $item;

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update_password_form($id)
    {

        $viewData = new stdClass();

        /** Tablodan Verilerin Getirilmesi.. */
        $item = $this->user_model->get(
            array(
                "id"    => $id,
            )
        );

        /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "password";
        $viewData->item = $item;

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }


    public function update($id)
    {

        $this->load->library("form_validation");

        $oldUser = $this->user_model->get(
            array(
                "id" => $id
            )
        );


        if ($oldUser->email != $this->input->post("email")) {
            $this->form_validation->set_rules("email", "E-mail", "required|trim|valid_email|is_unique[users.email]");
        }

        if ($oldUser->user_name != $this->input->post("user_name")) {
            $this->form_validation->set_rules("user_name", "Kullanıcı Adı", "required|trim|is_unique[users.user_name]");
        }



        $this->form_validation->set_rules("full_name", "Ad Soyad", "required|trim");



        $this->form_validation->set_message(
            array(
                "required"  => "<b>{field}</b> alanı doldurulmalıdır",
                "valid_email" => "Geçerli e-mail adresi girin",
                "is_unique" => "<b>{field}</b> alanı kayıtlıdır"
            )
        );

        // Form Validation Calistir
        $validate = $this->form_validation->run();

        if ($validate) {

            // Upload Süreci


            $update = $this->user_model->update(
                array("id" => $id),
                array(
                    "user_name"     => $this->input->post("user_name"),
                    "full_name"     => $this->input->post("full_name"),
                    "email"         => $this->input->post("email")
                )
            );

            // TODO Alert 
            if ($update) {

                $alert = array(
                    "title" => "Succesful",
                    "text" => "Updated",
                    "type"  => "warning"
                );
            } else {

                $alert = array(
                    "title" => "An Error Occured",
                    "text" => "Error",
                    "type"  => "error"
                );
            }

            // İşlemin Sonucunu Session'a yazma işlemi...
            $this->session->set_flashdata("alert", $alert);

            redirect(base_url("users"));
        } else {

            $viewData = new stdClass();

            /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "guncelleme";
            $viewData->form_error = true;

            /** Tablodan Verilerin Getirilmesi.. */
            $viewData->item = $this->user_model->get(
                array(
                    "id"    => $id,
                )
            );

            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function update_password($id)
    {

        $this->load->library("form_validation");

        $this->form_validation->set_rules("password", "Şifre", "required|trim|min_length[6]|max_length[10]");
        $this->form_validation->set_rules("re_password", "Şifre Tekrar", "required|trim|min_length[6]|max_length[10]|matches[password]");


        $this->form_validation->set_message(
            array(
                "required"  => "<b>{field}</b> alanı doldurulmalıdır",
                "matches"   => "Şifreler eşleşmedi"
            )
        );

        // Form Validation Calistir
        $validate = $this->form_validation->run();

        if ($validate) {

            // Upload Süreci


            $update = $this->user_model->update(
                array("id" => $id),
                array(
                    "password"     => md5($this->input->post("password")),
                )
            );

            // TODO Alert 
            if ($update) {

                $alert = array(
                    "title" => "Succesful",
                    "text" => "Updated",
                    "type"  => "warning"
                );
            } else {

                $alert = array(
                    "title" => "An Error Occured",
                    "text" => "Error",
                    "type"  => "error"
                );
            }

            // İşlemin Sonucunu Session'a yazma işlemi...
            $this->session->set_flashdata("alert", $alert);

            redirect(base_url("users"));
        } else {

            $viewData = new stdClass();

            /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "password";
            $viewData->form_error = true;

            /** Tablodan Verilerin Getirilmesi.. */
            $viewData->item = $this->user_model->get(
                array(
                    "id"    => $id,
                )
            );

            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }



    public function delete($id)
    {

        $delete = $this->user_model->delete(
            array(
                "id"    => $id
            )
        );

        // TODO Alert Sistemi Eklenecek...
        if ($delete) {

            $alert = array(
                "title" => "Succesful",
                "text" => "Deleted",
                "type"  => "success"
            );
        } else {

            $alert = array(
                "title" => "An Error Ocuured",
                "text" => "Error",
                "type"  => "error"
            );
        }

        $this->session->set_flashdata("alert", $alert);
        redirect(base_url("users"));
    }

    public function isActiveSetter($id)
    {

        if ($id) {

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->user_model->update(
                array(
                    "id"    => $id
                ),
                array(
                    "isActive"  => $isActive
                )
            );
        }
    }
}
