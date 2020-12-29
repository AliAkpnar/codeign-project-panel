<?php


class Useroperation extends CI_Controller
{

    public $viewFolder = "";

    public function __construct()
    {
        parent::__construct();

        $this->viewFolder = "users_v";

        $this->load->model("user_model");
    }



    public function login()
    {

        if (get_active_user()) {
            redirect(base_url());
        }

        $this->load->library("form_validation");

        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "login";
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }


    // login izni
    public function permissionLogin()
    {

        if (get_active_user()) {
            redirect(base_url());
        }


        $this->load->library("form_validation");

        $this->form_validation->set_rules("user_email", "E-mail", "required|trim|valid_email");
        $this->form_validation->set_rules("user_password", "Şifre", "required|trim|min_length[6]|max_length[10]");

        $this->form_validation->set_message(
            array(
                "required"  => "<b>{field}</b> alanı doldurulmalıdır",
                "valid_email" => "Geçerli e-mail adresi girin",
                "min_length" => "<b>{field}</b> En az 6 karakter olmalı",
                "max_length" => "<b>{field}</b> En fazla 10 karakter olmalı"
            )
        );

        // $validate = $this->form_validation->run();


        if ($this->form_validation->run() ==  false) {

            $viewData = new stdClass();

            /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "login";
            $viewData->form_error = true;

            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {

            $user = $this->user_model->get(
                array(
                    "email" => $this->input->post("user_email"), // formdan gelen email (post)
                    "password" => md5($this->input->post("user_password")),
                    "isActive"  => 1
                )
            );

            if ($user) {

                $this->session->set_userdata("user", $user);

                $alert = array(
                    "title" => "Succesful",
                    "text"  => "Giriş Yapıldı", // $user->full_name 
                    "type"  => "success"
                );

                $this->session->set_flashdata("alert", $alert);

                redirect(base_url());
            } else {

                // Hata login olamamışsa

                $alert = array(
                    "title" => "Failed",
                    "text"  => "Giriş Bilgilerini Kontrol Et",
                    "type"  => "error"
                );

                $this->session->set_flashdata("alert", $alert);

                redirect(base_url("login"));
            }
        }
    }

    public function logout()
    {
        $this->session->unset_userdata("user");
        redirect(base_url("login"));
    }

    public function forget_password()
    {

        if (get_active_user()) {
            redirect(base_url());
        }

        $this->load->library("form_validation");

        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "forget_password";
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function reset_password()
    {

        $this->load->library("form_validation");

        $this->form_validation->set_rules("email", "E-mail", "required|trim|valid_email");

        $this->form_validation->set_message(
            array(
                "required"  => "<b>{field}</b> alanı doldurulmalıdır",
                "valid_email" => "Geçerli <b>e-mail</b> adresi girin",

            )
        );


        if ($this->form_validation->run() == false) {

            $viewData = new stdClass();

            /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "forget_password";
            $viewData->form_error = true;

            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {


            $user = $this->user_model->get(
                array(
                    "isActive"   => 1,
                    "email"      => $this->input->post("email")
                )
            );

            if ($user) {

                $this->load->model("emailsettings_model");

                $this->load->helper("string");

                $temp_password = random_string();


                $email_settings = $this->emailsettings_model->get(
                    array(
                        "isActive"   => 1
                    )
                );


                $config = array(
                    "protocol"      => $email_settings->protocol,
                    "smtp_host"     => $email_settings->host,
                    "smtp_port"     => $email_settings->port,
                    "smtp_user"     => $email_settings->user,
                    "smtp_pass"     => $email_settings->password,
                    "starttls"      => true,
                    "charset"       => "utf-8",
                    "mailtype"      => "html",
                    "wordwrap"      => true,
                    "newline"       => "\r\n"
                );

                $this->load->library("email", $config);

                $this->email->from($email_settings->from, $email_settings->user_name); // E-mail Başlık = PANEL = user_name in db

                $this->email->to($user->email);

                $this->email->subject("Şifremi Unuttum");

                $this->email->message("Panel'e geçici olarak <b>{$temp_password}</b> şifresiyle giriş yapabilirsiniz");

                $send = $this->email->send();

                if ($send) {
                    echo "Email is sent";

                    $this->user_model->update(
                        array(
                            "id"    =>    $user->id
                        ),
                        array(
                            "password"   => md5($temp_password)
                        )
                    );

                    $alert = array(
                        "title" => "Successful",
                        "text"  => "Şifreniz resetlendi",
                        "type"  => "success"
                    );

                    $this->session->set_flashdata("alert", $alert);

                    redirect(base_url("login"));
                } else {

                    $alert = array(
                        "title" => "Failed",
                        "text"  => "E-Mail Gönderilemedi",
                        "type"  => "error"
                    );

                    $this->session->set_flashdata("alert", $alert);

                    redirect(base_url("sifremi-unuttum"));

                    die();
                }
            } else {

                $alert = array(
                    "title" => "Failed",
                    "text"  => "Kullanıcı Bulunamadı",
                    "type"  => "error"
                );

                $this->session->set_flashdata("alert", $alert);

                redirect(base_url("sifremi-unuttum"));
            }
        }
    }
}
